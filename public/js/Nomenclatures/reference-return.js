let reference = null;

class Reference {
    constructor() {
        this.dataTables();
        this.datePicker();
        this.events();
        this.missingElement();

        $('#provider-id').trigger('change');

        //get url params
        let urlParams = new URLSearchParams(window.location.search);
        let providerId = urlParams.get('provider_id');
        if (providerId) {
            $('#provider-id').val(providerId).trigger('change');
        }

        let businessId = urlParams.get('business_id');
        if (businessId) {
            $('#business-id').val(businessId).trigger('change');
        }

        let companyId = urlParams.get('company_id');
        if (companyId) {
            $('#company-id').val(companyId);
        }
    }

    missingElement() {
        let tableElement;

        $(document).on('click', 'button.add-missing-element', function() {
            let elementName = $(this).data('name');
            $('#missing-element-modal-name').text(elementName);

            tableElement = $(this).closest('tr');
        });

        $('#add-missing-btn').on('click', function() {
            let groupName = $('#missing-group').val();
            let elementName = $('#missing-element-modal-name').text();

            if(!groupName){
                alert('Моля, въведете група!');
                return;
            }

            $('#add-missing-btn').attr('disabled', true);

            $.ajax({
                url: '/nomenclatures/add-missing-element',
                method: 'POST',
                data: {
                    group_name: groupName,
                    element_name: elementName,
                },
                success: function(response) {
                    if(response.status === 'success') {
                        $('#missing-group').val('');
                        $('#missing-modal').modal('hide');
                        tableElement.remove();

                        alert('Успешно добавен нов елемент!');

                    } else {
                        alert('Възникна грешка при добавянето на нов елемент!');
                    }

                    $('#add-missing-btn').attr('disabled', false);
                },
                error: function() {
                    alert('Възникна грешка при добавянето на нов елемент!');
                    $('#add-missing-btn').attr('disabled', false);
                }
            });
        });
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
            let companyId = $('#company-id').val();
            let dateFrom = $('#date-from').val();
            let dateTo = $('#date-to').val();

            if (!providerId || parseInt(providerId) === 0) {
                alert('Моля, изберете доставчик!');
                return;
            }

            if (!businessId || parseInt(businessId) === 0) {
                alert('Моля, изберете фирма!');
                return;
            }

            let urlParams = `?provider_id=${providerId}&business_id=${businessId}&company_id=${companyId}&date_from=${dateFrom}&date_to=${dateTo}`;

            window.location.href = `/nomenclatures/reference-return/${urlParams}`;
        });

        $('#export-references-btn').on('click', function () {
            let providerId = $('#provider-id').val();
            let businessId = $('#business-id').val();
            let companyId = $('#company-id').val();
            let dateFrom = $('#date-from').val();
            let dateTo = $('#date-to').val();

            if (!providerId || parseInt(providerId) === 0) {
                alert('Моля, изберете доставчик!');
                return;
            }

            if (!businessId || parseInt(businessId) === 0) {
                alert('Моля, изберете фирма!');
                return;
            }

            let urlParams = `?provider_id=${providerId}&business_id=${businessId}&company_id=${companyId}&date_from=${dateFrom}&date_to=${dateTo}`;

            window.open(`/nomenclatures/reference-return-export/${urlParams}`, '_blank');
        });


        $('#provider-id').on('change', function () {
            let providerId = $(this).val();
            console.log(typeof providerId, providerId);
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

        $('#business-id').on('change', function () {
            let businessId = $(this).val();
            if (businessId === '0') {
                $('#company-id').val('0').attr('disabled', true);
            } else {
                $('#company-id').val('0').attr('disabled', false);
                $('#company-id option').each(function (i, e) {
                    let businesses = $(e).data('businesses');
                    if (typeof businesses !== 'undefined') {
                        // Splitting businesses into an array
                        let businessesArray = businesses.toString().split(',');

                        // Checking if businessId exists in the array
                        if (businessesArray.includes(businessId)) {
                            $(e).show();
                        } else {
                            $(e).hide();
                        }
                    }
                });
            }
        });
    }
}

$(document).ready(function () {
    reference = new Reference();
});
