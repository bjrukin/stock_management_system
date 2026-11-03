<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-chained/1.0.1/jquery.chained.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
    $("#program_id").chained("#level_id");
    $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.kyc.certificate') }}",
            type: "GET",
            data: function(d) {
                d.level_id = "{{ $level_id }}";
                d.program_id = "{{ $program_id }}";
                d.decision_date = "{{ $decision_date }}";
                d.is_printed = "{{ $is_printed }}";
            },
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
        order: [
            ['0', 'desc']
        ],
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
                name: 'name'
            },
            {
                data: 'professional_name',
                name: 'professional_name'
            },
            {
                data: 'decision_date',
                name: 'decision_date',
                orderable: false,
                searchable: true
            },
            {
                data: 'citizenship_number',
                name: 'citizenship_number',
                orderable: false,
                searchable: false
            },
            {
                data: 'cert_registration_number',
                name: 'cert_registration_number',
                orderable: false,
                searchable: true
            },
            {
                data: 'srn',
                name: 'srn'
            },
            {
                data: 'level_name',
                name: 'level_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'program_name',
                name: 'program_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
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

    //for modal form
    $('#allocateModal').on('show.bs.modal', function(e) {
        var button = $(e.relatedTarget);
        var id = button.data('id');

        var modal = $(this);
        $.ajax({
            url: "{{ route('admin.kyc.allocate')}}",
            type: 'GET',
            data: {
                id: id
            },
            success: function(data) {
                modal.find('#modal-content').html(data);
                modal.find('.select2-ajax').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            dropdownParent: modal,
                            width: '100%',
                            minimumInputLength: 2,
                            ajax: {
                                url: '{{ route ("admin.kyc.json") }}',
                                dataType: 'json',
                                delay: 250,
                                data: function(term) {
                                    return {
                                        term: term,
                                    };
                                },
                                processResults: function(data) {
                                    return {
                                        results: $.map(data, function(v) {
                                            return {
                                                id: v.professional_id,
                                                text: v.name,
                                                user: v
                                            };
                                        })
                                    };
                                }
                            },
                            templateResult: function(data) {
                                if (!data.id) {
                                    return data.text;
                                }
                                var user = data.user;
                                var imgSrc = user.full_profile_img;
                                var $result = $(
                                    `<div style="display: flex; align-items: center;">
                                    <img src="${imgSrc}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                    <div>
                                        <div><strong>${user.name}</strong> (${user.dob})</div>
                                        <div>User ID: ${user.professional_id}</div>
                                        <div>Registration Number: ${user.registration_number? user.registration_number:''}</div>
                                        <div>${user.symbol_number ? 'Symbol Number: ' + user.symbol_number : 'No Symbol Number'}</div>
                                    </div>
                                </div>`
                                );
                                return $result;
                            },
                            templateSelection: function(data) {
                                var user = data.user || {};
                                if (!user.name) {
                                    return data.text ||
                                        'Select an option';
                                }
                                return `${user.name} (${user.dob || 'Unknown DOB'})`;
                            },
                            escapeMarkup: function(markup) {
                                return markup;
                            }

                        });
                    }
                });
            },
            error: function() {
                modal.find('#modal-content').html(
                    '<div class="alert alert-danger">Failed to load data.</div>'
                );
            }
        });
    });

    $('#allocateModal').on('hidden.bs.modal', function() {
        let modal = $(this);
        modal.find('.select2-ajax').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        modal.find('#modal-content').html('');
    });

    $('#allocate_form').on('submit', function(e) {
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
                location.reload();
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