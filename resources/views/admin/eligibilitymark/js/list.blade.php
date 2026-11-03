<script>
$(document).ready(function() {
    $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.eligibilitymark') }}",
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
                data: 'level_name',
                name: 'level_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'min_percentage',
                name: 'min_percentage',
                orderable: false,
                searchable: false
            },
            {
                data: 'min_gpa',
                name: 'min_gpa',
                orderable: false,
                searchable: false
            },
            {
                data: 'min_physics_marks',
                name: 'min_physics_marks',
                orderable: false,
                searchable: false
            },
            {
                data: 'min_chemistry_marks',
                name: 'min_chemistry_marks',
                orderable: false,
                searchable: false
            },
            {
                data: 'min_bio_marks',
                name: 'min_bio_marks',
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
                url: '{{ route("admin.eligibilitymark.delete")}}',
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