<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\ContainerReleaseOrder;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

class ContainerReleaseOrderStoreUpdateTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedLookups();

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
        \DB::table('commodities')->updateOrInsert(
            ['id' => 1],
            [
                'commodity_number' => 'GEN-CARGO',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        \DB::table('pols')->updateOrInsert(
            ['id' => 1],
            [
                'city' => 'Testport',
                'country' => 'Testland',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        \DB::table('pods')->updateOrInsert(
            ['id' => 1],
            [
                'city' => 'Testport',
                'country' => 'Testland',
                'location_code' => 'DEST-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        \DB::table('bookings')->updateOrInsert(
            ['id' => 1],
            [
                'booking_no' => 'BK-0001',
                'reference_no' => 'REF-0001',
                'booking_date' => '2026-09-01',
                'sailing_date' => '2026-09-15',
                'cntr_owner' => 2,
                'commodity' => 1,
                'non_dg' => 1,
                'pol' => 1,
                'pofd' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
        \DB::table('bookings')->updateOrInsert(
            ['id' => 2],
            [
                'booking_no' => 'BK-0002',
                'reference_no' => 'REF-0002',
                'booking_date' => '2026-09-02',
                'sailing_date' => '2026-09-16',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function test_store_then_update_round_trip_persists_every_field(): void
    {
        $cntrOwner = (string) array_key_first(config('dropdowns.container_release_orders.cntr_owner'));
        $dgStatus = (string) array_key_first(config('dropdowns.container_release_orders.dg_status'));

        $payload = [
            'booking_no' => 'BK-0001',
            'booking_id' => '1',
            'reference_no' => 'CRO-TEST-001',
            'booking_date' => '2026-09-20',
            'cntr_owner' => $cntrOwner,
            'commodity_id' => '1',
            'dg_status' => $dgStatus,
            'pol_id' => '1',
            'pofd_id' => '1',
            'notes' => 'Handle with care.',
        ];

        $createResponse = $this->post(route('container-release-orders.store'), $payload);

        $createResponse->assertRedirect();
        $this->assertSame(1, ContainerReleaseOrder::count(), 'record should be created');

        $record = ContainerReleaseOrder::first();
        $this->assertNotNull($record, 'record fetched from DB');

        foreach ($payload as $field => $expected) {
            $actual = $record->{$field};
            if ($field === 'booking_date') {
                $this->assertSame($expected, $actual?->format('Y-m-d'), "create: {$field}");
            } else {
                $this->assertSame($expected, (string) $actual, "create: {$field}");
            }
        }

        $createResponse->assertSessionHas('status');

        $updatePayload = $payload;
        $updatePayload['booking_no'] = 'BK-0002';
        $updatePayload['booking_id'] = '2';
        $updatePayload['reference_no'] = 'CRO-TEST-002';
        $updatePayload['booking_date'] = '2026-10-01';
        $updatePayload['cntr_owner'] = '3';
        $updatePayload['dg_status'] = '1';
        $updatePayload['notes'] = 'Updated notes.';

        $updateResponse = $this->patch(
            route('container-release-orders.update', $record),
            $updatePayload
        );

        $updateResponse->assertRedirect();
        $this->assertSame(1, ContainerReleaseOrder::count(), 'update must not create a new row');

        $record->refresh();

        foreach ($updatePayload as $field => $expected) {
            $actual = $record->{$field};
            if ($field === 'booking_date') {
                $this->assertSame($expected, $actual?->format('Y-m-d'), "update: {$field}");
            } else {
                $this->assertSame($expected, (string) $actual, "update: {$field}");
            }
        }

        $updateResponse->assertSessionHas('status');

        $editResponse = $this->get(route('container-release-orders.edit', $record));
        $editResponse->assertOk();
        $editResponse->assertSee('CRO-TEST-002');
    }

    public function test_create_prefills_from_booking_url_param(): void
    {
        $response = $this->get(route('container-release-orders.create', ['booking' => 1]));

        $response->assertOk();

        $response->assertSee('value="BK-0001"', false);
        $response->assertSee('value="REF-0001"', false);
        $response->assertSee('value="2026-09-01"', false);
        $response->assertSee('value="1"', false);
        $response->assertSee('name="booking_id"', false);
        $response->assertSee('name="booking_no"', false);
        $response->assertSee('name="notes"', false);

        $response->getContent();
        preg_match_all('/<option value="(\\d+)" selected/', (string) $response->getContent(), $matches);
        $selectedValues = array_map('intval', $matches[1] ?? []);
        $this->assertContains(2, $selectedValues, 'cntr_owner 2 should be preselected');
        $this->assertContains(1, $selectedValues, 'commodity/pol/pofd/dg_status 1 should be preselected');
    }

    public function test_create_without_booking_param_stays_blank(): void
    {
        $response = $this->get(route('container-release-orders.create'));

        $response->assertOk();
        $response->assertSee('name="booking_no"', false);
        $response->assertDontSee('value="BK-0001"', false);
    }

    public function test_validation_rejects_bad_data(): void
    {
        $this->post(route('container-release-orders.store'), [
            'booking_no' => '',
            'reference_no' => '',
            'booking_date' => 'not-a-date',
            'cntr_owner' => '99',
            'commodity_id' => '9999',
            'dg_status' => '99',
            'pol_id' => '',
            'pofd_id' => '9999',
        ])->assertSessionHasErrors([
            'booking_no',
            'reference_no',
            'booking_date',
            'cntr_owner',
            'commodity_id',
            'dg_status',
            'pol_id',
            'pofd_id',
        ]);
    }

    public function test_pages_render(): void
    {
        $this->get(route('container-release-orders.index'))->assertOk()->assertSee('Container Release Orders');

        $this->get(route('container-release-orders.create'))->assertOk()->assertSee('Create Container Release Order');

        $cro = ContainerReleaseOrder::create([
            'booking_no' => 'BK-0001',
            'booking_id' => 1,
            'reference_no' => 'CRO-RENDER',
            'booking_date' => '2026-09-20',
            'cntr_owner' => 1,
            'commodity_id' => 1,
            'dg_status' => 0,
            'pol_id' => 1,
            'pofd_id' => 1,
        ]);

        $this->get(route('container-release-orders.edit', $cro))->assertOk()->assertSee('CRO-RENDER');
    }

    public function test_destroy_removes_record(): void
    {
        $cro = ContainerReleaseOrder::create([
            'booking_no' => 'BK-0001',
            'booking_id' => 1,
            'reference_no' => 'CRO-DEL',
            'booking_date' => '2026-09-20',
            'cntr_owner' => 1,
            'commodity_id' => 1,
            'dg_status' => 0,
            'pol_id' => 1,
            'pofd_id' => 1,
        ]);

        $this->delete(route('container-release-orders.destroy', $cro))->assertRedirect();

        $this->assertSoftDeleted('container_release_orders', ['id' => $cro->id, 'reference_no' => 'CRO-DEL']);
        $this->assertSame(0, ContainerReleaseOrder::count(), 'soft-deleted record must be excluded from default queries');
        $this->assertSame(1, ContainerReleaseOrder::onlyTrashed()->count(), 'record must remain in the trashed set');
    }

    public function test_pdf_streams_for_existing_order(): void
    {
        $cro = ContainerReleaseOrder::create([
            'booking_no' => 'BK-0001',
            'booking_id' => 1,
            'reference_no' => 'CRO-PDF',
            'booking_date' => '2026-09-20',
            'cntr_owner' => 1,
            'commodity_id' => 1,
            'dg_status' => 0,
            'pol_id' => 1,
            'pofd_id' => 1,
            'notes' => 'Handle with care.',
        ]);

        $response = $this->get(route('container-release-orders.pdf', $cro));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', (string) $response->getContent(), 'response body must be a PDF stream');
        $this->assertStringContainsString("\x00\x43\x00\x52\x00\x4f\x00\x2d\x00\x42\x00\x4b\x00\x2d\x00\x30\x00\x30\x00\x30\x00\x31", (string) $response->getContent(), 'PDF title must use the CRO-{booking_no} format');
    }
}
