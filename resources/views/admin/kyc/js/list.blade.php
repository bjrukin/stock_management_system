<script>
$(document).ready(function() {

    $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.kyc') }}",
            beforeSend: function() {
                $('.paginate_button').addClass('disabled-page');
            },
            complete: function() {
                $('.paginate_button').removeClass('disabled-page');
            },
            error: function() {
                $('.paginate_button').removeClass('disabled-page');
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
                data: 'profile_image',
                name: 'profile_image',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name',
            },
            {
                data: 'symbol_number',
                name: 'symbol_number',
                orderable: false,
                searchable: false
            },
            {
                data: 'registration_number',
                name: 'registration_number',
                orderable: false,
                searchable: false
            },
            {
                data: 'dob',
                name: 'dob',
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
                url: '{{ route("admin.kyc.delete")}}',
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