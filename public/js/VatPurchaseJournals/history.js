let vatPurchaseJournalsHistory;

class VatPurchaseJournalsHistory {
    constructor() {
        this.dataTables();
        this.events();
    }

    dataTables() {
        $('.dataTable').DataTable({
            // set default table lenght to 25
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Всички"]],
            "pageLength": 50,
            "order": [[ 0, "desc" ]],

            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json',
            },
        });
    }

    events() {
        $('body').on('click', 'a.delete-vpj', function() {
            let id = $(this).data('id');
            if(confirm('Наистина ли искате да изтриете този запис?')) {
                 window.location.href = '/vat-purchase-journals/delete/' + id;
            }
            // do something
        });
    }
}

$(document).ready(function () {
    vatPurchaseJournalsHistory = new VatPurchaseJournalsHistory();
});
