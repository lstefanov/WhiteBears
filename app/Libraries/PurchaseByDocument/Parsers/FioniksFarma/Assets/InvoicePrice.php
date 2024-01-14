<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

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


        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if ($lineCounter >= $invoiceDetailsLines[0] && $lineCounter <= $invoiceDetailsLines[1]) {
                $this->rawInfo .= $line . "\n";
            }
        }


        // Define an associative array to store the extracted values
        $pattern = [
            'ОБЛАГАЕМА СТОЙНОСТ' => '/ОБЛАГАЕМА СТОЙНОСТ\s*:\s*([^\n\r]+)/',
            'ОБЩА СТОЙНОСТ' => '/ОБЩА СТОЙНОСТ\s*:\s*([^\n\r]+)/',
            'ТЪРГОВСКА ОТСТЪПКА' => '/ТЪРГОВСКА ОТСТЪПКА\s*:\s*([^\n\r]+)/',
            'ДАНЪЧНА ОСНОВА ЗА  9 % ДДС' => '/ДАНЪЧНА ОСНОВА ЗА  9 % ДДС\s*:\s*([^\n\r]+)/',
            'НАЧИСЛЕН ДДС ЗА  9 % ДДС' => '/НАЧИСЛЕН ДДС ЗА  9 % ДДС\s*:\s*([^\n\r]+)/',
            'ДАНЪЧНА ОСНОВА ЗА 20 % ДДС' => '/ДАНЪЧНА ОСНОВА ЗА 20 % ДДС\s*:\s*([^\n\r]+)/',
            'НАЧИСЛЕН ДДС ЗА 20 % ДДС' => '/НАЧИСЛЕН ДДС ЗА 20 % ДДС\s*:\s*([^\n\r]+)/',
            'ДАНЪЧНА ОСНОВА ЗА  0 % ДДС' => '/ДАНЪЧНА ОСНОВА ЗА  0 % ДДС\s*:\s*([^\n\r]+)/',
            'СУМА ЗА ПЛАЩАНЕ' => '/СУМА ЗА ПЛАЩАНЕ\s*:\s*([^\n\r]+)/',
            'Словом' => '/Словом\s*:\s*([^\n\r]+)/',
            'Забележка' => '/Забележка:\s*([^\n\r]+)/',
        ];

        // Extract information using regular expressions
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $this->rawInfo, $matches)) {
                $this->parsedInfo[$key] = trim(preg_replace('/\s+/', ' ', $matches[1]));
            }
        }
    }

    public function fixResult()
    {
        $this->result = [
            'taxableValue' => $this->parsedInfo['ОБЛАГАЕМА СТОЙНОСТ'] ?? '',
            'totalPrice' => $this->parsedInfo['ОБЩА СТОЙНОСТ'] ?? '',
            'tradeDiscount' => $this->parsedInfo['ТЪРГОВСКА ОТСТЪПКА'] ?? '',
            'taxBase9' => $this->parsedInfo['ДАНЪЧНА ОСНОВА ЗА  9 % ДДС'] ?? '',
            'tax9' => $this->parsedInfo['НАЧИСЛЕН ДДС ЗА  9 % ДДС'] ?? '',
            'taxBase20' => $this->parsedInfo['ДАНЪЧНА ОСНОВА ЗА 20 % ДДС'] ?? '',
            'tax20' => $this->parsedInfo['НАЧИСЛЕН ДДС ЗА 20 % ДДС'] ?? '',
            'taxBase0' => $this->parsedInfo['ДАНЪЧНА ОСНОВА ЗА  0 % ДДС'] ?? '',
            'totalPriceWithTax' => $this->parsedInfo['СУМА ЗА ПЛАЩАНЕ'] ?? '',
            'totalPriceWithTaxInWords' => $this->parsedInfo['Словом'] ?? '',
            'note' => $this->parsedInfo['Забележка'] ?? '',
            'totalPriceFromSupplier' => '',
        ];

        /**
         * Split parsed totalPrice
         *
         *  1. Обща стойност на покупките, без ДДС, преди отстъпка;
         *  2. Обща стойност на стоката по цени, които доставчикът е определил като пределни за продажба;
         *  totalPriceWithTax -> 3. Обща стойност, която РЕАЛНО е за плащане за закупената стока = стойност преди отстъпка - стойността на отстъпка + ДДС.
         */
        if($this->result['totalPrice'] !== '') {
            $priceDetails = explode(' ', $this->result['totalPrice']);
            $this->result['totalPrice'] = $priceDetails[0];
            $this->result['totalPriceFromSupplier'] = $priceDetails[1] ?? '';
        }

        //Fix price to be able to fit in database
        $this->result['taxableValue'] = NumberFormat::formatPrice($this->result['taxableValue']);
        $this->result['totalPrice'] = NumberFormat::formatPrice($this->result['totalPrice']);
        $this->result['totalPriceFromSupplier'] = NumberFormat::formatPrice($this->result['totalPriceFromSupplier']);
        $this->result['tradeDiscount'] = NumberFormat::formatPrice($this->result['tradeDiscount']);
        $this->result['taxBase9'] = NumberFormat::formatPrice($this->result['taxBase9']);
        $this->result['tax9'] = NumberFormat::formatPrice($this->result['tax9']);
        $this->result['taxBase20'] = NumberFormat::formatPrice($this->result['taxBase20']);
        $this->result['tax20'] = NumberFormat::formatPrice($this->result['tax20']);
        $this->result['taxBase0'] = NumberFormat::formatPrice($this->result['taxBase0']);
        $this->result['totalPriceWithTax'] = NumberFormat::formatPrice($this->result['totalPriceWithTax']);
    }

    private function setAlias()
    {
        $this->alias = [
            'taxableValue' => 'ОБЛАГАЕМА СТОЙНОСТ',
            'totalPrice' => 'ОБЩА СТОЙНОСТ',
            'tradeDiscount' => 'ТЪРГОВСКА ОТСТЪПКА',
            'taxBase9' => 'ДАНЪЧНА ОСНОВА ЗА  9 % ДДС',
            'tax9' => 'НАЧИСЛЕН ДДС ЗА  9 % ДДС',
            'taxBase20' => 'ДАНЪЧНА ОСНОВА ЗА 20 % ДДС',
            'tax20' => 'НАЧИСЛЕН ДДС ЗА 20 % ДДС',
            'taxBase0' => 'ДАНЪЧНА ОСНОВА ЗА  0 % ДДС',
            'totalPriceWithTax' => 'СУМА ЗА ПЛАЩАНЕ',
            'totalPriceWithTaxInWords' => 'Словом',
            'note' => 'Забележка',
            'totalPriceFromSupplier' => 'ОБЩА СТОЙНОСТ ОТ ДОСТАВЧИКА (пределна)',
        ];
    }
}