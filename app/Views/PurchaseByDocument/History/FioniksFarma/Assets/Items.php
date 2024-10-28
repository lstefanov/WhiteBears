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
        <th class="text-right">Баз.цена</th>
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
            <td class="editable">
                <span data-item-id="<?= $item['id'] ?>" data-type="name"><?= $item['designation'] ?></span>
                <span class="edit-icon" data-provider-id="2" data-item-id="<?= $item['id'] ?>" data-item-name="<?= addslashes($item['designation']) ?>"><i class="fas fa-edit"></i></span>

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