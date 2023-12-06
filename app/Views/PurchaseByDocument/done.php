<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="row">
        <div class="col-12">

            <div class="alert alert-success" role="alert">
                <strong><?= $parsedDataStatistics['success'] ?></strong> <?= $parsedDataStatistics['success'] === 1 ? 'документ беше добавен' : 'документа бяха добавени' ?> успешно!
            </div>


            <div class="mb-3">
                <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary">Обратно</a>
            </div>
        </div>
    </div>

<?=$this->endSection()?>