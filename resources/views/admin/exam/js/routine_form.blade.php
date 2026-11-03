<script>
    // $(document).ready(function() {
    // });
    // function saveData(form) {
    //     $('.btn-loading').prop('disabled', true)
    //     $('.btn-loading').html(
    //         '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...'
    //     );
    //     $.ajax({
    //         type: 'post',
    //         url: $(form).attr('action'),
    //         data: $(form).serialize(),
    //         success: function(data) {
    //             // Swal.fire({
    //             //     icon: "success",
    //             //     title: data.message,
    //             //     showConfirmButton: false,
    //             //     timerProgressBar: true,
    //             //     allowOutsideClick: false,
    //             //     timer: timer,
    //             //     didOpen: () => {
    //             //         document.querySelector('.swal2-timer-progress-bar')
    //             //             .style.backgroundColor = 'green';
    //             //     }
    //             // });
    //             var routine_html = '<div class="card-body"><h4 class="header-title mt-0 mb-3">Exam Routine</h4>';
    //             $.each(data, function(day){
    //                 routine_html += `<div class="mb-3"><h5>Day ${day}</h5>`;
    //                 $.each(data[day], function(shift){
    //                     routine_html += ''
    //                 })
    //             })
    //             routine_html += '</div>';
    //             $('#routine').html(routine_html);
    //             $('.btn-loading').prop('disabled', false)
    //             $('.btn-loading').html(
    //                 'Submit'
    //             );
    //         },
    //         error: function(xhr) {
    //             $('.btn-loading').prop('disabled', false);
    //             $('.btn-loading').html('Submit');
    //             var res = $.parseJSON(xhr.responseText);
    //             if (res.error || res.message) {
    //                 Swal.fire({
    //                     title: res.error ?? res.message,
    //                     showConfirmButton: false,
    //                     timerProgressBar: true,
    //                     allowOutsideClick: false,
    //                     timer: timer,
    //                     customClass: {
    //                         title: 'swal2-left-title'
    //                     },
    //                     didOpen: () => {
    //                         document.querySelector('.swal2-timer-progress-bar')
    //                             .style.backgroundColor = 'red';
    //                     }
    //                 });
    //             }
    //         }
    //     });
    // }
</script>