<script>
$(document).ready(function() {
    $('.date').datepicker();
    $("#dob_nep").nepaliDatePicker({
        ndpYear: true,
        ndpMonth: true,
    });
    $("#dob_nep").on("blur", function() {
        let bsDate = $("#dob_nep").val(); // e.g. 2065-02-15
        if (bsDate) {
            let adDate = NepaliFunctions.BS2AD(bsDate, "YYYY-MM-DD");
            $("#dob_eng").val(adDate); // set converted AD date
        }
    });
    $("#dob_eng").on("change", function() {
        let adDate = $("#dob_eng").val(); // e.g. 2065-02-15
        if (adDate) {
            let bsDate = NepaliFunctions.AD2BS(adDate, "YYYY-MM-DD");
            $("#dob_nep").val(bsDate); // set converted BS date
        }
    });
    $('#form').on('submit', function(e) {
        e.preventDefault();
        saveData(this);
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
            $.ajax({
                url: '{{ route("admin.professional.imageQualificationDelete")}}', 
                type: 'POST',
                data: {
                    'id': '{{ ($professional_qualification) ? $professional_qualification->id : 0 }}',
                    'field_name': field_name,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $("#btn_" + field_name + "_delete").addClass('d-none');
                    $("#display_" + field_name).addClass('d-none');
                    $(".dropify-message-" + field_name).removeClass('d-none').addClass('d-block');
                    $("#display_" + field_name).attr("src", '');
                    $("#" + field_name).val('');
                }
            });
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