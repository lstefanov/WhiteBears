<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <style>
        .dropdown.bootstrap-select.show-tick.form-select{
            display: block;
            width: 100% !important;
        }
    </style>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>
                <?= ($action === 'add') ? 'Добавяне на обект' : 'Редактиране на обект' ?>
            </h5>
        </div>
        <div class="card-body">

            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger">
                    <?= session('errors') ?>
                </div>
            <?php endif ?>

            <?php if (session()->has('success')) : ?>
                <div class="alert alert-success">
                    <?= session('success') ?>
                </div>
            <?php endif ?>

            <form method="post" id="companies-manage-form" action="<?= base_url("companies/save") ?>" class="needs-validation" novalidate>
                <div class="mb-4">
                    <label for="name" class="form-label h5">Име на обекта:</label>
                    <input type="text" name="name" class="form-control" id="name" value="<?= old('name', $company['name'] ?? '' ) ?>" required />
                </div>


                <div class="mb-4">
                    <label for="code" class="form-label h5">Клиентски номер:</label>
                    <input type="text" name="client_number" class="form-control" id="client_number" value="<?= old('client_number', $company['client_number'] ?? '' ) ?>" />
                </div>

                <hr style="opacity: 1" />

                <div class="mb-4">
                    <label for="alias_1" class="form-label h6">Алиас 1:</label>
                    <input type="text" name="alias_1" class="form-control" id="alias_1" value="<?= old('alias_1', $company['alias_1'] ?? '' ) ?>" />
                </div>

                <div class="mb-4">
                    <label for="alias_2" class="form-label h6">Алиас 2:</label>
                    <input type="text" name="alias_2" class="form-control" id="alias_2" value="<?= old('alias_2', $company['alias_2'] ?? '' ) ?>" />
                </div>

                <hr style="opacity: 1" />

                <div class="mb-4">
                    <label for="providers" class="form-label h5">Фирми:</label>
                    <select class="form-select selectpicker" name="businesses[]" id="businesses" multiple required>
                        <?php foreach ($businesses as $business){ ?>
                            <option value="<?= $business['id'] ?>"
                                <?= (is_array(old('businesses')) && in_array($business['id'], old('businesses'))) ? 'selected' : '' ?>
                                <?= (isset($companyBusinesses) && is_array($companyBusinesses) && in_array($business['id'], $companyBusinesses)) ? 'selected' : '' ?>
                            >
                                <?= $business['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <hr style="opacity: 1" />

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Запазване</button>
                    <a href="<?= base_url("partners/companies") ?>" class="btn btn-secondary">Обратно</a>
                </div>

                <input type="hidden" name="action" value="<?= $action ?>" />
                <input type="hidden" name="id" value="<?= $company['id'] ?? 0 ?>" />
            </form>

        </div>
    </div>


<?=$this->endSection()?>