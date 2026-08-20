<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\ContainerActivity;
use App\Models\ContainerActivityDetail;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

class ContainerActivityStoreUpdateTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the lookup tables the controller/form need.
        // Without these, the exists/in-list validators reject every FK
        // and the FormRequest redirects back with errors.
        $this->seedLookups();

        // Disable auth + permission middleware so the request reaches the
        // controller. We then actAs a user so views that call
        // auth()->user()->... don't blow up.
        $this->withoutMiddleware([Authenticate::class, PermissionMiddleware::class]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);
    }

    protected function seedLookups(): void
    {
        \DB::table('vessel_voyages')->updateOrInsert(
            ['id' => 1],
            [
                'vessel_name' => 'Test Vessel',
                'voyage_number' => 'TEST-V001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        // Extra vessels used by the detail-grid dropdown (TS1/TS2/TS3 auto-fill tests).
        foreach ([
            ['id' => 2, 'vessel_name' => 'MV Atlantic Wind',  'voyage_number' => 'AUTO-101'],
            ['id' => 3, 'vessel_name' => 'MV Eastern Pearl',  'voyage_number' => 'AUTO-202'],
            ['id' => 4, 'vessel_name' => 'MV Gulf Stream',    'voyage_number' => 'AUTO-303'],
        ] as $row) {
            \DB::table('vessel_voyages')->updateOrInsert(
                ['id' => $row['id']],
                [
                    'vessel_name' => $row['vessel_name'],
                    'voyage_number' => $row['voyage_number'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        \DB::table('pols')->updateOrInsert(
            ['id' => 1],
            [
                'city' => 'Testport',
                'country' => 'Testland',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        // A second pol for the TS rows so the description differs from the main POD/destination.
        \DB::table('pols')->updateOrInsert(
            ['id' => 2],
            [
                'city' => 'TSSingapore',
                'country' => 'Testland',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        \DB::table('agents')->updateOrInsert(
            ['code' => 'AG-TEST'],
            ['name' => 'Test Agent', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('agents')->updateOrInsert(
            ['id' => 2],
            ['code' => 'AG-TS', 'name' => 'Transshipment Agent', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('carriers')->updateOrInsert(
            ['code' => 'CAR-TEST'],
            ['name' => 'Test Carrier', 'created_at' => now(), 'updated_at' => now()],
        );
    }

    public function test_store_then_update_round_trip_persists_every_field(): void
    {
        // Lookups are seeded in setUp() with id=1 / code 'AG-TEST' / 'CAR-TEST'.
        $vesselId = 1;
        $polId = 1;
        $agentCode = 'AG-TEST';
        $carrierCode = 'CAR-TEST';
        $activityType = config('dropdowns.container_activities_activity.0', 'Arrived at up country');

        $payload = [
            'doc_no' => 'CA-TEST-001',
            'activity_date' => '2026-08-20',
            'agent' => 'AGENT-XYZ',
            'activity' => $activityType,
            'free_days' => 7,
            'bl_number' => 'BL-001',
            'booking_number' => 'BK-001',
            'final_destination_code' => (string) $polId,
            'pod_code' => (string) $polId,
            'destination_agent' => $agentCode,
            'thru_bl' => 1,
            'sailing_date' => '2026-08-25',
            'vessel_voyage_id' => $vesselId,
            'voyage_number' => 'TEST-V001',
            'location' => 'Port A',
            'carrier' => $carrierCode,
            'ts_1_port' => 'TSSingapore',
            'ts_1_port_id' => 2,
            'ts_1_agent' => 'Transshipment Agent',
            'ts_1_agent_id' => 2,
            'ts_2_port' => 'TSSingapore',
            'ts_2_port_id' => 2,
            'ts_2_agent' => 'Transshipment Agent',
            'ts_2_agent_id' => 2,
            'ts_3_port' => 'TSSingapore',
            'ts_3_port_id' => 2,
            'ts_3_agent' => 'Transshipment Agent',
            'ts_3_agent_id' => 2,
            'remarks' => 'Initial create via test.',
        ];

        $createResponse = $this->post(route('container-activities.store'), $payload);

        $createResponse->assertRedirect();
        $this->assertSame(1, ContainerActivity::count(), 'record should be created');

        $record = ContainerActivity::first();
        $this->assertNotNull($record, 'record fetched from DB');

        foreach ($payload as $field => $expected) {
            $actual = $record->{$field};
            if (in_array($field, ['activity_date', 'sailing_date'], true)) {
                $this->assertSame(
                    $expected,
                    $actual?->format('Y-m-d'),
                    "create: {$field}"
                );
            } elseif ($field === 'thru_bl') {
                $this->assertSame(true, (bool) $actual, 'create: thru_bl');
            } elseif (str_ends_with($field, '_id')) {
                $this->assertSame($expected, (int) $actual, "create: {$field}");
            } else {
                $this->assertSame((string) $expected, (string) $actual, "create: {$field}");
            }
        }

        $createResponse->assertSessionHas('status');

        $updatePayload = $payload;
        $updatePayload['doc_no'] = 'CA-TEST-001-EDITED';
        $updatePayload['activity_date'] = '2026-09-01';
        $updatePayload['agent'] = 'AGENT-CHANGED';
        $updatePayload['activity'] = config('dropdowns.container_activities_activity.1', 'Container exchange in');
        $updatePayload['free_days'] = 14;
        $updatePayload['bl_number'] = 'BL-002';
        $updatePayload['booking_number'] = 'BK-002';
        $updatePayload['sailing_date'] = '2026-09-15';
        $updatePayload['voyage_number'] = 'TEST-V002';
        $updatePayload['location'] = 'Port B';
        $updatePayload['ts_1_port'] = 'TSSingapore';
        $updatePayload['ts_1_port_id'] = 2;
        $updatePayload['ts_1_agent'] = 'Transshipment Agent';
        $updatePayload['ts_1_agent_id'] = 2;
        $updatePayload['ts_2_port'] = 'TSSingapore';
        $updatePayload['ts_2_port_id'] = 2;
        $updatePayload['ts_2_agent'] = 'Transshipment Agent';
        $updatePayload['ts_2_agent_id'] = 2;
        $updatePayload['ts_3_port'] = 'TSSingapore';
        $updatePayload['ts_3_port_id'] = 2;
        $updatePayload['ts_3_agent'] = 'Transshipment Agent';
        $updatePayload['ts_3_agent_id'] = 2;
        $updatePayload['remarks'] = 'Edited via test.';
        $updatePayload['thru_bl'] = 0;

        $updateResponse = $this->patch(
            route('container-activities.update', $record),
            $updatePayload
        );

        $updateResponse->assertRedirect();
        $this->assertSame(1, ContainerActivity::count(), 'update must not create a new row');

        $record->refresh();

        foreach ($updatePayload as $field => $expected) {
            $actual = $record->{$field};
            if (in_array($field, ['activity_date', 'sailing_date'], true)) {
                $this->assertSame(
                    $expected,
                    $actual?->format('Y-m-d'),
                    "update: {$field}"
                );
            } elseif ($field === 'thru_bl') {
                $this->assertSame(false, (bool) $actual, 'update: thru_bl');
            } elseif (str_ends_with($field, '_id')) {
                $this->assertSame($expected, (int) $actual, "update: {$field}");
            } else {
                $this->assertSame((string) $expected, (string) $actual, "update: {$field}");
            }
        }

        $updateResponse->assertSessionHas('status');

        $editResponse = $this->get(route('container-activities.edit', $record));
        $editResponse->assertOk();
        $editResponse->assertSee('CA-TEST-001-EDITED');
        $editResponse->assertSee('AGENT-CHANGED');
    }

    public function test_navigate_endpoint_returns_record_payload(): void
    {
        $payload = [
            'doc_no' => 'CA-NAV-1',
            'activity' => 'LOAD',
            'vessel_voyage_id' => 1, // seeded in setUp
            'voyage_number' => 'NAV-001',
        ];

        $this->post(route('container-activities.store'), $payload)->assertRedirect();
        $record = ContainerActivity::first();

        $nav = $this->getJson(
            route('container-activities.navigate', $record),
            ['Accept' => 'application/json']
        );

        $nav->assertOk();
        $nav->assertJsonStructure([
            'activity' => ['id', 'doc_no', 'activity', 'voyage_number'],
            'total', 'current', 'firstId', 'lastId', 'prevId', 'nextId',
        ]);
        $nav->assertJsonPath('activity.doc_no', 'CA-NAV-1');
    }

    public function test_validation_rejects_bad_data(): void
    {
        $this->post(route('container-activities.store'), [
            'doc_no' => 'X',
            'activity_date' => 'not-a-date',
            'free_days' => 'not-an-int',
            'thru_bl' => 'not-a-bool',
        ])->assertSessionHasErrors(['activity_date', 'free_days', 'thru_bl']);
    }

    public function test_detail_store_update_destroy_round_trip(): void
    {
        // Create one parent activity first.
        $act = ContainerActivity::create([
            'doc_no' => 'CA-DET',
            'activity_date' => '2026-08-20',
        ]);

        $payload = [
            'containe_no' => 'MSCU1234567',
            'size_type' => '40HC',
            'principle' => 'MAERSK',
            'bl_number' => 'BL-DET-1',
            'booking_number' => 'BK-DET-1',
            'status' => 'OK',
            'cargo_type' => 'General',
            'one_door_open' => 'No',
            'last_activity' => 'Loaded',
            'system_remarks' => 'Test detail row',
            'vessel_ts1' => 'MV Atlantic Wind',
            'voyage_ts1' => 'AUTO-101',
            'sailing_date_ts1' => '2026-08-25',
            'vessel_ts2' => 'MV Eastern Pearl',
            'voyage_ts2' => 'AUTO-202',
            'sailing_date_ts2' => '2026-09-01',
            'vessel_ts3' => 'MV Gulf Stream',
            'voyage_ts3' => 'AUTO-303',
            'sailing_date_ts3' => '2026-09-10',
        ];

        // STORE — note JSON content type (the grid uses fetch JSON).
        $store = $this->postJson(
            route('container-activities.details.store', $act),
            $payload
        );

        $store->assertOk();
        $store->assertJsonStructure(['detail' => ['id']]);

        $detail = ContainerActivityDetail::first();
        $this->assertNotNull($detail);
        $this->assertSame($act->id, $detail->container_activity_id);

        foreach ($payload as $field => $expected) {
            $this->assertSame($expected, $detail->{$field}, "store: {$field}");
        }

        // UPDATE — change every field, confirm round-trip.
        $updated = $payload;
        $updated['containe_no'] = 'MSCU9999999';
        $updated['status'] = 'DIRTY';
        $updated['cargo_type'] = 'Reefer';
        $updated['one_door_open'] = 'Yes';
        $updated['system_remarks'] = 'Edited';

        $update = $this->patchJson(
            route('container-activities.details.update', [$act, $detail]),
            $updated
        );
        $update->assertOk();

        $detail->refresh();
        foreach ($updated as $field => $expected) {
            $this->assertSame($expected, $detail->{$field}, "update: {$field}");
        }

        // DESTROY.
        $this->deleteJson(route('container-activities.details.destroy', [$act, $detail]))
            ->assertOk();

        $this->assertSame(0, ContainerActivityDetail::count(), 'detail must be deleted');
    }

    public function test_navigate_endpoint_includes_details(): void
    {
        $act = ContainerActivity::create([
            'doc_no' => 'CA-NAV-DET',
        ]);
        $act->details()->create([
            'containe_no' => 'MSCU111',
            'status'      => 'OK',
        ]);

        $nav = $this->getJson(route('container-activities.navigate', $act));
        $nav->assertOk();
        $nav->assertJsonStructure([
            'activity' => ['id', 'doc_no'],
            'details'  => [['id', 'containe_no', 'status', 'container_activity_id']],
        ]);
        $nav->assertJsonPath('details.0.containe_no', 'MSCU111');
    }

    public function test_detail_delete_with_accept_json_header_succeeds(): void
    {
        // Mirrors what the JS deleteRow() does in the browser: sends a
        // DELETE with Accept: application/json + X-CSRF-TOKEN.
        $act = ContainerActivity::create(['doc_no' => 'CA-DEL']);
        $detail = $act->details()->create(['containe_no' => 'MSCU-DEL']);

        $resp = $this->withHeaders([
            'Accept'         => 'application/json',
            'X-CSRF-TOKEN'   => 'test-token',
        ])->deleteJson(
            route('container-activities.details.destroy', [$act, $detail])
        );

        $resp->assertOk();
        $this->assertSame(0, \App\Models\ContainerActivityDetail::count(), 'detail must be deleted');
        $this->assertSame(1, ContainerActivity::count(), 'parent must remain');
    }

    public function test_parent_destroy_cascades_details(): void
    {
        $act = ContainerActivity::create(['doc_no' => 'CA-CASCADE']);
        $act->details()->create(['containe_no' => 'MSCU-C1']);
        $act->details()->create(['containe_no' => 'MSCU-C2']);

        $this->assertSame(2, \App\Models\ContainerActivityDetail::count());

        $this->deleteJson(route('container-activities.destroy', $act))->assertRedirect();

        $this->assertSame(0, \App\Models\ContainerActivityDetail::count(), 'details must cascade');
        $this->assertSame(0, ContainerActivity::count(), 'parent must be deleted');
    }
}
