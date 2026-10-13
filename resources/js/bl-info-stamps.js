/**
 * BL Info > Stamps — multi-select picker.
 *
 * The card visuals (checked ring, badge, focus outline) are pure CSS via
 * :has() on the real checkbox and need no JavaScript. This module only keeps
 * the "Selected X of Y" counter in sync and powers the bulk Select all / Clear
 * controls. It never toggles display utilities, so it cannot trip the Tailwind
 * hidden-vs-flex cascade trap.
 */

jQuery(document).ready(function ($) {
    var $picker = $('[data-stamp-picker]');

    if ($picker.length === 0) {
        return;
    }

    var $cards = $picker.find('[data-stamp-checkbox]');
    var $count = $picker.find('[data-stamp-count]');
    var $selectAll = $picker.find('[data-stamp-select-all]');
    var $clear = $picker.find('[data-stamp-clear]');
    var total = $cards.length;

    function refresh() {
        var checked = $cards.filter(':checked').length;

        $count.text(checked);
        $selectAll.prop('checked', total > 0 && checked === total);
        $selectAll.prop('indeterminate', checked > 0 && checked < total);
    }

    $cards.on('change', refresh);

    $selectAll.on('change', function () {
        $cards.prop('checked', this.checked);
        refresh();
    });

    $clear.on('click', function (event) {
        event.preventDefault();
        $cards.prop('checked', false);
        refresh();
    });

    refresh();
});