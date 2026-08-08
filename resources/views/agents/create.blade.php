<x-app-layout :title="'New Agent'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-5xl space-y-8">
        <div>
            <div class="flex items-center gap-3">
                @include('components.icons.user', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">New Agent</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Create a new agent.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('POST', route('agents.store'))->class('grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3')->open() !!}

                <div>
                    <label for="name" class="{{ $labelClasses }}">Name <span class="text-red-500">*</span></label>
                    {!! html()->text('name')->class($inputClasses)->required() !!}
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="{{ $labelClasses }}">Code <span class="text-red-500">*</span></label>
                    {!! html()->text('code')->class($inputClasses)->required() !!}
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Port Location', 'pol_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="pol_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a port location</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" {{ old('pol_id') == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->country }})</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('pol_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Container Size', 'container_size_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="container_size_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a container size</option>
                            @foreach ($containerSizes as $containerSize)
                                <option value="{{ $containerSize->id }}" {{ old('container_size_id') == $containerSize->id ? 'selected' : '' }}>{{ $containerSize->size }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('container_size_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="amount" class="{{ $labelClasses }}">Amount <span class="text-red-500">*</span></label>
                    {!! html()->number('amount')->class($inputClasses)->required()->attribute('min', 0)->attribute('step', 'any') !!}
                    @error('amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('agents.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit('Create agent')->class($submitClasses . ' w-auto') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
