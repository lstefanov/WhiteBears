<table class="table table-bordered purchase-by-document-sub-table">
    <tr>
        <td>Всичко</td>
        <td class="font-weight-bold">
            <?= $data['invoice_price']['total_price'] ?>
            <span style="float: right;"><?= $data['invoice_price']['total_price_from_supplier'] ?></span>
        </td>
    </tr>
    <tr>
        <td>Отстъпка:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['trade_discount'] ?></td>
    </tr>
    <tr>
        <td>% ТО:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['trade_discount_percent'] ?></td>
    </tr>
    <tr>
        <td>Ст-ст на сделката:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['value_of_the_deal'] ?></td>
    </tr>

    <?php if((int) $data['data']['nzok'] === 0){ ?>
        <tr>
            <td>Данъчна основа:</td>
            <td class="font-weight-bold"><?= $data['invoice_price']['tax_base'] ?></td>
        </tr>
    <?php } ?>

    <tr>
        <td>ДДС 20 %:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['tax_20'] ?></td>
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