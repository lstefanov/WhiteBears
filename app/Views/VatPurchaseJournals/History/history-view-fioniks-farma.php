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
                        <th>Склад</th>
                        <th>Фирма</th>
                        <th>Клиентски номер</th>
                        <th>Клиент</th>
                        <th>Фактура</th>
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Падеж</th>
                        <th>Вид плащане</th>
                        <th>Стойност</th>
                        <th>Платено</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($vpjFioniksFarmaEntities as $item) { ?>
                            <tr>
                                <td><?= $item['warehouse'] ?></td>
                                <td><?= $item['business_name'] ?></td>
                                <td><?= $item['client_number'] ?></td>
                                <td><?= $item['company_name'] ?></td>
                                <td><?= $item['invoice'] ?></td>
                                <td><?= $item['invoice_date'] ?></td>
                                <td><?= $item['invoice_type'] ?></td>
                                <td><?= $item['due_date'] ?></td>
                                <td><?= $item['payment_type'] ?></td>
                                <td><?= $item['payment_summary'] ?></td>
                                <td><?= $item['payment_payed'] ?></td>
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