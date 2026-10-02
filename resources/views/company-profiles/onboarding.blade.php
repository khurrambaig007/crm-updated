<div class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/45 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true" aria-labelledby="company-onboarding-title" data-company-onboarding data-start-step="@if ($errors->has('pic_name') || $errors->has('pic_email') || $errors->has('pic_number')) 2 @elseif ($errors->has('message')) 3 @else 1 @endif">
    <div class="mx-auto my-2 w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl sm:my-6">
        <div class="relative overflow-hidden bg-gradient-to-r from-primary-900 via-primary-700 to-primary-500 px-6 py-7 text-white sm:px-9 sm:py-8">
            <div class="absolute -right-8 -top-20 h-64 w-64 rounded-full border border-white/15"></div>
            <div class="absolute right-12 top-4 h-36 w-36 rounded-full border border-white/15"></div>
            <div class="relative z-10 max-w-2xl">
                <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.24em] text-primary-100">✦ &nbsp; Let’s get you started</p>
                <h1 id="company-onboarding-title" class="text-2xl font-semibold tracking-tight sm:text-3xl" data-onboarding-hero>Build your company profile.</h1>
                <p class="mt-2 text-sm text-primary-100" data-onboarding-subtitle>Share a few details so we can set up your organization.</p>
            </div>
            <div class="pointer-events-none absolute bottom-2 right-12 hidden h-16 w-28 rotate-[-8deg] rounded-2xl border border-white/30 bg-white/15 p-3 sm:block" aria-hidden="true">
                <div class="h-2 w-10 rounded-full bg-white/50"></div>
                <div class="mt-4 flex h-7 items-end gap-1.5">
                    <span class="h-3 w-2 rounded-t bg-white/50"></span><span class="h-5 w-2 rounded-t bg-white/70"></span><span class="h-7 w-2 rounded-t bg-white/90"></span><span class="h-4 w-2 rounded-t bg-white/60"></span>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-4 pt-4 sm:grid-cols-3 sm:px-7 sm:pt-6" aria-label="Company setup steps">
            <div class="onboarding-step-indicator flex items-center gap-3 rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm font-semibold text-primary-900" data-step-indicator="1"><span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-600 text-xs text-white">1</span> Company Profile</div>
            <div class="onboarding-step-indicator flex items-center gap-3 rounded-xl border border-card-border bg-white px-4 py-3 text-sm text-topbar-muted" data-step-indicator="2"><span class="flex h-7 w-7 items-center justify-center rounded-full border border-card-border text-xs">2</span> Person in Contact</div>
            <div class="onboarding-step-indicator flex items-center gap-3 rounded-xl border border-card-border bg-white px-4 py-3 text-sm text-topbar-muted" data-step-indicator="3"><span class="flex h-7 w-7 items-center justify-center rounded-full border border-card-border text-xs">3</span> Message</div>
        </div>

        <form id="company-onboarding-skip" method="POST" action="{{ route('dashboard.onboarding.skip') }}" class="hidden">@csrf</form>
        <form method="POST" action="{{ route('company-profile.onboarding') }}" enctype="multipart/form-data" novalidate data-onboarding-form class="m-4 mt-4 overflow-hidden rounded-2xl border border-card-border sm:m-7 sm:mt-5">
            @csrf
            <div class="grid min-h-[390px] md:grid-cols-[220px_minmax(0,1fr)]">
                <aside class="border-b border-card-border bg-slate-50 px-5 py-6 md:border-b-0 md:border-r md:px-6 md:py-8">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary-600" data-onboarding-kicker>01 — Company Profile</p>
                    <h2 class="mt-4 text-xl font-semibold leading-snug text-slate-900" data-onboarding-title>Tell us about your company</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500" data-onboarding-description>Add the core details people will see across your workspace.</p>
                    <p class="mt-6 text-xs text-slate-400"><span class="font-bold text-rose-500">*</span> Required</p>
                </aside>

                <div class="flex flex-col justify-between p-5 sm:p-7">
                    <section class="onboarding-step space-y-5" data-onboarding-step="1">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="onboarding-name" class="mb-2 block text-sm font-medium text-slate-700">Company name <span class="text-rose-500">*</span></label>
                                <input id="onboarding-name" name="name" value="{{ old('name') }}" required maxlength="191" autocomplete="organization" placeholder="e.g. Acme Shipping" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="onboarding-email" class="mb-2 block text-sm font-medium text-slate-700">Company email <span class="text-rose-500">*</span></label>
                                <input id="onboarding-email" type="email" name="company_email" value="{{ old('company_email') }}" required maxlength="191" autocomplete="email" placeholder="hello@company.com" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                @error('company_email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="onboarding-website" class="mb-2 block text-sm font-medium text-slate-700">Website <span class="ml-1 text-xs font-normal uppercase tracking-wide text-slate-400">Optional</span></label>
                                <input id="onboarding-website" name="website" value="{{ old('website') }}" maxlength="191" placeholder="https://company.com" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                @error('website')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="onboarding-number" class="mb-2 block text-sm font-medium text-slate-700">Company number <span class="text-rose-500">*</span></label>
                                <input id="onboarding-number" name="number" value="{{ old('number') }}" required maxlength="191" autocomplete="tel" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                @error('number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="onboarding-subtitle" class="mb-2 block text-sm font-medium text-slate-700">Subtitle <span class="ml-1 text-xs font-normal uppercase tracking-wide text-slate-400">Optional</span></label>
                                <input id="onboarding-subtitle" name="subtitle" value="{{ old('subtitle') }}" maxlength="191" placeholder="A short line describing your company" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                <p class="mt-1.5 text-xs text-slate-400">Shown under your company name in the workspace.</p>
                                @error('subtitle')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="onboarding-logo" class="mb-2 block text-sm font-medium text-slate-700">Company logo <span class="ml-1 text-xs font-normal uppercase tracking-wide text-slate-400">Optional</span></label>
                                <label for="onboarding-logo" class="flex cursor-pointer items-center gap-4 rounded-xl border border-dashed border-primary-200 bg-primary-50/50 px-4 py-4 transition hover:bg-primary-50">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600" aria-hidden="true">↑</span>
                                    <span class="min-w-0 flex-1"><span class="block text-sm font-semibold text-slate-700" data-logo-name>Upload your logo</span><span class="mt-1 block text-xs text-slate-400">JPG, PNG, or WEBP · up to 2 MB</span></span>
                                    <span class="rounded-lg border border-primary-200 bg-white px-3 py-2 text-xs font-semibold text-primary-700">Browse</span>
                                </label>
                                <input id="onboarding-logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="sr-only">
                                @error('logo')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="onboarding-step hidden space-y-5" data-onboarding-step="2">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="onboarding-pic-name" class="mb-2 block text-sm font-medium text-slate-700">Contact name <span class="text-xs font-normal uppercase tracking-wide text-slate-400">Optional</span></label>
                                <input id="onboarding-pic-name" name="pic_name" value="{{ old('pic_name') }}" maxlength="191" autocomplete="name" placeholder="e.g. Alex Morgan" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                @error('pic_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="onboarding-pic-email" class="mb-2 block text-sm font-medium text-slate-700">Contact email <span class="text-xs font-normal uppercase tracking-wide text-slate-400">Optional</span></label>
                                <input id="onboarding-pic-email" type="email" name="pic_email" value="{{ old('pic_email') }}" maxlength="191" placeholder="alex@company.com" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                @error('pic_email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="onboarding-pic-number" class="mb-2 block text-sm font-medium text-slate-700">Contact number <span class="text-xs font-normal uppercase tracking-wide text-slate-400">Optional</span></label>
                                <input id="onboarding-pic-number" name="pic_number" value="{{ old('pic_number') }}" maxlength="191" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">
                                @error('pic_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="onboarding-step hidden space-y-5" data-onboarding-step="3">
                        <div>
                            <label for="onboarding-message" class="mb-2 block text-sm font-medium text-slate-700">Welcome message <span class="text-xs font-normal uppercase tracking-wide text-slate-400">Optional</span></label>
                            <textarea id="onboarding-message" name="message" rows="7" placeholder="Write a short welcome message for your workspace..." class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-400 focus:ring-4 focus:ring-primary-100">{{ old('message') }}</textarea>
                            <p class="mt-2 text-xs text-slate-400">You can change this later from Company Profile.</p>
                            @error('message')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </section>

                    @if ($errors->any())
                        <div class="mt-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">Please review the highlighted fields and try again.</div>
                    @endif

                    <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                        <div class="flex items-center gap-3">
                            <button type="submit" form="company-onboarding-skip" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800">Skip for now <span aria-hidden="true">→</span></button>
                            <button type="button" class="hidden rounded-lg px-3 py-2 text-sm font-medium text-slate-500 hover:bg-slate-100" data-onboarding-previous>← Back</button>
                        </div>
                        <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-primary-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-200" data-onboarding-next>Continue <span aria-hidden="true">→</span></button>
                        <button type="submit" class="hidden items-center gap-2 rounded-xl bg-primary-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-200" data-onboarding-finish>Finish setup <span aria-hidden="true">✓</span></button>
                    </div>
                    <p class="mt-4 text-center text-xs text-slate-400">Your company information is securely stored in your workspace.</p>
                </div>
            </div>
        </form>
    </div>
</div>
