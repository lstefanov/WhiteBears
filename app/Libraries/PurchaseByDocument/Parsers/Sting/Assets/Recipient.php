<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

class Recipient
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
            'address_reg' => '',
            'idNumber' => '',
            'vatNumber' => '',
            'license' => '',
            'opiatesLicense' => '',
            'mol' => '',
            'phone' => '',
            'client_id' => ''
        ];

        foreach ($this->fileContentByLines as $itemKey => $item) {
            foreach ($item as $itemSubKey => $itemSub) {
                if ($itemSub === 'Клиент') {
                    $this->result['name'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Идент. номер ЗДДС') !== false && $itemSubKey === 0) {
                    $this->result['vatNumber'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Идент. номер ДОПК') !== false && $itemSubKey === 0) {
                    $this->result['idNumber'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Разрешително') !== false && $itemSubKey === 0) {
                    $this->result['license'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Адрес') !== false &&
                    isset($this->fileContentByLines[$itemKey][$itemSubKey + 2]) &&
                    strpos($this->fileContentByLines[$itemKey][$itemSubKey + 2], 'Контролиращо лице') !== false) {
                    $this->result['address'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Телефон') !== false &&
                    isset($this->fileContentByLines[$itemKey][$itemSubKey + 2]) &&
                    strpos($this->fileContentByLines[$itemKey][$itemSubKey + 2], 'Адрес') !== false) {
                    $this->result['phone'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Лиц. наркотици') !== false &&
                    isset($this->fileContentByLines[$itemKey][$itemSubKey + 2]) &&
                    strpos($this->fileContentByLines[$itemKey][$itemSubKey + 2], 'Телефон') !== false) {
                    $this->result['opiatesLicense'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Адрес. Рег.') !== false && $itemSubKey === 0) {
                    $this->result['address_reg'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'Фирма') !== false &&
                    isset($this->fileContentByLines[$itemKey][$itemSubKey + 2]) &&
                    strpos($this->fileContentByLines[$itemKey][$itemSubKey + 2], 'Банка') !== false) {
                    $this->result['mol'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'ID Клиент') !== false &&
                    isset($this->fileContentByLines[$itemKey][$itemSubKey + 2]) &&
                    strpos($this->fileContentByLines[$itemKey][$itemSubKey + 2], 'BIC код') !== false) {
                    $this->result['client_id'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }
            }
        }

        $this->parsedInfo = $this->result;
    }


    private function setAlias()
    {
        $this->alias = [
            'name' => 'Клиент',
            'address' => 'Адрес',
            'address_reg' => 'Адрес. Рег.',
            'idNumber' => 'Идент. номер ДОПК',
            'vatNumber' => 'Идент. номер ЗДДС',
            'license' => 'Разрешително',
            'opiatesLicense' => 'Лиц. наркотици',
            'mol' => 'Фирма',
            'phone' => 'Телефон',
            'client_id' => 'ID Клиент'
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