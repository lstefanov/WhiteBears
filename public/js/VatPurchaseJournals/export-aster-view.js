let vatPurchaseJournalsExportView;

class VatPurchaseJournalsExportView {
    constructor() {
        this.dataTables();
        this.datePicker();
        this.events();
    }

    datePicker() {
        $("#export-date-filter").datepicker({
            language: "bg",
            format: "yyyy-mm",
            startView: "months",
            minViewMode: "months",
        }).on('changeDate', function (ev) {
        });

    }

    dataTables() {
        $('.dataTable').DataTable({
            // set default table lenght to 25
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Всички"]],
            "pageLength": -1,
            "aaSorting": [],

            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json',
            },
        });
    }


    events() {
        $('#view-btn').on('click', function () {
            let businessId = $('#business-id').val();
            let date = $('#export-date-filter').val();

            if(!date) {
                alert('Моля, въведете дата!');
                return;
            }

            window.location.href = `/vat-purchase-journals/export-aster/view/?date=${date}`;
        });

        $('#export-date-filter-btn').on('click', function () {
            let businessId = $('#business-id').val();
            let date = $('#export-date-filter').val();

            if(!date) {
                alert('Моля, въведете дата!');
                return;
            }

            let url = `/vat-purchase-journals/export-aster/export/?date=${date}`;
            window.open(url, '_blank');
        });
    }
}

$(document).ready(function () {
    vatPurchaseJournalsExportView = new VatPurchaseJournalsExportView();
});
