jQuery(document).ready(function ($) {
    var $bookingForm = $('#booking-form');
    if ($bookingForm.length === 0) {
        return;
    }

    var detailMap = {
        carrier: 'carrier_detail',
        commodity: 'commodity_detail',
        vessel_voyage: 'vessel_voyage_detail',
        pol: 'pol_detail',
        agent_pol: 'agent_pol_detail',
        pofd: 'pofd_detail',
        agent_pofd: 'agent_pofd_detail',
        pot_1: 'pot_1_detail',
        agent_1: 'agent_1_detail',
        pot_2: 'pot_2_detail',
        agent_2: 'agent_2_detail',
        shipper_bp: 'shipper_bp_detail',
        consignee: 'consignee_detail',
    };

    $.each(detailMap, function (name, detailId) {
        var $select = $bookingForm.find('select[name="' + name + '"]');
        if ($select.length === 0 || $('#' + detailId).length === 0) {
            return;
        }

        $select.on('change', function () {
            var selected = $select.find('option:selected');
            var detail = selected.data('detail') || '';
            $('#' + detailId).val(detail);
        });

        $select.trigger('change');
    });

    $('.tab-link-disabled').on('click', function (e) {
        e.preventDefault();
        window.Alerts.toast('Please save the booking first before filling Other Info or Message.', 'warning');
    });
});
