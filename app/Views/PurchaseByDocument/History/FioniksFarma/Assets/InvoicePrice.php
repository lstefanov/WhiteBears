<table class="table table-bordered purchase-by-document-sub-table">
    <tr>
        <td>ОБЩА СТОЙНОСТ</td>
        <td class="font-weight-bold">
            <?= $data['invoice_price']['total_price'] ?>
            <span style="float: right;"><?= $data['invoice_price']['total_price_from_supplier'] ?></span>
        </td>
    </tr>
    <tr>
        <td>ТЪРГОВСКА ОТСТЪПКА:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['trade_discount'] ?></td>
    </tr>
    <tr>
        <td>ДАНЪЧНА ОСНОВА ЗА  9 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['tax_base_9'] ?></td>
    </tr>
    <tr>
        <td>НАЧИСЛЕН ДДС ЗА  9 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['tax_9'] ?></td>
    </tr>
    <tr>
        <td>ДАНЪЧНА ОСНОВА ЗА 20 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['tax_base_20'] ?></td>
    </tr>
    <tr>
        <td>НАЧИСЛЕН ДДС ЗА 20 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['tax_20'] ?></td>
    </tr>
    <tr>
        <td>ДАНЪЧНА ОСНОВА ЗА  0 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['invoice_price']['tax_base_0'] ?></td>
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