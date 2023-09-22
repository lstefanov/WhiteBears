<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Добавяне на фактури</h6>
                </div>
                <div class="card-body">

                    <form method="post" action="<?= base_url('/vat-purchase-journals/submit') ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="providers" class="form-label h5">Доставчик:</label>
                            <select class="form-select" name="providers" id="providers" required>
                                <option selected value="">- изберете доставчик -</option>
                                <?php foreach ($providers as $provider){ ?>
                                    <option value="<?= $provider['id'] ?>"><?= $provider['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-4" id="business-holder">
                            <label for="businesses" class="form-label h5">Фирма:</label>
                            <select class="form-select" name="businesses" id="businesses" disabled required>
                                <option selected value="" data-type="disabled">- първо изберете доставчик -</option>
                                <option value="" data-type="placeholder">- изберете фирма -</option>
                                <?php foreach ($businesses as $business){ ?>
                                    <option value="<?= $business['id'] ?>" data-providers="<?= implode(',', array_column($business['providers'], 'provider_id')) ?>"><?= $business['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="businesses" class="form-label h5">Документи:</label>
                            <input class="form-control" type="file" id="files" name="files[]" multiple accept=".xls,.xlsx" required />
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" id="submit-btn">Обработка</button>
                        </div>


                    </form>



                </div>
            </div>

        </div>
    </div>

<?=$this->endSection()?>