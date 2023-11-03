<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5 style="float: left; margin-right: 100px;">Списък на обкетите</h5>

            <span style="float: left; padding-right: 10px; padding-top: 2px;">Фирма:</span>
            <select name="business_id" id="business-id" class="form-select form-select-sm" style="float: left; margin-right: 20px; padding-top: 2px; width: 200px;">
                <option value="0">- изберете фирма -</option>
                <?php foreach ($businesses as $business){ ?>
                    <option value="<?= $business['id'] ?>" <?php echo ($selectedBusinessId === (int)$business['id']) ? 'selected' : ''; ?>><?= $business['name'] ?></option>
                <?php } ?>
            </select>

            <button type="button" class="btn bt-sm btn-success" style="float: left; padding: 4px 10px; font-size: 14px;" id="view-btn">Преглед</button>

            <a href="<?= base_url('companies/manage?action=add') ?>" type="button" class="btn bt-sm btn-primary" style="float: right; margin-right 10px;">Добавяне на обект</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Обект</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($companies as $company){ ?>
                        <tr data-id="<?= $company['id'] ?>" class="<?= (int)$company['active'] === 0 ? 'deactivated' : '' ?>">
                            <td><?= $company['id'] ?></td>
                            <td><?= $company['name'] ?></td>
                            <td>
                                <a class="btn bt-sm btn-success" href="<?= base_url("companies/manage?action=edit&id={$company['id']}") ?>">
                                    Редактиране
                                </a>

                                <?php if((int)$company['active'] === 1){ ?>
                                    <button type="button" class="btn bt-sm btn-secondary" data-action="change-status" data-status="deactivate" data-id="<?= $company['id'] ?>">
                                        Деактивиране
                                    </button>
                                <?php }else{ ?>
                                    <button type="button" class="btn bt-sm btn-info" data-action="change-status" data-status="activate" data-id="<?= $company['id'] ?>">
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