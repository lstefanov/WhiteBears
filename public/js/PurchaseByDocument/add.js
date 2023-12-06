let purchaseByDocumentAdd;

class PurchaseByDocumentAdd {
    constructor() {
        this.events();
    }

    events() {

        //Add new text item
        $('#add-text-btn').on('click', function(){
            let randomId = Math.floor(Math.random() * 1000000000);

            let template = `
                <div data-type="text-item" id="item-${randomId}" class="mt-4 mb-3">
                    <label class="form-label">
                        Копирайте текста от документа:
                        <i class="fas fa-trash text-danger ml-3" data-action="remove-text-item" style="cursor: pointer" title="Премахване"></i>
                    </label>
                    <textarea class="form-control" name="texts[]" rows="15" style="width: 100%; white-space: pre;"></textarea>
                </div>
            `;

            $('#text-data-holder').append(template);
        });


        //Remove text item
        $('body').on('click', '[data-action="remove-text-item"]', function() {
            $(this).closest('[data-type="text-item"]').remove();
        });
    }

}

$(document).ready(function () {
    purchaseByDocumentAdd = new PurchaseByDocumentAdd();
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
                }  else {
                    $('#submit-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary').html('обработване...');
                }

                form.classList.add('was-validated')
            }, false)
        })
})()