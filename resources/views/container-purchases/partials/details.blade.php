                <div class="cp-tab-pane" id="container-purchase-details">
                    <div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
                        <h2 class="text-lg font-semibold text-topbar-text mb-6">Container Purchases Detail</h2>
                        {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('container-purchases.store') : route('container-purchases.update', $containerPurchase))->id('cp-form')->class('grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4')->open() !!}

                            <div>
                                {!! html()->label('Transaction No.', 'trans_no')->class($labelClasses) !!}
                                {!! html()->text('trans_no', $nextTransNo)->id('trans_no')->class($inputClasses)->attribute('readonly', 'readonly') !!}
                            </div>

                            <div>
                                {!! html()->label('Date', 'date')->class($labelClasses) !!}
                                {!! html()->date('date', $containerPurchase?->date?->format('Y-m-d'))->class($inputClasses) !!}
                            </div>

                            <div class="sm:col-span-2">
                                <span class="block text-xs font-medium text-topbar-text">Purchase Type</span>
                                <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2">
                                    @foreach ($purchaseTypes as $value => $label)
                                        <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-topbar-text">
                                            <input type="radio" name="normal_purchase" value="{{ $value }}" {{ old('normal_purchase', $containerPurchase?->normal_purchase) === $value ? 'checked' : '' }} class="{{ $radioClasses }}">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                {!! html()->label('Supplier', 'supplier_id')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="supplier_id" id="supplier_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                        <option value="">Select supplier</option>
                                        @foreach ($suppliers as $id => $name)
                                            <option value="{{ $id }}" {{ old('supplier_id', $containerPurchase?->supplier_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    {!! $chevron !!}
                                </div>
                            </div>

                            <div>
                                {!! html()->label('Port Name', 'port_id')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="port_id" id="port_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                        <option value="">Select port</option>
                                        @foreach ($ports as $id => $label)
                                            <option value="{{ $id }}" {{ old('port_id', $containerPurchase?->port_id) == $id ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    {!! $chevron !!}
                                </div>
                            </div>

                            <div>
                                {!! html()->label('Handling Agent', 'handling_id')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="handling_id" id="handling_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                        <option value="">Select agent</option>
                                        @foreach ($agents as $id => $label)
                                            <option value="{{ $id }}" {{ old('handling_id', $containerPurchase?->handling_id) == $id ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    {!! $chevron !!}
                                </div>
                            </div>

                            <div>
                                {!! html()->label('Expected Delivery', 'expected_delivery')->class($labelClasses) !!}
                                {!! html()->date('expected_delivery', $containerPurchase?->expected_delivery?->format('Y-m-d'))->class($inputClasses) !!}
                            </div>

                            <div>
                                {!! html()->label('Release Number', 'release_no')->class($labelClasses) !!}
                                {!! html()->text('release_no', $containerPurchase?->release_no)->class($inputClasses) !!}
                            </div>

                            <div>
                                {!! html()->label('Currency', 'currency')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="currency" id="cp-currency" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                        <option value="">Select currency</option>
                                        @foreach ($currencies as $code => $label)
                                            <option value="{{ $code }}" {{ old('currency', $containerPurchase?->currency) == $code ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    {!! $chevron !!}
                                </div>
                            </div>

                            <div>
                                {!! html()->label('Rate', 'rate')->class($labelClasses) !!}
                                {!! html()->number('rate', $containerPurchase?->rate)->id('cp-rate')->class($inputClasses)->attribute('step', 'any') !!}
                                <input type="hidden" name="currency_code" id="cp-currency-code" value="{{ old('currency_code', $containerPurchase?->currency_code) }}">
                                <p class="mt-1 text-xs text-topbar-muted">Exchange rates as of {{ $exchangeRateDate ?? 'N/A' }}</p>
                            </div>

                            <div>
                                {!! html()->label('Principal', 'principal')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="principal" id="principal" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                        <option value="">Select principal</option>
                                        @foreach ($principals as $value => $label)
                                            <option value="{{ $value }}" {{ old('principal', $containerPurchase?->principal) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    {!! $chevron !!}
                                </div>
                            </div>

                        {!! html()->form()->close() !!}

                        <div class="mt-6 flex items-center justify-end gap-3">
                            @if (! $isNew)
                                <form method="POST" action="{{ route('container-purchases.destroy', $containerPurchase) }}" class="delete-form inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-500">Delete</button>
                                </form>
                            @endif
                            <a href="{{ route('container-purchases.create') }}" class="{{ $submitClasses }}">New</a>
                            <button type="submit" form="cp-form" class="{{ $submitClasses }}">Save</button>
                        </div>

                        <div class="mt-6">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                    
                </div>
                    