/**
 * Bank Account form: repeatable "Additional Fields" rows.
 *
 * Lives in the Vite bundle (not an inline classic script) because window.$ is only
 * set once jquery-global runs inside the deferred module — inline blade scripts
 * would throw "ReferenceError: $ is not defined" (see .ai/rules/js.md).
 *
 * The label/input Tailwind class strings come from data-* attributes on the
 * #bank-account-form element, set by bank-accounts/_form.blade.php.
 */
jQuery(document).ready(function ($) {
    var $form = $('#bank-account-form');
    if (!$form.length) {
        return;
    }

    var inputClasses = $form.data('input-classes') || '';
    var labelClasses = $form.data('label-classes') || '';

    /**
     * Rewrite the numeric index of every repeatable input so the POST body
     * stays a dense, zero-based array after rows are removed.
     */
    function reindexRows(container, field) {
        $(container).find('.dyn-row').each(function (index) {
            $(this).find('[name^="' + field + '["]').each(function () {
                this.name = this.name.replace(/\[\d+\]/, '[' + index + ']');
            });

            $(this).find('input').each(function () {
                var id = $(this).attr('id');
                if (id) {
                    $(this).attr('id', id.replace(/\d+/, index));
                }
            });
        });
    }

    function trashButton() {
        return '<button type="button" class="remove-row inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">'
            + '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>'
            + '</button>';
    }

    function label(text, forId) {
        return $('<label>').addClass(labelClasses).attr('for', forId).text(text);
    }

    $('#add-field').on('click', function () {
        var container = document.getElementById('custom-fields-container');
        var index = $(container).find('.dyn-row').length;

        var $row = $('<div>').addClass('dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4');
        var $key = $('<div>').append(label('Label', 'custom_field_key_' + index));
        $key.append(
            $('<input>').attr({ type: 'text', name: 'custom_fields[' + index + '][key]', id: 'custom_field_key_' + index })
                .addClass(inputClasses).attr('placeholder', 'Label')
        );
        var $value = $('<div>').append(label('Value', 'custom_field_value_' + index));
        $value.append(
            $('<input>').attr({ type: 'text', name: 'custom_fields[' + index + '][value]', id: 'custom_field_value_' + index })
                .addClass(inputClasses).attr('placeholder', 'Value')
        );
        var $remove = $('<div>').addClass('flex items-end').html(trashButton());

        $row.append($key, $value, $remove, $('<div>'));
        $(container).append($row);
    });

    $form.on('click', '.remove-row', function () {
        var $row = $(this).closest('.dyn-row');
        $row.remove();

        reindexRows(document.getElementById('custom-fields-container'), 'custom_fields');
    });
});