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

        $('#providers').on('change', function() {
            let providerId = parseInt($(this).val());

            //Show /hide option for adding via text
            if(providerId === 3){
                $('#add-via-text-field').hide();
            } else {
                $('#add-via-text-field').show();
            }

            //show/hide option for adding via file/text
            if(isNaN(providerId)){
                $('#data-holder').hide();
                $('#submit-btn').prop('disabled', true);
            } else {
                $('#data-holder').show();
                $('#submit-btn').prop('disabled', false);
            }

            if(providerId === 1){
                $('#files').attr('accept', '.html');
                $('#accepted-files-info').html('.html');
            } else if(providerId === 2){
                $('#files').attr('accept', '.txt');
                $('#accepted-files-info').html('.txt');
            } else if(providerId === 3){
                $('#files').attr('accept', '.xlsx');
                $('#accepted-files-info').html('.xlsx');
            }
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