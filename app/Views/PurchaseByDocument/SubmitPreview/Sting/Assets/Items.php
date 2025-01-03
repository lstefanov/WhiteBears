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

        <?php if((int) $data['parsed']['nzok'] === 1){ ?>
            <th class="text-right">%A</th>
            <th class="text-right">НЗОК</th>
        <?php } else{ ?>
            <th class="text-right">Пред.ц.</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data['parsed']['invoiceItems']['result'] as $itemKey => $item){ ?>
        <tr>
            <td class="text-right"><?= $item['itemNumber'] ?></td>
            <td><?= $item['designation'] ?></td>
            <td><?= $item['manufacturer'] ?></td>
            <td><?= $item['batch'] ?></td>
            <td class="text-right"><?= $item['quantity'] ?></td>
            <td><?= $item['expiryDate'] ?></td>
            <td><?= $item['certificate'] ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['basePrice'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['tradeMarkup'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['tradeDiscount'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['wholesalerPrice'] : '' ?></td>
            <td class="text-right"><?= $item['value'] ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['priceWithVAT'] : '' ?></td>
            <td class="text-right"><?= $item['value'] > 0 ? $item['recommendedPrice'] : '' ?></td>

            <?php if((int) $data['parsed']['nzok'] === 1){ ?>
                <th class="text-right" style="font-weight: 400;"><?= $item['value'] > 0 ? $item['percent_a'] : '' ?></th>
                <th class="text-right" style="font-weight: 400;"><?= $item['value'] > 0 ? $item['nzok'] : '' ?></th>
            <?php } else{ ?>
                <th class="text-right" style="font-weight: 400;"><?= $item['value'] > 0 ? $item['limitPrice'] : '' ?></th>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>