<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>Преглед на добавените фактури</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Доставчик</th>
                        <th>Фирма</th>
                        <th>Фактура N</th>
                        <th>Фактура Дата</th>
                        <th>Сума за плащане</th>
                        <th>Записи</th>
                        <th>Дата на добавяне</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($history as $item){ ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td><?= $item['provider_name'] ?></td>
                                <td><?= $item['business_name'] ?></td>
                                <td><?= $item['invoice_number'] ?></td>
                                <td><?= $item['invoice_date'] ?></td>
                                <td><?= $item['payment_amount'] ?></td>
                                <td><?= $item['items'] ?> (<?= $item['entities'] ?>)</td>
                                <td><?= $item['created_at'] ?></td>
                                <td>
                                    <a href="<?= base_url('purchase-by-document/view/' . $item['id']) ?>" class="btn btn-primary btn-icon-split btn-sm mr-3">
                                        <span class="icon text-white-50"><i class="fas fa-list"></i></span>
                                        <span class="text">Преглед</span>
                                    </a>

                                    <?php if((int)$item['provider_id'] !== 3){ ?>
                                        <a href="<?= base_url('purchase-by-document/download/' . $item['id']) ?>" target="_blank" class="btn btn-primary btn-icon-split btn-sm mr-3">
                                            <span class="icon text-white-50"><i class="fas fa-download"></i></span>
                                            <span class="text">Изтегляне</span>
                                        </a>
                                    <?php } ?>

                                    <a href="#" data-id="<?= $item['id'] ?>" class="btn btn-danger btn-icon-split btn-sm delete-vpj">
                                        <span class="icon text-white-50"><i class="fas fa-trash"></i></span>
                                        <span class="text">Изтриване</span>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<?=$this->endSection()?>