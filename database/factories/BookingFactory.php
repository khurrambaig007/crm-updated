<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_no' => 'BK-' . fake()->unique()->bothify('##-??-####'),
            'approval_no' => 'AP-' . fake()->unique()->bothify('##-??-####'),
            'reference_no' => 'REF-' . fake()->unique()->bothify('##-??-####'),
            'booking_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'sailing_date' => fake()->dateTimeBetween('now', '+2 months'),
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
            'agent_pofd' => 1,
            'agent_1' => 1,
            'agent_2' => 1,
            'freight_type' => 1,
            'freight_type_sub' => 1,
            'consignee' => 1,
            'thru_bl' => true,
            'srr' => 0,
            'services' => 0,
            'services_sub' => 0,
            'booking_status' => 1,
            'is_split_booking' => false,
        ];
    }

    /**
     * Indicate that the booking has a specific booking number.
     */
    public function withNumber(string $number, string $approval = null, string $reference = null): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_no' => $number,
            'approval_no' => $approval ?? 'AP-' . substr($number, 3),
            'reference_no' => $reference ?? 'REF-' . substr($number, 3),
        ]);
    }

    /**
     * Indicate a pending booking (status 0).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => 0,
        ]);
    }

    /**
     * Indicate the booking has no optional lookups populated.
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'carrier' => null,
            'commodity' => null,
            'vessel_voyage' => null,
            'pol' => null,
            'pofd' => null,
            'pot_1' => null,
            'pot_2' => null,
            'shipper_bp' => null,
            'agent_pol' => null,
            'agent_pofd' => null,
            'agent_1' => null,
            'agent_2' => null,
            'consignee' => null,
        ]);
    }
}
