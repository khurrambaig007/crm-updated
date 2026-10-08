<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\Booking;
use App\Models\BookingBlDetail;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

class BlInfoTest extends BaseTestCase
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
        \DB::table('agents')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Test Agent', 'code' => 'AG-01', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('agents')->updateOrInsert(
            ['id' => 2],
            ['name' => 'Second Agent', 'code' => 'AG-02', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('vessel_voyages')->updateOrInsert(
            ['id' => 1],
            ['vessel_name' => 'Test Vessel', 'voyage_number' => 'VV-001', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('pols')->updateOrInsert(
            ['id' => 1],
            ['city' => 'Testport', 'country' => 'Testland', 'port_code' => 'POL-001', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('pods')->updateOrInsert(
            ['id' => 1],
            ['city' => 'Testport', 'country' => 'Testland', 'location_code' => 'DEST-001', 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('shipper_bps')->updateOrInsert(
            ['id' => 1],
            ['code' => 'SH-01', 'name' => 'Test Shipper', 'agent_id' => 1, 'port_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        );

        \DB::table('p_a_s')->updateOrInsert(
            ['id' => 1],
            ['code' => 'CN-01', 'name' => 'Test Consignee', 'created_at' => now(), 'updated_at' => now()],
        );
    }

    public function test_approving_a_booking_creates_bl_info_with_booking_fields_and_bl_number(): void
    {
        $booking = $this->approvedBooking([
            'booking_no' => 'BK-BL-1',
            'sailing_date' => '2026-08-10',
        ]);

        $this->patchJson(route('bookings.approve', $booking))
            ->assertOk()
            ->assertJson(['approved' => true]);

        $this->assertSame(1, BookingBlDetail::count(), 'approval must create exactly one BL record');

        $detail = BookingBlDetail::first();
        $this->assertSame($booking->id, $detail->booking_id);
        $this->assertSame('BK-BL-1', $detail->bl_info_booking_no);
        $this->assertSame('2026-08-10', $detail->bl_info_sailing_date);
        $this->assertSame('BL000001', $detail->bl_info_bl_number);
    }

    public function test_bl_number_increments_globally(): void
    {
        $first = $this->approvedBooking(['booking_no' => 'BK-BL-A']);
        $this->patchJson(route('bookings.approve', $first))->assertJson(['approved' => true]);

        $second = $this->approvedBooking(['booking_no' => 'BK-BL-B']);
        $this->patchJson(route('bookings.approve', $second))->assertJson(['approved' => true]);

        $this->assertSame('BL000002', BookingBlDetail::where('booking_id', $second->id)->value('bl_info_bl_number'));
    }

    public function test_reapproving_does_not_duplicate_or_clobber_bl_info(): void
    {
        $booking = $this->approvedBooking();

        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $detail = BookingBlDetail::first();
        $detail->update(['bl_info_carrier_mbl_no' => 'MBL-USER-EDIT']);

        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => false]);
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $this->assertSame(1, BookingBlDetail::count(), 're-approval must reuse the existing BL record');
        $this->assertSame(
            'MBL-USER-EDIT',
            BookingBlDetail::first()->bl_info_carrier_mbl_no,
            're-approval must not overwrite details the user already filled in',
        );
    }

    public function test_screen_is_not_accessible_for_an_unapproved_booking(): void
    {
        $booking = Booking::create([
            'booking_no' => 'BK-UNAPPROVED',
            'approval_no' => 'AP-UNAPPROVED',
            'reference_no' => 'REF-UNAPPROVED',
            'booking_date' => '2026-08-01',
            'sailing_date' => '2026-08-10',
        ]);

        $this->get(route('bl-info.index', $booking))->assertNotFound();
        $this->assertSame(0, BookingBlDetail::count(), 'an unapproved booking must not get a BL record');
    }

    public function test_unapproving_keeps_bl_info_but_removes_access(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);
        $detailId = BookingBlDetail::first()->id;

        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => false]);

        $this->assertNotNull(BookingBlDetail::find($detailId), 'BL record must survive un-approval for audit');
        $this->get(route('bl-info.index', $booking))->assertNotFound();
    }

    public function test_index_renders_booking_fields_and_screen_shell(): void
    {
        $booking = $this->approvedBooking(['booking_no' => 'BK-BL-VIEW']);
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $response = $this->get(route('bl-info.index', $booking));

        $response->assertOk();
        $response->assertSee('BK-BL-VIEW');
        $response->assertSee('BL000001');
        $response->assertSee('Booking Info');
        $response->assertSee('Release Instructions');
        $response->assertSee('Delivery Order');
        $response->assertSee('Lock Info');
        $response->assertSee('Authorization');
    }

    public function test_placeholder_tabs_render(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $this->get(route('bl-info.release-instructions', $booking))->assertOk()->assertSee('Release Instructions')->assertSee('Coming soon');
        $this->get(route('bl-info.delivery-order', $booking))->assertOk()->assertSee('Delivery Order');
        $this->get(route('bl-info.lock-info', $booking))->assertOk()->assertSee('Lock Info');
        $this->get(route('bl-info.authorization', $booking))->assertOk()->assertSee('Authorization');
    }

    public function test_booking_info_screen_renders_the_form_fields(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $response = $this->get(route('bl-info.booking-info', $booking));

        $response->assertOk();
        $response->assertSee('Booking Info');
        $response->assertSee('Carrier');
        $response->assertSee('Reference No.');
        $response->assertSee('Shipper/BP');
        $response->assertSee('Consignee');
        $response->assertSee('POL Detail');
        $response->assertDontSee('Coming soon');
    }

    public function test_booking_info_is_seeded_from_booking_on_approval(): void
    {
        $booking = $this->approvedBooking([
            'pol' => 1,
            'pofd' => 1,
            'pot_1' => 1,
            'pot_2' => 1,
            'shipper_bp' => 1,
            'consignee' => 1,
            'agent_pofd' => 2,
            'agent_1' => 1,
            'agent_2' => 2,
            'reference_no' => 'REF-SEED',
        ]);

        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $detail = BookingBlDetail::first();
        $this->assertSame('1', (string) $detail->booking_info_pol);
        $this->assertSame('1', (string) $detail->booking_info_pofd);
        $this->assertSame('1', (string) $detail->booking_info_pot_1);
        $this->assertSame('1', (string) $detail->booking_info_pot_2);
        $this->assertSame('1', (string) $detail->booking_info_shipper_bp);
        $this->assertSame('1', (string) $detail->booking_info_consignee);
        $this->assertSame('2', (string) $detail->booking_info_agent_pofd);
        $this->assertSame('1', (string) $detail->booking_info_agent_1);
        $this->assertSame('2', (string) $detail->booking_info_agent_2);
        $this->assertSame('REF-SEED', $detail->booking_info_reference);
    }

    public function test_booking_info_update_persists_fields_without_bl_info_dates(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $payload = [
            'booking_info_pol' => '1',
            'booking_info_cntr_owner' => '3',
            'booking_info_reference' => 'BK-REF-9',
            'booking_info_pofd' => '1',
            'booking_info_agent_pofd' => '2',
            'booking_info_pot_1' => '1',
            'booking_info_agent_1' => '1',
            'booking_info_pot_2' => '1',
            'booking_info_agent_2' => '2',
            'booking_info_shipper_bp' => '1',
            'booking_info_consignee' => '1',
        ];

        $this->patch(route('bl-info.booking-info.update', $booking), $payload)
            ->assertRedirect(route('bl-info.booking-info', $booking))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $detail = BookingBlDetail::first();
        $this->assertSame('1', (string) $detail->booking_info_pol);
        $this->assertSame('3', (string) $detail->booking_info_cntr_owner);
        $this->assertSame('BK-REF-9', $detail->booking_info_reference);
        $this->assertSame('1', (string) $detail->booking_info_pofd);
        $this->assertSame('2', (string) $detail->booking_info_agent_pofd);
        $this->assertSame('1', (string) $detail->booking_info_pot_1);
        $this->assertSame('1', (string) $detail->booking_info_agent_1);
        $this->assertSame('1', (string) $detail->booking_info_pot_2);
        $this->assertSame('2', (string) $detail->booking_info_agent_2);
        $this->assertSame('1', (string) $detail->booking_info_shipper_bp);
        $this->assertSame('1', (string) $detail->booking_info_consignee);
    }

    public function test_booking_info_update_rejects_invalid_references(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $this->patch(route('bl-info.booking-info.update', $booking), [
            'booking_info_pol' => '999',
            'booking_info_pofd' => '999',
            'booking_info_agent_1' => '999',
            'booking_info_shipper_bp' => '999',
            'booking_info_consignee' => '999',
            'booking_info_cntr_owner' => '99',
        ])->assertSessionHasErrors([
            'booking_info_pol',
            'booking_info_pofd',
            'booking_info_agent_1',
            'booking_info_shipper_bp',
            'booking_info_consignee',
            'booking_info_cntr_owner',
        ]);
    }

    public function test_update_persists_bl_info_fields(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $payload = [
            'bl_info_date' => '2026-08-05',
            'bl_info_agent' => '2',
            'bl_info_accounting_date' => '2026-08-06',
            'bl_info_carrier_mbl_no' => 'MBL-123',
            'bl_info_vessel_voyage_1' => '1',
            'bl_info_booking_si_status' => '2',
            'bl_info_transshipment' => '1',
        ];

        $this->patch(route('bl-info.update', $booking), $payload)
            ->assertRedirect(route('bl-info.index', $booking))
            ->assertSessionHas('status');

        $detail = BookingBlDetail::first();
        $this->assertSame('2026-08-05', $detail->bl_info_date);
        $this->assertSame('2', (string) $detail->bl_info_agent);
        $this->assertSame('2026-08-06', $detail->bl_info_accounting_date);
        $this->assertSame('MBL-123', $detail->bl_info_carrier_mbl_no);
        $this->assertSame('1', (string) $detail->bl_info_vessel_voyage_1);
        $this->assertSame('2', (string) $detail->bl_info_booking_si_status);
        $this->assertTrue($detail->bl_info_transshipment);
    }

    public function test_update_can_clear_transshipment(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        BookingBlDetail::first()->update(['bl_info_transshipment' => true]);

        $this->patch(route('bl-info.update', $booking), [
            'bl_info_date' => '2026-08-05',
            'bl_info_accounting_date' => '2026-08-06',
            'bl_info_transshipment' => '0',
        ])->assertRedirect(route('bl-info.index', $booking));

        $this->assertFalse(BookingBlDetail::first()->bl_info_transshipment);
    }

    public function test_update_requires_dates_and_accounting_not_before_bl_date(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $this->patch(route('bl-info.update', $booking), [])
            ->assertSessionHasErrors(['bl_info_date', 'bl_info_accounting_date']);

        $this->patch(route('bl-info.update', $booking), [
            'bl_info_date' => '2026-08-05',
            'bl_info_accounting_date' => '2026-08-04',
        ])->assertSessionHasErrors(['bl_info_accounting_date']);

        $this->patch(route('bl-info.update', $booking), [
            'bl_info_date' => '2026-08-05',
            'bl_info_accounting_date' => '2026-08-05',
        ])->assertSessionHasNoErrors();
    }

    public function test_update_rejects_invalid_references_and_status(): void
    {
        $booking = $this->approvedBooking();
        $this->patchJson(route('bookings.approve', $booking))->assertJson(['approved' => true]);

        $this->patch(route('bl-info.update', $booking), [
            'bl_info_agent' => '999',
            'bl_info_vessel_voyage_1' => '999',
            'bl_info_booking_si_status' => '99',
            'bl_info_date' => 'not-a-date',
            'bl_info_accounting_date' => '2026-08-06',
        ])->assertSessionHasErrors([
            'bl_info_agent',
            'bl_info_vessel_voyage_1',
            'bl_info_booking_si_status',
            'bl_info_date',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function approvedBooking(array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'booking_no' => 'BK-BL',
            'approval_no' => 'AP-BL',
            'reference_no' => 'REF-BL',
            'booking_date' => '2026-08-01',
            'sailing_date' => '2026-08-10',
            'pol' => null,
            'pofd' => null,
        ], $overrides));
    }
}
