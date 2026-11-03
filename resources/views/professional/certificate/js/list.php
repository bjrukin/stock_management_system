<script>
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Add confirmation for edit links
        $('a.confirm-edit').on('click', function (e) {
            e.preventDefault(); // Prevent immediate navigation
            const requestId = $(this).data('request-id');
            const editUrl = $(this).attr('href');

            Swal.fire({
                title: 'Edit Request',
                html: `
            <div class="text-start">
                <p>You are about to edit this pending certificate request.</p>
                <p><strong>Request ID:</strong> ${requestId}</p>
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> You can only edit requests that are pending approval.</p>
            </div>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continue to Edit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = editUrl;
                }
            });
        });

        // Status Information
        $('.status-info').click(function () {
            const status = $(this).data('status');

            let statusDetails = {
                'pending': {
                    title: 'Pending Status',
                    message: 'Your request is waiting for review by the administration team. You can still edit the request details during this stage.',
                    icon: 'info',
                    color: '#ffc107'
                },
                'approved': {
                    title: 'Approved Status',
                    message: 'Your request has been approved. A certificate has been created based on your request details.',
                    icon: 'success',
                    color: '#28a745'
                },
                'rejected': {
                    title: 'Rejected Status',
                    message: 'Your request has been rejected. Please contact the administration for more information or submit a new request.',
                    icon: 'error',
                    color: '#dc3545'
                }
            };

            const detail = statusDetails[status] || {
                title: 'Unknown Status',
                message: 'Status information not available.',
                icon: 'question',
                color: '#6c757d'
            };

            Swal.fire({
                title: detail.title,
                text: detail.message,
                icon: detail.icon,
                confirmButtonText: 'Understand',
                confirmButtonColor: detail.color,
            });
        });

        // Add hover effects for better UX
        $('.btn').hover(
            function () {
                $(this).css('transform', 'scale(1.05)');
                $(this).css('transition', 'transform 0.2s ease');
            },
            function () {
                $(this).css('transform', 'scale(1)');
            }
        );

        // Renew Certificate with Confirmation
        $('.renew-certificate').click(function () {
            const certificateId = $(this).data('certificate-id');
            const certificateNumber = $(this).data('certificate-number');
            const isExpired = $(this).data('is-expired');

            const title = isExpired ? 'Renew Expired Certificate' : 'Renew Certificate';
            const message = isExpired
                ? `Your certificate ${certificateNumber} has expired. Do you want to renew it?`
                : `Are you sure you want to renew certificate ${certificateNumber}?`;

            Swal.fire({
                title: title,
                text: message,
                icon: isExpired ? 'warning' : 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, Renew',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("professional.certificate.renew.search") }}';
                }
            });
        });
    });
</script>