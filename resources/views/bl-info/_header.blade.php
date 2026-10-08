<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
            <span class="text-2xl">@include('components.icons.package')</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">BL Info</h1>
            <p class="text-sm text-gray-500">
                {{ $headerSubtitle }}
                <span class="font-semibold text-gray-700">{{ $booking->booking_no }}</span>
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5">
                <path d="M20 6 9 17l-5-5" />
            </svg>
            Approved
        </span>
        <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <path d="m12 19-7-7 7-7" />
                <path d="M19 12H5" />
            </svg>
            Back to Booking
        </a>
    </div>
</div>
