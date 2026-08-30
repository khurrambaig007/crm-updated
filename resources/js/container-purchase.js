jQuery(document).ready(function ($) {
    var rates = $('[data-currency-rates]').data('currency-rates') || {};

    var formByTable = {
        'cp-purchase-table': 'cp-purchase-form',
        'cp-invoice-table': 'cp-invoice-form',
        'cp-release-table': 'cp-release-form',
        'cp-debit-table': 'cp-debit-form',
        'cp-po-cancel-table': 'cp-po-cancel-form'
    };

    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

    function isDataTable(selector) {
        return $.fn.dataTable && $.fn.dataTable.isDataTable(selector);
    }

    function resetForm($form) {
        $form.find(':input').each(function () {
            var $input = $(this);
            if ($input.attr('type') === 'hidden' && $input.attr('name') === '_token') {
                return;
            }
            if ($input.attr('type') === 'hidden' && $input.attr('name') === '_method') {
                $input.remove();
                return;
            }
            $input.val('').trigger('change');
        });
        $form.data('submit-url', $form.data('store-url'));
        $form.data('edit-id', null);
        $form.find('.cancel-edit-btn').addClass('hidden');
    }

    function reloadTable($form) {
        var tableId = $form.data('child-table');
        if (tableId && isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable().ajax.reload(null, false);
        }
    }

    $('#cp-currency').on('change', function () {
        var rate = (rates && rates[this.value]) || 1;
        $('#cp-rate').val(rate);
        $('#cp-currency-code').val(this.value);
    });

    $('.cp-transno-select').each(function () {
        var $select = $(this);
        var $form = $select.closest('form');
        var $hidden = $form.find('input[name="container_purchase_detail_id"]');

        $select.select2({
            placeholder: 'Type last digits of transaction no...',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: $select.data('url'),
                dataType: 'json',
                delay: 350,
                data: function (params) {
                    return { q: params.term, page: params.page || 1 };
                },
                processResults: function (data) {
                    return {
                        results: data.results,
                        pagination: { more: (data.pagination && data.pagination.more) || false }
                    };
                }
            }
        });

        $select.on('select2:select', function (e) {
            $hidden.val(e.params.data.id);
        });

        $select.on('select2:clear', function () {
            $hidden.val('');
        });
    });

    $(document).on('change', '.cp-currency-select', function () {
        var $form = $(this).closest('form');
        var rate = (rates && rates[this.value]) || '';
        $form.find('.cp-rate-input').val(rate);
        $form.find('.cp-currency-code-input').val(this.value);
    });

    $('form[data-submit-url]').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');

        $button.prop('disabled', true);

        $.ajax({
            url: $form.data('submit-url'),
            method: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function (response) {
                window.Alerts.toast(response.message || 'Saved successfully.', 'success');
                resetForm($form);
                reloadTable($form);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var messages = [];
                    $.each(xhr.responseJSON.errors, function (field, list) {
                        $.each(list, function (index, message) {
                            messages.push(message);
                        });
                    });
                    window.Alerts.validation(messages);
                    return;
                }

                var message = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                window.Alerts.error(message);
            },
            complete: function () {
                $button.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.cp-row-edit', function () {
        var $btn = $(this);
        var formId = formByTable[$btn.data('table-id')];
        var $form = formId ? $('#' + formId) : null;
        if (!$form || !$form.length) { return; }

        var payload = $btn.data('edit') || {};

        resetForm($form);

        $.each(payload, function (key, value) {
            if (key === 'container_purchase_trans_no' || key === 'id') { return; }
            var $field = $form.find('[name="' + key + '"]');
            if (!$field.length || $field.hasClass('cp-transno-select')) { return; }
            $field.val(value == null ? '' : value);
        });

        var $transno = $form.find('.cp-transno-select');
        if ($transno.length) {
            if (payload.container_purchase_detail_id) {
                var text = payload.container_purchase_trans_no || '#' + payload.container_purchase_detail_id;
                $transno.empty().append($('<option>', {
                    value: payload.container_purchase_detail_id,
                    text: text,
                    selected: true
                })).trigger('change');
            } else {
                $transno.val(null).trigger('change');
            }
        }

        $form.data('submit-url', $btn.data('update-url'));
        $form.data('edit-id', payload.id);

        if (!$form.find('[name="_method"]').length) {
            $form.append($('<input>', { type: 'hidden', name: '_method', value: 'PATCH' }));
        }
        $form.find('.cancel-edit-btn').removeClass('hidden');

        $('html, body').animate({ scrollTop: $form.offset().top - 80 }, 200);
    });

    $(document).on('click', '.cancel-edit-btn', function () {
        resetForm($(this).closest('form'));
    });

    $(document).on('click', '.cp-row-delete', function () {
        var $btn = $(this);

        window.Alerts.confirm({
            title: 'Are you sure?',
            text: $btn.data('confirm') || 'Are you sure you want to delete this record?',
            confirmText: 'Yes, delete it!'
        }).then(function (confirmed) {
            if (!confirmed) { return; }

            $.ajax({
                url: $btn.data('delete-url'),
                method: 'POST',
                dataType: 'json',
                data: { _method: 'DELETE', _token: csrfToken },
                success: function (response) {
                    window.Alerts.toast(response.message || 'Deleted successfully.', 'success');
                    var tableId = $btn.data('table-id');
                    if (tableId && isDataTable('#' + tableId)) {
                        $('#' + tableId).DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    var message = 'Something went wrong. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    window.Alerts.error(message);
                }
            });
        });
    });
});