<script>
    
$(document).ready(function() {
    $('.select2').select2();
    $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.applicant.pending') }}",
            type: "GET",
            data: function(d) {
                d.exam_id = "{{ $exam_id }}";
                d.level_id = "{{ $level_id }}";
                d.status = "{{ $status }}";
                d.program_id = "{{ $program_id }}";
                d.exam_id = "{{ $exam_id }}";
            }
        },
        pageLength: 10,
        lengthChange: false,
        order: [['0', 'desc']],
        columns: [
            {
                data: 'id',
                name: 'id',
                visible: false
            },
           {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'professional_name',
                name: 'professional_name'
            },
            {
                data: 'citizenship_number',
                name: 'citizenship_number',
                orderable: false,
                searchable: false
            },
            {
                data: 'dob_nep',
                name: 'dob_nep',
                orderable: false,
                searchable: false
            },
            {
                data: 'email',
                name: 'email',
                orderable: false,
                searchable: false
            },
            {
                data: 'applied_date',
                name: 'applied_date',
            },
            {
                data: 'exam_name',
                name: 'exam_name',
                orderable: false,
            },
            {
                data: 'level_name',
                name: 'level_name',
                orderable: false,
            },
            {
                data: 'status',
                name: 'status',
                orderable: false,
            },
            {
                data: 'state',
                name: 'state',
                orderable: false,
            },
            {
                data: 'action',
                name: 'action',
                className: 'text-center',
                orderable: false,
                searchable: false
            }
        ]
    });
    $('#dt-search-0').removeClass('form-control-sm');
});

function deleteData(id) {
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