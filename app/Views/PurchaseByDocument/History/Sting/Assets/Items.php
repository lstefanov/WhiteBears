<table class="table table-bordered">
    <thead>
    <tr>
        <th class="text-right">&nbsp;</th>
        <th>Лекарствено средство</th>
        <th>Оп</th>
        <th class="text-right">Серия</th>
        <th class="text-right">Колич.</th>
        <th class="text-right">Ср.Г.</th>
        <th class="text-right">Разр.</th>
        <th class="text-right">Баз.Ц</th>
        <th class="text-right">ТН</th>
        <th class="text-right">ТО</th>
        <th>Цена ТЕ</th>
        <th>Стойност</th>
        <th>Ц.с.ДДС</th>
        <th class="text-right">Преп.Ц</th>

        <?php if((int) $data['data']['nzok'] === 1){ ?>
            <th class="text-right">%A</th>
            <th class="text-right">НЗОК</th>
        <?php } else{ ?>
            <th class="text-right">Пред.ц.</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data['invoice_items'] as $itemKey => $item){ ?>
        <tr data-item-id="<?= $item['id'] ?>">
            <td class="text-right"><?= $item['number'] ?></td>
            <td class="editable">
                <span data-item-id="<?= $item['id'] ?>" data-type="name"><?= $item['designation'] ?></span>
                <span class="edit-icon" data-provider-id="1" data-item-id="<?= $item['id'] ?>" data-item-name="<?= addslashes($item['designation']) ?>"><i class="fas fa-edit"></i></span>
            </td>
            <td><?= $item['manufacturer'] ?></td>
            <td><?= $item['batch'] ?></td>
            <td class="text-right"><?= $item['quantity'] ?></td>
            <td><?= $item['expiry_date'] ?></td>
            <td><?= $item['certificate'] ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['base_price'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['trade_markup'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['trade_discount'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['wholesaler_price'] : '' ?></td>
            <td class="text-right"><?= $item['value'] ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['price_with_vat'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['recommended_price'] : '' ?></td>

            <?php if((int) $data['data']['nzok'] === 1){ ?>
                <th class="text-right" style="font-weight: 400;"><?= $item['value'] > 0 ? $item['percent_a'] : '' ?></th>
                <th class="text-right" style="font-weight: 400;"><?= $item['value'] > 0 ? $item['nzok'] : '' ?></th>
            <?php } else{ ?>
                <th class="text-right" style="font-weight: 400;"><?= $item['value'] > 0 ? $item['limit_price'] : '' ?></th>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>