<x-app-layout :title="'Edit Charge'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-5xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.badge-dollar-sign', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Edit Charge</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Update the details for {{ $charge->name }}.</p>
                </div>
            </div>
            <a href="{{ route('charges.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                Back to charges
            </a>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('PATCH', route('charges.update', $charge))->class('grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3')->open() !!}

                <div>
                    <label for="name" class="{{ $labelClasses }}">Name <span class="text-red-500">*</span></label>
                    {!! html()->text('name', $charge->name)->class($inputClasses)->required() !!}
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('charges.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit('Save changes')->class($submitClasses . ' w-auto px-6 py-2') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
