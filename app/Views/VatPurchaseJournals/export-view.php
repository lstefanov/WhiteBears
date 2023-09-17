<?=$this->extend("layouts/main")?>

<?=$this->section("content")?>

    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h5>Преглед</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Тип документ</th>
                        <th>Документ №</th>
                        <th>Дата на документ</th>
                        <th>Вальор</th>
                        <th>Стойност</th>
                        <th>ДДС</th>
                        <th>Вид Контрагент</th>
                        <th>Код на контрагент</th>
                        <th>Код стока</th>
                        <th>&nbsp;</th>
                        <th>$18 - (NSB) - Вид плащане</th>
                    </tr>
                    <tr>
                        <th>H.DOC_TYPE</th>
                        <th>H.DOC_NO</th>
                        <th>H.DOC_DATE</th>
                        <th>H.DOC_VALEUR</th>
                        <th>D.NET_VALUE</th>
                        <th>D.VAT_VALUE</th>
                        <th>D.PARTNER_TYPE</th>
                        <th>D.PARTNER</th>
                        <th>D.23</th>
                        <th>24</th>
                        <th>D.$18</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($data as $item) { ?>
                        <tr>
                            <td><?=$item['h_doc_type']?></td>
                            <td><?=$item['h_doc_no']?></td>
                            <td><?=$item['h_doc_date']?></td>
                            <td><?=$item['h_doc_valeur']?></td>
                            <td><?=$item['d_net_value']?></td>
                            <td><?=$item['d_vat_value']?></td>
                            <td><?=$item['d_partner_type']?></td>
                            <td><?=$item['d_partner']?></td>
                            <td><?=$item['d_23']?></td>
                            <td><?=$item['d_24']?></td>
                            <td><?=$item['d_$18']?></td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>



<?=$this->endSection()?>