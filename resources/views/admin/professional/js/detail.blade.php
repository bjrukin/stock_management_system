<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" rel="stylesheet"
    type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
    $('#frm_forward').on('submit', function(e) {
        e.preventDefault();
        saveStatus(this);
    });
    $('#frm_status').on('submit', function(e) {
        e.preventDefault();
        saveForward(this);
    });
});

function saveStatus(form) {
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
                window.location.href =
                    "{{ route('admin.professional.detail',  ['id' =>$professional->id]) }}";
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

function saveForward(form) {
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
                window.location.href =
                    "{{ route('admin.professional.detail',  ['id' =>$professional->id]) }}";
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


function deleteApplicant(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure to delete this?',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.applicant.delete")}}',
                type: 'POST',
                data: {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $("#tr" + id).remove();
                    Swal.fire({
                        icon: "success",
                        title: data.message,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        timer: timer,
                        didOpen: () => {
                            document.querySelector(
                                    '.swal2-timer-progress-bar')
                                .style.backgroundColor = 'green';
                        }
                    });
                    setTimeout(function() {
                        location.reload();
                    }, timer);
                },
                error: function(xhr) {
                    var res = $.parseJSON(xhr.responseText);
                    if (res.error) {
                        Swal.fire({
                            title: res.error,
                            showConfirmButton: false,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            timer: timer,
                            customClass: {
                                title: 'swal2-left-title'
                            },
                            didOpen: () => {
                                document.querySelector(
                                        '.swal2-timer-progress-bar')
                                    .style.backgroundColor = 'red';
                            }
                        })
                    }
                }
            })
        }
    })
}
</script>