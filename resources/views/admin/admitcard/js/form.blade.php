<script src="https://cdn.tiny.cloud/1/9wxitp8hjgt3gqxmixgfalplwr5vh5bu9od4v0uigyeze17l/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script src="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/js/nepali.datepicker.v5.0.6.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#form').on('submit', function(e) {
            e.preventDefault();
            saveData(this);
        });

        /* Select your element */
        var examDateInput = document.getElementById("exam_date");

        /* Initialize Datepicker with options */
        examDateInput.NepaliDatePicker();

        tinymce.init({
            selector: 'textarea',
            height: 500,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount',
                /* Premium plugins for demo purposes only */
                'mediaembed',
            ],
            toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
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
                        "{{ route('admin.exam.detail',  ['exam_id' =>  $id]) }}";
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