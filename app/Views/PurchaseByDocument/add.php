<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Добавяне на покупка по документ</h6>
                </div>
                <div class="card-body">

                    <form method="post" action="<?= base_url('/purchase-by-document/submit') ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="providers" class="form-label h5">Доставчик:</label>
                            <select class="form-select" name="providers" id="providers" required>
                                <option selected value="">- изберете доставчик -</option>
                                <?php foreach ($providers as $provider){ ?>
                                    <option value="<?= $provider['id'] ?>"><?= $provider['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div id="data-holder" style="display: none;">
                            <div class="mb-3 mt-5">
                                <label for="businesses" class="form-label h5">Добавяне на документи:</label>
                            </div>

                            <div class="mb-2 mt-1">
                                <label for="businesses" class="form-label">Фаилове:</label>
                                <input class="form-control" type="file" id="files" name="files[]" multiple accept=".txt,.html,.xlsx" />
                                <small>Позволени файлови формати: <span id="accepted-files-info" style="font-weight: bold;">-</span></small>
                            </div>

                            <hr />

                            <div class="mb-5" id="add-via-text-field">
                                <label for="businesses" class="form-label d-block">Текст:</label>

                                <div id="text-data-holder">

                                </div>

                                <button type="button" class="btn btn-info btn-icon-split mt-1" id="add-text-btn">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    <span class="text">добавяне на текстово поле</span>
                                </button>

                            </div>
                        </div>

                        <hr />

                        <div class="mb-3 mt-5">
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Обработка</button>
                        </div>


                    </form>



                </div>
            </div>

        </div>
    </div>

<?=$this->endSection()?>