<script>
$(document).ready(function() {
    $('.dataTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ route('admin.subjectcommitteeuser') }}",
        pageLength: 10,
        lengthChange: false,
        order: [],
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'user_name',
                name: 'user_name'
            },
            {
                data: 'subject_committee_name',
                name: 'subject_committee_name'
            },
            {
                data: 'coordinator',
                name: 'coordinator',
                orderable: false,
                searchable: false
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
$(document).on('change', '.switch-coordinator', function() {
    var val = $(this).data('coordinator-value') == 0 ? 1 : 0;
    $(this).attr('data-coordinator-value', val);
    var id = $(this).data('id');
    var subject_committee_id = $(this).data('subject_committee_id');
    $.ajax({
        url: '{{ route("admin.subjectcommitteeuser.status") }}',
        type: 'POST',
        data: {
            'id': id,
            'val': val,
            'subject_committee_id': subject_committee_id,
            'field_name': 'coordinator',
            '_token': '{{ csrf_token() }}'
        },
        success: function(data) {
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
    });
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
                url: '{{ route("admin.subjectcommitteeuser.delete")}}',
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