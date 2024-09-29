<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>Добавяне на синхронизиращ файл</h5>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url('/nomenclatures/add-sync-file-submit') ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="mb-2 mt-1">
                    <label for="businesses" class="form-label">Файл:</label>
                    <input class="form-control" type="file" id="files" name="file" accept=".xlsx" />
                    <small>Позволени файлови формати: <span id="accepted-files-info" style="font-weight: bold;">.xlsx</span></small>
                </div>

                <hr />

                <div class="mb-3 mt-5">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Обработка</button>
                </div>
            </form>
        </div>
    </div>


<?=$this->endSection()?>