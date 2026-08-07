<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="h-full bg-page-bg font-sans antialiased" data-theme="{{ auth()->user()->theme ?? 'slate-orange' }}">
        <div class="min-h-screen">
            <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-sidebar-bg/50 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden" aria-hidden="true"></div>

            <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 transform-gpu bg-sidebar-bg transition-transform duration-200 ease-out -translate-x-full lg:translate-x-0">
                <div class="flex h-full flex-col">
                    <div class="flex h-16 items-center gap-3 border-b border-sidebar-border px-6">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-500 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="8.5" cy="7" r="4" />
                                <path d="M20 8v6m3-3h-6" />
                            </svg>
                        </div>
                        <span class="text-lg font-semibold tracking-tight text-white">{{ config('app.name', 'Laravel') }}</span>
                    </div>

                    <nav class="flex-1 space-y-1 px-4 py-6">
                        <div class="text-xs font-semibold uppercase tracking-wider text-sidebar-muted px-2 mb-2">Workspace</div>

                        <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white bg-sidebar-active">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-primary-500">
                                <rect width="18" height="18" x="3" y="3" rx="2" />
                                <path d="M9 3v18" />
                                <path d="M15 3v18" />
                                <path d="M3 9h18" />
                                <path d="M3 15h18" />
                            </svg>
                            Dashboard
                        </a>

                        <a href="#" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-sidebar-muted group-hover:text-sidebar-icon-hover">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            Contacts
                        </a>

                        <a href="#" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-sidebar-muted group-hover:text-sidebar-icon-hover">
                                <path d="M6 22V8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v14" />
                                <path d="M6 12h12" />
                                <path d="M6 17h12" />
                                <path d="M6 7h12" />
                            </svg>
                            Deals
                        </a>

                        <div class="text-xs font-semibold uppercase tracking-wider text-sidebar-muted px-2 mt-8 mb-2">Account</div>

                        <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-sidebar-muted group-hover:text-sidebar-icon-hover">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Profile
                        </a>

                        <div class="text-xs font-semibold uppercase tracking-wider text-sidebar-muted px-2 mt-8 mb-2">Insights</div>

                        <a href="#" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-sidebar-muted group-hover:text-sidebar-icon-hover">
                                <path d="M3 3v18h18" />
                                <path d="m19 9-5 5-4-4-3 3" />
                            </svg>
                            Reports
                        </a>

                        <a href="#" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-hover-text transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-sidebar-muted group-hover:text-sidebar-icon-hover">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.74l-.7.4a2 2 0 0 1-2 0l-.49-.28a2 2 0 0 0-2.74.73l-.23.38a2 2 0 0 0 .73 2.73l.49.28a2 2 0 0 1 1 1.74V17a2 2 0 0 1-1 1.74l-.49.28a2 2 0 0 0-.73 2.73l.23.38a2 2 0 0 0 2.74.73l.49-.28a2 2 0 0 1 2 0l.7.4a2 2 0 0 1 1 1.74V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.74l.7-.4a2 2 0 0 1 2 0l.49.28a2 2 0 0 0 2.74-.73l.23-.38a2 2 0 0 0-.73-2.73l-.49-.28a2 2 0 0 1-1-1.74V7a2 2 0 0 1 1-1.74l.49-.28a2 2 0 0 0 .73-2.73l-.23-.38a2 2 0 0 0-2.74-.73l-.49.28a2 2 0 0 1-2 0l-.7-.4a2 2 0 0 1-1-1.74V4a2 2 0 0 0-2-2Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Settings
                        </a>
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
                    {{ $slot }}
                </main>
            </div>
        </div>

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
            })();
        </script>
    </body>
</html>
