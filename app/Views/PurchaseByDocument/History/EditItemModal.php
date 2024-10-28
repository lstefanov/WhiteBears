<!-- Modal Structure -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Редактиране на наименование</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="form-group">
                        <label for="edit-modal-item-name">Лекарствено средство</label>
                        <input type="text" class="form-control" id="edit-modal-item-name" name="item_name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="edit-modal-provider-id" value="" />
                <input type="hidden" id="edit-modal-item-id" value="" />
                <button type="button" class="btn btn-primary" id="edit-modal-save-item-btn">Запази</button>
            </div>
        </div>
    </div>
</div>