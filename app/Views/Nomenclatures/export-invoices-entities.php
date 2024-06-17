<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5 style="float: left; margin-right: 100px;">Експортиране на лекарства</h5>

            <div style="float: left; margin-right: 20px;">
                <span style="float: left; padding-right: 10px; padding-top: 4px;">Дата:</span>
                <span style="float: left; padding-right: 5px; padding-top: 3px;">от:</span> <input type="text" id="date-from" class="form-control" value="<?= $dateFrom ?>" style="width: 120px; float: left; margin-right: 10px;" />
                <span style="float: left;  padding-right: 5px; padding-top: 3px;">до:</span> <input type="text" id="date-to" class="form-control" value="<?= $dateTo ?>" style="width: 120px; float: left; margin-right: 20px;" />
            </div>

            <button type="button" class="btn bt-sm btn-success" style="float: left; padding: 7px 10px; font-size: 14px;" id="view-btn">Преглед</button>

            <button type="button" class="btn bt-sm btn-primary" style="float: right; margin-right 10px;" id="export-date-filter-btn">Експортиране</button>
        </div>
        <div class="card-body">
            <?php if($preview === 0){ ?>
                <div class="alert alert-warning" role="alert">
                    Моля изберете дата !
                </div>
            <?php }else{ ?>
                <div class="table-responsive">
                <table class="table table-bordered dataTable data-table-export" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>N</th>
                        <th>Име на лекарствения продукт</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($data as $counter => $item) { ?>
                        <tr>
                            <td><?=( $counter + 1) ?></td>
                            <td><?= mb_strtoupper($item) ?></td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>



<?=$this->endSection()?>