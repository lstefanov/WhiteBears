<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

class Supplier
{
    private array $fileContentByLines;

    private string $rawInfo = '';

    private array $parsedInfo = [];

    private array $result = [];

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
            'name' => '',
            'address' => '',
            'idNumber' => '',
            'vatNumber' => '',
            'license' => '',
            'opiatesLicense' => '',
            'phone' => '',
            'controlling_person' => ''
        ];

        foreach ($this->fileContentByLines as $itemKey => $item) {
            foreach ($item as $itemSubKey => $itemSub) {
                if ($itemSub === 'Доставчик') {
                    $this->result['name'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Идент. номер ЗДДС') !== false && $itemSubKey === 2) {
                    $this->result['vatNumber'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Идент. номер ДОПК') !== false && $itemSubKey === 2) {
                    $this->result['idNumber'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Разрешително') !== false && $itemSubKey === 2) {
                    $this->result['license'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Контролиращо лице') !== false && $itemSubKey === 2) {
                    $this->result['controlling_person'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Адрес') !== false &&
                    $itemSubKey === 2 &&
                    strpos($this->fileContentByLines[$itemKey][0], 'Телефон') !== false) {
                    $this->result['address'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Телефон') !== false &&
                    $itemSubKey === 2 &&
                    strpos($this->fileContentByLines[$itemKey][0], 'Лиц. наркотици') !== false) {
                    $this->result['phone'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Лиц. наркотици') !== false &&
                    $itemSubKey === 2 &&
                    strpos($this->fileContentByLines[$itemKey][0], 'Адрес. Рег.') !== false) {
                    $this->result['opiatesLicense'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }
            }
        }

        $this->parsedInfo = $this->result;
    }


    private function setAlias()
    {
        $this->alias = [
            'name' => 'Доставчик',
            'address' => 'Адрес',
            'address_reg' => 'Адрес. Рег.',
            'idNumber' => 'Идент. номер ДОПК',
            'vatNumber' => 'Идент. номер ЗДДС',
            'license' => 'Разрешително',
            'opiatesLicense' => 'Лиц. наркотици',
            'mol' => 'Фирма',
            'phone' => 'Телефон',
            'client_id' => 'ID Клиент',
            'controlling_person' => 'Контролиращо лице'
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