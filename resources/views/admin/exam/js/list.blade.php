<script>
$(document).ready(function() {
    $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.exam') }}",
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
                data: 'opening_date',
                name: 'opening_date'
            },
            {
                data: 'closing_date',
                name: 'closing_date'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'admit_card_generated',
                name: 'admit_card_generated',
                orderable: false,
                searchable: false
            },
            {
                data: 'result_published',
                name: 'result_published',
                orderable: false,
                searchable: false
            },
            {
                data: 'status',
                name: 'status',
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
$(document).on('change', '.switch-admit_card_generated', function() {
    var val = $(this).data('admit_card_generated-value') == 0 ? 1 : 0;
    $(this).attr('data-admit_card_generated-value', val);
    var id = $(this).data('id');
    $.ajax({
        url: '{{ route("admin.exam.status") }}',
        type: 'POST',
        data: {
            'id': id,
            'val': val,
            'field_name': 'admit_card_generated',
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
$(document).on('change', '.switch-result_published', function() {
    var val = $(this).data('result_published-value') == 0 ? 1 : 0;
    $(this).attr('data-result_published-value', val);
    var id = $(this).data('id');
    $.ajax({
        url: '{{ route("admin.exam.status") }}',
        type: 'POST',
        data: {
            'id': id,
            'val': val,
            'field_name': 'result_published',
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
$(document).on('change', '.switch-status', function() {
    var val = $(this).data('status-value') == 0 ? 1 : 0;
    $(this).attr('data-status-value', val);
    var id = $(this).data('id');
    $.ajax({
        url: '{{ route("admin.exam.status") }}',
        type: 'POST',
        data: {
            'id': id,
            'val': val,
            'field_name': 'status',
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
                url: '{{ route("admin.exam.delete")}}',
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