<style>
    .drag-container .img-delete-btn {
        background: #d3232a !important;
        color: #ffffff !important;
        border-radius: 4px !important;
        width: 24px !important;
        height: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        top: 5px !important;
        right: 5px !important;
    }

    .drag-container .img-delete-btn i {
        font-size: 14px !important;
        font-weight: bold !important;
    }

    .drag-container .img-delete-btn:hover {
        background: #b11d23 !important;
    }

    .drag-area-saved {
        cursor: default !important;
        border: 1px solid #ddd !important;
        background: #f9f9f9;
        border-style: solid !important;
    }
</style>
<script src="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/js/nepali.datepicker.v5.0.6.min.js"
    type="text/javascript"></script>
<link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.6.min.css"
    rel="stylesheet" type="text/css" />

<script>
    $(document).ready(function () {

        $('#dob').nepaliDatePicker({
            ndpYear: true,
            ndpMonth: true,
            readOnlyInput: true,
        });

        $('.drag-area').on('click', function () {
            if ($(this).hasClass('drag-area-saved')) return;
            $(this).siblings('.drag-image').click();
        });

        $('.drag-image').on('change', function (e) {
            const file = e.target.files[0];
            const id = $(this).attr('id').replace('drag-', '');
            if (file) {
                if (file.size > 600 * 1024) {
                    toastr.error('File size should be less than 600KB');
                    $(this).val('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    const container = $(`.drag-area-${id}`).closest('.drag-container');
                    container.find('.file-input').val(e.target.result);
                    container.find('.preview-image').removeClass('d-none').attr("src", e.target.result);
                    container.find('.dropify-message').addClass('d-none');
                    container.find('.img-delete-btn').removeClass('d-none').addClass('d-block');
                };
                reader.readAsDataURL(file);
            }
        });

        $('#form').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('.btn-loading');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                type: 'post',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function (data) {
                    Swal.fire({
                        icon: "success",
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    setTimeout(() => window.location.href = "{{ route('professional.dashboard') }}", 2000);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('Submit');
                    const res = xhr.responseJSON;
                    Swal.fire({
                        icon: "error",
                        title: res.error || res.message || 'Something went wrong',
                    });
                }
            });
        });
    });


    function imageDelete(field_name) {
        const container = $(`.drag-area-${field_name}`).closest('.drag-container');
        container.find('.file-input').val('');
        container.find('.preview-image').addClass('d-none').attr("src", "");
        container.find('.dropify-message').removeClass('d-none');
        container.find('.img-delete-btn').addClass('d-none');
        $(`#drag-${field_name}`).val('');
    }
</script>