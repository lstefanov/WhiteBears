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

                            <?php
                                if((int)$data['data']['document_type'] === 1){
                                    include( ROOTPATH . 'app/Views/PurchaseByDocument/History/FioniksFarma/Assets/Items.php');
                                } else {
                                    include( ROOTPATH . 'app/Views/PurchaseByDocument/History/FioniksFarma/Assets/ItemsSubscription.php');
                                }
                            ?>

                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center">
                            <div class="row">
                                <div class="col-6">

                                    <?php
                                        if((int)$data['data']['document_type'] === 1){
                                            include( ROOTPATH . 'app/Views/PurchaseByDocument/History/FioniksFarma/Assets/InvoicePrice.php');
                                        } else {
                                            include( ROOTPATH . 'app/Views/PurchaseByDocument/History/FioniksFarma/Assets/InvoicePriceSubscription.php');
                                        }
                                    ?>

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