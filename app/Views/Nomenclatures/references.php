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
                <?php if($selectedBusinessId !== 0){ ?>
                    <button type="button" class="btn btn-primary" style="float: left; padding: 4px 10px; font-size: 14px; margin-left: 10px;" id="export-references-btn">Експортиране</button>
                    <button type="button" class="btn btn-danger" style="float: left; padding: 4px 10px; font-size: 14px; margin-left: 10px;" id="export-invalid-references-btn">Експортиране на грешните</button>
                <?php } ?>
            </div>

        </div>
        <div class="card-body">
            <?php if($selectedBusinessId === 0){ ?>
                <div class="alert alert-warning" role="alert">
                    Моля попълнете всички полета!
                </div>
            <?php }else{ ?>
                <div class="table-responsive">
                    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Група</th>
                            <th>Код</th>
                            <th>Наименование</th>
                            <th>Брой</th>
                            <th>Обща цена</th>
                            <th>Средна цена</th>
                            <th>Фактури</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['elements'] as $row){ ?>
                                <tr>
                                    <td><?= $row['code_name'] ?></td>
                                    <td><?= $row['code_number'] ?? '-' ?></td>
                                    <td><?= $row['name'] ?></td>
                                    <td><?= $row['quantity'] ?></td>
                                    <td><?= number_format($row['price'], 2, '.', '') ?></td>
                                    <td>
                                        <?php
                                            $averagePrice = intval($row['quantity']) !== 0 ? doubleval($row['price']) / intval($row['quantity']) : 0;
                                            echo number_format($averagePrice, 2, '.', '');
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            $elements = [];
                                            foreach($row['invoices'] as $invoice){
                                                $elements[] = "<a href='".base_url("purchase-by-document/view/{$invoice['id']}")."' target='_blank'>{$invoice['number']}</a>";
                                            }
                                            echo implode(', ', $elements);
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <?php if(in_array($selectedProviderId, [1,2])){ ?>
                    <div class="table-responsive">
                        <br /><br />
                        <h4>Отстъпки по фактури</h4>

                        <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Отстъпка</th>
                                <th>Фактура</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data['discounts'] as $row){ ?>
                                <tr>
                                    <td><?= number_format($row['discount'], 2, '.', '') ?></td>
                                    <td>
                                        <a href="<?= base_url("purchase-by-document/view/{$row['id']}") ?>" target="_blank"><?= $row['number'] ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>

                <div class="table-responsive">
                    <br /><br />
                    <h4>Липсващи стоки</h4>

                    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Група</th>
                            <th>Наименование</th>
                            <th>Брой</th>
                            <th>Обща цена</th>
                            <th>Средна цена</th>
                            <th>Фактури</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['missing'] as $row){ ?>
                            <tr>
                                <td style="background-color: rgba(255,0,0, 0.1);">
                                    <button type="button"
                                            class="btn btn-danger add-missing-element" style="padding: 0px 5px; font-size: 12px;" data-toggle="modal" data-target="#missing-modal" data-name="<?= addslashes($row['name']) ?>">Добави</button>
                                </td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= number_format($row['price'], 2, '.', '') ?></td>
                                <td>
                                    <?php
                                    $averagePrice = intval($row['quantity']) !== 0 ? doubleval($row['price']) / intval($row['quantity']) : 0;
                                    echo number_format($averagePrice, 2, '.', '');
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $elements = [];
                                    foreach($row['invoices'] as $invoice){
                                        $elements[] = "<a href='".base_url("purchase-by-document/view/{$invoice['id']}")."'>{$invoice['number']}</a>";
                                    }
                                    echo implode(', ', $elements);
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="modal fade" id="missing-modal" tabindex="-1" role="dialog" aria-labelledby="missing-modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="missing-modal-title">Добавяне на липсваща стока</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="form-group">
                        <label for="missing-group" id="missing-element-modal-name">Група</label>
                        <select name="missing-group" id="missing-group" class="form-select">
                            <option value="">- изберете група -</option>
                            <option value="A">A (ЛЕКАРСТВЕНИ СР-ВА)</option>
                            <option value="B">B (АНАЛГЕТИЦИ)</option>
                            <option value="C">C (САНИТАРИЯ)</option>
                            <option value="D">D (КОЗМЕТИКА)</option>
                            <option value="Y">Y (УСЛУГИ)</option>
                        </select>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-success" id="add-missing-btn">Добави</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?=$this->endSection()?>