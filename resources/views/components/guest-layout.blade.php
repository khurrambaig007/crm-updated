@php
    // This layout renders pre-authentication (login, register, password reset), so
    // branding is resolved from the profile directly rather than from the gate.
    $profile = \App\Models\CompanyProfile::current();
    $brandName = $profile->displayName();
    $brandSubtitle = $profile->displaySubtitle();
    $brandLogoUrl = $profile->logoUrl();
    $brandFaviconUrl = $profile->faviconUrl();
    $brandMessage = $profile->message;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $brandName }}</title>
        <link rel="icon" type="image/x-icon" href="{{ $brandFaviconUrl ?? asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ $brandLogoUrl ?? asset('favicon.ico') }}">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="h-full bg-page-bg font-sans antialiased" data-theme="{{ auth()->user()->theme ?? 'slate-orange' }}">
        <div class="relative flex min-h-screen flex-col lg:flex-row">
            <div class="relative flex min-h-[32vh] flex-col justify-between overflow-hidden bg-slate-900 p-8 lg:fixed lg:inset-y-0 lg:left-0 lg:w-1/2 lg:min-h-screen">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 24px 24px;"></div>

                <div class="absolute -left-[10%] -top-[10%] h-[140%] w-[70%] -rotate-12 bg-gradient-to-br from-primary-500 via-primary-600 to-teal-700 opacity-90"></div>

                <div class="absolute inset-x-0 bottom-0 hidden h-24 bg-gradient-to-t from-slate-900/80 to-transparent sm:block"></div>

                <!-- Container stack card -->
                <div class="absolute bottom-[18%] left-[8%] z-10 hidden w-64 rounded-2xl border border-slate-700/50 bg-slate-800/70 p-5 shadow-2xl backdrop-blur duration-500 hover:scale-105 hover:border-slate-600 sm:block motion-safe:transition-transform">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-primary-500">
                            <path d="M21 16V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8" />
                            <path d="M3 20h18" />
                            <path d="M6 12h2" />
                            <path d="M16 12h2" />
                        </svg>
                        Yard inventory
                    </div>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-10 rounded-sm bg-primary-500 shadow-sm"></div>
                            <div class="text-xs text-slate-300">MSCU-4829-7</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-10 rounded-sm bg-teal-500 shadow-sm"></div>
                            <div class="text-xs text-slate-300">TRLU-1102-4</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-10 rounded-sm bg-blue-500 shadow-sm"></div>
                            <div class="text-xs text-slate-300">HLCU-9910-1</div>
                        </div>
                    </div>
                </div>

                <!-- Cargo ship illustration card -->
                <div class="absolute right-[8%] top-[14%] z-10 hidden rounded-2xl border border-slate-700/50 bg-slate-800/70 p-5 shadow-2xl backdrop-blur duration-500 hover:scale-105 hover:border-slate-600 sm:block motion-safe:transition-transform">
                    <svg class="h-40 w-72" viewBox="0 0 288 160" fill="none">
                        <rect width="288" height="160" rx="14" fill="#0f172a" />

                        <!-- Ocean -->
                        <path d="M0 120 Q36 112 72 120 T144 120 T216 120 T288 120 V160 H0 Z" fill="#1e293b" />
                        <path d="M0 128 Q36 120 72 128 T144 128 T216 128 T288 128" stroke="#334155" stroke-width="2" fill="none" />
                        <path d="M0 140 Q36 132 72 140 T144 140 T216 140 T288 140" stroke="#334155" stroke-width="2" fill="none" />

                        <!-- Ship hull -->
                        <path d="M40 110 L248 110 L228 125 H60 Z" fill="#475569" />

                        <!-- Containers on deck -->
                        <rect x="55" y="86" width="36" height="24" rx="2" fill="currentColor" class="text-primary-500" />
                        <rect x="55" y="62" width="36" height="24" rx="2" fill="#14b8a6" />
                        <rect x="95" y="86" width="36" height="24" rx="2" fill="#3b82f6" />
                        <rect x="135" y="86" width="36" height="24" rx="2" fill="currentColor" class="text-primary-500" />
                        <rect x="175" y="86" width="36" height="24" rx="2" fill="#14b8a6" />
                        <rect x="215" y="86" width="26" height="24" rx="2" fill="#3b82f6" />

                        <!-- Bridge -->
                        <rect x="230" y="72" width="22" height="14" rx="1" fill="#94a3b8" />
                        <rect x="234" y="76" width="6" height="6" rx="1" fill="#0f172a" />

                        <!-- Crane -->
                        <line x1="260" y1="40" x2="260" y2="110" stroke="currentColor" stroke-width="3" class="text-primary-500" />
                        <line x1="260" y1="40" x2="220" y2="40" stroke="currentColor" stroke-width="3" class="text-primary-500" />
                        <line x1="250" y1="40" x2="250" y2="85" stroke="currentColor" stroke-width="2" class="text-primary-400" />
                        <rect x="242" y="80" width="16" height="12" rx="1" fill="currentColor" class="text-primary-500" />
                    </svg>
                </div>

                <!-- Route line -->
                <svg class="absolute inset-0 z-0 hidden h-full w-full text-primary-500 sm:block" preserveAspectRatio="none">
                    <line x1="20%" y1="75%" x2="55%" y2="45%" stroke="currentColor" stroke-width="1.5" stroke-dasharray="6 6" opacity="0.6" />
                    <circle cx="55%" cy="45%" r="4" fill="currentColor" />
                    <circle cx="20%" cy="75%" r="3" fill="currentColor" />
                </svg>

                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        @if ($brandLogoUrl)
                            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="h-10 w-10 shrink-0 rounded-xl bg-white object-contain p-0.5 shadow-lg">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-500 text-white shadow-lg duration-300 hover:rotate-3 hover:scale-110 motion-safe:transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                                    <path d="M21 16V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8" />
                                    <path d="M3 20h18" />
                                    <path d="M6 12h2" />
                                    <path d="M16 12h2" />
                                </svg>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <span class="block truncate text-xl font-semibold tracking-tight text-white" title="{{ $brandName }}">{{ $brandName }}</span>
                            @if ($brandSubtitle)
                                <span class="block truncate text-xs font-medium uppercase tracking-wider text-slate-300">{{ $brandSubtitle }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-auto">
                    <blockquote class="max-w-md">
                        <p class="text-2xl font-semibold leading-tight text-white sm:text-3xl">
                            {{ filled($brandMessage) ? $brandMessage : 'Move cargo with confidence.' }}
                        </p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-300 sm:text-base">
                            {{ filled($brandMessage)
                                ? $brandName.' — track every load and unload, manage container flows, and keep shippers, carriers, and port ops on the same page.'
                                : 'Track every load and unload, manage container flows, and keep shippers, carriers, and port ops on the same page.' }}
                        </p>
                    </blockquote>
                </div>
            </div>

            <div class="flex flex-1 items-center justify-center p-6 lg:ml-[50%] lg:min-h-screen lg:p-12">
                <div class="w-full max-w-sm">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <x-alerts />
    </body>
</html>
