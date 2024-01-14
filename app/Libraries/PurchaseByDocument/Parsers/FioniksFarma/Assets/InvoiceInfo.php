<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

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
        $invoiceInfoLines = [16, 18]; //Start line and end line (default)
        $recipientContentMatrix = [0, 120]; //Start position and length


        $pattern = '/NO\. \d+ \/ Дата на издаване \d{2}\.\d{2}\.\d{4}/';
        foreach ( $this->fileContentByLines as $lineNumber => $line) {
            if (preg_match($pattern, $line)) {
                $invoiceInfoLines[0] = $lineNumber - 1;
                $invoiceInfoLines[1] = $lineNumber;
            }
        }

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if ($lineCounter >= $invoiceInfoLines[0] && $lineCounter <= $invoiceInfoLines[1]) {
                $this->rawInfo .= mb_substr($line, $recipientContentMatrix[0], $recipientContentMatrix[1]) . "\n";
            }
        }


        // Define an associative array to store the extracted values
        $pattern = [
            'NO.' => '/NO\.\s*([^\/\n\r]+)/',
            'Дата на издаване' => '/Дата на издаване\s*([^\/\n\r]+)/',
        ];

        // Extract information using regular expressions
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $this->rawInfo, $matches)) {
                $this->parsedInfo[$key] = trim($matches[1]);
            }
        }
    }


    private function fixResult()
    {
        $this->result = [
            'number' => $this->parsedInfo['NO.'] ?? '',
            'date' => $this->parsedInfo['Дата на издаване'] ?? '',
        ];

        //Convert Дата на издаване to Y-m-d format
        try{
            $date = \DateTime::createFromFormat('d.m.Y', $this->result['date']);
            if(!$date){ Throw new \Exception('Invalid date format'); }
            $this->result['date'] = $date->format('Y-m-d');
        } catch (\Exception $e) {
            $this->result['date'] = null;
        }
    }

    private function setAlias()
    {
        $this->alias = [
            'number' => 'NO.',
            'date' => 'Дата на издаване',
        ];
    }
}