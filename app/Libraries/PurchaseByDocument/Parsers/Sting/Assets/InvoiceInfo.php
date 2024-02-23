<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

class InvoiceInfo
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
            'number' => '',
            'date' => '',
        ];

        foreach ($this->fileContentByLines as $itemKey => $item) {
            foreach ($item as $itemSubKey => $itemSub) {
                if ($itemSub === 'О Р И Г И Н А Л') {
                    $invoiceDetails = $this->fileContentByLines[$itemKey + 1][0];
                    $invoiceDetails = explode('от', $invoiceDetails);

                    $this->result['number'] = trim(str_replace('No : ', '', $invoiceDetails[0]));
                    $this->result['date'] = preg_replace('/\s+/', '', $invoiceDetails[1]);

                    try {
                        $date = \DateTime::createFromFormat('d/m/y', $this->result['date']);
                        if (!$date) {
                            throw new \Exception('Invalid date format');
                        }
                        $this->result['date'] = $date->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->result['date'] = null;
                    }
                }
            }
        }

        $this->parsedInfo = $this->result;
    }


    private function setAlias()
    {
        $this->alias = [
            'number' => 'NO.',
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