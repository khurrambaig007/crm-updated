<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BookingSplitTest extends BaseTestCase
{
    use RefreshDatabase;

    /** Container size ids seeded by seedLookups(). */
    private const SIZE_40HC = 1;

    private const SIZE_20GP = 2;

    private const SIZE_45HC = 3;

    private const TYPE_DRY = 1;

    private const TYPE_REEFER = 2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedLookups();

        $this->withoutMiddleware([Authenticate::class, PermissionMiddleware::class]);

        $this->actingAs(User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]));
    }

    protected function seedLookups(): void
    {
        $now = now();

        DB::table('container_sizes')->insert([
            ['id' => self::SIZE_40HC, 'size' => '40HC', 'created_at' => $now, 'updated_at' => $now],
            ['id' => self::SIZE_20GP, 'size' => '20GP', 'created_at' => $now, 'updated_at' => $now],
            ['id' => self::SIZE_45HC, 'size' => '45HC', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('container_types')->insert([
            ['id' => self::TYPE_DRY, 'name' => 'Dry', 'created_at' => $now, 'updated_at' => $now],
            ['id' => self::TYPE_REEFER, 'name' => 'Reefer', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('pols')->insert([
            'id' => 1, 'city' => 'Testport', 'country' => 'Testland', 'port_code' => 'TST', 'created_at' => $now, 'updated_at' => $now,
        ]);

        DB::table('pods')->insert([
            'id' => 1, 'city' => 'Pod City', 'country' => 'Podland', 'location_code' => 'DST', 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    /**
     * BK-10025 with 40HC x10, 20GP x5, 45HC x2.
     */
    private function makeBooking(): Booking
    {
        $booking = Booking::create([
            'booking_no' => 'AMSTSTDST000001',
            'reporting_no' => 'B-1/26',
            'reference_no' => 'REF-1',
            'booking_date' => '2026-09-01',
            'sailing_date' => '2026-09-30',
            'pol' => 1,
            'pofd' => 1,
        ]);

        $booking->equipments()->create([
            'size' => self::SIZE_40HC, 'type' => self::TYPE_DRY, 'quantity' => 10, 'approval_status' => 1,
        ]);
        $booking->equipments()->create([
            'size' => self::SIZE_20GP, 'type' => self::TYPE_DRY, 'quantity' => 5, 'approval_status' => 1,
        ]);
        $booking->equipments()->create([
            'size' => self::SIZE_45HC, 'type' => self::TYPE_DRY, 'quantity' => 2, 'approval_status' => 1,
        ]);

        return $booking;
    }

    /**
     * @param  array<int, array<string, mixed>>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'size' => self::SIZE_40HC,
            'type' => self::TYPE_DRY,
            'quantity' => 4,
            'gross_weight' => '4000',
            'packages' => '40',
            'cargo_volumn' => '80',
            'approval_status' => 1,
        ], $overrides);
    }

    public function test_split_creates_child_booking_numbered_from_the_parent(): void
    {
        $booking = $this->makeBooking();

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [
                $this->payload(['size' => self::SIZE_40HC, 'quantity' => 4]),
                $this->payload(['size' => self::SIZE_20GP, 'quantity' => 2]),
            ],
        ])->assertRedirect();

        $child = Booking::where('parent_booking_id', $booking->id)->first();

        $this->assertNotNull($child);
        $this->assertSame('AMSTSTDST000001-01', $child->booking_no);
        $this->assertTrue($child->is_split_booking);
        $this->assertSame($booking->id, $child->parent_booking_id);
        $this->assertFalse($child->approved);

        // Header fields are inherited so the child routes the same voyage.
        $this->assertSame('REF-1', $child->reference_no);
        $this->assertSame(1, $child->pol);
        $this->assertSame(1, $child->pofd);
        $this->assertNotSame($booking->booking_no, $child->booking_no);
        $this->assertNotSame($booking->reporting_no, $child->reporting_no);
    }

    public function test_split_moves_equipment_from_parent_to_child(): void
    {
        $booking = $this->makeBooking();

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [
                $this->payload(['size' => self::SIZE_40HC, 'quantity' => 4]),
                $this->payload(['size' => self::SIZE_20GP, 'quantity' => 2]),
            ],
        ])->assertRedirect();

        $child = Booking::where('parent_booking_id', $booking->id)->first();

        $parentQuantities = $booking->equipments()
            ->get()
            ->mapWithKeys(fn ($e) => [$e->size => (float) $e->quantity])
            ->all();

        $childQuantities = $child->equipments()
            ->get()
            ->mapWithKeys(fn ($e) => [$e->size => (float) $e->quantity])
            ->all();

        // 40HC 10 -> 6 remaining, 20GP 5 -> 3 remaining, 45HC untouched.
        $this->assertSame(6.0, $parentQuantities[self::SIZE_40HC]);
        $this->assertSame(3.0, $parentQuantities[self::SIZE_20GP]);
        $this->assertSame(2.0, $parentQuantities[self::SIZE_45HC]);

        $this->assertSame(4.0, $childQuantities[self::SIZE_40HC]);
        $this->assertSame(2.0, $childQuantities[self::SIZE_20GP]);
        $this->assertCount(2, $child->equipments);
    }

    public function test_split_does_not_copy_revenue_or_cost_lines(): void
    {
        $booking = $this->makeBooking();

        DB::table('charges')->insert(['id' => 1, 'name' => 'Ocean Freight', 'created_at' => now(), 'updated_at' => now()]);

        $booking->revenues()->create([
            'charge_id' => 1, 'container_size_id' => self::SIZE_40HC, 'container_type_id' => self::TYPE_DRY,
            'quantity' => '10', 'amount' => 5000,
        ]);
        $booking->costs()->create([
            'charge_id' => 1, 'container_size_id' => self::SIZE_40HC, 'container_type_id' => self::TYPE_DRY,
            'quantity' => '10', 'cost' => 3000, 'amount' => 3000,
        ]);

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 4])],
        ])->assertRedirect();

        $child = Booking::where('parent_booking_id', $booking->id)->first();

        $this->assertCount(0, $child->revenues);
        $this->assertCount(0, $child->costs);
        $this->assertCount(1, $booking->fresh()->revenues);
        $this->assertCount(1, $booking->fresh()->costs);
    }

    public function test_split_copies_other_info_to_the_child(): void
    {
        $booking = $this->makeBooking();

        $booking->otherInfo()->create(['special_req' => 'Keep upright', 'free_days_pol' => 7]);

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 4])],
        ])->assertRedirect();

        $child = Booking::where('parent_booking_id', $booking->id)->first();

        $this->assertSame('Keep upright', $child->otherInfo->special_req);
        $this->assertSame(7, $child->otherInfo->free_days_pol);
    }

    public function test_split_rejects_more_equipment_than_the_booking_holds(): void
    {
        $booking = $this->makeBooking();

        $response = $this->from(route('bookings.split', $booking))
            ->post(route('bookings.split.store', $booking), [
                'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 12])],
            ]);

        $response->assertRedirect(route('bookings.split', $booking));
        $response->assertSessionHasErrors('equipment');

        $this->assertSame(1, Booking::count(), 'no child booking may be created');

        $this->assertSame(
            10.0,
            (float) $booking->equipments()->where('size', self::SIZE_40HC)->value('quantity'),
            'the parent equipment must be untouched'
        );
    }

    public function test_split_rejects_equipment_the_booking_does_not_own(): void
    {
        $booking = $this->makeBooking();

        $this->from(route('bookings.split', $booking))
            ->post(route('bookings.split.store', $booking), [
                'equipment' => [$this->payload(['size' => self::SIZE_45HC, 'type' => self::TYPE_REEFER, 'quantity' => 1])],
            ])
            ->assertSessionHasErrors('equipment');

        $this->assertSame(1, Booking::count());
    }

    public function test_split_rejects_when_every_quantity_is_zero(): void
    {
        $booking = $this->makeBooking();

        $this->from(route('bookings.split', $booking))
            ->post(route('bookings.split.store', $booking), [
                'equipment' => [
                    $this->payload(['size' => self::SIZE_40HC, 'quantity' => 0]),
                    $this->payload(['size' => self::SIZE_20GP, 'quantity' => 0]),
                ],
            ])
            ->assertSessionHasErrors('equipment');

        $this->assertSame(1, Booking::count());
    }

    public function test_split_can_take_the_entire_remaining_quantity(): void
    {
        $booking = $this->makeBooking();

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 10])],
        ])->assertRedirect();

        $this->assertNull(
            $booking->equipments()->where('size', self::SIZE_40HC)->first(),
            'a fully moved row must not linger with a zero quantity'
        );
        $this->assertSame(10.0, (float) Booking::where('parent_booking_id', $booking->id)->first()->equipments()->value('quantity'));
    }

    public function test_split_numbering_increments_for_each_child(): void
    {
        $booking = $this->makeBooking();

        foreach ([4, 2] as $quantity) {
            $this->post(route('bookings.split.store', $booking), [
                'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => $quantity])],
            ])->assertRedirect();
        }

        $numbers = $booking->splitBookings()->pluck('booking_no')->all();

        $this->assertSame(['AMSTSTDST000001-01', 'AMSTSTDST000001-02'], $numbers);
    }

    public function test_split_chains_from_the_immediate_parent(): void
    {
        $booking = $this->makeBooking();

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 4])],
        ])->assertRedirect();

        $firstChild = Booking::where('parent_booking_id', $booking->id)->first();

        $this->post(route('bookings.split.store', $firstChild), [
            'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 2])],
        ])->assertRedirect();

        $secondChild = Booking::where('parent_booking_id', $firstChild->id)->first();

        $this->assertSame('AMSTSTDST000001-01-01', $secondChild->booking_no);
        $this->assertTrue($secondChild->is_split_booking);

        // The tree is walkable in both directions.
        $this->assertSame($booking->id, $secondChild->parentBooking->parent_booking_id);
        $this->assertTrue($booking->hasSplitBookings());
        $this->assertFalse($secondChild->hasSplitBookings());
    }

    public function test_split_stores_the_supplied_equipment_attributes(): void
    {
        $booking = $this->makeBooking();

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [
                $this->payload([
                    'size' => self::SIZE_40HC,
                    'quantity' => 4,
                    'gross_weight' => '4400',
                    'packages' => '42',
                    'cargo_volumn' => '90.5',
                    'unit' => 7,
                    'approval_status' => 2,
                ]),
            ],
        ])->assertRedirect();

        $child = Booking::where('parent_booking_id', $booking->id)->first();
        $equipment = $child->equipments()->first();

        $this->assertSame('4400', $equipment->gross_weight);
        $this->assertSame('42', $equipment->packages);
        $this->assertSame('90.5', $equipment->cargo_volumn);
        $this->assertSame(7, $equipment->unit);
        $this->assertSame(2, $equipment->approval_status);
    }

    public function test_split_aggregates_equipment_rows_that_share_a_size_and_type(): void
    {
        $booking = $this->makeBooking();

        $booking->equipments()->create([
            'size' => self::SIZE_40HC, 'type' => self::TYPE_DRY, 'quantity' => 3, 'approval_status' => 1,
        ]);

        $this->assertSame(13.0, (float) $booking->equipments()->where('size', self::SIZE_40HC)->sum('quantity'));

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 13])],
        ])->assertRedirect();

        $child = Booking::where('parent_booking_id', $booking->id)->first();

        $this->assertCount(0, $booking->equipments()->where('size', self::SIZE_40HC)->get());
        $this->assertSame(13.0, (float) $child->equipments()->value('quantity'));
    }

    public function test_a_split_child_does_not_disturb_the_booking_number_sequence(): void
    {
        $booking = $this->makeBooking();

        $this->post(route('bookings.split.store', $booking), [
            'equipment' => [$this->payload(['size' => self::SIZE_40HC, 'quantity' => 4])],
        ])->assertRedirect();

        // A naive digit-strip of AMSTSTDST000001-01 yields 100001 and would push
        // every later booking up by 100000.
        $this->post(route('bookings.store'), [
            'approval_no' => 'AP-2',
            'reference_no' => 'REF-2',
            'booking_date' => '2026-09-02',
            'sailing_date' => '2026-10-01',
            'pol' => 1,
            'pofd' => 1,
        ])->assertRedirect();

        $fresh = Booking::whereNull('parent_booking_id')->orderByDesc('id')->first();

        $this->assertSame('AMSTSTDST000002', $fresh->booking_no);
    }

    public function test_split_form_lists_the_equipment_available_to_split(): void
    {
        $booking = $this->makeBooking();

        $this->get(route('bookings.split', $booking))
            ->assertOk()
            ->assertSee('AMSTSTDST000001-01')
            ->assertSee('40HC')
            ->assertSee('45HC')
            ->assertSee('max="10"', false)
            ->assertSee('max="2"', false);
    }

    public function test_split_form_wraps_the_equipment_inputs_so_they_submit(): void
    {
        $booking = $this->makeBooking();

        $html = $this->get(route('bookings.split', $booking))
            ->assertOk()
            ->getContent();

        // The app layout renders its own forms (logout, search), so scope the
        // check to the split form's own boundaries.
        $formStart = strpos($html, 'id="split-form"');
        $this->assertNotFalse($formStart, 'the split form must render');

        $formEnd = strpos($html, '</form>', $formStart);
        $this->assertNotFalse($formEnd, 'the split form must be closed');

        $inside = substr($html, $formStart, $formEnd - $formStart);

        // $inside starts at the form's own id attribute, so its own opening tag
        // is behind us: anything left would be an illegally nested form.
        $this->assertSame(
            0,
            substr_count($inside, '<form'),
            'the split form must not contain a nested form'
        );
        $this->assertGreaterThan(
            0,
            substr_count($inside, 'name="equipment['),
            'the equipment inputs must be inside the split form so they submit'
        );
        $this->assertStringContainsString('name="_token"', $inside, 'spatie injects the CSRF token');

        // Blade must interpolate the row index into every equipment field name;
        // an unbalanced brace silently swallows the rest of the template.
        $this->assertStringContainsString('name="equipment[0][cargo_volumn]"', $inside);
        $this->assertStringContainsString('name="equipment[2][quantity]"', $inside);
        $this->assertStringNotContainsString('{{', $inside);
        $this->assertStringNotContainsString('&lt;?php', $inside);
    }

    public function test_split_form_keeps_card_gaps_and_renders_actions_in_a_card(): void
    {
        $booking = $this->makeBooking();

        $html = $this->get(route('bookings.split', $booking))
            ->assertOk()
            ->getContent();

        $formStart = strpos($html, 'id="split-form"');
        $this->assertNotFalse($formStart, 'the split form must render');

        $tagStart = strrpos(substr($html, 0, $formStart), '<form');
        $tagEnd = strpos($html, '>', $formStart);
        $openTag = substr($html, (int) $tagStart, $tagEnd - $tagStart + 1);

        // The cards are children of the form, so the gap utility has to sit on
        // the form itself. On a wrapper above the form it silently collapses
        // every gap between the cards.
        $this->assertStringContainsString(
            'space-y-8',
            $openTag,
            'the form must carry the card spacing utility'
        );

        $formEnd = strpos($html, '</form>', $formStart);
        $inside = substr($html, $formStart, $formEnd - $formStart);

        // Child fields now live in their own card, matched to the table row by
        // group index rather than by row scope.
        $this->assertStringContainsString('data-split-pro-rata="', $inside);
        $this->assertStringContainsString('data-split-group="0"', $inside);
        $this->assertStringContainsString('data-split-detail-quantity="0"', $inside);

        // The submit button must live inside the form, after the detail card.
        $this->assertStringContainsString('id="split-submit"', $inside);
        $this->assertGreaterThan(
            (int) strpos($inside, 'New Booking Details'),
            (int) strpos($inside, 'id="split-submit"'),
            'the action card must render after the details card'
        );
    }

    public function test_split_form_repopulates_the_requested_quantities_after_a_rejection(): void
    {
        $booking = $this->makeBooking();

        $this->from(route('bookings.split', $booking))
            ->post(route('bookings.split.store', $booking), [
                'equipment' => [
                    $this->payload(['size' => self::SIZE_40HC, 'quantity' => 12]),
                ],
            ])
            ->assertRedirect(route('bookings.split', $booking));

        $this->get(route('bookings.split', $booking))
            ->assertOk()
            ->assertSee('You cannot split', false)
            ->assertSee('value="12"', false);
    }

    public function test_split_form_reports_when_there_is_no_equipment(): void
    {
        $booking = Booking::create([
            'booking_no' => 'AMSTSTDST000009',
            'reporting_no' => 'B-9/26',
            'reference_no' => 'REF-9',
            'booking_date' => '2026-09-01',
            'sailing_date' => '2026-09-30',
        ]);

        $this->get(route('bookings.split', $booking))
            ->assertOk()
            ->assertSee('no equipment to split');
    }
}
