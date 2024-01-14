<table class="table table-bordered">
    <thead>
    <tr>
        <th class="text-right">N</th>
        <th>Наименование</th>
        <th>М-ка</th>
        <th class="text-right">Кол.</th>
        <th class="text-right">Ед.цен</th>
        <th class="text-right">ТО</th>
        <th class="text-right">Стойност</th>
        <th class="text-right">Ц.с ддс</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data['parsed']['invoiceItems']['result'] as $itemKey => $item){ ?>
        <tr>
            <td class="text-right"><?= $item['itemNumber'] ?></td>
            <td>
                <?= $item['designation'] ?>
            </td>
            <td><?= $item['manufacturer'] ?></td>
            <td class="text-right"><?= $item['quantity'] ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['basePrice'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['tradeDiscount'] : '' ?></td>
            <td class="text-right"><?= $item['value'] ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['priceWithVAT'] : '' ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>