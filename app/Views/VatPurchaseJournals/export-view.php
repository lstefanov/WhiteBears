<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5 style="float: left; margin-right: 100px;">Преглед</h5>

            <span style="float: left; padding-right: 10px; padding-top: 2px;">Фирма:</span>
            <select name="business_id" id="business-id" class="form-select form-select-sm" style="float: left; margin-right: 20px; padding-top: 2px; width: 200px;">
                <option value="0">- изберете фирма -</option>
                <?php foreach ($businesses as $business){ ?>
                    <option value="<?= $business['id'] ?>" <?php echo ($selectedBusinessId === $business['id']) ? 'selected' : ''; ?>><?= $business['name'] ?></option>
                <?php } ?>
            </select>

            <span style="float: left; padding-right: 10px; padding-top: 2px;">Дата:</span>
            <input type="text" id="export-date-filter" class="form-control form-control-sm" value="<?= $date ?>" style="width: 100px; float: left; margin-right: 20px;" />

            <button type="button" class="btn bt-sm btn-success" style="float: left; padding: 4px 10px; font-size: 14px;" id="view-btn">Преглед</button>

            <button type="button" class="btn bt-sm btn-primary" style="float: right; margin-right 10px;" id="export-date-filter-btn">Експортиране</button>
        </div>
        <div class="card-body">
            <?php if($selectedBusinessId === 0){ ?>
                <div class="alert alert-warning" role="alert">
                    Моля изберете фирма !
                </div>
            <?php }else{ ?>
                <div class="table-responsive">
                <table class="table table-bordered dataTable data-table-export" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Тип документ</th>
                        <th>Документ №</th>
                        <th>Дата на документ</th>
                        <th>Вальор</th>
                        <th>Стойност</th>
                        <th>ДДС</th>
                        <th>Вид Контрагент</th>
                        <th>Код на контрагент</th>
                        <th>Код стока</th>
                        <th>&nbsp;</th>
                        <th>$18 - (NSB) - Вид плащане</th>
                    </tr>
                    <tr>
                        <th>H.DOC_TYPE</th>
                        <th>H.DOC_NO</th>
                        <th>H.DOC_DATE</th>
                        <th>H.DOC_VALEUR</th>
                        <th>D.NET_VALUE</th>
                        <th>D.VAT_VALUE</th>
                        <th>D.PARTNER_TYPE</th>
                        <th>D.PARTNER</th>
                        <th>D.23</th>
                        <th>24</th>
                        <th>D.$18</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($data as $item) { ?>
                        <tr>
                            <td><?=$item['h_doc_type']?></td>
                            <td><?=$item['h_doc_no']?></td>
                            <td><?=$item['h_doc_date']?></td>
                            <td><?=$item['h_doc_valeur']?></td>
                            <td><?=$item['d_net_value']?></td>
                            <td><?=$item['d_vat_value']?></td>
                            <td><?=$item['d_partner_type']?></td>
                            <td><?=$item['d_partner']?></td>
                            <td><?=$item['d_23']?></td>
                            <td><?=$item['d_24']?></td>
                            <td><?=$item['d_$18']?></td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>



<?=$this->endSection()?>