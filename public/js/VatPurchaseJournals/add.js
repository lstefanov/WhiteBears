let vatPurchaseJournalsAdd;

class VatPurchaseJournalsAdd {
    constructor() {
        this.events();
    }

    events() {

        $('#providers').on('change', function (e) {
            let selectedProvider = $(this).val();

            if(selectedProvider === ''){
                disableBusinesses();
            } else {
                enableBusinesses(selectedProvider);
            }


            //@todo: remove this when the providers are added to the database
            if( selectedProvider === '2' || selectedProvider === '3'){ // Фьоникс Фарма
                $('#business-holder').hide();
            } else {
                $('#business-holder').show();
            }



            function enableBusinesses(selectedProvider){
                $('#businesses').prop('disabled', false);

                $.each($('#businesses option'), function(){
                    let providers = $(this).data('providers');
                    let businessId = $(this).val();
                    let optionType = $(this).data('type');

                    //By default, disable all options
                    $(this).prop('disabled', true);

                    //Check for options that have the selected provider
                    if(typeof providers !== "undefined"){
                        providers = providers.toString();
                        let providersArray = providers.split(',');

                        //If the selected provider is in the array, enable the option
                        if(providersArray.includes(selectedProvider)){
                            $(this).prop('disabled', false);
                        }
                    }

                    //Hide the "Select a business" option
                    if(optionType === 'disabled'){
                        $(this).prop('hidden', true);
                    }

                    //Enable and autoselect the first option
                    if(optionType === 'placeholder'){
                        $(this).prop('disabled', false).prop('selected', true).prop('hidden', false);
                    }
                });
            }

            function disableBusinesses(){
                $('#businesses').prop('disabled', true);

                $.each($('#businesses option'), function(){
                    let businessId = $(this).val();
                    if(businessId === '-1'){
                        $(this).prop('disabled', false).prop('selected', true).prop('hidden', false);
                    }
                });
            }
        });

    }

}

$(document).ready(function () {
    vatPurchaseJournalsAdd = new VatPurchaseJournalsAdd();
});



// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                let businessId = $('#businesses').val();

                //check if element with id "businesses" is visibl
                if($('#businesses').is(':visible') && businessId === ''){
                    alert('Не е избрана фирма!!!');
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }  else {
                    $('#submit-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary').html('обработване...');
                }
                /*else if(businessId === '') {
                    alert('Не е избрана фирма!!!');
                    event.preventDefault()
                    event.stopPropagation()
                }*/

                form.classList.add('was-validated')
            }, false)
        })
})()