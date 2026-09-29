@php
    // Single source for both the profile gate and the application branding, so the
    // sidebar, title and favicon cost one query rather than one each.
    $profile = \App\Models\CompanyProfile::current();

    // missingFieldsOn() mirrors CompanyProfile::missingFields() used by the
    // EnsureCompanyProfileIsComplete middleware, so the lock and its explanation can
    // never disagree.
    $profileMissing = $profile->missingFieldsOn();
    $navLocked = $profileMissing !== [];

    $brandName = $profile->displayName();
    $brandSubtitle = $profile->displaySubtitle();
    $brandLogoUrl = $profile->logoUrl();
    $brandFaviconUrl = $profile->faviconUrl();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ filled($title ?? null) ? $title.' · '.$brandName : $brandName }}</title>
        <link rel="icon" type="image/x-icon" href="{{ $brandFaviconUrl ?? asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ $brandLogoUrl ?? asset('favicon.ico') }}">
        <link rel="stylesheet" href="{{ asset('css/ag-grid.css') }}">
        <link rel="stylesheet" href="{{ asset('css/ag-theme-quartz.css') }}">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .sidebar-children {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.25s ease-in-out;
            }
            .sidebar-children--open {
                max-height: 500px;
            }
            #sidebar nav {
                scrollbar-width: thin;
                scrollbar-color: var(--color-sidebar-border) transparent;
            }
            #sidebar nav::-webkit-scrollbar {
                width: 6px;
            }
            #sidebar nav::-webkit-scrollbar-thumb {
                background: var(--color-sidebar-border);
                border-radius: 9999px;
            }
            #sidebar nav::-webkit-scrollbar-track {
                background: transparent;
            }
        </style>
    </head>
    <body class="h-full bg-page-bg font-sans antialiased" data-theme="{{ auth()->user()->theme ?? 'slate-orange' }}">
        <div class="min-h-screen">
            <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-sidebar-bg/50 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden" aria-hidden="true"></div>

            <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 transform-gpu bg-sidebar-bg transition-transform duration-200 ease-out -translate-x-full lg:translate-x-0">
                <div class="flex h-full flex-col">
                    <div class="flex h-16 items-center gap-3 border-b border-sidebar-border px-6">
                        @if ($brandLogoUrl)
                            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="h-9 w-9 shrink-0 rounded-lg bg-white object-contain p-0.5">
                        @else
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="8.5" cy="7" r="4" />
                                    <path d="M20 8v6m3-3h-6" />
                                </svg>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <span class="block truncate text-lg font-semibold tracking-tight text-white" title="{{ $brandName }}">{{ $brandName }}</span>
                            @if ($brandSubtitle)
                                <span class="block truncate text-xs text-sidebar-muted" title="{{ $brandSubtitle }}">{{ $brandSubtitle }}</span>
                            @endif
                        </div>
                    </div>

                    <nav class="flex-1 min-h-0 space-y-1 overflow-y-auto overflow-x-hidden px-4 py-6">
                        @foreach (config('system.navigation') as $section)
                            @php
                                $visibleItems = collect($section['items'])->filter(function ($item) {
                                    if (auth()->user()->isSuperAdmin()) return true;
                                    return auth()->user()->can($item['permission'] ?? '__none__');
                                });
                            @endphp

                            @if ($visibleItems->isNotEmpty())
                                <div class="text-xs font-semibold uppercase tracking-wider text-sidebar-muted px-2 mb-2">{{ $section['title'] }}</div>

                                @foreach ($visibleItems as $item)
                                    @if (! empty($item['children']))
                                        @php
                                            $hasChildren = true;
                                            $visibleChildren = collect($item['children'])->filter(function ($child) {
                                                if (auth()->user()->isSuperAdmin()) return true;
                                                return auth()->user()->can($child['permission'] ?? '__none__');
                                            });
                                            $routePattern = str_ends_with($item['route'], '.index')
                                                ? str_replace('.index', '.*', $item['route'])
                                                : $item['route'];
                                            $isParentActive = $visibleChildren->contains(function ($child) {
                                                $routePattern = str_ends_with($child['route'], '.index')
                                                    ? str_replace('.index', '.*', $child['route'])
                                                    : $child['route'];
                                                return request()->routeIs($routePattern);
                                            });
                                            $isParentCurrentPage = request()->routeIs($routePattern);
                                            $isActive = $isParentActive || $isParentCurrentPage;
                                            $menuKey = Str::slug($item['label']);
                                            $parentLocked = $navLocked && $item['route'] !== 'company-profile.index' && ! $isParentCurrentPage;
                                        @endphp

                                        @if ($visibleChildren->isNotEmpty())
                                            <div class="sidebar-parent" data-menu="{{ $menuKey }}">
                                                <div class="flex items-center rounded-lg {{ $isActive ? 'bg-sidebar-active' : '' }}">
                                                    <a href="{{ route($item['route']) }}" class="sidebar-nav-item flex-1 min-w-0 flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ $isActive ? 'text-white sidebar-nav-item--active' : 'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text' }} transition-colors {{ $parentLocked ? 'pointer-events-none opacity-40' : '' }}">
                                                        @include('components.icons.' . $item['icon'], ['classes' => 'sidebar-nav-icon shrink-0 h-5 w-5 ' . ($isActive ? 'text-primary-500' : 'text-sidebar-muted group-hover:text-sidebar-icon-hover')])
                                                        <span class="min-w-0 truncate">{{ $item['label'] }}</span>
                                                    </a>
                                                    <button type="button" onclick="toggleSidebarMenu('{{ $menuKey }}')" class="sidebar-parent-btn px-3 py-2.5 text-sm {{ $isActive ? 'text-white' : 'text-sidebar-text hover:text-sidebar-hover-text' }} transition-colors {{ $parentLocked ? 'pointer-events-none opacity-40' : '' }}">
                                                        <svg class="h-4 w-4 transition-transform duration-200 {{ $isActive ? 'rotate-0' : '-rotate-90' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="m6 9 6 6 6-6" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="sidebar-children {{ $isActive ? 'sidebar-children--open' : '' }}">
                                                    @foreach ($visibleChildren as $child)
                                                        @php
                                                            $childRoutePattern = str_ends_with($child['route'], '.index')
                                                                ? str_replace('.index', '.*', $child['route'])
                                                                : $child['route'];
                                                            $isChildActive = request()->routeIs($childRoutePattern);
                                                            $childLocked = $navLocked && $child['route'] !== 'company-profile.index' && ! $isChildActive;
                                                        @endphp
                                                        <a href="{{ route($child['route']) }}" class="sidebar-child sidebar-nav-item flex min-w-0 items-center gap-2.5 pl-11 pr-3 py-2 text-xs font-medium rounded-lg transition-colors {{ $isChildActive ? 'text-primary-500 bg-primary-50 sidebar-nav-item--active' : 'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text' }} {{ $childLocked ? 'pointer-events-none opacity-40' : '' }}">
                                                            @if (! empty($child['icon']))
                                                                @include('components.icons.' . $child['icon'], ['classes' => 'sidebar-nav-icon shrink-0 h-4 w-4 ' . ($isChildActive ? 'text-primary-500' : 'text-sidebar-muted')])
                                                            @endif
                                                            <span class="min-w-0 truncate">{{ $child['label'] }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        @php
                                            $routePattern = str_ends_with($item['route'], '.index')
                                                ? str_replace('.index', '.*', $item['route'])
                                                : $item['route'];
                                            $isActive = request()->routeIs($routePattern);
                                            $iconClasses = 'h-5 w-5 ' . ($isActive ? 'text-primary-500' : 'text-sidebar-muted group-hover:text-sidebar-icon-hover');
                                            $itemLocked = $navLocked && $item['route'] !== 'company-profile.index' && ! $isActive;
                                        @endphp
                                        <a href="{{ route($item['route']) }}" class="sidebar-nav-item group flex min-w-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ $isActive ? 'text-white bg-sidebar-active sidebar-nav-item--active' : 'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text' }} transition-colors {{ $itemLocked ? 'pointer-events-none opacity-40' : '' }}">
                                            @include('components.icons.' . $item['icon'], ['classes' => 'sidebar-nav-icon shrink-0 ' . $iconClasses])
                                            <span class="min-w-0 truncate">{{ $item['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </nav>

                    <div class="border-t border-sidebar-border p-4">
                        <div class="mb-4 px-2">
                            <div class="text-xs font-semibold uppercase tracking-wider text-sidebar-muted mb-2">Theme</div>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('theme.update') }}" class="contents">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="theme" value="slate-orange">
                                    <button
                                        type="submit"
                                        aria-label="Use orange accent"
                                        class="h-8 w-8 rounded-full bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-sidebar-bg focus:ring-white {{ auth()->user()->theme === 'slate-orange' ? 'ring-2 ring-offset-2 ring-offset-sidebar-bg ring-white' : '' }}"
                                    ></button>
                                </form>

                                <form method="POST" action="{{ route('theme.update') }}" class="contents">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="theme" value="slate-blue">
                                    <button
                                        type="submit"
                                        aria-label="Use blue accent"
                                        class="h-8 w-8 rounded-full bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-sidebar-bg focus:ring-white {{ auth()->user()->theme === 'slate-blue' ? 'ring-2 ring-offset-2 ring-offset-sidebar-bg ring-white' : '' }}"
                                    ></button>
                                </form>

                                <form method="POST" action="{{ route('theme.update') }}" class="contents">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="theme" value="slate-teal">
                                    <button
                                        type="submit"
                                        aria-label="Use teal accent"
                                        class="h-8 w-8 rounded-full bg-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-sidebar-bg focus:ring-white {{ auth()->user()->theme === 'slate-teal' ? 'ring-2 ring-offset-2 ring-offset-sidebar-bg ring-white' : '' }}"
                                    ></button>
                                </form>

                                <form method="POST" action="{{ route('theme.update') }}" class="contents">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="theme" value="slate-violet">
                                    <button
                                        type="submit"
                                        aria-label="Use violet accent"
                                        class="h-8 w-8 rounded-full bg-violet-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-sidebar-bg focus:ring-white {{ auth()->user()->theme === 'slate-violet' ? 'ring-2 ring-offset-2 ring-offset-sidebar-bg ring-white' : '' }}"
                                    ></button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="group flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-sidebar-muted group-hover:text-sidebar-icon-hover">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <path d="M16 17l5-5-5-5" />
                                    <path d="M21 12H9" />
                                </svg>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="lg:pl-64">
                <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-topbar-border bg-topbar-bg/80 px-4 backdrop-blur sm:px-6 lg:px-8">
                    <button id="sidebar-toggle" type="button" class="-ml-2 inline-flex items-center justify-center rounded-md p-2 text-topbar-icon hover:bg-page-bg hover:text-topbar-text lg:hidden">
                        <span class="sr-only">Open sidebar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                            <path d="M4 6h16" />
                            <path d="M4 12h16" />
                            <path d="M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex flex-1 items-center justify-end gap-4">
                        <div class="relative hidden w-full max-w-xs sm:block">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-topbar-muted">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                            <input type="search" placeholder="Search..." class="w-full rounded-lg border-0 bg-page-bg py-2 pl-9 pr-4 text-sm text-topbar-text ring-1 ring-inset ring-topbar-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500">
                        </div>

                        <button type="button" class="relative rounded-full p-2 text-topbar-icon hover:bg-page-bg hover:text-topbar-text">
                            <span class="sr-only">Notifications</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                            </svg>
                            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-primary-500 ring-2 ring-white"></span>
                        </button>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 border-l border-topbar-border pl-4 transition-all duration-200 hover:opacity-80">
                            <div class="hidden text-right sm:block">
                                <div class="text-sm font-medium text-topbar-text">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-topbar-muted">{{ auth()->user()->email }}</div>
                            </div>
                            <div class="h-9 w-9 rounded-full bg-primary-100 p-0.5">
                                <div class="flex h-full w-full items-center justify-center rounded-full bg-primary-500 text-sm font-semibold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </div>
                        </a>
                    </div>
                </header>

                <main class="px-4 py-8 sm:px-6 lg:px-8">
                    @if ($profileMissing)
                        <div class="mb-6 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-inset ring-amber-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4" />
                                <path d="M12 16h.01" />
                            </svg>
                            <div>
                                <p class="font-semibold">Complete your company profile to continue</p>
                                <p class="mt-0.5 text-amber-700">Missing {{ \Illuminate\Support\Str::plural('field', count($profileMissing)) }}: {{ implode(', ', $profileMissing) }}. Every other screen stays locked until {{ count($profileMissing) === 1 ? 'it is' : 'they are' }} set.</p>
                            </div>
                            <a href="{{ route('company-profile.index') }}" class="ml-auto shrink-0 rounded-lg bg-amber-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-amber-700">Set it now</a>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-alerts />

        <script>
            (function () {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                const toggle = document.getElementById('sidebar-toggle');

                function setOpen(open) {
                    sidebar.classList.toggle('-translate-x-full', !open);
                    sidebar.classList.toggle('translate-x-0', open);
                    backdrop.classList.toggle('opacity-0', !open);
                    backdrop.classList.toggle('pointer-events-none', !open);
                }

                if (toggle) {
                    toggle.addEventListener('click', () => {
                        const isClosed = sidebar.classList.contains('-translate-x-full');
                        setOpen(isClosed);
                    });
                }

                if (backdrop) {
                    backdrop.addEventListener('click', () => setOpen(false));
                }

                // Sidebar sub-menu expand/collapse
                const menuStates = JSON.parse(localStorage.getItem('sidebar-menus') || '{}');

                document.querySelectorAll('.sidebar-parent[data-menu]').forEach(function (parent) {
                    const key = parent.dataset.menu;
                    const btn = parent.querySelector('.sidebar-parent-btn');
                    const children = parent.querySelector('.sidebar-children');
                    const chevron = btn.querySelector('svg:last-child');

                    if (menuStates[key] === true) {
                        children.classList.add('sidebar-children--open');
                        chevron.classList.remove('-rotate-90');
                        chevron.classList.add('rotate-0');
                    } else if (menuStates[key] === false) {
                        children.classList.remove('sidebar-children--open');
                        chevron.classList.remove('rotate-0');
                        chevron.classList.add('-rotate-90');
                    }
                });
            })();

            function toggleSidebarMenu(menuKey) {
                const parent = document.querySelector('.sidebar-parent[data-menu="' + menuKey + '"]');
                if (!parent) return;

                const children = parent.querySelector('.sidebar-children');
                const chevron = parent.querySelector('.sidebar-parent-btn svg:last-child');
                const isOpen = children.classList.contains('sidebar-children--open');

                children.classList.toggle('sidebar-children--open');
                chevron.classList.toggle('-rotate-90');
                chevron.classList.toggle('rotate-0');

                const menuStates = JSON.parse(localStorage.getItem('sidebar-menus') || '{}');
                menuStates[menuKey] = !isOpen;
                localStorage.setItem('sidebar-menus', JSON.stringify(menuStates));
            }
        </script>

        @stack('scripts')
    </body>
</html>
