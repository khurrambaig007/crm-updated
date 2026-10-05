import $ from './jquery-global';

function parseAmount(value) {
    const amount = Number.parseFloat(value);
    return Number.isFinite(amount) ? amount : 0;
}

function currencyCode() {
    return $('#sales-invoice-page').data('currency-code') || 'PKR';
}

function formatAmount(value) {
    return `${currencyCode()} ${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function recalculateInvoice() {
    let subtotal = 0;

    $('#sales-invoice-details .sales-invoice-detail').each(function () {
        const amount = parseAmount($(this).find('input[name$="[amount]"]').val());
        subtotal += amount;
        $(this).find('.line-total').text(formatAmount(amount));
    });

    subtotal = Math.round((subtotal + Number.EPSILON) * 100) / 100;
    const vatRate = Math.min(100, Math.max(0, parseAmount($('#vat_rate').val())));
    const vatAmount = Math.round((subtotal * vatRate / 100 + Number.EPSILON) * 100) / 100;

    $('#invoice-subtotal').text(formatAmount(subtotal));
    $('#invoice-vat-rate').text(vatRate.toFixed(2));
    $('#invoice-vat-amount').text(formatAmount(vatAmount));
    $('#invoice-total').text(formatAmount(subtotal + vatAmount));
    $('#invoice-total-label').text(`Total ${currencyCode()}`);
}

$(document).ready(function () {
    let nextIndex = Date.now();

    $('#add-sales-invoice-detail').on('click', function () {
        const row = $('#sales-invoice-detail-template').html().replaceAll('__INDEX__', nextIndex++);
        $('#sales-invoice-details').append(row);
        recalculateInvoice();
    });

    $(document).on('click', '.remove-sales-invoice-detail', function () {
        const $rows = $('#sales-invoice-details .sales-invoice-detail');

        if ($rows.length === 1) {
            $rows.find('input, textarea').val('');
        } else {
            $(this).closest('.sales-invoice-detail').remove();
        }

        recalculateInvoice();
    });

    $(document).on('input change', '#sales-invoice-details input[name$="[amount]"], #vat_rate', recalculateInvoice);

    $(document).on('change', '#currency_code', function () {
        const code = $(this).val() || 'PKR';
        $('#sales-invoice-page').data('currency-code', code);
        $('.line-total, #invoice-subtotal, #invoice-vat-amount, #invoice-total').each(function () {
            const parts = $(this).text().split(' ');
            if (parts.length > 1) {
                $(this).text(`${code} ${parts.slice(1).join(' ')}`);
            }
        });
        $('#invoice-total-label').text(`Total ${code}`);
        recalculateInvoice();
    });

    recalculateInvoice();
});