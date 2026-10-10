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

    bindDetailFields($('#delivery-order-form'), {
        delivery_order_agent: 'delivery_order_agent_detail',
        delivery_order_vessel_voyage: 'delivery_order_vessel_voyage_detail',
    });

    /**
     * Live character counts on the BL Detail particulars block. Carriers reject
     * bills of lading whose free-text fields overrun their limits, so the count
     * turns amber approaching the cap and red once it is passed.
     */
    function bindCharCounters($scope) {
        $scope.find('[data-counter]').each(function () {
            var $field = $(this);
            var $counter = $scope.find('[data-counter-for="' + $field.attr('name') + '"]');

            if ($field.length === 0 || $counter.length === 0) {
                return;
            }

            var max = parseInt($field.attr('maxlength'), 10);
            var update = function () {
                var length = $field.val().length;

                $counter.text(max > 0 ? length + ' / ' + max : String(length));
                $counter.toggleClass('text-amber-600', length > max * 0.9 && length <= max);
                $counter.toggleClass('text-red-600', length > max);
            };

            $field.on('input change', update);
            update();
        });
    }

    /**
     * The units dropdown decides how the gross weight figure is read, so mirror
     * that choice underneath the weight box instead of leaving it to memory.
     */
    function bindUnitHint($scope) {
        var $units = $scope.find('select[name="bl_detail_units"]');
        var $hint = $scope.find('[data-unit-hint]');

        if ($units.length === 0 || $hint.length === 0) {
            return;
        }

        var update = function () {
            var hint = $units.find('option:selected').data('hint');

            $hint.text(hint || '').toggleClass('hidden', !hint);
        };

        $units.on('change', update);
        update();
    }

    bindCharCounters($('#bl-detail-form'));
    bindUnitHint($('#bl-detail-form'));

    /**
     * Section dropdown in the BL Info sub-navigation bar.
     *
     * Runs from the bundle (see .ai/rules/js.md): the Vite app.js module is the
     * only place window.jQuery is set, so this selector logic must not live in
     * an inline blade script where jQuery would be undefined.
     */
    var $navToggle = $('[data-bl-nav-toggle]');

    if ($navToggle.length) {
        var $navParent = $navToggle.closest('[data-bl-nav-parent]');
        var $navPanel = $navParent.children('.absolute');
        var $navChevron = $navToggle.find('svg');

        var openNavPanel = function () {
            $navPanel.removeClass('hidden opacity-0 -translate-y-1').addClass('block opacity-100 translate-y-0');
            $navChevron.removeClass('rotate-0').addClass('rotate-180');
            $navToggle.attr('aria-expanded', 'true');
        };

        var closeNavPanel = function () {
            $navPanel.removeClass('block opacity-100 translate-y-0').addClass('hidden opacity-0 -translate-y-1');
            $navChevron.removeClass('rotate-180').addClass('rotate-0');
            $navToggle.attr('aria-expanded', 'false');
        };

        $navToggle.on('click', function (e) {
            e.stopPropagation();

            if ($navPanel.hasClass('block')) {
                closeNavPanel();
            } else {
                openNavPanel();
            }
        });

        // Dismiss when clicking anywhere outside the dropdown parent.
        $(document).on('click', function (e) {
            if ($navPanel.hasClass('block') && !$navParent.is(e.target) && $navParent.has(e.target).length === 0) {
                closeNavPanel();
            }
        });

        // Dismiss on Escape, then give focus back to the toggle.
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $navPanel.hasClass('block')) {
                closeNavPanel();
                $navToggle.trigger('focus');
            }
        });
    }
});
