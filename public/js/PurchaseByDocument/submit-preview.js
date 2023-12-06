let purchaseByDocumentSubmitPreview;

class PurchaseByDocumentSubmitPreview {
    constructor() {
        this.dataTables();
        this.events();
    }

    events() {
        $('#finish-btn').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if($(this).hasClass('btn-secondary')){
                return false;
            }

            let url = $(this).attr('href');

            $(this).removeClass('btn-primary').addClass('btn-secondary').html('обработване...').attr('disabled', true);
            window.location.href = url;
        });
    }

    dataTables() {
        $('.dataTable').DataTable({
            // set default table lenght to 25
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Всички"]],
            "pageLength": 10,
            "order": [[ 0, "asc" ]],

            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json',
            },
        });
    }

}

$(document).ready(function () {
    purchaseByDocumentSubmitPreview = new PurchaseByDocumentSubmitPreview();
});
