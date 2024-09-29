<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h4>Справка за закупена стока</h4>

            <div style="clear: both; overflow: hidden; margin-top: 30px;">
                <span style="float: left; padding-right: 10px; padding-top: 6px; width: 130px;">Доставчик:</span>
                <select name="provider_id" id="provider-id" class="form-select" style="float: left; width: 300px;">
                    <option value="0">- изберете доставчик -</option>
                    <?php foreach ($providers as $provider){ ?>
                        <option value="<?= $provider['id'] ?>" <?php echo ($selectedProviderId === $provider['id']) ? 'selected' : ''; ?>><?= $provider['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div style="clear: both; overflow: hidden; margin-top: 10px;">
                <span style="float: left; padding-right: 10px; padding-top: 6px; width: 130px;">Фирма:</span>
                <select name="business_id" id="business-id" class="form-select" style="float: left; width: 300px;" <?php if($selectedProviderId === 0) { echo 'disabled';} ?>>
                    <option value="0">- изберете фирма -</option>
                    <?php foreach ($businesses as $business){ ?>
                        <option
                            value="<?= $business['id'] ?>"
                            <?php echo ($selectedBusinessId === $business['id']) ? 'selected' : ''; ?>
                            data-providers="<?= implode(',', $business['providers']) ?>"
                        ><?= $business['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div style="clear: both; overflow: hidden; margin-top: 10px;">
                <span style="float: left; padding-right: 10px; padding-top: 6px; width: 130px;">Обекти:</span>
                <select name="company_id" id="company-id" class="form-select" style="float: left; width: 300px;" <?php if($selectedCompanyId === 0) { echo 'disabled';} ?>>
                    <option value="0">- изберете обект -</option>
                    <?php foreach ($companies as $company){ ?>
                        <option
                                value="<?= $company['id'] ?>"
                            <?php echo ($selectedCompanyId === $company['id']) ? 'selected' : ''; ?>
                                data-businesses="<?= implode(',', $company['businesses']) ?>"
                        ><?= $company['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div style="clear: both; margin-top: 10px; overflow: hidden;">
                <span style="float: left; padding-right: 10px; padding-top: 6px;  width: 130px;">Дата:</span>
                <span style="float: left; padding-right: 5px; padding-top: 3px;">от:</span> <input type="text" id="date-from" class="form-control" value="<?= $dateFrom ?>" style="width: 120px; float: left; margin-right: 10px;" />
                <span style="float: left;  padding-right: 5px; padding-top: 3px;">до:</span> <input type="text" id="date-to" class="form-control" value="<?= $dateTo ?>" style="width: 120px; float: left; margin-right: 20px;" />
            </div>


            <div style="clear: both; margin-top: 10px; overflow: hidden;">
                <span style="float: left; padding-right: 10px; padding-top: 2px;  width: 130px;">&nbsp;</span>
                <button type="button" class="btn btn-success" style="float: left; padding: 4px 10px; font-size: 14px;" id="view-btn">Преглед</button>
            </div>

        </div>
        <div class="card-body">
            <?php if($selectedBusinessId === 0){ ?>
                <div class="alert alert-warning" role="alert">
                    Моля попълнете всички полета!
                </div>
            <?php }else{ ?>
                <div class="table-responsive">
                    <table class="table table-bordered dataTable data-table-export" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Група</th>
                            <th>Код</th>
                            <th>Наименование</th>
                            <th>Брой</th>
                            <th>Обща цена</th>
                            <th>Средна цена</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $row){ ?>

                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>



<?=$this->endSection()?>