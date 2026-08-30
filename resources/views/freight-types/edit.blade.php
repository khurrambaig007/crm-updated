<x-app-layout :title="'Edit Freight Type'">
    <div class="mx-auto max-w-2xl space-y-8">
        <div class="flex items-center gap-3">
            @include('components.icons.tag', ['classes' => 'h-7 w-7 text-primary-600'])
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Edit Freight Type</h1>
                <p class="mt-1 text-sm text-topbar-muted">Update the freight type details.</p>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-lg ring-1 ring-card-border sm:p-8">
            {!! html()->form('PATCH', route('freight-types.update', $freightType))->class('space-y-6')->open() !!}
                @include('freight-types._form', ['freightType' => $freightType])

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('freight-types.index') }}"
                       class="rounded-xl px-5 py-2.5 text-sm font-semibold text-topbar-muted transition-colors hover:bg-topbar-muted/10">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-primary-700/30">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
                        Update freight type
                    </button>
                </div>
            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
