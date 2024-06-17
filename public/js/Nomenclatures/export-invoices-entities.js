let exportInvoicesEntities = null;

class ExportInvoicesEntities {
    constructor() {
        this.datePicker();
        this.events();
    }

    datePicker() {
        $("#date-from").datepicker({
            language: "bg",
            format: "yyyy-mm-dd",
            //startView: "months",
            //minViewMode: "months",
        }).on('changeDate', function (ev) {
        });

        $("#date-to").datepicker({
            language: "bg",
            format: "yyyy-mm-dd",
            //startView: "months",
            //minViewMode: "months",
        }).on('changeDate', function (ev) {
        });

    }


    events() {
        $('#view-btn').on('click', function () {
            let dateFrom = $('#date-from').val();
            let dateTo = $('#date-to').val();


            let urlParams = `?date_from=${dateFrom}&date_to=${dateTo}&preview=1`;

            window.location.href = `/nomenclatures/export-invoices-entities/${urlParams}`;
        });


        $('#export-date-filter-btn').on('click', function () {
            let dateFrom = $('#date-from').val();
            let dateTo = $('#date-to').val();

            if(!dateFrom || !dateTo) {
                alert('Моля, въведете дата!');
                return;
            }

            let url = `/nomenclatures/export-invoices-entities/export/?date_from=${dateFrom}&date_to=${dateTo}`;
            window.open(url, '_blank');
        });
    }
}

$(document).ready(function () {
    exportInvoicesEntities = new ExportInvoicesEntities();
});
