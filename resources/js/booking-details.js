jQuery(document).ready(function ($) {
    $('.tab-link:not(.tab-link-disabled)').on('click', function (e) {
        e.preventDefault();
        var target = $(this).attr('href').substring(1);

        $('.tab-pane').addClass('hidden');
        $('#' + target).removeClass('hidden');

        $('.tab-link').removeClass('border-primary-600 text-primary-600').addClass('border-transparent text-gray-500');
        $(this).removeClass('border-transparent text-gray-500').addClass('border-primary-600 text-primary-600');
    });

    $('.sub-tab-link').on('click', function (e) {
        e.preventDefault();
        var target = $(this).attr('href').substring(1);

        $('.sub-tab-pane').addClass('hidden');
        $('#' + target).removeClass('hidden');

        $('.sub-tab-link').removeClass('border-primary-600 text-primary-600').addClass('border-transparent text-gray-500');
        $(this).removeClass('border-transparent text-gray-500').addClass('border-primary-600 text-primary-600');
    });

    var urlParams = new URLSearchParams(window.location.search);
    var activeTab = urlParams.get('tab');
    if (activeTab) {
        $('.tab-link[href="#' + activeTab + '"]').trigger('click');
    }

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
