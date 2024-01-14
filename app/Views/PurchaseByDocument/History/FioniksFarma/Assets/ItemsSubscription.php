<table class="table table-bordered">
    <thead>
    <tr>
        <th class="text-right">N</th>
        <th>Наименование</th>
        <th>М-ка</th>
        <th class="text-right">Кол.</th>
        <th class="text-right">Ед.цена</th>
        <th class="text-right">ТО</th>
        <th class="text-right">Стойност</th>
        <th class="text-right">Ц.с ддс</th>
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
            <td class="text-right"><?= $item['value'] > 0 ? $item['trade_discount'] : '' ?></td>
            <td class="text-right"><?= $item['value'] ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['price_with_vat'] : '' ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>