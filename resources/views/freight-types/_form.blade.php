<div class="space-y-2">
    {!! html()->label('Name', 'name')->class($labelClasses ?? 'block text-sm font-medium text-topbar-text') !!}
    {!! html()->text('name', old('name', $freightType->name ?? null))
        ->id('name')
        ->class($inputClasses ?? 'w-full rounded-xl border-0 bg-topbar-muted/5 px-4 py-2.5 text-sm text-topbar-text ring-1 ring-inset ring-card-border transition focus:bg-white focus:ring-2 focus:ring-primary-500')
        ->placeholder('Enter freight type name') !!}
    @error('name')
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
