<x-app-layout :title="'System Operations'">
    @php $user = auth()->user(); @endphp

    <div class="mx-auto max-w-7xl space-y-8">
        <div>
            <div class="flex items-center gap-3">
                @include('components.icons.cpu', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">System Operations</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Monitor and manage system-wide settings and operations.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @if ($user->isSuperAdmin() || $user->can('container_sizes.view'))
                <a href="{{ route('container-sizes.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M21 16V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8" /><path d="M3 20h18" /><path d="M6 12h2" /><path d="M16 12h2" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Container Size</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['container_sizes'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('port_locations.view'))
                <a href="{{ route('port-locations.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 4V8L8 12V8L2 12Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Port Location</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['port_locations'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('carriers.view'))
                <a href="{{ route('carriers.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.5 0 2.5-2 5-2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1" /><path d="M19.38 20A11.6 11.6 0 0 0 21 14l-9-4-9 4c0 2.9.94 5.34 2.81 7.76" /><path d="M19 13V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v6" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Carrier</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['carriers'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('agents.view'))
                <a href="{{ route('agents.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 to-violet-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Agent</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['agents'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('container_types.view'))
                <a href="{{ route('container-types.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z" /><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65" /><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Container Type</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['container_types'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('container_kinds.view'))
                <a href="{{ route('container-kinds.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-400 to-fuchsia-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="m7.5 4.27 9 5.15" />
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                <path d="m3.3 7 8.7 5 8.7-5" />
                                <path d="M12 22V12" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Container Kind</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['container_kinds'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('commodities.view'))
                <a href="{{ route('commodities.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-lime-400 to-lime-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z" />
                                <path d="M7 7h.01" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Commodity</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['commodities'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('vessel_voyages.view'))
                <a href="{{ route('vessel-voyages.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="3" />
                                <path d="M12 2v7" /><path d="m15.5 3.8 2.6 6.5" /><path d="M8.5 3.8l-2.6 6.5" />
                                <path d="m21 12-7 0" /><path d="m3 12 7 0" />
                                <path d="m15.5 20.2 2.6-6.5" /><path d="M8.5 20.2l-2.6-6.5" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Vessel Voyage</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['vessel_voyages'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('charges.view'))
                <a href="{{ route('charges.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-red-400 to-red-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                                <path d="M12 18V6" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Charge</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['charges'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('slots.view'))
                <a href="{{ route('slots.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-cyan-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <rect width="18" height="18" x="3" y="3" rx="2" /><path d="M7 7h10" /><path d="M7 12h10" /><path d="M7 17h10" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Slot</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['slots'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('investors.view'))
                <a href="{{ route('investors.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-rose-400 to-rose-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" />
                                <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Investor</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['investors'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('parties.view'))
                <a href="{{ route('parties.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <rect width="16" height="20" x="4" y="2" rx="2" ry="2" />
                                <path d="M9 22v-4h6v4" />
                                <path d="M8 6h.01" />
                                <path d="M16 6h.01" />
                                <path d="M12 6h.01" />
                                <path d="M12 10h.01" />
                                <path d="M12 14h.01" />
                                <path d="M16 10h.01" />
                                <path d="M16 14h.01" />
                                <path d="M8 10h.01" />
                                <path d="M8 14h.01" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">PA Party</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['parties'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('suppliers.view'))
                <a href="{{ route('suppliers.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" />
                                <path d="M15 18H9" />
                                <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" />
                                <circle cx="17" cy="18" r="2" />
                                <circle cx="7" cy="18" r="2" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Suppliers</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['suppliers'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('shipper_bps.view'))
                <a href="{{ route('shipper-bps.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-slate-400 to-slate-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="m7.5 4.27 9 5.15" />
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                <path d="m3.3 7 8.7 5 8.7-5" />
                                <path d="M12 22V12" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Shipper / BP</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['shipper_bps'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('sub_companies.view'))
                <a href="{{ route('sub-companies.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <line x1="3" x2="21" y1="22" y2="22" />
                                <line x1="6" x2="6" y1="18" y2="11" />
                                <line x1="10" x2="10" y1="18" y2="11" />
                                <line x1="14" x2="14" y1="18" y2="11" />
                                <line x1="18" x2="18" y1="18" y2="11" />
                                <polygon points="12 2 20 7 4 7" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Sub Companies</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['sub_companies'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif

            @if ($user->isSuperAdmin() || $user->can('settlement_types.view'))
                <a href="{{ route('settlement-types.index') }}" class="group rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-all hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 text-white shadow-lg transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                                <path d="M12 17.5v-11" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-topbar-text">Settlement Type</div>
                            <div class="mt-1 text-xs text-topbar-muted">{{ $stats['settlement_types'] }} records</div>
                        </div>
                        <span class="text-xs font-medium text-primary-600 transition-colors group-hover:text-primary-700">View Details &rarr;</span>
                    </div>
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
