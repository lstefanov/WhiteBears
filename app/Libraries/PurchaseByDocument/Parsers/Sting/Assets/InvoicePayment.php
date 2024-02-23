<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

use App\Helpers\NumberFormat;

class InvoicePayment
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
            'paymentInfo' => '',
            'taxEventDate' => '',
            'payerBIC' => '',
            'payerIBAN' => '',
            'payerBank' => '',
        ];

        foreach ($this->fileContentByLines as $itemKey => $item) {
            foreach ($item as $itemSubKey => $itemSub) {
                if ($itemSub === 'ПЛАЩАНЕ :') {
                    $this->result['paymentInfo'] = $this->fileContentByLines[$itemKey][$itemSubKey + 1] . ', ' . $this->fileContentByLines[$itemKey][$itemSubKey + 2];
                    $this->result['taxEventDate'] = str_replace('Падеж : ', '',
                        $this->fileContentByLines[$itemKey][$itemSubKey + 3]);

                    try {
                        $taxEventDate = \DateTime::createFromFormat('d/m/y', $this->result['taxEventDate']);
                        if (!$taxEventDate) {
                            throw new \Exception('Invalid date format');
                        }
                        $this->result['taxEventDate'] = $taxEventDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->result['taxEventDate'] = null;
                    }
                }


                if (strpos($itemSub, 'Банка') !== false &&
                    $itemSubKey === 2 &&
                    strpos($this->fileContentByLines[$itemKey][0], 'Фирма') !== false) {
                    $this->result['payerBank'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'BIC код') !== false &&
                    $itemSubKey === 2 &&
                    strpos($this->fileContentByLines[$itemKey][0], 'ID Клиент') !== false) {
                    $this->result['payerBIC'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if (strpos($itemSub, 'IBAN') !== false &&
                    $itemSubKey === 2) {
                    $this->result['payerIBAN'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

            }
        }

        $this->parsedInfo = $this->result;
    }


    public function fixResult()
    {
        $this->result = [
            'paymentInfo' => $this->parsedInfo['Плащане'] ?? '',
            'paymentDate' => $this->parsedInfo['Дата на плащане'] ?? '',
            'taxEventDate' => $this->parsedInfo['Дата на данъчно събитие'] ?? '',
            'payerBIC' => $this->parsedInfo['Раэплащателна с-ка BIC'] ?? '',
            'payerIBAN' => $this->parsedInfo['IBAN'] ?? '',
            'payerBank' => $this->parsedInfo['Банка'] ?? '',
        ];


        //Convert Дата на данъчно събитие to Y-m-d format
        try {
            $taxEventDate = \DateTime::createFromFormat('d.m.Y', $this->result['taxEventDate']);
            if (!$taxEventDate) {
                throw new \Exception('Invalid date format');
            }
            $this->result['taxEventDate'] = $taxEventDate->format('Y-m-d');
        } catch (\Exception $e) {
            $this->result['taxEventDate'] = null;
        }
    }


    private function setAlias()
    {
        $this->alias = [
            'paymentInfo' => 'Плащане',
            'taxEventDate' => 'Падеж',
            'payerBIC' => 'Раэплащателна с-ка BIC',
            'payerIBAN' => 'IBAN',
            'payerBank' => 'Банка',
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