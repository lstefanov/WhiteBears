<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h4>Съпоставка данъчни основи</h4>

            <div style="clear: both; overflow: hidden; margin-top: 30px;">
                <span style="float: left; padding-right: 10px; padding-top: 6px; width: 130px;">Доставчик:</span>
                <select name="provider_id" id="provider-id" class="form-select" style="float: left; width: 300px;">
                    <option value="0">- изберете доставчик -</option>
                    <?php foreach ($providers as $provider){ ?>
                        <?php if((int)$provider['id'] === 3){ continue; } ?>
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
                <span style="float: left; padding-right: 10px; padding-top: 6px; width: 130px;">Фактура N:</span>
                <input class="form-control" type="text" name="invoice_number" id="invoice-number" value="<?= $invoiceNumber ?>" style="float: left; width: 300px;"  />
            </div>

            <div style="clear: both; margin-top: 10px; overflow: hidden;">
                <span style="float: left; padding-right: 10px; padding-top: 6px;  width: 130px;">Стойност:</span>
                <span style="float: left; padding-right: 5px; padding-top: 3px;">от:</span> <input type="text" id="price-from" class="form-control" value="<?= $priceFrom ?>" style="width: 120px; float: left; margin-right: 10px;" />
                <span style="float: left;  padding-right: 5px; padding-top: 3px;">до:</span> <input type="text" id="price-to" class="form-control" value="<?= $priceTo ?>" style="width: 120px; float: left; margin-right: 20px;" />
            </div>

            <div style="clear: both; margin-top: 10px; overflow: hidden;">
                <span style="float: left; padding-right: 10px; padding-top: 6px;  width: 130px;">Дата:</span>
                <span style="float: left; padding-right: 5px; padding-top: 3px;">от:</span> <input type="text" id="date-from" class="form-control" value="<?= $dateFrom ?>" style="width: 120px; float: left; margin-right: 10px;" />
                <span style="float: left;  padding-right: 5px; padding-top: 3px;">до:</span> <input type="text" id="date-to" class="form-control" value="<?= $dateTo ?>" style="width: 120px; float: left; margin-right: 20px;" />
            </div>

            <div style="clear: both; overflow: hidden; margin-top: 10px;">
                <span style="float: left; padding-right: 10px; padding-top: 6px; width: 130px;">Вид документ:</span>
                <select name="document_type" id="document-type" class="form-select" style="float: left; width: 300px;">
                    <option value="0">- изберете вид документ -</option>
                    <option value="901" <?php echo ($selectedDocumentType == 901) ? 'selected' : ''; ?>>901</option>
                    <option value="904" <?php echo ($selectedDocumentType == 904) ? 'selected' : ''; ?>>904</option>
                </select>
            </div>

            <div style="clear: both; overflow: hidden; margin-top: 10px;">
                <span style="float: left; padding-right: 10px; padding-top: 6px; width: 130px;">Статус:</span>
                <select name="match_status" id="match-status" class="form-select" style="float: left; width: 300px;">
                    <option value="0">- изберете статус -</option>
                    <option value="1" <?php echo ($matchStatus == 1) ? 'selected' : ''; ?>>съвпада</option>
                    <option value="2" <?php echo ($matchStatus == 2) ? 'selected' : ''; ?>>разминаване</option>
                    <option value="3" <?php echo ($matchStatus == 3) ? 'selected' : ''; ?>>липсва</option>
                    <option value="4" <?php echo ($matchStatus == 4) ? 'selected' : ''; ?>>съвпада ( ДДС: 0%/9%)</option>
                </select>
            </div>

            <div style="clear: both; margin-top: 10px; overflow: hidden;">
                <span style="float: left; padding-right: 10px; padding-top: 2px;  width: 130px;">&nbsp;</span>
                <button type="button" class="btn btn-success" style="float: left; padding: 4px 10px; font-size: 14px;" id="view-btn">Преглед</button>
            </div>

        </div>
        <div class="card-body">
            <?php if($selectedBusinessId === 0){ ?>
                <div class="alert alert-warning" role="alert">
                    Моля изберете доставчик и фирма !
                </div>
            <?php }else{ ?>
                <div class="table-responsive">
                    <table class="table table-bordered dataTable data-table-export" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Документ №</th>
                            <th>Дата на документ</th>
                            <th>Стойност</th>
                            <th>Статус</th>
                            <th>Детайли</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $row){ ?>
                            <tr>
                                <td><?= $row['doc_n'] ?></td>
                                <td><?= date('d.m.Y', strtotime($row['export_date_full'])) ?></td>
                                <td>
                                    <?= $row['payment_summary'] ?>

                                    <?php if((int) $row['status'] === 2 ){ ?>
                                        &nbsp;/&nbsp;<?= $row['purchase_by_document_data']['tax_base_amount'] ?>
                                    <?php } ?>

                                    <?php if( (int) $row['status'] === 1 AND ($row['payment_summary'] - $row['purchase_by_document_data']['tax_base_amount'] != 0.00)){ ?>
                                        &nbsp;/&nbsp;<?= $row['purchase_by_document_data']['tax_base_amount'] ?>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if((int) $row['status'] === 1 ){ ?>
                                        <span class="badge badge-success">съвпада</span>
                                    <?php }elseif((int) $row['status'] === 2 ){ ?>
                                        <span class="badge badge-warning">разминаване</span>
                                    <?php }elseif((int) $row['status'] === 4 ){ ?>
                                        <span class="badge badge-warning">съвпада с ДДС 0%/9% във фактурата</span>
                                    <?php }else{ ?>
                                        <span class="badge badge-danger">липсва</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('vat-purchase-journals/view/'.$row['vat_purchase_journals_id']) ?>" target="_blank">Дневник за покупки</a>
                                    <?php if((int) $row['status'] === 1 || (int) $row['status'] === 2 || (int) $row['status'] === 4 ){ ?>
                                        <br />
                                        <a href="<?= base_url('purchase-by-document/view/'.$row['purchase_by_document_data']['purchase_by_document_id']) ?>" target="_blank">Покупка по документ</a>
                                    <?php } ?>
                                </td>

                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>



<?=$this->endSection()?>