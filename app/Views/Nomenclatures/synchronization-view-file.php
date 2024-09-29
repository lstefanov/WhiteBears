<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>Преглед на синхронизиращ файл</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Група</th>
                        <th>Име на лекарствения продукт</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($data as $counter => $item){ ?>
                        <tr>
                            <td><?= $counter + 1 ?></td>
                            <td><?= $item['code_name'] ?></td>
                            <td><?= $item['name'] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<?=$this->endSection()?>