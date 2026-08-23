<div class="mt-6 space-y-5">
    <div class="mb-8 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
            <li class="mr-2">
                <a href="#equipments" class="sub-tab-link inline-block p-4 border-b-2 border-primary-600 text-primary-600 rounded-t-lg active">Equipments</a>
            </li>
            <li class="mr-2">
                <a href="#revenue" class="sub-tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">Revenue</a>
            </li>
            <li class="mr-2">
                <a href="#cost" class="sub-tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">Cost</a>
            </li>
        </ul>
    </div>

    <div id="equipments" class="sub-tab-pane space-y-5">
        @include('bookings._equipment')
    </div>

    <div id="revenue" class="sub-tab-pane hidden space-y-5">
        <p class="text-sm text-topbar-muted">No revenue entries added yet.</p>
    </div>

    <div id="cost" class="sub-tab-pane hidden space-y-5">
        <p class="text-sm text-topbar-muted">No cost entries added yet.</p>
    </div>
</div>
