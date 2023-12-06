<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>
                <?= $data['data']['source_type'] === 'file' ? 'ФАЙЛ:' : 'ТЕКСТ' ?>
                <?= $data['data']['source_name'] ?>
                <small>(Брой: <?= $data['data']['items'] ?>, общо количество: <?= $data['data']['entities'] ?> бр.)</small>

                <a href="<?= base_url('purchase-by-document/download/' . $data['data']['id']) ?>" style="float: right;" target="_blank" class="btn btn-secondary btn-sm">изтегляне</a>
            </h5>
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
                                    <td class="font-weight-bold"><?= $data['recipient']['name'] ?></td>
                                </tr>
                                <tr>
                                    <td>Адрес:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['address'] ?></td>
                                </tr>
                                <tr>
                                    <td>Ид.н.допк:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['in_number'] ?></td>
                                </tr>
                                <tr>
                                    <td>Ид.н.зддс:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['vat_number'] ?></td>
                                </tr>
                                <tr>
                                    <td>Лиценз:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['license'] ?></td>
                                </tr>
                                <tr>
                                    <td>Л.опиати:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['opiates_license'] ?></td>
                                </tr>
                                <tr>
                                    <td>МОЛ:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['mol'] ?></td>
                                </tr>
                                <tr>
                                    <td>Телефон:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['phone'] ?></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 33%">
                            <h5 class="font-weight-bold">Доставчик</h5>
                            <table class="table table-bordered purchase-by-document-sub-table">
                                <tr>
                                    <td>Име:</td>
                                    <td class="font-weight-bold"><?= $data['supplier']['name'] ?></td>
                                </tr>
                                <tr>
                                    <td>Адрес:</td>
                                    <td class="font-weight-bold"><?= $data['supplier']['address'] ?></td>
                                </tr>
                                <tr>
                                    <td>Ид.н.допк:</td>
                                    <td class="font-weight-bold"><?= $data['supplier']['in_number'] ?></td>
                                </tr>
                                <tr>
                                    <td>Ид.н.зддс:</td>
                                    <td class="font-weight-bold"><?= $data['supplier']['vat_number'] ?></td>
                                </tr>
                                <tr>
                                    <td>Лиценз:</td>
                                    <td class="font-weight-bold"><?= $data['supplier']['license'] ?></td>
                                </tr>
                                <tr>
                                    <td>Л.опиати:</td>
                                    <td class="font-weight-bold"><?= $data['supplier']['opiates_license'] ?></td>
                                </tr>
                                <tr>
                                    <td>МОЛ:</td>
                                    <td class="font-weight-bold"><?= $data['supplier']['mol'] ?></td>
                                </tr>

                            </table>
                        </td>
                        <td style="width: 33%">
                            <h5>&nbsp;</h5>
                            <table class="table table-bordered purchase-by-document-sub-table">
                                <tr>
                                    <td>Ср.на доставка:</td>
                                    <td class="font-weight-bold"><?= !empty($data['delivery']['average_delivery']) ? date('d.m.y', strtotime($data['delivery']['average_delivery'])) : '' ?></td>
                                </tr>
                                <tr>
                                    <td>Място:</td>
                                    <td class="font-weight-bold"><?= $data['delivery']['place'] ?></td>
                                </tr>
                                <tr>
                                    <td>Адрес:</td>
                                    <td class="font-weight-bold"><?= $data['delivery']['address'] ?></td>
                                </tr>
                                <tr>
                                    <td>Маршрут:</td>
                                    <td class="font-weight-bold"><?= $data['delivery']['route'] ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center">
                            <h5 class="font-weight-bold">ФАКТУРА <?= (int) $data['data']['nzok'] === 1 ? 'ПО НЗОК' : '' ?></h5>
                            <table class="table table-bordered purchase-by-document-sub-table">
                                <tr>
                                    <td>NO.</td>
                                    <td class="font-weight-bold"><?= $data['data']['invoice_number'] ?></td>
                                </tr>
                                <tr>
                                    <td>Дата на издаване:</td>
                                    <td class="font-weight-bold"><?= !empty($data['data']['invoice_date']) ? date('d.m.y', strtotime($data['data']['invoice_date'])) : '' ?></td>
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
                                    <?php if((int) $data['data']['nzok'] === 1){ ?>
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
                                <?php foreach ($data['invoice_items'] as $itemKey => $item){ ?>
                                    <tr>
                                        <td class="text-right"><?= $item['number'] ?></td>
                                        <?php if((int) $data['data']['nzok'] === 1){ ?>
                                            <td><?= $item['code'] ?></td>
                                        <?php } ?>
                                        <td>
                                            <?= $item['designation'] ?>
                                            <?= !empty($item['inn']) ? "<br /><small>{$item['inn']}</small>" : '' ?>
                                        </td>
                                        <td><?= $item['manufacturer'] ?></td>
                                        <td class="text-right"><?= $item['quantity'] ?></td>
                                        <td class="text-right"><?= $item['value'] > 0 ? $item['base_price'] : '' ?></td>
                                        <td class="text-right"><?= $item['value'] > 0 ? $item['trade_markup'] : '' ?></td>
                                        <td class="text-right"><?= $item['value'] > 0 ? $item['trade_discount'] : '' ?></td>
                                        <td class="text-right"><?= $item['value'] > 0 ? $item['wholesaler_price'] : '' ?></td>
                                        <td class="text-right"><?= $item['value'] ?></td>
                                        <td class="text-right"><?= $item['value'] > 0 ? $item['price_with_vat'] : '' ?></td>
                                        <td><?= $item['batch'] ?></td>
                                        <td><?= $item['certificate'] ?></td>
                                        <td><?= $item['expiry_date'] ?></td>
                                        <td class="text-right"><?= $item['value'] > 0 ? $item['pharmacy_price'] : '' ?></td>
                                        <td class="text-right"><?= $item['value'] > 0 ? $item['limit_price'] : '' ?></td>
                                        <td><?= $item['limit_price_type'] ?></td>
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
                                                <?= $data['invoice_price']['total_price'] ?>
                                                <span style="float: right;"><?= $data['invoice_price']['total_price_from_supplier'] ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ТЪРГОВСКА ОТСТЪПКА:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['trade_discount'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>ДАНЪЧНА ОСНОВА ЗА  9 % ДДС:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['tax_base_9'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>НАЧИСЛЕН ДДС ЗА  9 % ДДС:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['tax_9'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>ДАНЪЧНА ОСНОВА ЗА 20 % ДДС:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['tax_base_20'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>НАЧИСЛЕН ДДС ЗА 20 % ДДС:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['tax_20'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>ДАНЪЧНА ОСНОВА ЗА  0 % ДДС:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['tax_base_0'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>СУМА ЗА ПЛАЩАНЕ:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['total_price_with_tax'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Словом:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['total_price_with_tax_in_words'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Забележка:</td>
                                            <td class="font-weight-bold"><?= $data['invoice_price']['note'] ?></td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-6">
                                    <table class="table table-bordered purchase-by-document-sub-table">
                                        <tr>
                                            <td>Плащане</td>
                                            <td class="font-weight-bold"><?= $data['invoice_payment']['payment_info'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Дата на плащане</td>
                                            <td class="font-weight-bold">
                                                <?= !empty($data['invoice_payment']['payment_date']) ? date('d.m.y', strtotime($data['invoice_payment']['payment_date'])) : '' ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Дата на данъчно събитие</td>
                                            <td class="font-weight-bold">
                                                <?= !empty($data['invoice_payment']['tax_event_date']) ? date('d.m.y', strtotime($data['invoice_payment']['tax_event_date'])) : '' ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Раэплащателна с-ка BIC</td>
                                            <td class="font-weight-bold"><?= $data['invoice_payment']['payer_bic'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>IBAN</td>
                                            <td class="font-weight-bold"><?= $data['invoice_payment']['payer_iban'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Банка</td>
                                            <td class="font-weight-bold"><?= $data['invoice_payment']['payer_bank'] ?></td>
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


<?=$this->endSection()?>