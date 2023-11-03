let partnersBusinesses;

class PartnersBusinesses{
    constructor() {
        this.dataTables();
        this.events();
    }

    dataTables() {
        $('.dataTable').DataTable({
            // set default table lenght to 25
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Всички"]],
            "pageLength": 50,
            "order": [[ 1, "asc" ]],

            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json',
            },
        });
    }

    events() {
        $('#view-btn').on('click', function () {
            let providerId = $('#provider-id').val();

            window.location.href = `/partners/businesses/?provider_id=${providerId}`;
        });


        $('body').on('click', 'button[data-action="change-status"]', function() {
            let isActiveNow = $(this).data('status') === 'deactivate';
            let businessId = $(this).data('id');

            if(isActiveNow){
                $(`tr[data-id="${businessId}"]`).addClass('deactivated');
                $(this).data('status', 'activate').addClass('btn-info').removeClass('btn-secondary').text('Активиране');
            } else {
                $(`tr[data-id="${businessId}"]`).removeClass('deactivated');
                $(this).data('status', 'deactivate').addClass('btn-secondary').removeClass('btn-info').text('Деактивиране');
            }

            //send ajax request to change status
            $.ajax({
                url: '/businesses/change-status',
                method: 'POST',
                data: {
                    business_id: businessId,
                    active_status: isActiveNow ? 0 : 1,
                },
                success: function (response) {

                },
                error: function () {
                    alert('Възникна грешка при изпълнението на заявката');
                }
            });
        });
    }
}

$(document).ready(function () {
    partnersBusinesses = new PartnersBusinesses();
});