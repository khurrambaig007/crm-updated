<x-app-layout :title="$tabTitle">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full appearance-none rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition cursor-pointer';
        $textareaClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $chevron = '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg></div>';
        $lockedClasses = 'mt-1 block w-full rounded-md border-0 bg-gray-100 px-2.5 py-1.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Bill of lading header for booking'])

        @include('bl-info._nav')

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    @include('components.icons.key', ['classes' => 'h-5 w-5'])
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-topbar-text">{{ $tabTitle }}</h2>
                    <p class="text-xs text-topbar-muted">{{ $tabDescription }}</p>
                </div>
            </div>

            {!! html()->form('PATCH', route('bl-info.lock-info.update', $booking))->id('lock-info-form')->class('space-y-5')->open() !!}

    {{-- Row 1: POL · POL Locked On · POT 1 · POT 1 Locked On --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            {!! html()->label('POL#', 'lock_info_pol_locked_to')->class($labelClasses) !!}
            <div class="relative">
                <select name="lock_info_pol_locked_to" id="lock_info_pol_locked_to" class="{{ $selectClasses }}">
                    <option value="">Select POL</option>
                    @foreach ($pols as $pol)
                        <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('lock_info_pol_locked_to', $blDetail->lock_info_pol_locked_to ?? $booking->pol) == $pol->id ? 'selected' : '' }}>{{ $pol->city }}@if($pol->country) - {{ $pol->country }}@endif</option>
                    @endforeach
                </select>
                {!! $chevron !!}
            </div>
            @error('lock_info_pol_locked_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            {!! html()->label('Locked on', 'lock_info_pol_locked_on')->class($labelClasses) !!}
            {!! html()->date('lock_info_pol_locked_on', old('lock_info_pol_locked_on', $blDetail->lock_info_pol_locked_on))->class($inputClasses) !!}
            @error('lock_info_pol_locked_on')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            {!! html()->label('POT 1', 'lock_info_pot_1_locked_to')->class($labelClasses) !!}
            <div class="relative">
                <select name="lock_info_pot_1_locked_to" id="lock_info_pot_1_locked_to" class="{{ $selectClasses }}">
                    <option value="">Select POT 1</option>
                    @foreach ($pols as $pol)
                        <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('lock_info_pot_1_locked_to', $blDetail->lock_info_pot_1_locked_to ?? $booking->pot_1) == $pol->id ? 'selected' : '' }}>{{ $pol->city }}@if($pol->country) - {{ $pol->country }}@endif</option>
                    @endforeach
                </select>
                {!! $chevron !!}
            </div>
            @error('lock_info_pot_1_locked_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            {!! html()->label('Locked on', 'lock_info_pot_1_locked_on')->class($labelClasses) !!}
            {!! html()->date('lock_info_pot_1_locked_on', old('lock_info_pot_1_locked_on', $blDetail->lock_info_pot_1_locked_on))->class($inputClasses) !!}
            @error('lock_info_pot_1_locked_on')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Row 2: POT 2 · POT 2 Locked On · POFD · POFD Locked On --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            {!! html()->label('POT 2', 'lock_info_pot_2_locked_to')->class($labelClasses) !!}
            <div class="relative">
                <select name="lock_info_pot_2_locked_to" id="lock_info_pot_2_locked_to" class="{{ $selectClasses }}">
                    <option value="">Select POT 2</option>
                    @foreach ($pols as $pol)
                        <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('lock_info_pot_2_locked_to', $blDetail->lock_info_pot_2_locked_to ?? $booking->pot_2) == $pol->id ? 'selected' : '' }}>{{ $pol->city }}@if($pol->country) - {{ $pol->country }}@endif</option>
                    @endforeach
                </select>
                {!! $chevron !!}
            </div>
            @error('lock_info_pot_2_locked_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            {!! html()->label('Locked on', 'lock_info_pot_2_locked_on')->class($labelClasses) !!}
            {!! html()->date('lock_info_pot_2_locked_on', old('lock_info_pot_2_locked_on', $blDetail->lock_info_pot_2_locked_on))->class($inputClasses) !!}
            @error('lock_info_pot_2_locked_on')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            {!! html()->label('POFD', 'lock_info_pofd_locked_to')->class($labelClasses) !!}
            <div class="relative">
                <select name="lock_info_pofd_locked_to" id="lock_info_pofd_locked_to" class="{{ $selectClasses }}">
                    <option value="">Select POFD</option>
                    @foreach ($pofds as $pod)
                        <option value="{{ $pod->id }}" data-detail="{{ $pod->city }}, {{ $pod->country }}" {{ old('lock_info_pofd_locked_to', $blDetail->lock_info_pofd_locked_to ?? $booking->pofd) == $pod->id ? 'selected' : '' }}>{{ $pod->city }}@if($pod->country) - {{ $pod->country }}@endif</option>
                    @endforeach
                </select>
                {!! $chevron !!}
            </div>
            @error('lock_info_pofd_locked_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            {!! html()->label('Locked on', 'lock_info_pofd_locked_on')->class($labelClasses) !!}
            {!! html()->date('lock_info_pofd_locked_on', old('lock_info_pofd_locked_on', $blDetail->lock_info_pofd_locked_on))->class($inputClasses) !!}
            @error('lock_info_pofd_locked_on')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Row 3: Locked Details (full width) --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-4">
            {!! html()->label('Locked Details', 'lock_info_locked_detail')->class($labelClasses) !!}
            {!! html()->textarea('lock_info_locked_detail', old('lock_info_locked_detail', $blDetail->lock_info_locked_detail))->class($textareaClasses)->rows(4)->placeholder('Enter lock details...') !!}
            @error('lock_info_locked_detail')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200">Save Lock Info</button>
    </div>

    {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>