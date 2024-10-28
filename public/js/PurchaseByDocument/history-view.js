let purchaseByDocumentHistoryView;

class PurchaseByDocumentHistoryView {
    constructor() {
        this.editElement()
    }

    editElement() {
        let tableElement;

        $(document).on('click', 'span.edit-icon', function() {
            let providerId = $(this).data('provider-id');
            let itemId = $(this).data('item-id');
            let itemName = $(this).data('item-name');

            $('#edit-modal-item-name').val(itemName);
            $('#edit-modal-provider-id').val(providerId);
            $('#edit-modal-item-id').val(itemId);

            $('#editModal').modal('show');

            tableElement = $(this).closest('tr');
        });

        $('#edit-modal-save-item-btn').on('click', function() {

            if($('#edit-modal-item-name').val() == ''){
                alert('Моля, въведете наименование!');
                return;
            }

            $('#edit-modal-save-item-btn').attr('disabled', true);

            let itemId = $('#edit-modal-item-id').val();
            let itemName = $('#edit-modal-item-name').val();

            $.ajax({
                url: '/purchase-by-document/rename-element',
                method: 'POST',
                data: {
                    item_name : $('#edit-modal-item-name').val(),
                    provider_id : $('#edit-modal-provider-id').val(),
                    item_id : $('#edit-modal-item-id').val()
                },
                success: function(response) {
                    if(response.status === 'success') {

                        $('#edit-modal-item-name').val('');
                        $('#edit-modal-provider-id').val('');
                        $('#edit-modal-item-id').val('');

                        tableElement.find(`span[data-type="name"][data-item-id="${itemId}"]`).text(itemName);
                        $('#editModal').modal('hide');

                        alert('Успешно преименуване на елемент!');

                    } else {
                        alert('Възникна грешка при преименуването на елемент!');
                    }

                    $('#edit-modal-save-item-btn').attr('disabled', false);
                },
                error: function() {
                    alert('Възникна грешка при преименуването на елемент!');
                    $('#edit-modal-save-item-btn').attr('disabled', false);
                }
            });
        });
    }

}

$(document).ready(function () {
    purchaseByDocumentHistoryView = new PurchaseByDocumentHistoryView();
});
