document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('submit', function (e) {
        const form = e.target.closest('.delete-form');
        if (!form) return;
        
        e.preventDefault();
        const message = form.dataset.confirm || 'Are you sure you want to delete this item?';
        
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'rounded-lg px-4 py-2',
                cancelButton: 'rounded-lg px-4 py-2 ml-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
