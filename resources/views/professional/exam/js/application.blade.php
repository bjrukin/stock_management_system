<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-chained/1.0.1/jquery.chained.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
    $("#program").chained("#level");
    $('#form').on('submit', function(e) {
        e.preventDefault();
        saveData(this);
    });
});

function checkPaymentBy(payment_by) {
    if(payment_by == "Bank") {
        $('#voucherSection').removeClass('d-none');
    } else {
        $('#voucherSection').addClass('d-none');
    }
}

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
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.href = "{{ route('professional.exam.status') }}";
                }
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

function imageDelete(field_name) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure to delete this?',
        text: "",
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            $("#btn_" + field_name + "_delete").addClass('d-none');
            $("#display_" + field_name).addClass('d-none');
            $(".dropify-message-" + field_name).removeClass('d-none').addClass('d-block');
            $("#display_" + field_name).attr("src", '');
            $("#" + field_name).val('');
        } else {
            Swal.fire({
                icon: "error",
                title: 'Cancelled',
                showConfirmButton: false,
                timerProgressBar: true,
                allowOutsideClick: false,
                timer: timer,
                didOpen: () => {
                    document.querySelector('.swal2-timer-progress-bar').style.backgroundColor =
                        'red';
                }
            });
        }
    })
}
</script>