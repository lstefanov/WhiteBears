<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>
                <?= ($action === 'add') ? 'Добавяне на фирма' : 'Редактиране на фирма' ?>
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

            <form method="post" id="businesses-manage-form" action="<?= base_url("businesses/save") ?>" class="needs-validation" novalidate>
                <div class="mb-4">
                    <label for="name" class="form-label h5">Име на Фирмата:</label>
                    <input type="text" name="name" class="form-control" id="name" value="<?= old('name', $business['name'] ?? '' ) ?>" required />
                </div>

                <hr style="opacity: 1" />

                <div class="mb-4">
                    <label for="code" class="form-label h5">Код:</label>
                    <input type="text" name="code" class="form-control" id="code" value="<?= old('code', $business['code'] ?? '' ) ?>" required />
                </div>

                <div class="mb-4">
                    <label for="in_number" class="form-label h5">ИН Номер:</label>
                    <input type="text" name="in_number" class="form-control" id="in_number" value="<?= old('in_number', $business['in_number'] ?? '' ) ?>" required />
                </div>


                <hr style="opacity: 1" />

                <div class="mb-4">
                    <label for="alias_1" class="form-label h6">Алиас 1:</label>
                    <input type="text" name="alias_1" class="form-control" id="alias_1" value="<?= old('alias_1', $business['alias_1'] ?? '' ) ?>" />
                </div>

                <div class="mb-4">
                    <label for="alias_2" class="form-label h6">Алиас 2:</label>
                    <input type="text" name="alias_2" class="form-control" id="alias_2" value="<?= old('alias_2', $business['alias_2'] ?? '' ) ?>" />
                </div>

                <div class="mb-4">
                    <label for="alias_3" class="form-label h6">Алиас 3:</label>
                    <input type="text" name="alias_3" class="form-control" id="alias_3" value="<?= old('alias_3', $business['alias_3'] ?? '' ) ?>" />
                </div>

                <div class="mb-4">
                    <label for="alias_4" class="form-label h6">Алиас 4:</label>
                    <input type="text" name="alias_4" class="form-control" id="alias_4" value="<?= old('alias_4', $business['alias_4'] ?? '' ) ?>" />
                </div>

                <div class="mb-4">
                    <label for="alias_5" class="form-label h6">Алиас 5:</label>
                    <input type="text" name="alias_5" class="form-control" id="alias_5" value="<?= old('alias_5', $business['alias_5'] ?? '' ) ?>" />
                </div>

                <hr style="opacity: 1" />

                <div class="mb-4">
                    <label for="providers" class="form-label h5">Доставчик:</label>
                    <select class="form-select" name="providers[]" id="providers" multiple required>
                        <?php foreach ($providers as $provider){ ?>
                            <option value="<?= $provider['id'] ?>"
                                <?= (is_array(old('providers')) && in_array($provider['id'], old('providers'))) ? 'selected' : '' ?>
                                <?= (isset($businessProviders) && is_array($businessProviders) && in_array($provider['id'], $businessProviders)) ? 'selected' : '' ?>
                            >
                                <?= $provider['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <hr style="opacity: 1" />

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Запазване</button>
                    <a href="<?= base_url("partners/businesses") ?>" class="btn btn-secondary">Обратно</a>
                </div>

                <input type="hidden" name="action" value="<?= $action ?>" />
                <input type="hidden" name="id" value="<?= $business['id'] ?? 0 ?>" />
            </form>

        </div>
    </div>


<?=$this->endSection()?>