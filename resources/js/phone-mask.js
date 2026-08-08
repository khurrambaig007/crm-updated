document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-phone-mask]').forEach(function (input) {
        function formatDisplay(value) {
            var digits = value.replace(/\D/g, '');
            if (digits.startsWith('92')) digits = digits.substring(2);
            else if (digits.startsWith('0')) digits = digits.substring(1);
            if (digits.length > 10) digits = digits.substring(0, 10);
            var formatted = '0';
            for (var i = 0; i < digits.length; i++) {
                if (i === 3 || i === 6) formatted += ' ';
                formatted += digits[i];
            }
            return formatted;
        }

        input.addEventListener('input', function (e) {
            var pos = e.target.selectionStart;
            var oldLen = e.target.value.length;
            e.target.value = formatDisplay(e.target.value);
            var diff = e.target.value.length - oldLen;
            e.target.setSelectionRange(pos + diff, pos + diff);
        });

        input.addEventListener('blur', function (e) {
            var digits = e.target.value.replace(/\D/g, '');
            e.target.setCustomValidity(digits.length > 0 && digits.length < 10
                ? 'Please enter a valid 10-digit phone number (e.g., 0321 123 4567)'
                : '');
        });

        if (input.value) input.value = formatDisplay(input.value);
    });
});
