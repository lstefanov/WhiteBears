<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Преглед на добавените фактури</h1>
    <?php if(count($parsedData) > 1){ ?>
        <p class="mb-4">Добавени са <strong><?= count($parsedData); ?></strong> документ за покупка - доставчик <strong>Астер Русе ЕООД</strong></p>
    <?php }else{ ?>
        <p class="mb-4">Добавен е <strong>1</strong> документ за покупка - доставчик <strong>Стинг</strong></p>
    <?php } ?>


    <div class="row">
        <div class="col-12">
            <?php if($parsedDataStatistics['errors'] === 0){ ?>
                <div class="alert alert-success" role="alert">
                    Всички документи за покупка са валидни
                </div>
            <?php }elseif($parsedDataStatistics['total'] > $parsedDataStatistics['errors']){ ?>
                <div class="alert alert-warning" role="alert">
                    Има невалидни документи за покупка
                </div>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">
                    Няма валидни документи за покупка !
                </div>
            <?php } ?>
        </div>
    </div>



        <?php $counter = 0; ?>
        <?php foreach ($parsedData as $parsedDataKey => $data){ ?>
            <?php $counter++; ?>
            <div class="card shadow mb-5">
                <div class="card-header py-3">
                    <h5>
                        <?= $counter ?>. &nbsp; N:<?= $data['parsed']['invoice_number'] ?>
                        <small>(общо количество: <?= $data['parsed']['itemsCount'] ?> бр.)</small>

                        <a href="<?= base_url("/purchase-by-document/aster-add-debug/?item={$parsedDataKey}") ?>" style="float: right;" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-fw fa-bug"></i> </a>
                    </h5>
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?php if(!empty($data['error'])){ ?>
                            <span class="text-danger">Грешка</span>

                            <ul style="margin-bottom: 0;">
                                <li><?= $data['error'] ?></li>
                            </ul>
                        <?php }else{ ?>
                            <span class="text-success">Успешно</span>
                        <?php } ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td style="width: 50%">
                                        <h5 class="font-weight-bold">Получател</h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>Клиент:</td>
                                                <td class="font-weight-bold">
                                                    <?= $data['parsed']['company_name'] ?>
                                                    <?php if(!empty($data['parsed']['founded_company_name'])){ ?>
                                                        (<?= $data['parsed']['founded_company_name'] ?>)
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Адрес:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['address'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Фирма:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['founded_business_name'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Идент. номер:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['founded_business_in_number'] ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 50%">
                                        <h5 class="font-weight-bold">ФАКТУРА</h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>NO.</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['invoice_number'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Дата на издаване:</td>
                                                <td class="font-weight-bold"><?= date('d.n.Y', strtotime($data['parsed']['invoice_date'])) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Стойност на продаденото количество без ДДС:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['totalPrice'] ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" >
                                        <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                                            <thead>
                                            <tr>
                                                <th>Продуктов код</th>
                                                <th>Име на лекарствения продукт</th>
                                                <th>Код на аптеката</th>
                                                <th>Продадено количество</th>
                                                <th>Стойност на продаденото количество без ДДС</th>
                                                <th>Единична цена без ддс</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($data['parsed']['items'] as $item){ ?>
                                                <tr>
                                                    <td><?= $item['product_code'] ?></td>
                                                    <td><?= $item['product_name'] ?></td>
                                                    <td><?= $item['pharmacy_code'] ?></td>
                                                    <td><?= $item['quantity'] ?></td>
                                                    <td><?= $item['totalValue'] ?></td>
                                                    <td><?= $item['price_per_item'] ?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php } ?>



    <div class="row mb-5">
        <div class="col-12">
            <?php if($parsedDataStatistics['total'] > $parsedDataStatistics['errors']){ ?>
                <a href="<?= base_url('/purchase-by-document/finish') ?>" class="btn btn-primary" id="finish-btn">Завършване</a>
            <?php } else { ?>
                <a href="<?= base_url('/purchase-by-document/add') ?>" class="btn btn-primary">Обратно</a>
            <?php } ?>
        </div>
    </div>


<?=$this->endSection()?>