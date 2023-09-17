let vatPurchaseJournalsExportView;

class VatPurchaseJournalsExportView {
    constructor() {
        this.dataTables();
    }

    dataTables() {
        $('.dataTable').DataTable({
            // set default table lenght to 25
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Всички"]],
            "pageLength": -1,

            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json',
            },
        });
    }

}

$(document).ready(function () {
    vatPurchaseJournalsExportView = new VatPurchaseJournalsExportView();
});
