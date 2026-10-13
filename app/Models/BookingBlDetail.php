<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['booking_id', 'bl_info_date', 'bl_info_agent', 'bl_info_booking_no', 'bl_info_sailing_date', 'bl_info_accounting_date', 'bl_info_carrier_mbl_no', 'bl_info_bl_number', 'bl_info_vessel_voyage_1', 'bl_info_vessel_voyage_2', 'bl_info_transshipment', 'bl_info_booking_si_status', 'booking_info_pol', 'booking_info_pofd', 'booking_info_pot_1', 'booking_info_pot_2', 'booking_info_shipper_bp', 'booking_info_cntr_owner', 'booking_info_agent_pofd', 'booking_info_agent_1', 'booking_info_agent_2', 'booking_info_consignee', 'booking_info_reference', 'release_instruction_date', 'release_instruction_status', 'release_instruction_type', 'release_instruction_description', 'delivery_order_doc', 'delivery_order_date', 'delivery_order_validity_date', 'delivery_order_agent', 'delivery_order_vessel_voyage', 'delivery_order_deliver_to', 'delivery_order_remarks', 'lock_info_pol_locked_to', 'lock_info_pol_locked_on', 'lock_info_pot_1_locked_to', 'lock_info_pot_1_locked_on', 'lock_info_pot_2_locked_to', 'lock_info_pot_2_locked_on', 'lock_info_pofd_locked_to', 'lock_info_pofd_locked_on', 'lock_info_locked_detail', 'bl_detail_shipper', 'bl_detail_consignee', 'bl_detail_notify_party_1', 'bl_detail_notify_party_2', 'bl_detail_pol', 'bl_detail_vessel_voyage', 'bl_detail_pofd', 'bl_detail_place_of_delivery', 'bl_detail_original_bls', 'bl_detail_units', 'bl_detail_freight_type', 'bl_detail_delivery_agent', 'bl_detail_marks_and_numbers', 'bl_detail_no_of_packages', 'bl_detail_description_of_goods', 'bl_detail_gross_weight', 'bl_detail_measurement', 'bl_detail_on_board_date', 'bl_detail_place_of_issue', 'bl_detail_date_of_issue', 'refno_invoice_no', 'refno_invoice_date', 'refno_export_no', 'refno_export_date', 'refno_contact_no', 'refno_contact_date', 'refno_lc_no', 'refno_lc_date', 'refno_shipper_ref', 'refno_client_ref'])]
final class BookingBlDetail extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'bl_info_transshipment' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * The stamps selected for this bill of lading, in selection order.
     */
    public function stamps(): BelongsToMany
    {
        return $this
            ->belongsToMany(Stamp::class, 'booking_bl_detail_stamp')
            ->withTimestamps()
            ->orderBy('booking_bl_detail_stamp.id');
    }

    public function deliveryOrderAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'delivery_order_agent');
    }

    public function deliveryOrderVesselVoyage(): BelongsTo
    {
        return $this->belongsTo(VesselVoyage::class, 'delivery_order_vessel_voyage');
    }

    /**
     * Booking header fields mirrored onto the BL Info screens (the BL Info
     * header and the Booking Info tab), keyed by the booking_bl_details column
     * and sourced from the matching booking column.
     *
     * @return array<string, mixed>
     */
    public static function fromBooking(Booking $booking): array
    {
        return [
            'bl_info_agent' => $booking->agent_pol,
            'bl_info_vessel_voyage_1' => $booking->vessel_voyage,
            'booking_info_pol' => $booking->pol,
            'booking_info_pofd' => $booking->pofd,
            'booking_info_pot_1' => $booking->pot_1,
            'booking_info_pot_2' => $booking->pot_2,
            'booking_info_shipper_bp' => $booking->shipper_bp,
            'booking_info_consignee' => $booking->consignee,
            'booking_info_agent_pofd' => $booking->agent_pofd,
            'booking_info_agent_1' => $booking->agent_1,
            'booking_info_agent_2' => $booking->agent_2,
            'booking_info_reference' => $booking->reference_no,
            'booking_info_cntr_owner' => $booking->carrier,
            'delivery_order_agent' => $booking->agent_pol,
            'delivery_order_vessel_voyage' => $booking->vessel_voyage,
            'lock_info_pol_locked_to' => $booking->pol,
            'lock_info_pot_1_locked_to' => $booking->pot_1,
            'lock_info_pot_2_locked_to' => $booking->pot_2,
            'lock_info_pofd_locked_to' => $booking->pofd,
            'bl_detail_shipper' => $booking->shipper_bp,
            'bl_detail_consignee' => $booking->consignee,
            'bl_detail_pol' => $booking->pol,
            'bl_detail_vessel_voyage' => $booking->vessel_voyage,
            'bl_detail_pofd' => $booking->pofd,
            'bl_detail_freight_type' => $booking->freight_type,
            // The client's own reference is the booking's reference number.
            'refno_client_ref' => $booking->reference_no,
        ];
    }

    /**
     * Next BL number in the global "BL000001" sequence.
     *
     * Parsed in PHP rather than SQL so the same code path works on MySQL and
     * the SQLite test connection.
     */
    public static function nextBlNumber(): string
    {
        $max = self::query()
            ->where('bl_info_bl_number', 'like', 'BL%')
            ->pluck('bl_info_bl_number')
            ->map(fn (?string $number): int => (int) preg_replace('/\D/', '', (string) $number))
            ->max() ?? 0;

        return 'BL'.str_pad((string) ($max + 1), 6, '0', STR_PAD_LEFT);
    }
}
