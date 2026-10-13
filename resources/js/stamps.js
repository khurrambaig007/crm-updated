/**
 * Stamps Info — stamp image upload field.
 *
 * Wraps the native file input in a drop zone: drag and drop or click to browse,
 * then a preview with the file name, a human readable size and a way to clear
 * it. The input itself stays a real <input type="file"> inside the label, so it
 * remains keyboard reachable and still submits with the form.
 *
 * The size and type limits are checked here as well as on the server purely to
 * give immediate feedback — the Form Request remains the authority.
 */

jQuery(document).ready(function ($) {
    var ACCEPTED = ['image/jpeg', 'image/png', 'image/webp'];
    var MAX_BYTES = 2 * 1024 * 1024;

    function humanSize(bytes) {
        if (bytes < 1024) {
            return bytes + ' B';
        }

        if (bytes < 1024 * 1024) {
            return Math.round(bytes / 1024) + ' KB';
        }

        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function setFeedback($field, message, tone) {
        var $feedback = $field.find('[data-upload-feedback]');

        $feedback
            .removeClass('text-emerald-600 text-red-600')
            .addClass(tone === 'error' ? 'text-red-600' : 'text-emerald-600')
            .text(message);
    }

    $.each($('[data-stamp-upload]'), function () {
        var $field = $(this);
        var $input = $field.find('[data-upload-input]');
        var $idle = $field.find('[data-upload-idle]');
        var $preview = $field.find('[data-upload-preview]');
        var $previewImage = $field.find('[data-upload-preview-image]');
        var $previewName = $field.find('[data-upload-preview-name]');
        var $previewSize = $field.find('[data-upload-preview-size]');
        var $removeExisting = $field.find('[name="remove_image"]');

        function renderFile(file) {
            if (!file) {
                return;
            }

            if (ACCEPTED.indexOf(file.type) === -1) {
                setFeedback($field, file.type ? 'That file type is not supported. Use JPG, PNG or WEBP.' : 'That file is not an image. Use JPG, PNG or WEBP.', 'error');
                $input.val('');
                return;
            }

            if (file.size > MAX_BYTES) {
                setFeedback($field, 'That image is ' + humanSize(file.size) + '. The limit is 2 MB.', 'error');
                $input.val('');
                return;
            }

            var reader = new FileReader();

            reader.onload = function (event) {
                $previewImage.attr('src', event.target.result);
                $previewName.text(file.name);
                $previewSize.text(humanSize(file.size));
                $field.attr('data-has-file', 'true');
                setFeedback($field, 'Ready to save.', 'success');

                // A fresh upload wins over the remove flag, so clear it rather
                // than leaving the form in a contradictory state.
                if ($removeExisting.length) {
                    $removeExisting.prop('checked', false);
                    $field.attr('data-removed', 'false');
                }
            };

            reader.readAsDataURL(file);
        }

        $input.on('change', function () {
            renderFile(this.files && this.files[0]);
        });

        $field.on('dragenter dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $field.attr('data-dragging', 'true');
        });

        $field.on('dragleave dragend', function () {
            $field.removeAttr('data-dragging');
        });

        $field.on('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $field.removeAttr('data-dragging');

            var file = event.originalEvent.dataTransfer && event.originalEvent.dataTransfer.files[0];

            if (!file) {
                return;
            }

            // The input is read-only from script, so the transfer is rebuilt.
            var transfer = new DataTransfer();
            transfer.items.add(file);
            $input[0].files = transfer.files;
            renderFile(file);
        });

        $field.on('click', '[data-upload-clear]', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $input.val('');
            $field.attr('data-has-file', 'false');
            $previewImage.removeAttr('src');
            $previewName.text('');
            $previewSize.text('');
            setFeedback($field, '', 'success');
        });

        $removeExisting.on('change', function () {
            $field.attr('data-removed', this.checked ? 'true' : 'false');
        });
    });
});