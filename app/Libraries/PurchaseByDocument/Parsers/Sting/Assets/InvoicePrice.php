<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

use App\Helpers\NumberFormat;

class InvoicePrice
{
    private array $fileContentByLines;

    private string $rawInfo = '';

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

    public function getRawInfo(): string
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

    public function execute()
    {
        $this->result = [
            'totalPrice' => '',
            'totalPriceFromSupplier' => '',
            'tradeDiscountPercent' => '',
            'valueOfTheDeal' => '',
            'tax20' => '',
            'totalPriceWithTax' => '',
            'totalPriceWithTaxInWords' => '',
            'taxBase' => '',
            'docNumber' => '',
            'note' => '',
        ];

        foreach ($this->fileContentByLines as $itemKey => $item) {
            foreach ($item as $itemSubKey => $itemSub) {
                if ($itemSub === 'Всичко :') {
                    $this->result['totalPrice'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                    $this->result['totalPriceFromSupplier'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 2]);
                }

                if ($itemSub === 'Отстъпка :') {
                    $this->result['tradeDiscount'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, '% ТО') !== false || strpos($itemSub, '%ТО') !== false) {
                    $this->result['tradeDiscountPercent'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if ($itemSub === 'Ст-ст на сделката :') {
                    $this->result['valueOfTheDeal'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if ($itemSub === 'ДДС 20 % :') {
                    $this->result['tax20'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if ($itemSub === 'СУМА ЗА ПЛАЩАНЕ :') {
                    $this->result['totalPriceWithTax'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Док.номер') !== false) {
                    $this->result['docNumber'] = str_replace('Док.номер ', '', $itemSub);
                }

                if ($itemSub === 'Словом :') {
                    $this->result['totalPriceWithTaxInWords'] = $this->fileContentByLines[$itemKey][$itemSubKey + 1];
                }

                if (strpos($itemSub, 'Стойност на фактурата') !== false) {
                    $this->result['note'] = $itemSub;
                }

                if (strpos($itemSub, 'Данъчна основа') !== false ) {
                    $this->result['taxBase'] = NumberFormat::formatPrice($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }
            }
        }

        $this->parsedInfo = $this->result;
    }


    private function setAlias()
    {
        $this->alias = [
            'totalPrice' => 'ОБЩА СТОЙНОСТ',
            'totalPriceWithTax' => 'СУМА ЗА ПЛАЩАНЕ',
            'totalPriceFromSupplier' => 'ОБЩА СТОЙНОСТ ОТ ДОСТАВЧИКА (пределна)',
            'discount' => 'ОТСТЪПКА',
            'tradeDiscountPercent' => 'ТЪРГОВСКА ОТСТЪПКА %',
            'valueOfTheDeal' => 'Ст-ст на сделката :',
            'tax20' => 'НАЧИСЛЕН ДДС ЗА 20 % ДДС',
            'taxBase' => 'ДАНЪЧНА ОСНОВА',
            'totalPriceWithTaxInWords' => 'Словом',
            'docNumber' => 'Док.номер',
            'note' => 'Забележка',
        ];
    }


    private function filterValue(string $string)
    {
        //if string start with : remove it and trip the value
        if (strpos($string, ':') === 0) {
            return trim(mb_substr($string, 1));
        } else {
            return trim($string);
        }
    }
}