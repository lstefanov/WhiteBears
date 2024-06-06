let vatPurchaseJournalsExportView;

class VatPurchaseJournalsExportView {
    constructor() {
        this.dataTables();
        this.datePicker();
        this.events();

        $('#provider-id').trigger('change');

        //get url params
        let urlParams = new URLSearchParams(window.location.search);
        let businessId = urlParams.get('business_id');
        if(businessId) {
            $('#business-id').val(businessId).trigger('change');
        }
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
            let providerId = $('#provider-id').val();
            let businessId = $('#business-id').val();
            let invoiceNumber = $('#invoice-number').val();
            let priceFrom = $('#price-from').val();
            let priceTo = $('#price-to').val();
            let dateFrom = $('#date-from').val();
            let dateTo = $('#date-to').val();
            let documentType = $('#document-type').val();
            let matchStatus = $('#match-status').val();

            if (!providerId || parseInt(providerId) === 0) {
                alert('Моля, изберете доставчик!');
                return;
            }

            if (!businessId || parseInt(businessId) === 0) {
                alert('Моля, изберете фирма!');
                return;
            }

            let urlParams = `?provider_id=${providerId}&business_id=${businessId}&invoice_number=${invoiceNumber}&price_from=${priceFrom}&price_to=${priceTo}&date_from=${dateFrom}&date_to=${dateTo}&document_type=${documentType}&match_status=${matchStatus}`;

            window.location.href = `/reference/dds-vs-items-from-invoice/${urlParams}`;
        });


        $('#provider-id').on('change', function () {
            let providerId = $(this).val();

            if (providerId === '0') {
                $('#business-id').val('0').attr('disabled', true);
            } else {
                $('#business-id').val('0').attr('disabled', false);

                $('#business-id option').each(function (i, e) {
                    let providers = $(e).data('providers');
                    if (typeof providers !== 'undefined') {

                        //convert providers to string
                        providers = providers.toString();

                        if (providers.indexOf(providerId) === -1) {
                            $(e).hide();
                        } else {
                            $(e).show();
                        }
                    }
                });
            }
        });

        //event on dynamic build elements with data-action="delete" , on click
        $(document).on('click', '[data-action="delete"]', function () {
            let element = $(this);
            let id = $(this).data('id');

            if (confirm('Сигурни ли сте, че искате да изтриете записа?')) {
                $.ajax({
                    url: '/purchase-by-document/delete/' + id + '?ajax=1',
                    method: 'GET',
                    success: function (response) {
                        element.closest('tr').remove();
                    }
                });
            }
        });
    }
}

$(document).ready(function () {
    vatPurchaseJournalsExportView = new VatPurchaseJournalsExportView();
});
