<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5 style="float: left; margin-right: 100px;">Списък на фирмите</h5>

            <span style="float: left; padding-right: 10px; padding-top: 2px;">Доставчик:</span>
            <select name="provider_id" id="provider-id" class="form-select form-select-sm" style="float: left; margin-right: 20px; padding-top: 2px; width: 200px;">
                <option value="0">- изберете доставчик -</option>
                <?php foreach ($providers as $provider){ ?>
                    <option value="<?= $provider['id'] ?>" <?php echo ($selectedProviderId === (int)$provider['id']) ? 'selected' : ''; ?>><?= $provider['name'] ?></option>
                <?php } ?>
            </select>

            <button type="button" class="btn bt-sm btn-success" style="float: left; padding: 4px 10px; font-size: 14px;" id="view-btn">Преглед</button>

            <a href="<?= base_url('businesses/manage?action=add') ?>" type="button" class="btn bt-sm btn-primary" style="float: right; margin-right 10px;">Добавяне на фирма</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Фирма</th>
                        <th>Обслужвани обекта</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($businesses as $business){ ?>
                        <tr data-id="<?= $business['id'] ?>" class="<?= (int)$business['active'] === 0 ? 'deactivated' : '' ?>">
                            <td><?= $business['id'] ?></td>
                            <td><?= $business['name'] ?></td>
                            <td>
                                <a class="badge badge-info" href="<?= base_url("partners/companies??business_id={$business['id']}") ?>">
                                    <?= count($business['companies']) ?>
                                </a>
                            </td>
                            <td>
                                <a class="btn bt-sm btn-success" href="<?= base_url("businesses/manage?action=edit&id={$business['id']}") ?>">
                                    Редактиране
                                </a>

                                <?php if((int)$business['active'] === 1){ ?>
                                    <button type="button" class="btn bt-sm btn-secondary" data-action="change-status" data-status="deactivate" data-id="<?= $business['id'] ?>">
                                        Деактивиране
                                    </button>
                                <?php }else{ ?>
                                    <button type="button" class="btn bt-sm btn-info" data-action="change-status" data-status="activate" data-id="<?= $business['id'] ?>">
                                        Активиране
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<?=$this->endSection()?>