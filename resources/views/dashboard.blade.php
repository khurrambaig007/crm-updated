<x-app-layout :title="'Dashboard'">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Dashboard</h1>
                <p class="mt-1 text-sm text-topbar-muted">Here's what's happening in your CRM today.</p>
            </div>
            <a href="#" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
                New contact
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-topbar-muted">Total contacts</div>
                    <div class="rounded-full bg-primary-50 p-2 text-primary-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <div class="text-3xl font-bold tracking-tight text-topbar-text">1,248</div>
                    <div class="text-xs font-medium text-emerald-600">+12% this month</div>
                </div>
            </div>

            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-topbar-muted">Active deals</div>
                    <div class="rounded-full bg-blue-50 p-2 text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M6 22V8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v14" />
                            <path d="M6 12h12" />
                            <path d="M6 17h12" />
                            <path d="M6 7h12" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <div class="text-3xl font-bold tracking-tight text-topbar-text">86</div>
                    <div class="text-xs font-medium text-emerald-600">+5 this week</div>
                </div>
            </div>

            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-topbar-muted">Pipeline value</div>
                    <div class="rounded-full bg-emerald-50 p-2 text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M12 2v20" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <div class="text-3xl font-bold tracking-tight text-topbar-text">$428.5k</div>
                    <div class="text-xs font-medium text-emerald-600">+8.2%</div>
                </div>
            </div>

            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-topbar-muted">Win rate</div>
                    <div class="rounded-full bg-violet-50 p-2 text-violet-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                            <path d="M22 12a10 10 0 0 0-10-10" />
                            <path d="m8 2.83 2.83 2.83" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <div class="text-3xl font-bold tracking-tight text-topbar-text">34%</div>
                    <div class="text-xs font-medium text-topbar-muted">+1.2% this quarter</div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border lg:col-span-2">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-topbar-text">Revenue trend</h2>
                        <p class="text-sm text-topbar-muted">Monthly recurring revenue over the last 6 months.</p>
                    </div>
                    <select class="rounded-lg border-0 bg-page-bg py-1.5 pl-3 pr-8 text-sm font-medium text-topbar-text ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500">
                        <option>Last 6 months</option>
                        <option>This year</option>
                        <option>All time</option>
                    </select>
                </div>

                <div class="relative h-64 w-full rounded-xl bg-page-bg ring-1 ring-inset ring-card-border overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center text-sm text-topbar-muted">Chart placeholder — wire up a chart library here.</div>
                    <svg class="absolute inset-0 h-full w-full" preserveAspectRatio="none">
                        <path d="M0,200 L100,160 L200,180 L300,120 L400,140 L500,80 L600,100 L700,40 L800,60" fill="none" stroke="currentColor" stroke-width="2" class="text-primary-500" />
                        <path d="M0,200 L100,160 L200,180 L300,120 L400,140 L500,80 L600,100 L700,40 L800,60 V240 H0 Z" fill="url(#gradient)" opacity="0.15" />
                        <defs>
                            <linearGradient id="gradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="currentColor" class="text-primary-500" />
                                <stop offset="100%" stop-color="currentColor" stop-opacity="0" class="text-primary-500" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>

            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-topbar-text">Recent activity</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75 motion-safe:animate-ping"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </span>
                        Live
                    </span>
                </div>

                <div class="space-y-5">
                    <div class="flex gap-3">
                        <div class="mt-0.5 h-8 w-8 rounded-full bg-blue-100">
                            <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-blue-600">JD</div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-topbar-text"><span class="font-medium">Jane Doe</span> added a new contact.</p>
                            <p class="mt-0.5 text-xs text-topbar-muted">2 minutes ago</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-0.5 h-8 w-8 rounded-full bg-primary-100">
                            <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-primary-600">AS</div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-topbar-text"><span class="font-medium">Acme Solutions</span> moved to Closed Won.</p>
                            <p class="mt-0.5 text-xs text-topbar-muted">15 minutes ago</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-0.5 h-8 w-8 rounded-full bg-violet-100">
                            <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-violet-600">MK</div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-topbar-text"><span class="font-medium">Mike King</span> scheduled a follow-up.</p>
                            <p class="mt-0.5 text-xs text-topbar-muted">1 hour ago</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-0.5 h-8 w-8 rounded-full bg-emerald-100">
                            <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-emerald-600">SP</div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-topbar-text"><span class="font-medium">Sarah Park</span> created a new task.</p>
                            <p class="mt-0.5 text-xs text-topbar-muted">3 hours ago</p>
                        </div>
                    </div>
                </div>

                <a href="#" class="mt-6 block text-center text-sm font-medium text-primary-600 hover:text-primary-700">View all activity</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="#" class="group rounded-2xl bg-card-bg p-5 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-primary-50 p-2 text-primary-500 transition-colors group-hover:bg-primary-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-topbar-text">Add contact</div>
                        <div class="text-xs text-topbar-muted">Create a new lead or customer.</div>
                    </div>
                </div>
            </a>

            <a href="#" class="group rounded-2xl bg-card-bg p-5 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-blue-50 p-2 text-blue-500 transition-colors group-hover:bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M6 22V8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v14" />
                            <path d="M6 12h12" />
                            <path d="M6 17h12" />
                            <path d="M6 7h12" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-topbar-text">New deal</div>
                        <div class="text-xs text-topbar-muted">Log an opportunity.</div>
                    </div>
                </div>
            </a>

            <a href="#" class="group rounded-2xl bg-card-bg p-5 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-violet-50 p-2 text-violet-500 transition-colors group-hover:bg-violet-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M3 3v18h18" />
                            <path d="m19 9-5 5-4-4-3 3" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-topbar-text">View reports</div>
                        <div class="text-xs text-topbar-muted">Check performance.</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
