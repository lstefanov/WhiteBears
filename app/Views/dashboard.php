<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="row">
        <div class="col-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Дневници за покупки по ЗДДС</h6>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('/vat-purchase-journals/add') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-upload"></i></span>
                        <span class="text">Добавяне на справка</span>
                    </a>

                    <br /><br />

                    <a href="<?= base_url('vat-purchase-journals/history') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-list"></i></span>
                        <span class="text">Списък</span>
                    </a>

                    <br /><br />

                    <a href="<?= base_url('vat-purchase-journals/export/view') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-download"></i></span>
                        <span class="text">Изтегляне</span>
                    </a>

                    <br /><br />

                    <a href="<?= base_url('vat-purchase-journals/export-aster/view') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-download"></i></span>
                        <span class="text">Дневник на продажбите на Астер Русе</span>
                    </a>

                </div>
            </div>

        </div>

        <div class="col-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Покупка по документ</h6>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('/purchase-by-document/add') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-upload"></i></span>
                        <span class="text">Добавяне на документ</span>
                    </a>

                    <br /><br />

                    <a href="<?= base_url('purchase-by-document/history') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-list"></i></span>
                        <span class="text">Списък</span>
                    </a>
                </div>
            </div>

        </div>

        <div class="col-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Разплащане по фактури</h6>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-secondary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50"><i class="fas fa-ellipsis-h"></i></span>
                        <span class="text">разработва се</span>
                    </a>
                </div>
            </div>

        </div>


        <div class="col-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Сътрудници</h6>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('partners/providers') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-list"></i></span>
                        <span class="text">Доставчици</span>
                    </a>

                    <br /><br />

                    <a href="<?= base_url('partners/businesses') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-list"></i></span>
                        <span class="text">Фирми</span>
                    </a>

                    <br /><br />

                    <a href="<?= base_url('partners/companies') ?>" class="btn btn-primary btn-icon-split btn-lg" style="display: block; text-align: left;">
                        <span class="icon text-white-50" style="float: left;"><i class="fas fa-list"></i></span>
                        <span class="text">Обекти</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

<?=$this->endSection()?>