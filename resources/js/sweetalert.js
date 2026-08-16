const baseCustomClass = {
    popup: 'rounded-lg !w-auto !min-w-0 !p-4',
    confirmButton: 'rounded-lg px-3 py-1.5 text-sm',
    cancelButton: 'rounded-lg px-3 py-1.5 text-sm ml-2',
};

window.Alerts = {
    /**
     * Success toast/alert.
     */
    success(message, title = 'Success') {
        Swal.fire({
            title,
            text: message,
            icon: 'success',
            width: '24em',
            confirmButtonColor: '#059669',
            confirmButtonText: 'OK',
            customClass: baseCustomClass,
        });
    },

    /**
     * Error alert.
     */
    error(message, title = 'Error') {
        Swal.fire({
            title,
            text: message,
            icon: 'error',
            width: '24em',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'OK',
            customClass: baseCustomClass,
        });
    },

    /**
     * Validation error alert listing all messages.
     */
    validation(messages, title = 'Please fix the following') {
        const list = Array.isArray(messages) ? messages : [messages];
        const html = list.map((m) => `<li class="text-left">${m}</li>`).join('');

        Swal.fire({
            title,
            icon: 'warning',
            width: '24em',
            html: `<ul class="space-y-1">${html}</ul>`,
            confirmButtonColor: '#d97706',
            confirmButtonText: 'OK',
            customClass: baseCustomClass,
        });
    },

    /**
     * Confirmation dialog. Resolves true/false.
     */
    confirm({
        title = 'Are you sure?',
        text = 'This action cannot be undone.',
        icon = 'warning',
        confirmText = 'Yes, continue',
        cancelText = 'Cancel',
        confirmColor = '#dc2626',
    } = {}) {
        return Swal.fire({
            title,
            text,
            icon,
            width: '24em',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true,
            customClass: baseCustomClass,
        }).then((result) => result.isConfirmed);
    },

    /**
     * Toast notification (auto-dismissing).
     */
    toast(message, type = 'success') {
        const icons = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icons[type] || 'info',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-lg',
            },
        });
    },
};

document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('submit', function (e) {
        const form = e.target.closest('.delete-form');
        if (!form) return;

        e.preventDefault();
        const message = form.dataset.confirm || 'Are you sure you want to delete this item?';

        window.Alerts.confirm({
            title: 'Are you sure?',
            text: message,
            confirmText: 'Yes, delete it!',
        }).then((confirmed) => {
            if (confirmed) {
                form.submit();
            }
        });
    });
});
