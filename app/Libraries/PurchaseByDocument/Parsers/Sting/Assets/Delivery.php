<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

class Delivery
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
            'pharmacistInCharge' => '',
            'place' => '',
            'address' => '',
            'route' => '',
            'date' => '',
        ];


        foreach ($this->fileContentByLines as $itemKey => $item) {
            foreach ($item as $itemSubKey => $itemSub) {

                if ($itemSub === 'Отг.маг. фармацевт') {
                    $this->result['pharmacistInCharge'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if ($itemSub === 'Адрес на склад') {
                    $this->result['address'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if ($itemSub === 'Място на сделката') {
                    $this->result['place'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);
                }

                if ($itemSub === 'Маршрут:') {
                    $this->result['route'] = $this->filterValue($this->fileContentByLines[$itemKey][$itemSubKey + 1]);

                    if (isset($this->fileContentByLines[$itemKey + 1][1])) {
                        try {
                            $taxEventDate = \DateTime::createFromFormat('d/m/Y H:i',
                                $this->fileContentByLines[$itemKey + 1][1]);
                            if (!$taxEventDate) {
                                throw new \Exception('Invalid date format');
                            }
                            $this->result['date'] = $taxEventDate->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $this->result['date'] = null;
                        }
                    } else {
                        $this->result['date'] = null;
                    }

                }
            }
        }

        $this->parsedInfo = $this->result;
    }

    private function fixResult()
    {
        $this->result = [];
    }

    private function setAlias()
    {
        $this->alias = [
            'pharmacistInCharge' => 'Отг.маг. фармацевт',
            'place' => 'Място',
            'address' => 'Адрес',
            'route' => 'Маршрут',
            'date' => 'Дата',
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