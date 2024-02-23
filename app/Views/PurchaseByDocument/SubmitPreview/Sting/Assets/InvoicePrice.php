<table class="table table-bordered purchase-by-document-sub-table">
    <tr>
        <td>Всичко</td>
        <td class="font-weight-bold">
            <?= $data['parsed']['invoicePrice']['result']['totalPrice'] ?>
            <span style="float: right;"><?= $data['parsed']['invoicePrice']['result']['totalPriceFromSupplier'] ?></span>
        </td>
    </tr>
    <tr>
        <td>Отстъпка:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tradeDiscount'] ?></td>
    </tr>
    <tr>
        <td>% ТО:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tradeDiscountPercent'] ?></td>
    </tr>
    <tr>
        <td>Ст-ст на сделката:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['valueOfTheDeal'] ?></td>
    </tr>

    <?php if((int) $data['parsed']['nzok'] === 0){ ?>
        <tr>
            <td>Данъчна основа:</td>
            <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['taxBase'] ?></td>
        </tr>
    <?php } ?>

    <tr>
        <td>ДДС 20 %:</td>
        <td class="font-weight-bold"><?= $data['parsed']['invoicePrice']['result']['tax20'] ?></td>
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