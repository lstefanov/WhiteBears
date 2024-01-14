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