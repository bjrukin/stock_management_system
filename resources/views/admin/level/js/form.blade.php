<script src="https://unpkg.com/nepalify@0.5.0/umd/nepalify.production.min.js"></script>
<script>
$(document).ready(function() {
    $('#form').on('submit', function(e) {
        e.preventDefault();
        saveData(this);
    });
    $('.nepali-input').each(function() {
        nepalify.interceptElement(this);
    });
});

function saveData(form) {
    $('.btn-loading').prop('disabled', true)
    $('.btn-loading').html(
        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...'
    );
    $.ajax({
        type: 'post',
        url: $(form).attr('action'),
        data: $(form).serialize(),
        success: function(data) {
            Swal.fire({
                icon: "success",
                title: data.message,
                showConfirmButton: false,
                timerProgressBar: true,
                allowOutsideClick: false,
                timer: timer,
                didOpen: () => {
                    document.querySelector('.swal2-timer-progress-bar')
                        .style.backgroundColor = 'green';
                }
            });
            setTimeout(function() {
                window.location.href = "{{route('admin.level')}}";
            }, timer);
        },
        error: function(xhr) {
            $('.btn-loading').prop('disabled', false);
            $('.btn-loading').html('Submit');
            var res = $.parseJSON(xhr.responseText);
            if (res.error || res.message) {
                Swal.fire({
                    title: res.error ?? res.message,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    timer: timer,
                    customClass: {
                        title: 'swal2-left-title'
                    },
                    didOpen: () => {
                        document.querySelector('.swal2-timer-progress-bar')
                            .style.backgroundColor = 'red';
                    }
                });
            }
        }
    });
}
</script>