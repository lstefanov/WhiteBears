<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

use App\Helpers\NumberFormat;
use Faker\Core\Number;

class Items
{
    private array $fileContentByLines;

    private bool $invoiceItemsWithCodeField = false;

    private array $rawInfo = [];

    private array $parsedInfo = [];


    private array $result;
    private array $alias;

    public function __construct(array $fileContentByLines)
    {
        $this->fileContentByLines = $fileContentByLines;
    }

    public function getParsedInfo(): array
    {
        return $this->parsedInfo;
    }

    public function getRawInfo(): array
    {
        return $this->rawInfo;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getAlias(): array
    {
        $this->setAlias();
        return $this->alias;
    }

    public function getIsNZOK(): int
    {
        return $this->invoiceItemsWithCodeField ? 1 : 0;
    }

    public function execute()
    {
        $this->checkInvoiceItemsForCodeField(); //NZOK
        $this->getInvoiceItems();
    }

    private function checkInvoiceItemsForCodeField()
    {
        foreach ($this->fileContentByLines as $itemKey => $item) {
            foreach ($item as $itemSubKey => $itemSub) {

                if (strpos($itemSub, '***** З А  Н З О К *****') !== false) {
                    $this->invoiceItemsWithCodeField = true;
                }

            }
        }

    }


    private function getInvoiceItems()
    {
        $foundedItems = [];
        $startItemsSection = false;
        foreach ($this->fileContentByLines as $itemKey => $item) {
            if ($startItemsSection === true) {
                $foundedItems[] = $item;
            }

            foreach ($item as $itemSubKey => $itemSub) {

                if (strpos($itemSub, 'Лекарствено средство') !== false) {
                    $startItemsSection = true;
                }

                if (strpos($itemSub, 'Всичко :') !== false) {
                    $startItemsSection = false;
                }
            }
        }

        //remove last element from $foundedItems
        if (count($foundedItems) > 1) {
            array_pop($foundedItems);
        }


        foreach ($foundedItems as $foundedItemKey => $foundedItem) {
            $element = [
                'itemNumber' => '',
                'designation' => '',
                'manufacturer' => '',
                'batch' => '',
                'quantity' => '.',
                'expiryDate' => '',
                'certificate' => '',
                'basePrice' => '',
                'tradeMarkup' => '',
                'tradeDiscount' => '',
                'wholesalerPrice' => '',
                'value' => '',
                'priceWithVAT' => '',
                'recommendedPrice' => '',

                'limitPrice' => '',

                'percent_a' => '',
                'nzok' => '',
            ];


            $element['itemNumber'] = $foundedItem[0] ?? '';
            $element['designation'] = $foundedItem[1] ?? '';
            $element['manufacturer'] = $foundedItem[2] ?? '';
            $element['batch'] = $foundedItem[3] ?? '';
            $element['quantity'] = $foundedItem[4] ?? '';
            $element['expiryDate'] = $foundedItem[5] ?? '';
            $element['certificate'] = $foundedItem[6] ?? '';
            $element['basePrice'] = $foundedItem[7] ?? '';
            $element['tradeMarkup'] = $foundedItem[8] ?? '';
            $element['tradeDiscount'] = $foundedItem[9] ?? '';
            $element['wholesalerPrice'] = $foundedItem[10] ?? '';
            $element['value'] = $foundedItem[11] ?? '';
            $element['priceWithVAT'] = $foundedItem[12] ?? '';
            $element['recommendedPrice'] = $foundedItem[13] ?? '';

            if ($this->invoiceItemsWithCodeField === false) {
                $element['limitPrice'] = $foundedItem[14] ?? '';
            } else {
                $element['percent_a'] = $foundedItem[14] ?? '';
                $element['nzok'] = $foundedItem[15] ?? '';
            }


            //Fix price to be able to fit in database
            $element['basePrice'] = NumberFormat::formatPrice($element['basePrice']);
            $element['wholesalerPrice'] = NumberFormat::formatPrice($element['wholesalerPrice']);
            $element['value'] = NumberFormat::formatPrice($element['value']);
            $element['priceWithVAT'] = NumberFormat::formatPrice($element['priceWithVAT']);
            $element['recommendedPrice'] = NumberFormat::formatPrice($element['recommendedPrice']);


            $this->result[] = $element;
        }

        $this->parsedInfo = $this->result;
    }


    private function setAlias()
    {
        $this->alias = [
            'itemNumber' => 'Номер на артикул',
            'designation' => 'Наименование',
            'manufacturer' => 'Оп',
            'batch' => 'Серия',
            'quantity' => 'Кол.',
            'expiryDate' => 'Ср.г.',
            'certificate' => 'Разрешително',
            'basePrice' => 'Баз.цен',
            'tradeMarkup' => 'ТН',
            'tradeDiscount' => 'ТО',
            'wholesalerPrice' => 'Цена ТЕ',
            'value' => 'Стойност',
            'priceWithVAT' => 'Ц.с ддс',
            'recommendedPrice' => 'Преп.Ц',

            'limitPrice' => 'Пред.ц.',

            'percent_a' => '%A',
            'nzok' => 'NZOK',
        ];
    }
}