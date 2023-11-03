<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>Списък на доставчиците</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Доставчик</th>
                        <th>Обслужвани фирми</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($providers as $provider){ ?>
                        <tr>
                            <td><?= $provider['id'] ?></td>
                            <td><?= $provider['name'] ?></td>
                            <td>
                                <a class="badge badge-info" href="<?= base_url("partners/businesses??provider_id={$provider['id']}") ?>">
                                    <?= count($provider['businesses']) ?>
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