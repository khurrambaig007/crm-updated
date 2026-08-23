<x-app-layout :title="'Edit Cost'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div>
            <div class="flex items-center gap-3">
                @include('components.icons.badge-dollar-sign', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Edit Cost</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Update the shipping cost record.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('PATCH', route('costs.update', $cost))->id('cost-form')->class('space-y-5')->open() !!}

                {{-- Row 1: Dropdowns --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-topbar-text">POL</span>
                            <span class="text-xs text-topbar-muted">(Port of Loading)</span>
                        </div>
                        <div class="relative">
                            <select name="pol_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select a port</option>
                                @foreach ($pols as $pol)
                                    <option value="{{ $pol->id }}" {{ old('pol_id', $cost->pol_id) == $pol->id ? 'selected' : '' }}>{{ $pol->city }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('pol_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-topbar-text">POD</span>
                            <span class="text-xs text-topbar-muted">(Port of Discharge)</span>
                        </div>
                        <div class="relative">
                            <select name="pod_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select a port</option>
                                @foreach ($pods as $pod)
                                    <option value="{{ $pod->id }}" {{ old('pod_id', $cost->pod_id) == $pod->id ? 'selected' : '' }}>{{ $pod->city }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('pod_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Container Type', 'container_type_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="container_type_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select a type</option>
                                @foreach ($containerTypes as $containerType)
                                    <option value="{{ $containerType->id }}" {{ old('container_type_id', $cost->container_type_id) == $containerType->id ? 'selected' : '' }}>{{ $containerType->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('container_type_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Feeder', 'feeder_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="feeder_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select a feeder</option>
                                @foreach ($feeders as $feeder)
                                    <option value="{{ $feeder->id }}" {{ old('feeder_id', $cost->feeder_id) == $feeder->id ? 'selected' : '' }}>{{ $feeder->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('feeder_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Row 2: Dropdowns --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Size', 'container_size_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="container_size_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select a size</option>
                                @foreach ($containerSizes as $containerSize)
                                    <option value="{{ $containerSize->id }}" {{ old('container_size_id', $cost->container_size_id) == $containerSize->id ? 'selected' : '' }}>{{ $containerSize->size }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('container_size_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Term', 'slot_term_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="slot_term_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select a term</option>
                                @foreach ($slotTerms as $slotTerm)
                                    <option value="{{ $slotTerm->id }}" {{ old('slot_term_id', $cost->slot_term_id) == $slotTerm->id ? 'selected' : '' }}>{{ $slotTerm->term }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('slot_term_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-topbar-text">Pod Agent</span>
                            <span class="text-xs text-topbar-muted">(Agent at discharge port)</span>
                        </div>
                        <div class="relative">
                            <select name="pod_agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select an agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('pod_agent_id', $cost->pod_agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('pod_agent_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-topbar-text">Pol Agent</span>
                            <span class="text-xs text-topbar-muted">(Agent at loading port)</span>
                        </div>
                        <div class="relative">
                            <select name="pol_agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select an agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('pol_agent_id', $cost->pol_agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('pol_agent_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Cost fields --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="slot" class="{{ $labelClasses }}">Slot</label>
                        {!! html()->text('slot', old('slot', $cost->slot))->class($inputClasses)->placeholder('0') !!}
                        @error('slot')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dthc" class="{{ $labelClasses }}">DTHC</label>
                        {!! html()->text('dthc', old('dthc', $cost->dthc))->class($inputClasses)->placeholder('0') !!}
                        @error('dthc')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="wrr" class="{{ $labelClasses }}">WRR</label>
                        {!! html()->text('wrr', old('wrr', $cost->wrr))->class($inputClasses)->placeholder('0') !!}
                        @error('wrr')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div></div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="ts_thc" class="{{ $labelClasses }}">T/S THC</label>
                        {!! html()->text('ts_thc', old('ts_thc', $cost->ts_thc))->class($inputClasses)->placeholder('0') !!}
                        @error('ts_thc')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ts_commission" class="{{ $labelClasses }}">T/S Commission</label>
                        {!! html()->text('ts_commission', old('ts_commission', $cost->ts_commission))->class($inputClasses)->placeholder('0') !!}
                        @error('ts_commission')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Dynamic cost fields --}}
                <div id="add-field-container" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div class="flex items-center justify-end mb-3">
                        <button type="button" id="add-label" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition-all hover:bg-emerald-500 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
                            ADD FIELD
                        </button>
                    </div>
                </div>
                <div id="labels-container" class="space-y-5">
                    @foreach ($cost->labels as $label)
                        <div class="dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <input type="text" name="labels[{{ $loop->index }}][key]" value="{{ $label->key }}" placeholder="Label" class="{{ $inputClasses }}">
                            </div>
                            <div>
                                <input type="text" name="labels[{{ $loop->index }}][value]" value="{{ $label->value }}" placeholder="Value" class="{{ $inputClasses }}">
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-row inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                </button>
                            </div>
                            <div></div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div>
                        <label for="total_cost" class="{{ $labelClasses }}">TOTAL COST</label>
                        <input type="text" id="total_cost" name="total_cost" value="{{ old('total_cost', $cost->total_cost ?? '0') }}" readonly class="{{ $inputClasses }} text-right font-semibold">
                    </div>
                </div>

                {{-- Collection fields --}}
                <div class="border-t border-card-border py-6">
                    <h3 class="text-xl font-semibold text-gray-800">Collection</h3>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="of" class="{{ $labelClasses }}">OF</label>
                        {!! html()->text('of', old('of', $cost->of))->class($inputClasses)->placeholder('0') !!}
                        @error('of')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pod_rebate" class="{{ $labelClasses }}">POD Rebate</label>
                        {!! html()->text('pod_rebate', old('pod_rebate', $cost->pod_rebate))->class($inputClasses)->placeholder('0') !!}
                        @error('pod_rebate')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="free_days" class="{{ $labelClasses }}">Free Days</label>
                        {!! html()->text('free_days', old('free_days', $cost->free_days))->class($inputClasses)->placeholder('0') !!}
                        @error('free_days')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div></div>
                </div>

                {{-- Dynamic collection fields --}}
                <div id="add-field-container-collection" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div class="flex items-center justify-end mb-3">
                        <button type="button" id="add-label-collection" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition-all hover:bg-emerald-500 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
                            ADD FIELD
                        </button>
                    </div>
                </div>
                <div id="label-collections-container" class="space-y-5">
                    @foreach ($cost->labelCollections as $labelCollection)
                        <div class="dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <input type="text" name="label_collections[{{ $loop->index }}][key]" value="{{ $labelCollection->key }}" placeholder="Label" class="{{ $inputClasses }}">
                            </div>
                            <div>
                                <input type="text" name="label_collections[{{ $loop->index }}][value]" value="{{ $labelCollection->value }}" placeholder="Value" class="{{ $inputClasses }}">
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-row inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                </button>
                            </div>
                            <div></div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div>
                        <label for="total_collection" class="{{ $labelClasses }}">TOTAL COLLECTION</label>
                        <input type="text" id="total_collection" name="total_collection" value="{{ old('total_collection', $cost->total_collection ?? '0') }}" readonly class="{{ $inputClasses }} text-right font-semibold">
                    </div>
                </div>

                <div class="border-t border-card-border py-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div>
                            <label for="net_shipping" class="{{ $labelClasses }}">NET SHIPPING</label>
                            <input type="text" id="net_shipping" name="net_shipping" value="{{ old('net_shipping', $cost->net_shipping ?? '0') }}" readonly class="{{ $inputClasses }} text-right font-semibold">
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-3 pt-2">
                    <a href="{{ route('costs.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    <div class="flex items-center gap-3">
                        <button type="button" id="calculate-btn" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 hover:shadow-md transition-all duration-200">Calculate</button>
                        {!! html()->submit('Save changes')->class($submitClasses . ' w-auto') !!}
                    </div>
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('add-label').addEventListener('click', () => addLabelRow());
            document.getElementById('add-label-collection').addEventListener('click', () => addLabelCollectionRow());
            document.getElementById('calculate-btn').addEventListener('click', calculateTotals);

            document.querySelectorAll('#cost-form input[name="slot"], #cost-form input[name="dthc"], #cost-form input[name="wrr"], #cost-form input[name="ts_thc"], #cost-form input[name="ts_commission"], #cost-form input[name="of"], #cost-form input[name="pod_rebate"], #cost-form input[name="free_days"]').forEach(input => {
                input.addEventListener('input', calculateTotals);
            });

            document.querySelectorAll('.remove-row').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.closest('.dyn-row').remove();
                    reindexRows(document.getElementById('labels-container'));
                    reindexRows(document.getElementById('label-collections-container'));
                    calculateTotals();
                });
            });
        });

        function addLabelRow() {
            const container = document.getElementById('labels-container');
            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4';
            row.innerHTML = `
                <div>
                    <input type="text" name="labels[${index}][key]" placeholder="Label" class="{{ $inputClasses }}">
                </div>
                <div>
                    <input type="text" name="labels[${index}][value]" placeholder="Value" class="{{ $inputClasses }}">
                </div>
                <div class="flex items-end">
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                    </button>
                </div>
                <div></div>
            `;
            row.querySelector('button').addEventListener('click', () => {
                row.remove();
                reindexRows(container);
                calculateTotals();
            });
            row.querySelectorAll('input').forEach(input => input.addEventListener('input', calculateTotals));
            container.appendChild(row);
        }

        function addLabelCollectionRow() {
            const container = document.getElementById('label-collections-container');
            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4';
            row.innerHTML = `
                <div>
                    <input type="text" name="label_collections[${index}][key]" placeholder="Label" class="{{ $inputClasses }}">
                </div>
                <div>
                    <input type="text" name="label_collections[${index}][value]" placeholder="Value" class="{{ $inputClasses }}">
                </div>
                <div class="flex items-end">
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                    </button>
                </div>
                <div></div>
            `;
            row.querySelector('button').addEventListener('click', () => {
                row.remove();
                reindexRows(container);
                calculateTotals();
            });
            row.querySelectorAll('input').forEach(input => input.addEventListener('input', calculateTotals));
            container.appendChild(row);
        }

        function reindexRows(container) {
            container.querySelectorAll('.dyn-row').forEach((row, index) => {
                const keyInput = row.querySelector('input[name$="[key]"]');
                const valueInput = row.querySelector('input[name$="[value]"]');
                if (keyInput) keyInput.name = keyInput.name.replace(/\[\d+\]/, `[${index}]`);
                if (valueInput) valueInput.name = valueInput.name.replace(/\[\d+\]/, `[${index}]`);
            });
        }

        function calculateTotals() {
            const costFields = ['slot', 'dthc', 'wrr', 'ts_thc', 'ts_commission'];
            const collectionFields = ['of', 'pod_rebate', 'free_days'];

            let totalCost = 0;
            costFields.forEach(name => {
                const input = document.querySelector(`input[name="${name}"]`);
                if (input) totalCost += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('#labels-container input[name$="[value]"]').forEach(input => {
                totalCost += parseFloat(input.value) || 0;
            });

            let totalCollection = 0;
            collectionFields.forEach(name => {
                const input = document.querySelector(`input[name="${name}"]`);
                if (input) totalCollection += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('#label-collections-container input[name$="[value]"]').forEach(input => {
                totalCollection += parseFloat(input.value) || 0;
            });

            document.getElementById('total_cost').value = totalCost.toFixed(2);
            document.getElementById('total_collection').value = totalCollection.toFixed(2);
            document.getElementById('net_shipping').value = (totalCollection - totalCost).toFixed(2);
        }
    </script>
</x-app-layout>
