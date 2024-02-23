<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Преглед на добавените фактури</h1>
    <?php if(count($parsedData) > 1){ ?>
        <p class="mb-4">Добавени са <strong><?= count($parsedData); ?></strong> документ за покупка - доставчик <strong>Стинг</strong></p>
    <?php }else{ ?>
        <p class="mb-4">Добавен е <strong>1</strong> документ за покупка - доставчик <strong>Стинг</strong></p>
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

                        <a href="<?= base_url("/purchase-by-document/sting-add-debug/?item={$parsedDataKey}") ?>" style="float: right;" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-fw fa-bug"></i> </a>
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
                                                <td>Клиент:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['name'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Адрес:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['address'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Адрес. Рег.:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['address_reg'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Идент. номер ЗДДС:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['vatNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Идент. номер ДОПК:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['idNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Разрешително:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['license'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Лиц. наркотици:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['opiatesLicense'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Фирма:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['mol'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Телефон:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['phone'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>ID Клиент:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['recipient']['result']['client_id'] ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 33%">
                                        <h5 class="font-weight-bold">Доставчик</h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>Доставчик:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['name'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Адрес:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['address'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Идент. номер ЗДДС:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['vatNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Идент. номер ДОПК:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['idNumber'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Разрешително:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['license'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Лиц. наркотици:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['opiatesLicense'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Контролиращо лице:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['supplier']['result']['controlling_person'] ?></td>
                                            </tr>

                                        </table>
                                    </td>
                                    <td style="width: 33%">
                                        <h5>&nbsp;</h5>
                                        <table class="table table-bordered purchase-by-document-sub-table">
                                            <tr>
                                                <td>Отг.маг. фармацевт:</td>
                                                <td class="font-weight-bold"><?= $data['parsed']['delivery']['result']['pharmacistInCharge'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Дата:</td>
                                                <td class="font-weight-bold"><?= !empty($data['parsed']['delivery']['result']['date']) ? date('d/m/Y H:i', strtotime($data['parsed']['delivery']['result']['date'])) : '' ?></td>
                                            </tr>
                                            <tr>
                                                <td>Място на сделката:</td>
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
                                                <td class="font-weight-bold"><?= !empty($data['parsed']['invoiceInfo']['result']['date']) ? date('d/m/y', strtotime($data['parsed']['invoiceInfo']['result']['date'])) : '' ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" >
                                        <?php
                                            if($data['invoiceType'] === 1){
                                                include( ROOTPATH . 'app/Views/PurchaseByDocument/SubmitPreview/Sting/Assets/Items.php');
                                            } else {

                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <div class="row">
                                            <div class="col-6">

                                                <?php
                                                    if($data['invoiceType'] === 1){
                                                        include( ROOTPATH . 'app/Views/PurchaseByDocument/SubmitPreview/Sting/Assets/InvoicePrice.php');
                                                    } else {

                                                    }
                                                ?>
                                            </div>

                                            <div class="col-6">
                                                <table class="table table-bordered purchase-by-document-sub-table">
                                                    <tr>
                                                        <td>Плащане</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePayment']['result']['paymentInfo'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Падеж</td>
                                                        <td class="font-weight-bold">
                                                            <?= !empty($data['parsed']['invoicePayment']['result']['taxEventDate']) ? date('d/m/y', strtotime($data['parsed']['invoicePayment']['result']['taxEventDate'])) : '' ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>BIC код</td>
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
                                                    <tr>
                                                        <td>Док.номер:</td>
                                                        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['docNumber'] ?></td>
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