<table class="table table-bordered purchase-by-document-sub-table">
    <tr>
        <td>ОБЩА СТОЙНОСТ</td>
        <td class="font-weight-bold">
            <?= $data['parsed']['invoicePrice']['result']['totalPrice'] ?>
            <span style="float: right;"><?= $data['parsed']['invoicePrice']['result']['totalPriceFromSupplier'] ?></span>
        </td>
    </tr>
    <tr>
        <td>ТЪРГОВСКА ОТСТЪПКА:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tradeDiscount'] ?></td>
    </tr>
    <tr>
        <td>ДАНЪЧНА ОСНОВА ЗА  9 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['taxBase9'] ?></td>
    </tr>
    <tr>
        <td>НАЧИСЛЕН ДДС ЗА  9 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tax9'] ?></td>
    </tr>
    <tr>
        <td>ДАНЪЧНА ОСНОВА ЗА 20 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['taxBase20'] ?></td>
    </tr>
    <tr>
        <td>НАЧИСЛЕН ДДС ЗА 20 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tax20'] ?></td>
    </tr>
    <tr>
        <td>ДАНЪЧНА ОСНОВА ЗА  0 % ДДС:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['taxBase0'] ?></td>
    </tr>
    <tr>
        <td>СУМА ЗА ПЛАЩАНЕ:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['totalPriceWithTax'] ?></td>
    </tr>
    <tr>
        <td>Словом:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['totalPriceWithTaxInWords'] ?></td>
    </tr>
    <tr>
        <td>Забележка:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['note'] ?></td>
    </tr>
</table>