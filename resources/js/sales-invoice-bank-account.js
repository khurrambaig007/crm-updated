import $ from './jquery-global';

/**
 * Bank-account quick-create modal on the sales invoice form.
 *
 * The "+" button opens this compact modal; a successful POST appends the new
 * account to the #bank_account_id dropdown and selects it without touching the
 * rest of the invoice form. Validation errors come back as a JSON 422 and are
 * shown via the shared SweetAlert wrapper.
 */
jQuery(document).ready(function ($) {
    var $modal = $('#bank-account-quick-create-modal');
    if (!$modal.length) {
        return;
    }

    var $select = $('#bank_account_id');

    function openModal() {
        $modal.removeClass('hidden');
        $modal.find('select, input[type="text"]').first().trigger('focus');
    }

    function closeModal() {
        $modal.addClass('hidden');
    }

    $(document).on('click', '[data-modal-target="bank-account-quick-create-modal"]', openModal);

    $(document).on('click', '#bank-account-quick-create-modal [data-modal-close]', closeModal);

    $(document).on('click', '#bank-account-quick-create-modal', function (e) {
        if ($(e.target).is($modal)) {
            closeModal();
        }
    });

    $(document).on('keyup', function (e) {
        if (e.key === 'Escape' && $modal.is(':visible')) {
            closeModal();
        }
    });

    $(document).on('submit', '#bank-account-quick-create-form', function (e) {
        e.preventDefault();

        var $button = $(this).find('button[type="submit"]');
        $button.prop('disabled', true);

        $.ajax({
            url: $(this).data('submit-url') || this.action,
            method: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: function (response) {
                if (!response.id) {
                    window.Alerts.error('Bank account could not be created. Please try again.');
                    return;
                }

                var $option = $select.find('option[value="' + response.id + '"]');
                if ($option.length) {
                    $option.prop('selected', true);
                } else {
                    $select.append(new Option(response.label, response.id, true, true));
                }

                closeModal();
                e.target.reset();
                window.Alerts.toast('Bank account created.', 'success');
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    window.Alerts.validation(Object.values(xhr.responseJSON.errors).flat());
                    return;
                }
                window.Alerts.error((xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.');
            },
            complete: function () {
                $button.prop('disabled', false);
            },
        });
    });
});