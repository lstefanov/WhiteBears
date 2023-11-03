let companiesManage;

class CompaniesManage {
    constructor() {
        this.formValidation();
        this.events();
    }

    formValidation() {

    }

    events() {

    }
}

$(document).ready(function () {
    companiesManage = new CompaniesManage();
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
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    $('#submit-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary').html('обработване...');
                }
                form.classList.add('was-validated')
            }, false)
        })
})()