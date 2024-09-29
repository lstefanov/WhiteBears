<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5 style="float: left; margin-right: 100px;">Синхронизация на лекарства</h5>
            <a href="<?= base_url('nomenclatures/add-sync-file') ?>" type="button" class="btn bt-sm btn-primary" style="float: right; margin-right 10px;">Добавяне на синхронизиращ файл</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата на добавяне</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($data as $item){ ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= $item['date'] ?></td>
                            <td>
                                <a href="<?= base_url('nomenclatures/synchronization-view-file/' . $item['id']) ?>" class="btn btn-primary btn-icon-split btn-sm mr-3">
                                    <span class="icon text-white-50"><i class="fas fa-list"></i></span>
                                    <span class="text">Преглед</span>
                                </a>

                                <a href="<?= base_url('nomenclatures/synchronization-download-file/' . $item['id']) ?>" target="_blank" class="btn btn-primary btn-icon-split btn-sm mr-3">
                                    <span class="icon text-white-50"><i class="fas fa-download"></i></span>
                                    <span class="text">Изтегляне</span>
                                </a>

                                <a href="#" data-id="<?= $item['id'] ?>" class="btn btn-danger btn-icon-split btn-sm delete-sync-file">
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