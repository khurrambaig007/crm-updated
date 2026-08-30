import $ from 'jquery';

function formatNumber(value) {
    var num = parseFloat(value);
    if (isNaN(num)) {
        num = 0;
    }
    return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function sumGridAmount(gridApi) {
    var total = 0;
    if (gridApi && typeof gridApi.forEachNode === 'function') {
        gridApi.forEachNode(function (node) {
            var amount = parseFloat(node.data && node.data.amount);
            if (!isNaN(amount)) {
                total += amount;
            }
        });
    }
    return total;
}

window.computeBookingTotals = function () {
    var revenue = sumGridAmount(window.__revenueGridApi);
    var cost = sumGridAmount(window.__costGridApi);
    var net = revenue - cost;

    $('#booking-revenue-total').val(formatNumber(revenue));
    $('#booking-cost-total').val(formatNumber(cost));
    $('#booking-net-total').val(formatNumber(net));
};

$(function () {
    $('#booking-approve-btn').on('click', function () {
        var $btn = $(this);
        var bookingId = $btn.data('booking-id');

        if (bookingId === undefined) {
            return;
        }

        $.ajax({
            url: '/bookings/' + bookingId + '/approve',
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '' },
            success: function (response) {
                var approved = !!(response.approved);
                $btn.data('approved', approved ? 1 : 0);
                $btn.text(approved ? 'Approved' : 'Approve');
                $btn.removeClass('bg-primary-600 bg-emerald-600 hover:bg-primary-700 hover:bg-emerald-700 shadow-primary-600/25 shadow-emerald-600/25');
                if (approved) {
                    $btn.addClass('bg-emerald-600 text-white shadow-emerald-600/25 hover:bg-emerald-700');
                } else {
                    $btn.addClass('bg-primary-600 text-white shadow-primary-600/25 hover:bg-primary-700');
                }
                window.Alerts.toast(response.message, approved ? 'success' : 'info');
            },
            error: function () {
                window.Alerts.error('Failed to update approval status.');
            },
        });
    });
});
