@php
    /**
     * Stamp image upload field, shared by the create and edit screens.
     *
     * $stamp is the existing record on edit, or null on create.
     */
    $stamp = $stamp ?? null;
    $existingUrl = null;
    $existingPath = null;

    if ($stamp !== null && $stamp->imagePath() !== null && is_file($stamp->imagePath())) {
        $existingUrl = $stamp->imageUrl();
        $existingPath = $stamp->imagePath();
    }

    // Plain spaces: this string is printed through {{ }}, which would escape an
    // &nbsp; entity and show it literally.
    $hint = $stamp === null
        ? 'Required. JPG, PNG or WEBP, up to 2 MB.'
        : 'Leave empty to keep the current stamp. JPG, PNG or WEBP, up to 2 MB.';
@endphp

<style>
    /*
     * State driven by data attributes rather than a hidden/flex class pair:
     * Tailwind decides display-utility winners by stylesheet order, so
     * toggling `hidden` against `flex` on one element is not reliable.
     */
    [data-stamp-upload] .upload-idle {
        display: flex;
    }

    [data-stamp-upload][data-has-file="true"] .upload-idle {
        display: none;
    }

    [data-stamp-upload] .upload-preview {
        display: none;
    }

    [data-stamp-upload][data-has-file="true"] .upload-preview {
        display: flex;
    }

    [data-stamp-upload][data-removed="true"] [data-upload-existing] {
        opacity: 0.45;
        filter: grayscale(1);
    }

    [data-stamp-upload][data-removed="true"] .upload-removed-tag {
        display: inline-flex;
    }

    [data-stamp-upload] .upload-removed-tag {
        display: none;
    }

    [data-stamp-upload][data-dragging="true"] .upload-drop {
        border-color: var(--color-primary-500);
        background-color: var(--color-primary-50);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-primary-500) 18%, transparent);
    }

    [data-stamp-upload][data-dragging="true"] .upload-drop-icon {
        transform: scale(1.08);
    }

    [data-stamp-upload] .upload-drop-icon {
        transition: transform 200ms ease;
    }

    [data-stamp-upload] .upload-drop:focus-within .upload-drop-surface {
        box-shadow: 0 0 0 2px var(--color-primary-500);
    }
</style>

<div data-stamp-upload data-has-file="false" @if ($stamp !== null) data-removed="false" @endif>
    {{-- The stamp already stored on this record --}}
    @if ($existingUrl !== null)
        <div class="upload-existing mb-4 flex items-center gap-4 rounded-xl bg-page-bg p-4 ring-1 ring-card-border" data-upload-existing>
            <img src="{{ $existingUrl }}" alt="{{ $stamp->name }}" class="h-16 w-16 shrink-0 rounded-lg object-contain ring-1 ring-card-border bg-white">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-topbar-text">Current stamp</p>
                <p class="mt-0.5 truncate text-xs text-topbar-muted" title="{{ basename($existingPath) }}">{{ basename($existingPath) }}</p>
                <label class="mt-2 flex cursor-pointer items-center gap-2 text-xs text-topbar-muted">
                    <input type="checkbox" name="remove_image" id="remove_image" value="1"
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span>Remove this stamp</span>
                    <span class="upload-removed-tag ml-1 items-center rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-700 ring-1 ring-red-200">Will be removed on save</span>
                </label>
            </div>
        </div>
    @endif

    {{-- Drop zone: the label is the hit target, the input stays real and focusable --}}
    <div class="upload-drop relative rounded-xl border-2 border-dashed border-card-border bg-page-bg transition-all duration-200 hover:border-primary-300 hover:bg-primary-50/40">
        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
            data-upload-input
            @if ($stamp === null) required @endif
            class="upload-drop-surface absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">

        <div class="upload-idle flex flex-col items-center gap-3 px-6 py-10 text-center" data-upload-idle>
            <span class="upload-drop-icon flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                @include('components.icons.stamp', ['classes' => 'h-6 w-6'])
            </span>
            <div>
                <p class="text-sm font-semibold text-topbar-text">
                    {{ $stamp === null ? 'Drop the stamp image here' : 'Drop a new stamp to replace it' }}
                </p>
                <p class="mt-1 text-xs text-topbar-muted">or click to browse your files</p>
            </div>
            <span class="rounded-full bg-card-bg px-2.5 py-1 text-[11px] font-medium text-topbar-muted ring-1 ring-card-border">JPG · PNG · WEBP</span>
        </div>

        {{-- Chosen-file preview --}}
        <div class="upload-preview items-center gap-4 px-4 py-4" data-upload-preview>
            <img data-upload-preview-image alt="" class="h-16 w-16 shrink-0 rounded-lg object-contain ring-1 ring-card-border bg-white">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-topbar-text">New stamp selected</p>
                <p data-upload-preview-name class="mt-0.5 truncate text-xs text-topbar-muted"></p>
                <p data-upload-preview-size class="mt-0.5 text-xs font-medium text-topbar-text"></p>
            </div>
            <button type="button" data-upload-clear
                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-card-bg px-3 py-2 text-xs font-semibold text-topbar-muted ring-1 ring-card-border transition-colors hover:bg-red-50 hover:text-red-600 hover:ring-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5">
                    <path d="M3 6h18" />
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                </svg>
                Clear
            </button>
        </div>
    </div>

    <p class="mt-2 text-xs text-topbar-muted" data-upload-feedback></p>
    <p class="mt-1 text-xs text-topbar-muted">{{ $hint }}</p>

    @error('image')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>