<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

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
        $invoiceDetailsLines = [0, 0]; //Start line and end line
        $invoicePaymentLines = [0, 0]; //Start line and end line

        $patternBegin = '------------------------------------------------------------------------------------------------';
        $patternEnd = 'Получил стоката : ...........';
        $count = 0;  // Variable to track the occurrence count
        $lineNumber = 0;  // Variable to track the current line number


        foreach ($this->fileContentByLines as $lineCounter => $line) {
            $lineNumber++;

            //Search for line where the pattern begins
            if (strpos($line, $patternBegin) !== false) {
                $count++;
                if ($count == 3) {
                    $invoiceDetailsLines[0] = $lineNumber;
                }
            }

            //Search for line where the pattern ends
            if (strpos($line, $patternEnd) !== false) {
                $invoiceDetailsLines[1] = $lineNumber - 2;
            }
        }


        $invoicePaymentLines[0] = $invoiceDetailsLines[1] - 1;
        $invoicePaymentLines[1] = $invoiceDetailsLines[1];


        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if ($lineCounter >= $invoicePaymentLines[0] && $lineCounter <= $invoicePaymentLines[1]) {
                $this->rawInfo .= $line . "\n";
            }
        }

        // Define an associative array to store the extracted values
        $pattern = [
            'Плащане' => '/Плащане\s*:\s*([^\n\r]+?)\s*Дата на плащане\s*:/',
            'Дата на плащане' => '/Дата на плащане\s*:\s*([^\n\r]+?)\s*Дата на данъчно събитие\s*:/',
            'Дата на данъчно събитие' => '/Дата на данъчно събитие\s*:\s*([^\n\r]+)/',
            'Раэплащателна с-ка BIC' => '/Раэплащателна с-ка BIC\s*:\s*([^\n\r]+?)\s*IBAN\s*:/',
            'IBAN' => '/IBAN\s*:\s*([^\n\r]+?)\s*Банка\s*:/',
            'Банка' => '/Банка\s*:\s*([^\n\r]+)/',
        ];

        // Extract information using regular expressions
        $result = [];
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $this->rawInfo, $matches)) {
                $this->parsedInfo[$key] = trim(preg_replace('/\s+/', ' ', $matches[1]));
            }
        }
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

        //Convert Дата на плащане to Y-m-d format
        try{
            $paymentDate = \DateTime::createFromFormat('d.m.y', $this->result['paymentDate']);
            if(!$paymentDate){ Throw new \Exception('Invalid date format'); }
            $this->result['paymentDate'] = $paymentDate->format('Y-m-d');
        } catch (\Exception $e) {
            $this->result['paymentDate'] = null;
        }


        //Convert Дата на данъчно събитие to Y-m-d format
        try{
            $taxEventDate = \DateTime::createFromFormat('d.m.Y', $this->result['taxEventDate']);
            if(!$taxEventDate){ Throw new \Exception('Invalid date format'); }
            $this->result['taxEventDate'] = $taxEventDate->format('Y-m-d');
        } catch (\Exception $e) {
            $this->result['taxEventDate'] = null;
        }
    }


    private function setAlias()
    {
        $this->alias = [
            'paymentInfo' => 'Плащане',
            'paymentDate' => 'Дата на плащане',
            'taxEventDate' => 'Дата на данъчно събитие',
            'payerBIC' => 'Раэплащателна с-ка BIC',
            'payerIBAN' => 'IBAN',
            'payerBank' => 'Банка',
        ];
    }
}