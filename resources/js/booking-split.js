jQuery(document).ready(function ($) {
    var $page = $('[data-booking-split]');

    if ($page.length === 0) {
        return;
    }

    var $form = $('#split-form');
    var $submit = $('[data-split-submit]');
    var $summary = $('[data-split-summary]');
    var nextBookingNo = $page.data('next-booking-no') || '';

    function numOrNull(value) {
        var parsed = parseFloat(value);

        return isNaN(parsed) ? 0 : parsed;
    }

    function round(value) {
        return Math.round(value * 100) / 100;
    }

    function format(value) {
        return String(round(value));
    }

    /**
     * Live totals for one equipment row. A row is over-split when the requested
     * quantity exceeds what the booking actually holds; the server re-checks
     * this under a row lock, this is only the fast client feedback.
     */
    function refreshRow($row) {
        var max = numOrNull($row.data('max'));
        var $input = $row.find('[data-split-quantity]');
        var requested = numOrNull($input.val());

        if (requested < 0) {
            requested = 0;
            $input.val('0');
        }

        var invalid = requested > max + 0.005;

        if (invalid) {
            $input.addClass('border-red-400 ring-red-300').attr('aria-invalid', 'true');
        } else {
            $input.removeClass('border-red-400 ring-red-300').removeAttr('aria-invalid');
        }

        $row.find('[data-split-remaining]').text(format(Math.max(0, max - requested)));

        // The child's detail fields live in a separate card, not inside this
        // row, so they are matched by group rather than by row scope.
        var group = $row.data('split-group');

        $page
            .find('[data-split-detail-quantity="' + group + '"]')
            .text(format(requested));

        // Pro-rate the child's gross weight / packages / cargo volume from the
        // parent totals, but stop once the user has typed their own value.
        var ratio = max > 0 ? requested / max : 0;

        $page.find('[data-split-pro-rata][data-split-group="' + group + '"]').each(function () {
            var $field = $(this);

            if ($field.data('splitOverridden') === true) {
                return;
            }

            var total = numOrNull($field.data('split-pro-rata'));
            $field.val(total > 0 ? format(total * ratio) : '');
        });

        return { invalid: invalid, requested: requested };
    }

    function refresh() {
        var invalidCount = 0;
        var totalRequested = 0;

        $page.find('[data-split-row]').each(function () {
            var result = refreshRow($(this));

            if (result.invalid) {
                invalidCount++;
            }

            totalRequested += result.requested;
        });

        $submit.prop('disabled', invalidCount > 0 || totalRequested <= 0);

        if (invalidCount > 0) {
            $summary
                .removeClass('text-topbar-muted')
                .addClass('text-red-600')
                .text('You cannot split more equipment than this booking holds. Reduce the highlighted quantity.');
        } else if (totalRequested > 0) {
            $summary
                .removeClass('text-topbar-muted text-red-600')
                .addClass('text-topbar-muted')
                .text(
                    'Creating ' +
                        nextBookingNo +
                        ' with ' +
                        format(totalRequested) +
                        ' of the selected equipment. Revenue and cost lines stay on the original booking.'
                );
        } else {
            $summary
                .removeClass('text-topbar-muted text-red-600')
                .addClass('text-topbar-muted')
                .text('Select how much equipment to move to the new booking. Revenue and cost lines stay on the original booking.');
        }
    }

    $page.on('input change', '[data-split-quantity]', function () {
        refresh();
    });

    // A field the user has edited keeps their value instead of being re-pro-rated.
    $page.on('input', '[data-split-pro-rata]', function () {
        $(this).data('splitOverridden', true);
    });

    $form.on('submit', function (e) {
        if ($submit.prop('disabled')) {
            e.preventDefault();
            return;
        }

        e.preventDefault();

        window.Alerts.confirm({
            title: 'Create Split Booking?',
            text: 'The selected equipment will be moved to ' + nextBookingNo + '. This cannot be undone automatically.',
            confirmText: 'Yes, create split',
        }).then(function (confirmed) {
            if (!confirmed) {
                return;
            }

            $form[0].submit();
        });
    });

    refresh();
});
