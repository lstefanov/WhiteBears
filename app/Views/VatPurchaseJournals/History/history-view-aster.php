<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>


    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5><?= $vpjDetails['id'] ?> | <?= $vpjDetails['file_name'] ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Номер на документа</th>
                        <th>Дата на издаване на документа</th>
                        <th>ЕИК на контрагента</th>
                        <th>Име на контрагента</th>
                        <th>Предмет на сделката</th>
                        <th>Обща с-ст на сделката (вкл. ДДС)</th>
                        <th>Ст-ст на облаг. сделки</th>
                        <th>ДДС на облаг. сделки</th>
                        <th>Ст-ст по пок. цени</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vpjAsterEntitiesModel as $item) { ?>
                            <tr>
                                <td><?= $item['invoice'] ?></td>
                                <td><?= $item['invoice_date'] ?></td>
                                <td><?= $item['eik'] ?></td>
                                <td><?= $item['business_name'] ?></td>
                                <td><?= $item['subject_of_the_transaction'] ?></td>
                                <td><?= $item['total_price_inc_vat'] ?></td>
                                <td><?= $item['price_without_vat'] ?></td>
                                <td><?= $item['price_vat'] ?></td>
                                <td><?= $item['price_purchase'] ?></td>
                                <td>
                                    <?php if ($item['status'] === 'success') { ?>
                                        <span class="badge badge-success">Успешно</span>
                                    <?php } else { ?>
                                        <span class="text text-danger"><?= $item['status_details'] ?></span>
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