<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Преглед на добавените фактури</h1>
    <?php if(count($parsedData) > 1){ ?>
        <p class="mb-4">Добавени са <strong><?= count($parsedData); ?></strong> документ за покупка - доставчик <strong>Фьоникс Фарма</strong></p>
    <?php }else{ ?>
        <p class="mb-4">Добавен е <strong>1</strong> документ за покупка - доставчик <strong>Фьоникс Фарма</strong></p>
    <?php } ?>


    <div class="row">
        <div class="col-12">
            <?php if($parsedDataStatistics['errors'] === 0){ ?>
                <div class="alert alert-success" role="alert">
                    Всички документ за покупка са валидни
                </div>
            <?php }elseif($parsedDataStatistics['total'] > $parsedDataStatistics['errors']){ ?>
                <div class="alert alert-warning" role="alert">
                    Има невалидни документ за покупка
                </div>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">
                    Няма валидни документ за покупка !
                </div>
            <?php } ?>
        </div>
    </div>




        <?php foreach ($parsedData as $parsedDataKey => $data){ ?>

            <div class="card shadow mb-5">
                <div class="card-header py-3">
                    <h5>
                        <?= $data['type'] === 'file' ? 'ФАЙЛ:' : 'ТЕКСТ' ?>
                        <?= $data['name'] ?>
                        <small>(общо количество: <?= $data['parsed']['itemsCount'] ?> бр.)</small>

                        <a href="<?= base_url("/purchase-by-document/fioniks-farma-add-debug/?item={$parsedDataKey}") ?>" style="float: right;" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-fw fa-bug"></i> </a>
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
                                    <td style="width: 33%">
                                        <h5 class="font-weight-bold">Получател</h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>Име:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['name'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Адрес:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['address'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Ид.н.допк:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['idNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Ид.н.зддс:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['vatNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Лиценз:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['license'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Л.опиати:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['opiatesLicense'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>МОЛ:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['mol'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Телефон:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['phone'] ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 33%">
                                        <h5 class="font-weight-bold">Доставчик</h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>Име:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['name'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Адрес:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['address'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Ид.н.допк:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['idNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Ид.н.зддс:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['vatNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Лиценз:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['license'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Л.опиати:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['opiatesLicense'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>МОЛ:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['mol'] ?></td>
                                            </tr>

                                        </table>
                                    </td>
                                    <td style="width: 33%">
                                        <h5>&nbsp;</h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>Ср.на доставка:</td>
                                                <td class="font-weight-bold"><?= !empty($data['parsed']['delivery']['result']['averageDelivery']) ? date('d.m.y', strtotime($data['parsed']['delivery']['result']['averageDelivery'])) : '' ?></td>
                                            </tr>
                                            <tr>
                                                <td>Място:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['delivery']['result']['place'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Адрес:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['delivery']['result']['address'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Маршрут:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['delivery']['result']['route'] ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <h5 class="font-weight-bold">ФАКТУРА <?= (int) $data['parsed']['nzok'] === 1 ? 'ПО НЗОК' : '' ?></h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>NO.</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['invoiceInfo']['result']['number'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Дата на издаване:</td>
                                                <td class="font-weight-bold"><?= !empty($data['parsed']['invoiceInfo']['result']['date']) ? date('d.m.y', strtotime($data['parsed']['invoiceInfo']['result']['date'])) : '' ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" >
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-right">N</th>
                                                    <?php if((int) $data['parsed']['nzok'] === 1){ ?>
                                                        <th>Код</th>
                                                    <?php } ?>
                                                    <th>Наименование</th>
                                                    <th>М-ка</th>
                                                    <th class="text-right">Кол.</th>
                                                    <th class="text-right">Баз.цен</th>
                                                    <th class="text-right">ТН</th>
                                                    <th class="text-right">ТО</th>
                                                    <th class="text-right">Цена ТЕ</th>
                                                    <th class="text-right">Стойност</th>
                                                    <th class="text-right">Ц.с ддс</th>
                                                    <th>Партида</th>
                                                    <th>Cертификат</th>
                                                    <th>Ср.г.</th>
                                                    <th class="text-right">Ц.Апт.</th>
                                                    <th class="text-right">Пред.цена</th>
                                                    <th>Пред.цена тип</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['parsed']['invoiceItems']['result'] as $itemKey => $item){ ?>
                                                    <tr>
                                                        <td class="text-right"><?= $item['itemNumber'] ?></td>
                                                        <?php if((int) $data['parsed']['nzok'] === 1){ ?>
                                                            <td><?= $item['code'] ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <?= $item['designation'] ?>
                                                            <?= !empty($item['INN']) ? "<br /><small>{$item['INN']}</small>" : '' ?>
                                                        </td>
                                                        <td><?= $item['manufacturer'] ?></td>
                                                        <td class="text-right"><?= $item['quantity'] ?></td>
                                                        <td class="text-right"><?= $item['value'] > 0 ? $item['basePrice'] : '' ?></td>
                                                        <td class="text-right"><?= $item['value'] > 0 ? $item['tradeMarkup'] : '' ?></td>
                                                        <td class="text-right"><?= $item['value'] > 0 ? $item['tradeDiscount'] : '' ?></td>
                                                        <td class="text-right"><?= $item['value'] > 0 ? $item['wholesalerPrice'] : '' ?></td>
                                                        <td class="text-right"><?= $item['value'] ?></td>
                                                        <td class="text-right"><?= $item['value'] > 0 ? $item['priceWithVAT'] : '' ?></td>
                                                        <td><?= $item['batch'] ?></td>
                                                        <td><?= $item['certificate'] ?></td>
                                                        <td><?= $item['expiryDate'] ?></td>
                                                        <td class="text-right"><?= $item['value'] > 0 ? $item['pharmacyPrice'] : '' ?></td>
                                                        <td class="text-right"><?= $item['value'] > 0 ? $item['limitPrice'] : '' ?></td>
                                                        <td><?= $item['limitPriceType'] ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <div class="row">
                                            <div class="col-6">
                                                <table class="table table-bordered purchase-by-document-sub-table">
                                                    <tr>
                                                        <td>ОБЩА СТОЙНОСТ</td>
                                                        <td class="font-weight-bold">
                                                            <?= $data['parsed']['invoicePrice']['result']['totalPrice'] ?>
                                                            <span style="float: right;"><?= $data['parsed']['invoicePrice']['result']['totalPriceFromSupplier'] ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>ТЪРГОВСКА ОТСТЪПКА:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tradeDiscount'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>ДАНЪЧНА ОСНОВА ЗА  9 % ДДС:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['taxBase9'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>НАЧИСЛЕН ДДС ЗА  9 % ДДС:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tax9'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>ДАНЪЧНА ОСНОВА ЗА 20 % ДДС:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['taxBase20'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>НАЧИСЛЕН ДДС ЗА 20 % ДДС:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tax20'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>ДАНЪЧНА ОСНОВА ЗА  0 % ДДС:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['taxBase0'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>СУМА ЗА ПЛАЩАНЕ:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['totalPriceWithTax'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Словом:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['totalPriceWithTaxInWords'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Забележка:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['note'] ?></td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div class="col-6">
                                                <table class="table table-bordered purchase-by-document-sub-table">
                                                    <tr>
                                                        <td>Плащане</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePayment']['result']['paymentInfo'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Дата на плащане</td>
                                                        <td class="font-weight-bold">
                                                            <?= !empty($data['parsed']['invoicePayment']['result']['paymentDate']) ? date('d.m.y', strtotime($data['parsed']['invoicePayment']['result']['paymentDate'])) : '' ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Дата на данъчно събитие</td>
                                                        <td class="font-weight-bold">
                                                            <?= !empty($data['parsed']['invoicePayment']['result']['taxEventDate']) ? date('d.m.y', strtotime($data['parsed']['invoicePayment']['result']['taxEventDate'])) : '' ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Раэплащателна с-ка BIC</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePayment']['result']['payerBIC'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>IBAN</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePayment']['result']['payerIBAN'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Банка</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePayment']['result']['payerBank'] ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

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