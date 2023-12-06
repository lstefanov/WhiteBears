<?=$this->extend("layouts/debug")?>

<?=$this->section("content")?>

<style>
    .debug-element {
        background: #fff;
        padding: 10px;
        margin: 20px;

        overflow: scroll;
    }

    .debug-element table tbody tr td:first-child {
        width: 30%;
        word-wrap: break-word;
    }

    .debug-element table tbody tr td:nth-child(2) {
        width: 10%;
        word-wrap: break-word;
    }

    .debug-element table tbody tr td:nth-child(3) {
        width: 30%;
        word-wrap: break-word;
    }
</style>

<?php if(isset($originalContent)){ ?>
    <div class="debug-element">
        <h2>Сурово съдържание</h2>
        <pre><?=print_r($originalContent, true)?></pre>
    </div>
<?php } ?>

<div class="debug-element">
    <h2>Общо количество: <?= $data['itemsCount'] ?> бр.</h2>
</div>

<div class="debug-element">
    <h2>Получател</h2>
    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
        <thead>
            <th>Чиста</th>
            <th>Алиас</th>
            <th>Обработена</th>
            <th>Сурова</th>
        </thead>
        <tbody>
            <tr>
                <td>
                    <pre><?=print_r($data['recipient']['result'], true)?></pre>
                </td>
                <td>
                    <pre><?=print_r($data['recipient']['alias'], true)?></pre>
                </td>
                <td>
                    <pre><?=print_r($data['recipient']['parsed'], true)?></pre>
                </td>
                <td>
                    <pre><?=print_r($data['recipient']['raw'], true)?></pre>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<div class="debug-element">
    <h2>Доставчик</h2>
    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
        <thead>
            <th>Чиста</th>
            <th>Алиас</th>
            <th>Обработена</th>
            <th>Сурова</th>
        </thead>
        <tbody>
        <tr>
            <td>
                <pre><?=print_r($data['supplier']['result'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['supplier']['alias'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['supplier']['parsed'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['supplier']['raw'], true)?></pre>
            </td>
        </tr>
        </tbody>
    </table>
</div>



<div class="debug-element">
    <h2>Доставка</h2>
    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
        <thead>
            <th>Чиста</th>
            <th>Алиас</th>
            <th>Обработена</th>
            <th>Сурова</th>
        </thead>
        <tbody>
        <tr>
            <td>
                <pre><?=print_r($data['delivery']['result'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['delivery']['alias'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['delivery']['parsed'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['delivery']['raw'], true)?></pre>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<div class="debug-element">
    <h2>Фактура: Информация</h2>
    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
        <thead>
            <th>Чиста</th>
            <th>Алиас</th>
            <th>Обработена</th>
            <th>Сурова</th>
        </thead>
        <tbody>
        <tr>
            <td>
                <pre><?=print_r($data['invoiceInfo']['result'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoiceInfo']['alias'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoiceInfo']['parsed'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoiceInfo']['raw'], true)?></pre>
            </td>
        </tr>
        </tbody>
    </table>
</div>



<div class="debug-element">
    <h2>Фактура: Сума</h2>
    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
        <thead>
            <th>Чиста</th>
            <th>Алиас</th>
            <th>Обработена</th>
            <th>Сурова</th>
        </thead>
        <tbody>
        <tr>
            <td>
                <pre><?=print_r($data['invoicePrice']['result'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoicePrice']['alias'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoicePrice']['parsed'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoicePrice']['raw'], true)?></pre>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<div class="debug-element">
    <h2>Фактура: Плащане</h2>
    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
        <thead>
            <th>Чиста</th>
            <th>Алиас</th>
            <th>Обработена</th>
            <th>Сурова</th>
        </thead>
        <tbody>
        <tr>
            <td>
                <pre><?=print_r($data['invoicePayment']['result'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoicePayment']['alias'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoicePayment']['parsed'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoicePayment']['raw'], true)?></pre>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<div class="debug-element">
    <h2>Елементи/Продукти</h2>
    <table class="table table-bordered dataTable" width="100%" cellspacing="0">
        <thead>
            <th>Чиста</th>
            <th>Алиас</th>
            <th>Обработена</th>
            <th>Сурова</th>
        </thead>
        <tbody>
        <tr>
            <td>
                <pre><?=print_r($data['invoiceItems']['result'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoiceItems']['alias'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoiceItems']['parsed'], true)?></pre>
            </td>
            <td>
                <pre><?=print_r($data['invoiceItems']['raw'], true)?></pre>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<?=$this->endSection()?>