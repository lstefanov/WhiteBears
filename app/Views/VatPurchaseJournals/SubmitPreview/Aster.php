<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Преглед на добавените фактури</h1>
    <?php if(count($parsedData) > 1){ ?>
        <p class="mb-4">Добавени са <strong><?= count($parsedData); ?></strong> фактури за доставчик <strong>Астер Русе ЕООД</strong></p>
    <?php }else{ ?>
        <p class="mb-4">Добавена е <strong>1</strong> фактура за доставчик <strong>Астер Русе ЕООД</strong></p>
    <?php } ?>


    <div class="row">
        <div class="col-12">
            <?php if($parsedDataStatistics['errors'] === 0){ ?>
                <div class="alert alert-success" role="alert">
                    Всички фактури са валидни
                </div>
            <?php }elseif($parsedDataStatistics['total'] > $parsedDataStatistics['errors']){ ?>
                <div class="alert alert-warning" role="alert">
                    Има невалидни фактури
                </div>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">
                    Няма валидни фактури за добавяне !
                </div>
            <?php } ?>
        </div>
    </div>


    <?php foreach ($parsedData as $data){ ?>

        <div class="card shadow mb-5">
            <div class="card-header py-3">
                <h5><?= $data['fileName'] ?></h5>
                <h6 class="m-0 font-weight-bold text-primary">
                    <?php if(!empty($data['errors'])){ ?>
                        <span class="text-danger"><?= count($data['errors']) > 1 ? 'Грешки' : 'Грешка' ?></span>
                        <ul style="margin-bottom: 0;">
                            <?php foreach ($data['errors'] as $error){ ?>
                                <li><?= $error ?></li>
                            <?php } ?>
                        </ul>
                    <?php }else{ ?>
                        <span class="text-success">Успешно</span>
                        <small style="margin-left: 10px; color: #666;">(
                            общо: <strong style="color: #000;"><?= $data['entities_statistics']['total'] ?></strong> |
                            успешни: <strong class="text-success"><?= $data['entities_statistics']['success'] ?></strong> |
                            грешни: <strong class="text-danger"><?= $data['entities_statistics']['error'] ?></strong>
                            )</small>
                    <?php } ?>
                </h6>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Ред</th>
                            <th>Вид на документа</th>
                            <th>Номер на документа</th>
                            <th>Дата на издаване</th>
                            <th>ЕИК</th>
                            <th>Контрагент</th>
                            <th>Предмет на сделката</th>
                            <th>Обща с-ст на сделката (вкл. ДДС)</th>
                            <th>Ст-ст на облаг. сделки</th>
                            <th>ДДС</th>
                            <th>Ст-ст по пок. цени</th>
                            <?php if(empty($data['errors']) || $data['errorType'] === 2){ ?>
                                <th>Валидация</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['parsedData'] as $parsedDataKey => $parsedDataValue){ ?>
                            <?php if($parsedDataKey < 7 || count($data['parsedData']) === $parsedDataKey){ continue; } ?>
                            <tr>
                                <td><?= $parsedDataKey ?></td>
                                <td><?= $parsedDataValue['B'] ?></td>
                                <td>
                                    <?php
                                        $invoiceNumbers1 = $parsedDataValue['D'];
                                        $invoiceNumbers2 = $parsedDataValue['E'];

                                        if(!empty($invoiceNumbers1)){
                                            $check = explode('/', $invoiceNumbers1);
                                            $invoiceNumbers = trim($check[0]);
                                        }else{
                                            $invoiceNumbers = $invoiceNumbers2;
                                        }
                                        echo $invoiceNumbers;
                                    ?>
                                </td>
                                <td><?= $parsedDataValue['F'] ?></td>
                                <td><?= $parsedDataValue['H'] ?></td>
                                <td><?= $parsedDataValue['I'] ?></td>
                                <td><?= $parsedDataValue['L'] ?></td>
                                <td><?= $parsedDataValue['M'] ?></td>
                                <td><?= $parsedDataValue['N'] ?></td>
                                <td><?= $parsedDataValue['O'] ?></td>
                                <td><?= $parsedDataValue['P'] ?></td>
                                <?php if(empty($data['errors']) || $data['errorType'] === 2){ ?>
                                    <td>
                                        <?php if($parsedDataValue['status'] === 'success'){ ?>
                                            <span class="text-success">Успешна</span>
                                        <?php }else{ ?>
                                            <span class="text-danger"><?= implode('<br>', $parsedDataValue['status_details']) ?></span>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php } ?>


    <div class="row mb-5">
        <div class="col-12">
            <?php if($parsedDataStatistics['total'] > $parsedDataStatistics['errors']){ ?>
                <a href="<?= base_url('/vat-purchase-journals/finish') ?>" class="btn btn-primary" id="finish-btn">Завършване</a>
            <?php } else { ?>
                <a href="<?= base_url('/vat-purchase-journals/add') ?>" class="btn btn-primary">Обратно</a>
            <?php } ?>
        </div>
    </div>


<?=$this->endSection()?>