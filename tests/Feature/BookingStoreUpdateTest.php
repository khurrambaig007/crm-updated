<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\Booking;
use App\Models\BookingOtherInfo;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

class BookingStoreUpdateTest extends BaseTestCase
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
        \DB::table('carriers')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Test Carrier', 'code' => 'CAR-01', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('commodities')->updateOrInsert(
            ['id' => 1],
            ['commodity_number' => 'CM-001', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('vessel_voyages')->updateOrInsert(
            ['id' => 1],
            ['vessel_name' => 'Test Vessel', 'voyage_number' => 'VV-001', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('pols')->updateOrInsert(
            ['id' => 1],
            ['city' => 'Testport', 'country' => 'Testland', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('pods')->updateOrInsert(
            ['id' => 1],
            ['city' => 'Pod City', 'country' => 'Podland', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('agents')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Test Agent', 'code' => 'AG-01', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('agents')->updateOrInsert(
            ['id' => 2],
            ['name' => 'Agent Two', 'code' => 'AG-02', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('shipper_bps')->updateOrInsert(
            ['id' => 1],
            [
                'code' => 'SB-01',
                'name' => 'Test Shipper',
                'agent_id' => 1,
                'port_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        \DB::table('p_a_s')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Test Consignee',
                'code' => 'CON-01',
                'agent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'booking_no' => 'BK-TEST-001',
            'approval_no' => 'AP-TEST-001',
            'reference_no' => 'REF-TEST-001',
            'booking_date' => '2026-08-23',
            'sailing_date' => '2026-08-30',
            'carrier' => 1,
            'cntr_owner' => 1,
            'commodity' => 1,
            'non_dg' => 0,
            'vessel_voyage' => 1,
            'pol' => 1,
            'pofd' => 1,
            'pot_1' => 1,
            'pot_2' => 1,
            'shipper_bp' => 1,
            'agent_pol' => 1,
            'agent_pofd' => 2,
            'agent_1' => 1,
            'agent_2' => 2,
            'freight_type' => 1,
            'freight_type_sub' => 1,
            'consignee' => 1,
            'thru_bl' => 1,
            'special_req' => 'Handle with care',
            'free_days_pol' => 7,
            'detention_free_pofd' => 5,
            'detention_tariff' => 1,
            'detention_currency' => 'USD',
            'message' => 'Test message',
        ], $overrides);
    }

    public function test_store_creates_booking_and_other_info(): void
    {
        $payload = $this->validPayload();

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertSame(1, Booking::count(), 'booking should be created');

        $booking = Booking::first();
        $this->assertSame('BK-TEST-001', $booking->booking_no);
        $this->assertSame('AP-TEST-001', $booking->approval_no);
        $this->assertSame('REF-TEST-001', $booking->reference_no);
        $this->assertSame('2026-08-23', $booking->booking_date->format('Y-m-d'));
        $this->assertSame('2026-08-30', $booking->sailing_date->format('Y-m-d'));
        $this->assertSame(1, $booking->carrier);
        $this->assertSame(1, $booking->cntr_owner);
        $this->assertSame(1, $booking->commodity);
        $this->assertSame(0, $booking->non_dg);
        $this->assertSame(1, $booking->vessel_voyage);
        $this->assertSame(1, $booking->pol);
        $this->assertSame(1, $booking->pofd);
        $this->assertSame(1, $booking->pot_1);
        $this->assertSame(1, $booking->pot_2);
        $this->assertSame(1, $booking->shipper_bp);
        $this->assertSame(1, $booking->agent_pol);
        $this->assertSame(2, $booking->agent_pofd);
        $this->assertSame(1, $booking->agent_1);
        $this->assertSame(2, $booking->agent_2);
        $this->assertSame(1, $booking->freight_type);
        $this->assertSame(1, $booking->freight_type_sub);
        $this->assertSame(1, $booking->consignee);
        $this->assertTrue($booking->thru_bl);

        $this->assertSame(1, BookingOtherInfo::count(), 'other info should be created');
        $otherInfo = $booking->otherInfo;
        $this->assertNotNull($otherInfo);
        $this->assertSame('Handle with care', $otherInfo->special_req);
        $this->assertSame(7, $otherInfo->free_days_pol);
        $this->assertSame(5, $otherInfo->detention_free_pofd);
        $this->assertTrue($otherInfo->detention_tariff);
        $this->assertSame('USD', $otherInfo->detention_currency);
        $this->assertSame('Test message', $otherInfo->message);
    }

    public function test_update_modifies_booking_and_upserts_other_info(): void
    {
        $booking = Booking::create([
            'booking_no' => 'BK-ORIG',
            'approval_no' => 'AP-ORIG',
            'reference_no' => 'REF-ORIG',
            'booking_date' => '2026-08-01',
            'sailing_date' => '2026-08-10',
        ]);

        $booking->otherInfo()->create([
            'special_req' => 'Original note',
            'free_days_pol' => 3,
        ]);

        $updatePayload = $this->validPayload([
            'booking_no' => 'BK-EDITED',
            'approval_no' => 'AP-EDITED',
            'reference_no' => 'REF-EDITED',
            'booking_date' => '2026-09-01',
            'sailing_date' => '2026-09-10',
            'carrier' => 1,
            'commodity' => 1,
            'vessel_voyage' => 1,
            'pol' => 1,
            'pofd' => 1,
            'pot_1' => 1,
            'pot_2' => 1,
            'shipper_bp' => 1,
            'agent_pol' => 1,
            'agent_pofd' => 2,
            'agent_1' => 1,
            'agent_2' => 2,
            'freight_type' => 2,
            'freight_type_sub' => 2,
            'consignee' => 1,
            'thru_bl' => 0,
            'special_req' => 'Updated note',
            'free_days_pol' => 10,
            'detention_free_pofd' => 8,
            'detention_tariff' => 0,
            'detention_currency' => 'EUR',
            'message' => 'Updated message',
        ]);

        $response = $this->patch(route('bookings.update', $booking), $updatePayload);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertSame(1, Booking::count(), 'update must not create a new row');

        $booking->refresh();
        $this->assertSame('BK-EDITED', $booking->booking_no);
        $this->assertSame('AP-EDITED', $booking->approval_no);
        $this->assertSame('REF-EDITED', $booking->reference_no);
        $this->assertSame('2026-09-01', $booking->booking_date->format('Y-m-d'));
        $this->assertSame('2026-09-10', $booking->sailing_date->format('Y-m-d'));
        $this->assertSame(2, $booking->freight_type);
        $this->assertSame(2, $booking->freight_type_sub);
        $this->assertFalse($booking->thru_bl);

        $this->assertSame(1, BookingOtherInfo::count(), 'must not create a second other info row');
        $otherInfo = $booking->otherInfo->fresh();
        $this->assertSame('Updated note', $otherInfo->special_req);
        $this->assertSame(10, $otherInfo->free_days_pol);
        $this->assertSame(8, $otherInfo->detention_free_pofd);
        $this->assertFalse($otherInfo->detention_tariff);
        $this->assertSame('EUR', $otherInfo->detention_currency);
        $this->assertSame('Updated message', $otherInfo->message);
    }

    public function test_update_creates_other_info_when_missing(): void
    {
        $booking = Booking::create([
            'booking_no' => 'BK-NO-INFO',
            'approval_no' => 'AP-NO-INFO',
            'reference_no' => 'REF-NO-INFO',
            'booking_date' => '2026-08-01',
            'sailing_date' => '2026-08-10',
        ]);

        $this->assertNull($booking->otherInfo);

        $payload = $this->validPayload([
            'special_req' => 'First time info',
            'free_days_pol' => 14,
            'message' => 'New message',
        ]);

        $this->patch(route('bookings.update', $booking), $payload)->assertRedirect();

        $booking->refresh();
        $this->assertNotNull($booking->otherInfo);
        $this->assertSame('First time info', $booking->otherInfo->special_req);
        $this->assertSame(14, $booking->otherInfo->free_days_pol);
        $this->assertSame('New message', $booking->otherInfo->message);
    }

    public function test_store_without_optional_fields_succeeds(): void
    {
        $payload = [
            'booking_no' => 'BK-MINIMAL',
            'approval_no' => 'AP-MINIMAL',
            'reference_no' => 'REF-MINIMAL',
            'booking_date' => '2026-08-23',
            'sailing_date' => '2026-08-30',
            'thru_bl' => 0,
        ];

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertRedirect();
        $booking = Booking::first();
        $this->assertSame('BK-MINIMAL', $booking->booking_no);
        $this->assertNull($booking->carrier);
        $this->assertNull($booking->commodity);

        $otherInfo = $booking->otherInfo;
        $this->assertNotNull($otherInfo, 'other info record should still be created');
        $this->assertNull($otherInfo->special_req);
        $this->assertNull($otherInfo->free_days_pol);
    }

    public function test_validation_rejects_missing_required_fields(): void
    {
        $this->post(route('bookings.store'), [])
            ->assertSessionHasErrors([
                'booking_no',
                'approval_no',
                'reference_no',
                'booking_date',
                'sailing_date',
            ]);
    }

    public function test_validation_rejects_bad_date(): void
    {
        $this->post(route('bookings.store'), $this->validPayload([
            'booking_date' => 'not-a-date',
        ]))->assertSessionHasErrors(['booking_date']);
    }

    public function test_validation_rejects_bad_integer_fields(): void
    {
        $this->post(route('bookings.store'), $this->validPayload([
            'carrier' => 'not-an-int',
            'freight_type_sub' => 'not-an-int',
        ]))->assertSessionHasErrors(['carrier', 'freight_type_sub']);
    }

    public function test_destroy_deletes_booking_and_cascades(): void
    {
        $booking = Booking::create([
            'booking_no' => 'BK-DEL',
            'approval_no' => 'AP-DEL',
            'reference_no' => 'REF-DEL',
            'booking_date' => '2026-08-01',
            'sailing_date' => '2026-08-10',
        ]);

        $booking->otherInfo()->create([
            'special_req' => 'To be deleted',
        ]);

        $this->assertSame(1, Booking::count());
        $this->assertSame(1, BookingOtherInfo::count());

        $response = $this->delete(route('bookings.destroy', $booking));

        $response->assertRedirect();
        $this->assertSame(0, Booking::count(), 'booking should be deleted');
        $this->assertSame(0, BookingOtherInfo::count(), 'other info should cascade');
    }

    public function test_edit_view_displays_existing_data(): void
    {
        $booking = Booking::create([
            'booking_no' => 'BK-VIEW',
            'approval_no' => 'AP-VIEW',
            'reference_no' => 'REF-VIEW',
            'booking_date' => '2026-08-01',
            'sailing_date' => '2026-08-10',
            'carrier' => 1,
        ]);

        $booking->otherInfo()->create([
            'special_req' => 'Visible note',
            'free_days_pol' => 7,
        ]);

        $response = $this->get(route('bookings.edit', $booking));

        $response->assertOk();
        $response->assertSee('BK-VIEW');
        $response->assertSee('AP-VIEW');
        $response->assertSee('REF-VIEW');
        $response->assertSee('Visible note');
    }
}
