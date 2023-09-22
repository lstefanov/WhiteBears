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
                        <th>No Док</th>
                        <th>Тип Док</th>
                        <th>Дата на Док</th>
                        <th>Начин на плащане</th>
                        <th>Сума с ДДС</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($vpjStingEntities as $item) { ?>
                            <tr>
                                <td><?= $item['doc_n'] ?></td>
                                <td><?= $item['doc_type'] ?></td>
                                <td><?= $item['doc_date'] ?></td>
                                <td><?= $item['doc_date'] ?></td>
                                <td><?= $item['payment_summary'] ?></td>
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