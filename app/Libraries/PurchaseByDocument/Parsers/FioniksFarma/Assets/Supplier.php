<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

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
        $this->fixResult();
        return $this->result;
    }

    public function getAlias(): array
    {
        $this->setAlias();
        return $this->alias;
    }

    public function execute()
    {
        $recipientAndSupplierInfoLines = [0, 16]; //Start line and end line (default)
        $supplierContentMatrix = [43, 43]; //Start position and length


        //Try to find where info for recipient starts
        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if (mb_strpos($line, 'Д О С Т А В Ч И К')) {
                $recipientAndSupplierInfoLines[0] = $lineCounter;
            }
            if (preg_match('/NO\. \d+ \/ Дата на издаване \d{2}\.\d{2}\.\d{4}/', $line)) {
                $recipientAndSupplierInfoLines[1] = $lineCounter;
            }
        }


        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if ($lineCounter >= $recipientAndSupplierInfoLines[0] && $lineCounter <= $recipientAndSupplierInfoLines[1]) {
                $this->rawInfo .= mb_substr($line, $supplierContentMatrix[0], $supplierContentMatrix[1]) . "\n";
            }
        }

        // Define an associative array to store the extracted values
        $pattern = [
            'Име' => '/Име:\s*([^\n\r]+)/',
            'Адрес' => '/Адрес:\s*([\s\S]+?)\s*Ид.н.допк:/',
            'Ид.н.допк' => '/Ид.н.допк:\s*([^\n\r]+)/',
            'Ид.н.зддс' => '/Ид.н.зддс:\s*([^\n\r]+)/',
            'Лиценз' => '/Лиценз:\s*([^\n\r]+)/',
            'Л.опиати' => '/Л.опиати:\s*([^\n\r]+)/',
            'МОЛ' => '/МОЛ:\s*([^\n\r]+)/',
            'Телефон' => '/Телефон:\s*([^\n\r]+)/',
        ];

        // Extract information using regular expressions
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $this->rawInfo, $matches)) {
                $this->parsedInfo[$key] = trim(preg_replace('/\s+/', ' ', $matches[1]));
            }
        }
    }


    private function fixResult()
    {
        $this->result = [
            'name' => $this->parsedInfo['Име'] ?? '',
            'address' => $this->parsedInfo['Адрес'] ?? '',
            'idNumber' => $this->parsedInfo['Ид.н.допк'] ?? '',
            'vatNumber' => $this->parsedInfo['Ид.н.зддс'] ?? '',
            'license' => $this->parsedInfo['Лиценз'] ?? '',
            'opiatesLicense' => $this->parsedInfo['Л.опиати'] ?? '',
            'mol' => $this->parsedInfo['МОЛ'] ?? '',
            'phone' => $this->parsedInfo['Телефон'] ?? '',
        ];
    }


    private function setAlias()
    {
        $this->alias = [
            'name' => 'Име',
            'address' => 'Адрес',
            'idNumber' => 'Ид.н.допк',
            'vatNumber' => 'Ид.н.зддс',
            'license' => 'Лиценз',
            'opiatesLicense' => 'Л.опиати',
            'mol' => 'МОЛ',
            'phone' => 'Телефон',
        ];
    }
}