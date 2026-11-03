<script>
    function calculatePCLAveragePCB() {
        const physics = parseFloat(document.querySelector('#pclPhysics').value) || 0;
        const chemistry = parseFloat(document.querySelector('#pclChemistry').value) || 0;
        const biology = parseFloat(document.querySelector('#pclBiology').value) || 0;

        const average = ((physics + chemistry + biology) / 3).toFixed(2);
        document.querySelector('#pclAveragePcb').value = average;
    }

    document.addEventListener('DOMContentLoaded', function () {
        ['pclPhysics', 'pclChemistry', 'pclBiology'].forEach(field => {
            document.querySelector(`#${field}`).addEventListener('input', calculatePCLAveragePCB);
        });
        calculatePCLAveragePCB();
    });


    $(document).ready(function () {
        $('.select2').select2();

        function handleCollegeType() {
            let college_type = $('#college_type').val();
            if (college_type == 'nepal') {
                $('#college_name').removeClass('d-none');
                $('#international_college_name').addClass('d-none');
            } else {
                $('#college_name').addClass('d-none');
                $('#international_college_name').removeClass('d-none');
            }
        }

        function handleResultType() {
            let resultType = $('#result_type').val();
            if (resultType === 'gpa') {
                $('#gpa_div').show();
                $('#percentage_div').hide();
            } else if (resultType === 'percentage') {
                $('#gpa_div').hide();
                $('#percentage_div').show();
            } else {
                $('#gpa_div').hide();
                $('#percentage_div').hide();
            }
        }

        $('#college_type').change(handleCollegeType);
        $('#result_type').change(handleResultType);

        handleCollegeType();
        handleResultType();

        $('#form').on('submit', function (e) {
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
            success: function (data) {
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
                setTimeout(function () {
                    window.location.href = "{{ route('professional.profile.bachelor') }}";
                }, timer);
            },
            error: function (xhr) {
                $('.btn-loading').prop('disabled', false);
                $('.btn-loading').html('Next');
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
                    url: '{{ route("professional.profile.qualification.imageDelete")}}',
                    type: 'POST',
                    data: {
                        'id': '{{ ($professional_qualification) ? $professional_qualification->id : 0 }}',
                        'field_name': field_name,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (data) {
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