<x-app-layout :title="$tabTitle">
    @php
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <style>
        /*
         * Card selection is driven purely by CSS :has() on the real checkbox,
         * so no display-utility toggling and no JS is needed for the visuals;
         * the module only maintains the counter and the select-all control.
         */
        .stamp-card {
            transition: border-color 150ms ease, background-color 150ms ease, box-shadow 150ms ease;
        }

        .stamp-card:hover {
            border-color: var(--color-primary-300);
        }

        .stamp-card .stamp-check {
            opacity: 0;
            transform: scale(0.8);
            transition: opacity 150ms ease, transform 150ms ease;
        }

        .stamp-card:has([data-stamp-checkbox]:checked) {
            border-color: var(--color-primary-500);
            background-color: var(--color-primary-50);
            box-shadow: 0 0 0 1px var(--color-primary-500);
        }

        .stamp-card:has([data-stamp-checkbox]:checked) .stamp-check {
            opacity: 1;
            transform: scale(1);
        }

        .stamp-card:has([data-stamp-checkbox]:focus-visible) {
            outline: 2px solid var(--color-primary-500);
            outline-offset: 2px;
        }
    </style>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Stamps applied to this bill of lading'])

        @include('bl-info._nav')

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    @include('components.icons.landmark', ['classes' => 'h-5 w-5'])
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-topbar-text">Stamps</h2>
                    <p class="text-xs text-topbar-muted">Pick every stamp this bill of lading should carry — vessel, signature, freight or agent stamps</p>
                </div>
            </div>

            {!! html()->form('PATCH', route('bl-info.stamps.update', $booking))->id('stamps-form')->class('space-y-5')->open() !!}

            @if ($stamps->isEmpty())
                <div class="flex flex-col items-center gap-3 rounded-xl bg-page-bg px-6 py-12 text-center ring-1 ring-card-border">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                        @include('components.icons.stamp', ['classes' => 'h-6 w-6'])
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-topbar-text">No stamps in the library yet</p>
                        <p class="mt-1 text-xs text-topbar-muted">Add stamps under System Operations &rarr; Stamps Info, then come back to select them here.</p>
                    </div>
                    {{-- The library lives behind stamps.view, so only offer the
                     shortcut to someone who can actually open it. --}}
                    @if (auth()->user()->isSuperAdmin() || auth()->user()->can('stamps.view'))
                        <a href="{{ route('stamps.index') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5">
                            Open Stamps Info
                        </a>
                    @endif
                </div>
            @else
                <div data-stamp-picker>
                    {{-- Toolbar: running count + bulk controls --}}
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-topbar-muted">
                            Selected
                            <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-primary-100 px-2 py-0.5 text-xs font-bold text-primary-700" data-stamp-count>0</span>
                            of {{ $stamps->count() }}
                        </p>

                        <div class="flex items-center gap-4">
                            <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-topbar-text">
                                <input type="checkbox" id="stamp-select-all" data-stamp-select-all
                                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                Select all
                            </label>
                            <button type="button" data-stamp-clear
                                class="text-sm font-medium text-topbar-muted transition-colors hover:text-red-600">
                                Clear
                            </button>
                        </div>
                    </div>

                    {{-- Multi-select grid: one card per library stamp --}}
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5">
                        @foreach ($stamps as $stamp)
                            <label class="stamp-card group relative flex cursor-pointer flex-col items-center gap-3 rounded-xl border-2 border-card-border bg-page-bg p-4"
                                title="{{ $stamp->name }}">
                                <input type="checkbox" name="stamp_ids[]" value="{{ $stamp->id }}" data-stamp-checkbox
                                    class="sr-only"
                                    @checked(in_array($stamp->id, $selectedStampIds, true))>

                                <span class="relative flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white ring-1 ring-card-border">
                                    @if ($stamp->imageUrl() !== null && is_file($stamp->imagePath()))
                                        <img src="{{ $stamp->imageUrl() }}" alt="{{ $stamp->name }}" class="h-full w-full object-contain">
                                    @else
                                        <span class="flex h-full w-full items-center justify-center text-topbar-muted">
                                            @include('components.icons.stamp', ['classes' => 'h-9 w-9'])
                                        </span>
                                    @endif

                                    <span class="stamp-check absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 text-white shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                            <path d="M20 6 9 17l-5-5" />
                                        </svg>
                                    </span>
                                </span>

                                <span class="w-full truncate text-center text-xs font-semibold text-topbar-text">{{ $stamp->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-6">
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
                @if (! $stamps->isEmpty())
                    {!! html()->submit('Save Stamps')->class($submitClasses) !!}
                @endif
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>