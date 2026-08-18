# Container Activity CRUD Screen — Implementation Plan (Revised)

> **For Hermes:** Use subagent-driven-development skill to implement this plan task-by-task.

**Goal:** Build a fully functional Container Activity CRUD screen following the Purchase Invoice record-navigation pattern (no index list, single edit view, First/Prev/New/Next/Last nav). Main form only — agGrid ContainerActivityDetail subgrid deferred.

**Architecture:** Single edit/create Blade view at `resources/views/container-activities/edit.blade.php`, backed by a `ContainerActivityController` mirroring `PurchaseInvoiceController`. Vessel uses a real FK (`vessel_voyage_id`) with voyage_number as a read-only derived display, matching the `MaintenanceRepairEntry` pattern. Free-text dropdown values (e.g. `activity`) are sourced from a new `config/dropdowns.php` file using flat `screen.field` keys.

**Tech Stack:** Laravel 13, spatie/laravel-html, Blade.

---

## Key Decisions (Revised)

| Decision | Rationale |
|---|---|
| `vessel_yoyage` column → real FK `vessel_voyage_id` | Matches `maintenance_repair_entries.vessel_voyage_id` pattern (the only real FK in the codebase); the old column is plain text and not actually a FK |
| `voyage_number` as separate text column | Spec asks for both `Vessel : select` and `Voyage : text`. Store FK for vessel, free-text for voyage |
| Voyage_number shown read-only when vessel selected | Matches MaintenanceRepairEntry UX (display next to vessel select, populated via JS) |
| `Activity` dropdown sourced from `config/dropdowns.php` | New project-wide convention for free-text dropdowns |
| `Current Agent` is plain text | Per spec |
| `Destination Agent` is a select from `Agent` model (stores `code`) | Per existing pattern |
| `Carrier` is a select from `Carrier` model (stores `code`) | Per existing pattern |
| `POD` and `Final Destination` are selects from `Pol` model (store `id`) | Both ports use the `Pol` model elsewhere |
| `Thru BL/Carrier Tshp Responsibility` is a checkbox | Boolean field |
| No agGrid in this phase | User deferred it |

---

## Database Changes

The existing `container_activities` table has all needed columns except:
- `activity_date` is `string` → must be `date`
- `sailing_date` is `string` → must be `date`
- `vessel_yoyage` is `longText` → rename to `vessel_voyage_id` and make it a real FK; also need a new `voyage_number` text column
- Missing: `voyage_number`

A single new migration handles all of this.

---

## Task Breakdown

### Task 1: Create `config/dropdowns.php`

**Objective:** Establish a project-wide flat-key config for free-text dropdown values.

**Files:**
- Create: `config/dropdowns.php`

**Step 1: Create the config file**

```php
<?php

/*
|--------------------------------------------------------------------------
| Dropdown Values
|--------------------------------------------------------------------------
|
| Free-text dropdown values used across the application that do NOT have
| a dedicated Laravel model. Convention: flat keys with format
| "{screen}.{field}" so they can be looked up via
| config('dropdowns.container_activities.activity').
|
*/

return [

    // Container Activity screen
    'container_activities.activity' => [
        'In Transit',
        'Delivered',
        'Empty Return',
        'Discharged',
        'Loaded',
        'Gate In',
        'Gate Out',
        'Customs Hold',
        'Released',
    ],

];
```

**Step 2: Verify config loads**

Run: `php artisan config:clear && php artisan tinker` then `config('dropdowns.container_activities.activity')`
Expected: array of 9 activity strings.

---

### Task 2: New migration — fix container_activities schema

**Objective:** Convert string columns to date, add FK for vessel, add voyage_number column.

**Files:**
- Create: `database/migrations/2026_08_19_000000_update_container_activities_for_form_fields.php`

**Step 1: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_activities', function (Blueprint $table) {
            // activity_date: string → date
            $table->date('activity_date_new')->nullable()->after('doc_no');
            $table->dropColumn('activity_date');
            $table->renameColumn('activity_date_new', 'activity_date');

            // sailing_date: string → date
            $table->date('sailing_date_new')->nullable()->after('thru_bl');
            $table->dropColumn('sailing_date');
            $table->renameColumn('sailing_date_new', 'sailing_date');

            // vessel_yoyage (longText) → vessel_voyage_id (FK) + voyage_number (text)
            $table->dropColumn('vessel_yoyage');
            $table->foreignId('vessel_voyage_id')->nullable()->after('sailing_date')
                ->constrained('vessel_voyages')->nullOnDelete();
            $table->string('voyage_number', 191)->nullable()->after('vessel_voyage_id');
        });
    }

    public function down(): void
    {
        Schema::table('container_activities', function (Blueprint $table) {
            $table->dropForeign(['vessel_voyage_id']);
            $table->dropColumn(['vessel_voyage_id', 'voyage_number']);
            $table->longText('vessel_yoyage')->nullable();

            $table->string('activity_date', 191)->nullable();
            $table->string('sailing_date', 191)->nullable();
        });
    }
};
```

**Step 2: Run migration**

Run: `php artisan migrate`
Expected: migration runs successfully.

**Step 3: Verify**

Run: `php artisan tinker` then:
```php
DB::getSchemaBuilder()->getColumnListing('container_activities')
// expect: ..., activity_date, ..., sailing_date, ..., vessel_voyage_id, voyage_number, ...
collect(Schema::getForeignKeys('container_activities'))->pluck('columns')
// expect: vessel_voyage_id
```

---

### Task 3: Update the ContainerActivity model

**Objective:** Replace `vessel_yoyage` with `vessel_voyage_id` + `voyage_number`, add `belongsTo` for vessel.

**Files:**
- Modify: `app/Models/ContainerActivity.php`

**Step 1: Replace model contents**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'doc_no',
    'activity_date',
    'agent',
    'activity',
    'free_days',
    'bl_number',
    'booking_number',
    'final_destination_code',
    'pod_code',
    'destination_agent',
    'thru_bl',
    'sailing_date',
    'vessel_voyage_id',
    'voyage_number',
    'location',
    'carrier',
    'ts_1_port',
    'ts_1_agent',
    'ts_2_port',
    'ts_2_agent',
    'ts_3_port',
    'ts_3_agent',
    'remarks',
])]
class ContainerActivity extends Model
{
    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'sailing_date'  => 'date',
            'free_days'     => 'integer',
            'thru_bl'       => 'boolean',
        ];
    }

    public function vesselVoyage(): BelongsTo
    {
        return $this->belongsTo(VesselVoyage::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ContainerActivityDetail::class);
    }
}
```

**Step 2: Verify**

Run: `php artisan tinker` then `new App\Models\ContainerActivity`
Expected: no errors.

---

### Task 4: Create request classes (Store + Update)

**Files:**
- Create: `app/Http/Requests/ContainerActivity/StoreContainerActivityRequest.php`
- Create: `app/Http/Requests/ContainerActivity/UpdateContainerActivityRequest.php`

**Step 1: Store request**

```php
<?php

namespace App\Http\Requests\ContainerActivity;

use Illuminate\Foundation\Http\FormRequest;

class StoreContainerActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doc_no'                 => ['nullable', 'string', 'max:191'],
            'activity_date'          => ['nullable', 'date'],
            'agent'                  => ['nullable', 'string', 'max:191'],
            'activity'               => ['nullable', 'string', 'max:191'],
            'free_days'              => ['nullable', 'integer'],
            'bl_number'              => ['nullable', 'string', 'max:191'],
            'booking_number'         => ['nullable', 'string', 'max:191'],
            'final_destination_code' => ['nullable', 'string', 'max:191'],
            'pod_code'               => ['nullable', 'string', 'max:191'],
            'destination_agent'      => ['nullable', 'string', 'max:191'],
            'thru_bl'                => ['nullable', 'boolean'],
            'sailing_date'           => ['nullable', 'date'],
            'vessel_voyage_id'       => ['nullable', 'exists:vessel_voyages,id'],
            'voyage_number'          => ['nullable', 'string', 'max:191'],
            'location'               => ['nullable', 'string', 'max:191'],
            'carrier'                => ['nullable', 'string', 'max:191'],
            'ts_1_port'              => ['nullable', 'string', 'max:191'],
            'ts_1_agent'             => ['nullable', 'string', 'max:191'],
            'ts_2_port'              => ['nullable', 'string', 'max:191'],
            'ts_2_agent'             => ['nullable', 'string', 'max:191'],
            'ts_3_port'              => ['nullable', 'string', 'max:191'],
            'ts_3_agent'             => ['nullable', 'string', 'max:191'],
            'remarks'                => ['nullable', 'string'],
        ];
    }
}
```

**Step 2: Update request** (identical rules)

```php
<?php

namespace App\Http\Requests\ContainerActivity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContainerActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doc_no'                 => ['nullable', 'string', 'max:191'],
            'activity_date'          => ['nullable', 'date'],
            'agent'                  => ['nullable', 'string', 'max:191'],
            'activity'               => ['nullable', 'string', 'max:191'],
            'free_days'              => ['nullable', 'integer'],
            'bl_number'              => ['nullable', 'string', 'max:191'],
            'booking_number'         => ['nullable', 'string', 'max:191'],
            'final_destination_code' => ['nullable', 'string', 'max:191'],
            'pod_code'               => ['nullable', 'string', 'max:191'],
            'destination_agent'      => ['nullable', 'string', 'max:191'],
            'thru_bl'                => ['nullable', 'boolean'],
            'sailing_date'           => ['nullable', 'date'],
            'vessel_voyage_id'       => ['nullable', 'exists:vessel_voyages,id'],
            'voyage_number'          => ['nullable', 'string', 'max:191'],
            'location'               => ['nullable', 'string', 'max:191'],
            'carrier'                => ['nullable', 'string', 'max:191'],
            'ts_1_port'              => ['nullable', 'string', 'max:191'],
            'ts_1_agent'             => ['nullable', 'string', 'max:191'],
            'ts_2_port'              => ['nullable', 'string', 'max:191'],
            'ts_2_agent'             => ['nullable', 'string', 'max:191'],
            'ts_3_port'              => ['nullable', 'string', 'max:191'],
            'ts_3_agent'             => ['nullable', 'string', 'max:191'],
            'remarks'                => ['nullable', 'string'],
        ];
    }
}
```

**Step 3: Verify**

Run: `php artisan view:cache` (compiles all views including any that might reference these) — expected: no errors.

---

### Task 5: Create the ContainerActivityController

**Objective:** Mirror `PurchaseInvoiceController` structure with vessel FK and dropdown-config-driven activity options.

**Files:**
- Create: `app/Http/Controllers/ContainerActivityController.php`

**Step 1: Write controller**

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContainerActivity\StoreContainerActivityRequest;
use App\Http\Requests\ContainerActivity\UpdateContainerActivityRequest;
use App\Models\Agent;
use App\Models\Carrier;
use App\Models\ContainerActivity;
use App\Models\Pol;
use App\Models\VesselVoyage;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContainerActivityController extends Controller
{
    public function index(): RedirectResponse
    {
        $last = ContainerActivity::orderBy('id', 'desc')->first();

        if ($last) {
            return redirect()->route('container-activities.edit', $last);
        }

        return redirect()->route('container-activities.create');
    }

    public function create(): View
    {
        $record = new ContainerActivity;
        $total = ContainerActivity::count();
        $current = $total + 1;
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');

        return view('container-activities.edit', array_merge($this->formData(), [
            'record'  => $record,
            'total'   => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId'  => $lastId,
            'prevId'  => null,
            'nextId'  => null,
        ]));
    }

    public function store(StoreContainerActivityRequest $request): RedirectResponse
    {
        $record = ContainerActivity::create($request->validated());

        return redirect()->route('container-activities.edit', $record)
            ->with('status', 'Container Activity created successfully.');
    }

    public function edit(ContainerActivity $containerActivity): View
    {
        $containerActivity->load('vesselVoyage');
        $total = ContainerActivity::count();
        $current = ContainerActivity::where('id', '<=', $containerActivity->id)->count();
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');
        $prevId = ContainerActivity::where('id', '<', $containerActivity->id)->orderBy('id', 'desc')->value('id');
        $nextId = ContainerActivity::where('id', '>', $containerActivity->id)->orderBy('id')->value('id');

        return view('container-activities.edit', array_merge($this->formData(), [
            'record'  => $containerActivity,
            'total'   => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId'  => $lastId,
            'prevId'  => $prevId,
            'nextId'  => $nextId,
        ]));
    }

    public function update(UpdateContainerActivityRequest $request, ContainerActivity $containerActivity): RedirectResponse
    {
        $containerActivity->update($request->validated());

        return redirect()->route('container-activities.edit', $containerActivity)
            ->with('status', 'Container Activity updated successfully.');
    }

    public function destroy(Request $request, ContainerActivity $containerActivity): RedirectResponse
    {
        try {
            $containerActivity->details()->delete();
            $containerActivity->delete();
        } catch (QueryException $e) {
            return redirect()->route('container-activities.index')
                ->with('error', 'This Container Activity is in use and cannot be deleted.');
        }

        return redirect()->route('container-activities.index')
            ->with('status', 'Container Activity deleted successfully.');
    }

    public function navigate(Request $request, ContainerActivity $containerActivity): JsonResponse
    {
        $containerActivity->load('vesselVoyage');
        $total = ContainerActivity::count();
        $current = ContainerActivity::where('id', '<=', $containerActivity->id)->count();
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');
        $prevId = ContainerActivity::where('id', '<', $containerActivity->id)->orderBy('id', 'desc')->value('id');
        $nextId = ContainerActivity::where('id', '>', $containerActivity->id)->orderBy('id')->value('id');

        return response()->json([
            'activity' => $containerActivity,
            'total'    => $total,
            'current'  => $current,
            'firstId'  => $firstId,
            'lastId'   => $lastId,
            'prevId'   => $prevId,
            'nextId'   => $nextId,
        ]);
    }

    private function formData(): array
    {
        return [
            'agents'        => Agent::orderBy('code')->get(),
            'carriers'      => Carrier::orderBy('name')->get(),
            'vessels'       => VesselVoyage::orderBy('vessel_name')->get(),
            'pols'          => Pol::orderBy('city')->get(),
            'activityTypes' => config('dropdowns.container_activities.activity', []),
        ];
    }
}
```

**Step 2: Verify controller compiles**

Run: `php artisan tinker` then `app('App\Http\Controllers\ContainerActivityController')`
Expected: no errors.

---

### Task 6: Register routes in web.php

**Files:**
- Modify: `routes/web.php`

**Step 1: Add import** (after `use App\Http\Controllers\ContainerTypeController;`):

```php
use App\Http\Controllers\ContainerActivityController;
```

**Step 2: Add routes** (after the Purchase Invoices block, before Agent Receipt Payments):

```php
Route::middleware('permission:container_activities.view')->get('container-activities', [ContainerActivityController::class, 'index'])->name('container-activities.index');
Route::middleware('permission:container_activities.add')->group(function () {
    Route::get('container-activities/create', [ContainerActivityController::class, 'create'])->name('container-activities.create');
    Route::post('container-activities', [ContainerActivityController::class, 'store'])->name('container-activities.store');
});
Route::middleware('permission:container_activities.edit')->group(function () {
    Route::get('container-activities/{containerActivity}/edit', [ContainerActivityController::class, 'edit'])->name('container-activities.edit');
    Route::patch('container-activities/{containerActivity}', [ContainerActivityController::class, 'update'])->name('container-activities.update');
    Route::get('container-activities/{containerActivity}/navigate', [ContainerActivityController::class, 'navigate'])->name('container-activities.navigate');
});
Route::middleware('permission:container_activities.delete')->delete('container-activities/{containerActivity}', [ContainerActivityController::class, 'destroy'])->name('container-activities.destroy');
```

**Step 3: Verify**

Run: `php artisan route:list --name=container-activities`
Expected: 7 routes listed.

---

### Task 7: Add screen + navigation entry in config/system.php

**Files:**
- Modify: `config/system.php`

**Step 1: Add to `screens` array** (after `'purchase_invoices'` block):

```php
'container_activities' => [
    'label' => 'Container Activity',
    'permissions' => ['view', 'add', 'edit', 'delete'],
],
```

**Step 2: Add to `navigation.workspace.items`** (after Purchase Invoices item):

```php
[
    'label' => 'Container Activity',
    'route' => 'container-activities.index',
    'icon'  => 'box',
    'permission' => 'container_activities.view',
],
```

**Step 3: Verify**

Run: `php artisan config:clear && php artisan config:cache`
Expected: no errors.

---

### Task 8: Seed the new permissions

Run: `php artisan permissions:sync`
Expected: 4 new permissions added for `container_activities`.

---

### Task 9: Build the Blade view

**Files:**
- Create: `resources/views/container-activities/edit.blade.php`

**Form layout — 4-column grid rows:**

Row 1: Doc# (text) | Activity Date (date) | Current Agent (text) | Activity (select from config)
Row 2: Free Days (number) | BL Number (text) | Booking# (text) | Final Destination (select from Pol)
Row 3: POD (select from Pol) | Destination Agent (select from Agent) | [Thru BL checkbox] | Sailing Date (date)
Row 4: Vessel (select from VesselVoyage FK) | Voyage (disabled display) | Location (text) | Carrier (select)
Row 5: TS(1) Port (text) | TS(1) Agent (text) | TS(2) Port (text) | TS(2) Agent (text)
Row 6: TS(3) Port (text) | TS(3) Agent (text) | Remarks (textarea, col-span-2)

**Step 1: Write the view**

```blade
<x-app-layout :title="'Container Activity'">
    @php
        $isNew = ! $record->exists;
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $disabledClasses = 'mt-1 block w-full rounded-md border-0 bg-gray-100 px-2.5 py-1.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $navBtnClasses = 'inline-flex items-center justify-center rounded-lg p-1.5 text-topbar-muted transition-all hover:bg-primary-50 hover:text-primary-600 disabled:opacity-40 disabled:cursor-not-allowed';
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.box', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Container Activity</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Create and manage container activities.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            <div class="mb-6 flex items-center justify-center gap-1">
                <button type="button" id="nav-first" class="nav-btn {{ $navBtnClasses }}" title="First" data-direction="first" {{ $firstId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m11 17-5-5 5-5"/><path d="m18 17-5-5 5-5"/></svg>
                </button>
                <button type="button" id="nav-prev" class="nav-btn {{ $navBtnClasses }}" title="Previous" data-direction="prev" {{ $prevId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" id="nav-new" class="{{ $navBtnClasses }} ml-2" title="New" onclick="newRecord()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </button>
                <button type="button" id="nav-next" class="nav-btn {{ $navBtnClasses }}" title="Next" data-direction="next" {{ $nextId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <button type="button" id="nav-last" class="nav-btn {{ $navBtnClasses }}" title="Last" data-direction="last" {{ $lastId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m13 17 5-5-5-5"/><path d="m6 17 5-5-5-5"/></svg>
                </button>
                <span class="ml-3 text-sm font-medium text-topbar-muted">
                    <span id="nav-current">{{ $current }}</span> of <span id="nav-total">{{ $total }}</span>
                </span>
            </div>

            {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('container-activities.store') : route('container-activities.update', $record))->id('header-form')->class('grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4')->open() !!}

                {{-- Row 1: Doc#, Activity Date, Current Agent, Activity --}}
                <div>
                    <label for="doc_no" class="{{ $labelClasses }}">Doc #</label>
                    {!! html()->text('doc_no', $record->doc_no)->class($inputClasses) !!}
                    @error('doc_no') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="activity_date" class="{{ $labelClasses }}">Activity Date</label>
                    {!! html()->date('activity_date', $record->activity_date?->format('Y-m-d'))->class($inputClasses) !!}
                    @error('activity_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="agent" class="{{ $labelClasses }}">Current Agent</label>
                    {!! html()->text('agent', $record->agent)->class($inputClasses) !!}
                    @error('agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    {!! html()->label('Activity', 'activity')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="activity" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select activity</option>
                            @foreach ($activityTypes as $type)
                                <option value="{{ $type }}" {{ old('activity', $record->activity) == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                        </div>
                    </div>
                    @error('activity') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Row 2: Free Days, BL Number, Booking#, Final Destination --}}
                <div>
                    <label for="free_days" class="{{ $labelClasses }}">Free Days</label>
                    {!! html()->number('free_days', $record->free_days)->class($inputClasses)->attribute('min', 0) !!}
                    @error('free_days') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="bl_number" class="{{ $labelClasses }}">BL Number</label>
                    {!! html()->text('bl_number', $record->bl_number)->class($inputClasses) !!}
                    @error('bl_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="booking_number" class="{{ $labelClasses }}">Booking #</label>
                    {!! html()->text('booking_number', $record->booking_number)->class($inputClasses) !!}
                    @error('booking_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    {!! html()->label('Final Destination', 'final_destination_code')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="final_destination_code" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select port</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" {{ old('final_destination_code', $record->final_destination_code) == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->country }})</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                        </div>
                    </div>
                    @error('final_destination_code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Row 3: POD, Destination Agent, Thru BL checkbox, Sailing Date --}}
                <div>
                    {!! html()->label('POD', 'pod_code')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="pod_code" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select port</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" {{ old('pod_code', $record->pod_code) == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->country }})</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                        </div>
                    </div>
                    @error('pod_code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    {!! html()->label('Destination Agent', 'destination_agent')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="destination_agent" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->code }}" {{ old('destination_agent', $record->destination_agent) == $agent->code ? 'selected' : '' }}>{{ $agent->code }} - {{ $agent->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                        </div>
                    </div>
                    @error('destination_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="thru_bl" value="0">
                        <input type="checkbox" name="thru_bl" value="1" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('thru_bl', $record->thru_bl) ? 'checked' : '' }}>
                        <span class="text-sm text-topbar-text">Thru BL / Carrier Tshp Responsibility</span>
                    </label>
                </div>

                <div>
                    <label for="sailing_date" class="{{ $labelClasses }}">Sailing Date</label>
                    {!! html()->date('sailing_date', $record->sailing_date?->format('Y-m-d'))->class($inputClasses) !!}
                    @error('sailing_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Row 4: Vessel (FK), Voyage (display), Location, Carrier --}}
                <div>
                    {!! html()->label('Vessel', 'vessel_voyage_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="vessel_voyage_id" id="vessel_voyage_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateVesselVoyage(this, 'voyage_number_display')">
                            <option value="">Select a vessel</option>
                            @foreach ($vessels as $vessel)
                                <option value="{{ $vessel->id }}" data-voyage="{{ $vessel->voyage_number }}" {{ old('vessel_voyage_id', $record->vessel_voyage_id) == $vessel->id ? 'selected' : '' }}>{{ $vessel->vessel_name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                        </div>
                    </div>
                    @error('vessel_voyage_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Voyage</label>
                    <input type="text" id="voyage_number_display" class="{{ $disabledClasses }}" disabled value="{{ old('vessel_voyage_id') ? $vessels->find(old('vessel_voyage_id'))?->voyage_number : $record->vesselVoyage?->voyage_number }}">
                    <input type="hidden" name="voyage_number" id="voyage_number_hidden" value="{{ $record->voyage_number }}">
                </div>

                <div>
                    <label for="location" class="{{ $labelClasses }}">Location</label>
                    {!! html()->text('location', $record->location)->class($inputClasses) !!}
                    @error('location') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    {!! html()->label('Carrier', 'carrier')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="carrier" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select carrier</option>
                            @foreach ($carriers as $carrier)
                                <option value="{{ $carrier->code }}" {{ old('carrier', $record->carrier) == $carrier->code ? 'selected' : '' }}>{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                        </div>
                    </div>
                    @error('carrier') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Row 5: TS(1) Port, TS(1) Agent, TS(2) Port, TS(2) Agent --}}
                <div>
                    <label for="ts_1_port" class="{{ $labelClasses }}">TS (1) Port</label>
                    {!! html()->text('ts_1_port', $record->ts_1_port)->class($inputClasses) !!}
                    @error('ts_1_port') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ts_1_agent" class="{{ $labelClasses }}">TS (1) Agent</label>
                    {!! html()->text('ts_1_agent', $record->ts_1_agent)->class($inputClasses) !!}
                    @error('ts_1_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ts_2_port" class="{{ $labelClasses }}">TS (2) Port</label>
                    {!! html()->text('ts_2_port', $record->ts_2_port)->class($inputClasses) !!}
                    @error('ts_2_port') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ts_2_agent" class="{{ $labelClasses }}">TS (2) Agent</label>
                    {!! html()->text('ts_2_agent', $record->ts_2_agent)->class($inputClasses) !!}
                    @error('ts_2_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Row 6: TS(3) Port, TS(3) Agent, Remarks (col-span-2) --}}
                <div>
                    <label for="ts_3_port" class="{{ $labelClasses }}">TS (3) Port</label>
                    {!! html()->text('ts_3_port', $record->ts_3_port)->class($inputClasses) !!}
                    @error('ts_3_port') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ts_3_agent" class="{{ $labelClasses }}">TS (3) Agent</label>
                    {!! html()->text('ts_3_agent', $record->ts_3_agent)->class($inputClasses) !!}
                    @error('ts_3_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="remarks" class="{{ $labelClasses }}">Remarks</label>
                    {!! html()->textarea('remarks', $record->remarks)->class($inputClasses . ' h-24 resize-y') !!}
                    @error('remarks') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('container-activities.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m12 19-7-7 7-7" /><path d="M19 12H5" /></svg>
                        Cancel
                    </a>
                    {!! html()->submit($isNew ? 'Create Activity' : 'Save changes')->class($submitClasses . ' w-auto px-6 py-2') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>

    <script type="text/javascript">
        const vesselsData = @json($vessels->pluck('voyage_number', 'id'));

        const activityId = {{ $record->id ?? 'null' }};
        const isNew = {{ $isNew ? 'true' : 'false' }};
        const navData = {
            total:   {{ $total }},
            current: {{ $current }},
            firstId: {{ $firstId ?? 'null' }},
            lastId:  {{ $lastId ?? 'null' }},
            prevId:  {{ $prevId ?? 'null' }},
            nextId:  {{ $nextId ?? 'null' }},
        };

        document.addEventListener('DOMContentLoaded', function () {
            updateNavButtons();
            // Initialise hidden voyage_number from the loaded value
            syncHiddenVoyage();
        });

        function updateVesselVoyage(select, targetId) {
            const selected = select.options[select.selectedIndex];
            const voyage   = selected.getAttribute('data-voyage') || '';
            const target   = document.getElementById(targetId);
            const hidden   = document.getElementById('voyage_number_hidden');
            if (target) target.value = voyage;
            if (hidden) hidden.value = voyage;
        }

        function syncHiddenVoyage() {
            const sel = document.getElementById('vessel_voyage_id');
            const hidden = document.getElementById('voyage_number_hidden');
            if (!sel || !hidden) return;
            if (sel.value && !hidden.value) {
                hidden.value = vesselsData[sel.value] || '';
            }
        }

        function newRecord() {
            const form = document.getElementById('header-form');
            form.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]), select, textarea').forEach(el => {
                if (el.name === '_token') return;
                el.value = '';
            });
            form.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
            form.action = '{{ route('container-activities.store') }}';
            form.method = 'POST';
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

            document.getElementById('nav-current').textContent = navData.total + 1;
            document.getElementById('nav-total').textContent  = navData.total;

            window.history.replaceState({}, '', '{{ route('container-activities.create') }}');

            navData.current = navData.total + 1;
            navData.firstId = null;
            navData.lastId  = null;
            navData.prevId  = null;
            navData.nextId  = null;
            updateNavButtons();
        }

        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', async function (e) {
                e.preventDefault();
                const direction = this.dataset.direction;
                const targetId  = navData[direction + 'Id'];
                if (!targetId) return;

                try {
                    const resp = await fetch(`/container-activities/${targetId}/navigate`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await resp.json();
                    if (resp.ok) {
                        updateFormWithData(data);
                    }
                } catch (err) {
                    console.error('Navigation error:', err);
                }
            });
        });

        function updateFormWithData(data) {
            const act = data.activity;

            document.querySelector('input[name="doc_no"]').value                 = act.doc_no || '';
            document.querySelector('input[name="activity_date"]').value          = act.activity_date || '';
            document.querySelector('input[name="agent"]').value                  = act.agent || '';
            document.querySelector('select[name="activity"]').value              = act.activity || '';
            document.querySelector('input[name="free_days"]').value              = act.free_days || '';
            document.querySelector('input[name="bl_number"]').value              = act.bl_number || '';
            document.querySelector('input[name="booking_number"]').value         = act.booking_number || '';
            document.querySelector('select[name="final_destination_code"]').value = act.final_destination_code || '';
            document.querySelector('select[name="pod_code"]').value              = act.pod_code || '';
            document.querySelector('select[name="destination_agent"]').value     = act.destination_agent || '';
            document.querySelector('input[name="thru_bl"]').checked              = !!act.thru_bl;
            document.querySelector('input[name="sailing_date"]').value           = act.sailing_date || '';
            document.querySelector('select[name="vessel_voyage_id"]').value      = act.vessel_voyage_id || '';
            document.querySelector('input[name="voyage_number"]').value          = act.voyage_number || '';
            document.querySelector('input[name="location"]').value               = act.location || '';
            document.querySelector('select[name="carrier"]').value               = act.carrier || '';
            document.querySelector('input[name="ts_1_port"]').value              = act.ts_1_port || '';
            document.querySelector('input[name="ts_1_agent"]').value             = act.ts_1_agent || '';
            document.querySelector('input[name="ts_2_port"]').value              = act.ts_2_port || '';
            document.querySelector('input[name="ts_2_agent"]').value             = act.ts_2_agent || '';
            document.querySelector('input[name="ts_3_port"]').value              = act.ts_3_port || '';
            document.querySelector('input[name="ts_3_agent"]').value             = act.ts_3_agent || '';
            document.querySelector('textarea[name="remarks"]').value             = act.remarks || '';

            // Update the displayed voyage from the loaded vessel FK
            const voyageDisplay = document.getElementById('voyage_number_display');
            if (voyageDisplay) {
                voyageDisplay.value = (act.vessel_voyage && act.vessel_voyage.voyage_number) || act.voyage_number || '';
            }

            const form = document.getElementById('header-form');
            form.action = `/container-activities/${act.id}`;
            form.method = 'POST';
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type  = 'hidden';
                methodInput.name  = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);
            } else {
                methodInput.value = 'PATCH';
            }

            document.getElementById('nav-current').textContent = data.current;
            document.getElementById('nav-total').textContent   = data.total;

            navData.total   = data.total;
            navData.current = data.current;
            navData.firstId = data.firstId;
            navData.lastId  = data.lastId;
            navData.prevId  = data.prevId;
            navData.nextId  = data.nextId;
            updateNavButtons();

            window.history.replaceState({}, '', `/container-activities/${act.id}/edit`);
        }

        function updateNavButtons() {
            const updateBtn = (id, selector) => {
                const el = document.getElementById(selector);
                if (!el) return;
                if (id) {
                    el.removeAttribute('disabled');
                    el.style.pointerEvents = '';
                    el.style.opacity = '';
                } else {
                    el.setAttribute('disabled', '');
                    el.style.pointerEvents = 'none';
                    el.style.opacity = '0.4';
                }
            };
            updateBtn(navData.firstId, 'nav-first');
            updateBtn(navData.prevId,  'nav-prev');
            updateBtn(navData.nextId,  'nav-next');
            updateBtn(navData.lastId,  'nav-last');
        }
    </script>
</x-app-layout>
```

**Step 2: Compile view**

Run: `php artisan view:cache`
Expected: no Blade compilation errors.

---

### Task 10: Verify the full screen end-to-end

**Step 1: Routes**
Run: `php artisan route:list --name=container-activities`
Expected: 7 routes visible.

**Step 2: View compiles**
Run: `php artisan view:clear && php artisan view:cache`
Expected: no errors.

**Step 3: Permissions seeded**
Run: `php artisan tinker` then `App\Models\Permission::where('name', 'like', 'container_activities%')->pluck('name')->toArray()`
Expected: `['container_activities.view', 'container_activities.add', 'container_activities.edit', 'container_activities.delete']`.

**Step 4: Config dropdown loads**
Run: `php artisan tinker` then `config('dropdowns.container_activities.activity')`
Expected: array of 9 activity strings.

**Step 5: Run Pint**
Run: `vendor\bin\pint app/Http/Controllers/ContainerActivityController.php app/Http/Requests/ContainerActivity/ app/Models/ContainerActivity.php`
Expected: code styled without errors.

---

## Files Changed Summary

| File | Action |
|---|---|
| `config/dropdowns.php` | Create (new project-wide free-text dropdown convention) |
| `database/migrations/2026_08_19_000000_update_container_activities_for_form_fields.php` | Create |
| `app/Models/ContainerActivity.php` | Modify (fillable, casts, vesselVoyage belongsTo) |
| `app/Http/Requests/ContainerActivity/StoreContainerActivityRequest.php` | Create |
| `app/Http/Requests/ContainerActivity/UpdateContainerActivityRequest.php` | Create |
| `app/Http/Controllers/ContainerActivityController.php` | Create |
| `routes/web.php` | Modify (import + 7-route block) |
| `config/system.php` | Modify (screen entry + nav entry) |
| `resources/views/container-activities/edit.blade.php` | Create |

---

## Risks / Open Questions

| Item | Detail |
|---|---|
| **`voyage_number` is stored twice** | The vessel `voyage_number` is fetched from `vessel_voyages` AND copied into `voyage_number` on save (so historical records keep their voyage even if the vessel record changes). A hidden input `voyage_number_hidden` is set by JS to keep the table value in sync with the selected vessel. If a user edits `voyage_number` separately later, that's not supported through this UI — they must re-select the vessel. |
| **Existing data loss** | Any rows in `container_activities` that have a `vessel_yoyage` text value will lose it on migration (no data was found to migrate from). Acceptable since this is a fresh screen. |
| **Container Activity Details (agGrid)** | Deferred per user request. The `ContainerActivityDetail` model already exists. When ready, add `storeDetail/updateDetail/destroyDetail` controller methods + 3 routes + agGrid JS module. |
| **`Destination Agent` and `Carrier` store `code` not IDs** | Inconsistent with the new vessel FK but matches existing app convention. Leave for now; revisit if asked. |
| **Nav button SVGs** | The plan above shows the full SVGs. When implementing, double-check they exactly match the Purchase Invoice view for visual consistency. |
