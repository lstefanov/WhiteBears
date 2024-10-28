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
                        <td style="width: 50%">
                            <h5 class="font-weight-bold">Получател</h5>
                            <table class="table table-bordered purchase-by-document-sub-table">
                                <tr>
                                    <td>Клиент:</td>
                                    <td class="font-weight-bold">
                                        <?= $data['recipient']['company_name'] ?>
                                        <?php if(!empty($data['recipient']['company_name_real'])){ ?>
                                            (<?= $data['recipient']['company_name_real'] ?>)
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Адрес:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['address'] ?></td>
                                </tr>
                                <tr>
                                    <td>Фирма:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['business_name'] ?></td>
                                </tr>
                                <tr>
                                    <td>Идент. номер:</td>
                                    <td class="font-weight-bold"><?= $data['recipient']['business_in_number'] ?></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 50%">
                            <h5 class="font-weight-bold">ФАКТУРА</h5>
                            <table class="table table-bordered purchase-by-document-sub-table">
                                <tr>
                                    <td>NO.</td>
                                    <td class="font-weight-bold"><?= $data['data']['invoice_number'] ?></td>
                                </tr>
                                <tr>
                                    <td>Дата на издаване:</td>
                                    <td class="font-weight-bold"><?= date('d.n.Y', strtotime($data['data']['invoice_date'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Стойност на продаденото количество без ДДС:</td>
                                    <td class="font-weight-bold"><?= $data['data']['payment_amount'] ?></td>
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
                                <?php foreach($data['invoice_items'] as $item){ ?>
                                    <tr>
                                        <td><?= $item['product_code'] ?></td>
                                        <td class="editable">
                                            <span data-item-id="<?= $item['id'] ?>" data-type="name"><?= $item['product_name'] ?></span>
                                            <span class="edit-icon" data-provider-id="3" data-item-id="<?= $item['id'] ?>" data-item-name="<?= addslashes($item['product_name']) ?>"><i class="fas fa-edit"></i></span>
                                        </td>
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

<?php include( ROOTPATH . 'app/Views/PurchaseByDocument/History/EditItemModal.php'); ?>

<?=$this->endSection()?>