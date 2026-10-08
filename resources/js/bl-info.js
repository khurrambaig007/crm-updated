jQuery(document).ready(function ($) {
    /**
     * Fill a disabled "resolved value" box from the data-detail attribute of the
     * selected option in its paired select. Scoped to a form so multiple BL Info
     * tabs can coexist without name collisions.
     */
    function bindDetailFields($scope, detailMap) {
        if ($scope.length === 0) {
            return;
        }

        $.each(detailMap, function (name, detailId) {
            var $select = $scope.find('select[name="' + name + '"]');
            var $detail = $('#' + detailId);

            if ($select.length === 0 || $detail.length === 0) {
                return;
            }

            $select.on('change', function () {
                $detail.val($select.find('option:selected').data('detail') || '');
            });

            $select.trigger('change');
        });
    }

    bindDetailFields($('#bl-info-form'), {
        bl_info_agent: 'bl_info_agent_detail',
        bl_info_vessel_voyage_1: 'bl_info_vessel_voyage_detail',
    });

    bindDetailFields($('#booking-info-form'), {
        booking_info_pol: 'booking_info_pol_detail',
        booking_info_pofd: 'booking_info_pofd_detail',
        booking_info_pot_1: 'booking_info_pot_1_detail',
        booking_info_pot_2: 'booking_info_pot_2_detail',
        booking_info_agent_pofd: 'booking_info_agent_pofd_detail',
        booking_info_agent_1: 'booking_info_agent_1_detail',
        booking_info_agent_2: 'booking_info_agent_2_detail',
        booking_info_shipper_bp: 'booking_info_shipper_bp_detail',
        booking_info_consignee: 'booking_info_consignee_detail',
    });
});
