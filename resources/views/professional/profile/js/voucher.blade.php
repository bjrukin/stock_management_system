<script>
    $(document).ready(function () {
        var dropAreaImage = document.querySelector('.drag-area-voucher_image');
        var voucher_image = dropAreaImage.querySelector('#drag-voucher_image')

        dropAreaImage.onclick = () => {
            voucher_image.click();
        };
        voucher_image.addEventListener('change', function () {
            var fileExtension = ['jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG'];
            var maxSizeKB = 600; // Maximum file size in kilobytes
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                toastr['error']("Allowed file formats : " + fileExtension.join(', '));
                $(this).val('');
            } else {
                file = this.files[0];
                var fileSizeKB = file.size / 1024; // File size in kilobytes
                if (fileSizeKB > maxSizeKB) {
                    Swal.fire({
                        icon: "warning",
                        title: "Maximum file size allowed is " + maxSizeKB + "KB",
                        timer: 4000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    $(this).val('');
                    return;
                } else {
                    var reader = new FileReader();
                    reader.onloadend = function () {
                        $("#btn_voucher_image_delete").removeClass('d-none');
                        $("#display_voucher_image").removeClass('d-none');
                        $("#voucher_image").val(reader.result);
                        $("#display_voucher_image").attr("src", reader.result);
                        $(".dropify-message-voucher_image").removeClass('d-block').addClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            }
        });
        $("#form").submit(function (e) {
            e.preventDefault();
            $('.btn-loading').prop('disabled', true)
            $('.btn-loading').html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...'
            );
            $.ajax({
                type: 'post',
                url: $('#form').attr('action'),
                data: $("#form").serialize(),
                success: function (data) {
                    setTimeout(function () {
                        window.location.href = "{{ route('professional.exam.status') }}";
                    }, 1000);
                },
                error: function (xhr) {
                    $('.btn-loading').prop('disabled', false);
                    $('.btn-loading').html('Next');
                    var res = $.parseJSON(xhr.responseText);
                    if (res.error) {
                        toastr['error'](res.error);
                    }
                }
            });
        });
    });

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
                    url: '{{ route('professional.profile.voucher.imageDelete') }}',
                    type: 'POST',
                    data: {
                        'id': '{{ $applicant ? $applicant->id : 0 }}',
                        'field_name': field_name,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        $("#btn_" + field_name + "_delete").addClass('d-none');
                        $("#display_" + field_name).addClass('d-none');
                        $(".dropify-message-" + field_name).removeClass('d-none')
                            .addClass('d-block');
                        $("#display_" + field_name).attr("src", '');
                        $("#" + field_name).val('');
                        toastr["success"](data.message);
                    }
                });
            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                toastr["error"]('Cancelled.');
            }
        })
    }
</script>