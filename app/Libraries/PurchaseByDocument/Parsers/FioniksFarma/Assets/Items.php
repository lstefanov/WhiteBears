<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

use App\Helpers\NumberFormat;
use Faker\Core\Number;

class Items
{
    private array $fileContentByLines;

    private bool $invoiceItemsWithCodeField = false;

    private array $rawInfo = [];

    private array $parsedInfo = [];

    private array $itemDetailsMatrixWithCode = [
        'Код' => [6, 6],
        'Наименование' => [12, 22],
        'М-ка' => [35, 5],
        'Кол.' => [40, 5],
        'Баз.цен' => [45, 8],
        'ТН' => [53, 3],
        'ТО' => [56, 3],
        'Цена ТЕ' => [59, 8],
        'Стойност' => [67, 10],
        'Ц.с ддс' => [77, 8],
        'Партида' => [85, 9],
        'Cертификат' => [94, 11],
        'Ср.г.' => [105, 6],
        'Ц.Апт.' => [111, 9],
        'Пред.цена' => [120, 10]
    ];


    private array $itemDetailsMatrixWithoutCode = [
        'Наименование' => [7, 24],
        'М-ка' => [32, 5],
        'Кол.' => [38, 7],
        'Баз.цен' => [45, 8],
        'ТН' => [53, 3],
        'ТО' => [56, 3],
        'Цена ТЕ' => [59, 8],
        'Стойност' => [67, 10],
        'Ц.с ддс' => [77, 8],
        'Партида' => [85, 9],
        'Cертификат' => [94, 11],
        'Ср.г.' => [105, 6],
        'Ц.Апт.' => [111, 9],
        'Пред.цена' => [120, 10]
    ];
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

    public function getRawInfo(): array
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

    public function getIsNZOK()
    {
        return $this->invoiceItemsWithCodeField ? 1 : 0;
    }

    public function execute()
    {
        $this->checkInvoiceItemsForCodeField(); //Done
        $this->parseInvoiceItems();
        $this->getInvoiceItems();
    }

    private function checkInvoiceItemsForCodeField()
    {
        $patternBegin = '------------------------------------------------------------------------------------------------';
        $lineNumber = 0;  // Variable to track the current line number
        $count = 0;  // Variable to track the occurrence count
        $foundedContent = '';

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            $lineNumber++;

            if (strpos($line, $patternBegin) !== false) {
                $count++;
            }

            if ($count == 1) {
                $foundedContent = $this->fileContentByLines[$lineNumber - 1];
            }
        }


        if (mb_strpos($foundedContent, 'Код') !== false) {
            $this->invoiceItemsWithCodeField = true;
        }
    }


    private function parseInvoiceItems()
    {
        $invoiceItemsLines = [0, 0]; //Start line and end line

        $patternItemSeparator = '------------------------------------------------------------------------------------------------';
        $count = 0;  // Variable to track the occurrence count
        $lineNumber = 0;  // Variable to track the current line number


        foreach ($this->fileContentByLines as $lineCounter => $line) {
            $lineNumber++;

            //Search for line where the pattern begins
            if (strpos($line, $patternItemSeparator) !== false) {
                $count++;
                if ($count == 2) {
                    $invoiceItemsLines[0] = $lineNumber;
                }

                if ($count == 3) {
                    $invoiceItemsLines[1] = $lineNumber - 2;
                }
            }
        }


        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if ($lineCounter >= $invoiceItemsLines[0] && $lineCounter <= $invoiceItemsLines[1]) {
                $this->rawInfo[] = $line;
            }
        }
    }


    private function getInvoiceItems()
    {
        $itemNumberMatrix = [0, 7];
        $currentItemNumber = 0;

        $itemDetailsMatrix = $this->invoiceItemsWithCodeField ? $this->itemDetailsMatrixWithCode : $this->itemDetailsMatrixWithoutCode;

        /**
         * Get item number and matrix
         */
        foreach ($this->rawInfo as $lineNumber => $line) {
            $invoiceItemNumberMatrixForLine = mb_substr($line, $itemNumberMatrix[0], $itemNumberMatrix[1]);

            /*
             * Validate for item number
             */

            //empty matrix
            if (empty($invoiceItemNumberMatrixForLine)) {
                continue;
            }

            //INN: string ???
            if (mb_strpos(mb_strtolower($invoiceItemNumberMatrixForLine), 'inn:') !== false) {
                continue;
            }

            //is numeric
            if (!is_numeric($invoiceItemNumberMatrixForLine)) {
                continue;
            }


            //set invoice number that we found in the line
            $invoiceItemNumber = (int)$invoiceItemNumberMatrixForLine;

            if ($invoiceItemNumber > $currentItemNumber) {
                $currentItemNumber = $invoiceItemNumber;

                if ($currentItemNumber > 1) {
                    $this->parsedInfo[$currentItemNumber - 1]['matrix'][1] = $lineNumber - 1;
                }

                $this->parsedInfo[$invoiceItemNumber] = [
                    'matrix' => [$lineNumber, 0],
                    'details' => [],
                ];
            }
        }

        //Set last element matrix (line end
        $this->parsedInfo[$currentItemNumber]['matrix'][1] = count($this->rawInfo) - 1;


        /**
         * Get item content
         */
        foreach ($this->parsedInfo as $itemNumber => $item) {

            if(!isset($item['matrix'][0]) || !isset($item['matrix'][1])){
                continue;
            }

            foreach ($this->rawInfo as $lineNumber => $line) {
                if ($lineNumber >= $item['matrix'][0] && $lineNumber <= $item['matrix'][1]) {
                    $this->parsedInfo[$itemNumber]['content'][] = $line;
                }
            }
        }


        /**
         * Get item details
         */
        foreach ($this->parsedInfo as $itemNumber => $item) {
            $itemDetails = [];

            if(empty($item['content'])){
                continue;
            }

            foreach ($item['content'] as $lineNumber => $line) {
                if (mb_strpos(mb_strtolower($line), 'inn:') !== false) {
                    $this->parsedInfo[$itemNumber]['details']['INN'] = $line;
                    continue;
                }

                //check if this is the main line
                $invoiceItemNumberMatrixForLine = mb_substr($line, $itemNumberMatrix[0], $itemNumberMatrix[1]);
                if (!empty($invoiceItemNumberMatrixForLine) && is_numeric($invoiceItemNumberMatrixForLine)) {
                    foreach ($itemDetailsMatrix as $key => $matrix) {
                        $itemDetailsMatrixValue = mb_substr($line, $matrix[0], $matrix[1]);
                        $this->parsedInfo[$itemNumber]['details'][$key] = $itemDetailsMatrixValue;
                    }
                } else {
                    //other lines (not main and not INN)
                    foreach ($itemDetailsMatrix as $key => $matrix) {
                        $itemDetailsMatrixValue = mb_substr($line, $matrix[0], $matrix[1]);
                        $this->parsedInfo[$itemNumber]['details'][$key] .= trim($itemDetailsMatrixValue);
                    }
                }
            }
        }


        //Clear item content from whitespaces
        foreach ($this->parsedInfo as $itemNumber => $item) {
            if(empty($item['details'])){
                continue;
            }

            foreach ($item['details'] as $key => $value) {
                $this->parsedInfo[$itemNumber]['details'][$key] = trim($value);
            }
        }
    }


    /**
     * М-ка: Мярка
     * Кол.: Количество
     * Баз.цена: Базисна цена
     * ТН: Търговска надценка
     * ТО: Търговска отстъпка
     * Цена ТЕ: Цена на търговец на едро
     * Стойност: Стойност
     * Ц. с ддс: Цена с ДДС
     * Партида и Сертификат са ясни
     * Ср. г.: Срок на годност
     * Ц. апт.: Цена на аптека
     * Пред. цена: Пределна цена
     */
    private function fixResult()
    {
        foreach ($this->parsedInfo as $parsedKey => $parsedItem) {

            $itemDetails = [
                'itemNumber' => $parsedKey,
                'code' => $parsedItem['details']['Код'] ?? '',
                'designation' => $parsedItem['details']['Наименование'] ?? '',
                'manufacturer' => $parsedItem['details']['М-ка'] ?? '',
                'quantity' => $parsedItem['details']['Кол.'] ?? '',
                'basePrice' => $parsedItem['details']['Баз.цен'] ?? '',
                'tradeMarkup' => $parsedItem['details']['ТН'] ?? '',
                'tradeDiscount' => $parsedItem['details']['ТО'] ?? '',
                'wholesalerPrice' => $parsedItem['details']['Цена ТЕ'] ?? '',
                'value' => $parsedItem['details']['Стойност'] ?? '',
                'priceWithVAT' => $parsedItem['details']['Ц.с ддс'] ?? '',
                'batch' => $parsedItem['details']['Партида'] ?? '',
                'certificate' => $parsedItem['details']['Cертификат'] ?? '',
                'expiryDate' => $parsedItem['details']['Ср.г.'] ?? '',
                'pharmacyPrice' => $parsedItem['details']['Ц.Апт.'] ?? '',
                'limitPrice' => $parsedItem['details']['Пред.цена'] ?? '',
                'limitPriceType' => '',
                'INN' => $parsedItem['details']['INN'] ?? '',
            ];

            //Fix Пределна цена
            if ($itemDetails['limitPrice'] != '') {
                $limitPrice = explode(' ', $itemDetails['limitPrice']);
                $itemDetails['limitPrice'] = $limitPrice[0];
                $itemDetails['limitPriceType'] = $limitPrice[1] ?? '';
            }


            //Fix price to be able to fit in database
            $itemDetails['basePrice'] = NumberFormat::formatPrice($itemDetails['basePrice']);
            $itemDetails['tradeMarkup'] = NumberFormat::formatPrice($itemDetails['tradeMarkup']);
            $itemDetails['tradeDiscount'] = NumberFormat::formatPrice($itemDetails['tradeDiscount']);
            $itemDetails['wholesalerPrice'] = NumberFormat::formatPrice($itemDetails['wholesalerPrice']);
            $itemDetails['value'] = NumberFormat::formatPrice($itemDetails['value']);
            $itemDetails['priceWithVAT'] = NumberFormat::formatPrice($itemDetails['priceWithVAT']);
            $itemDetails['pharmacyPrice'] = NumberFormat::formatPrice($itemDetails['pharmacyPrice']);
            $itemDetails['limitPrice'] = NumberFormat::formatPrice($itemDetails['limitPrice']);


            $this->result[] = $itemDetails;
        }
    }


    private function setAlias()
    {
        $this->alias = [
            'itemNumber' => 'Номер на артикул',
            'code' => 'Код',
            'designation' => 'Наименование',
            'manufacturer' => 'М-ка',
            'quantity' => 'Кол.',
            'basePrice' => 'Баз.цен',
            'tradeMarkup' => 'ТН',
            'tradeDiscount' => 'ТО',
            'wholesalerPrice' => 'Цена ТЕ',
            'value' => 'Стойност',
            'priceWithVAT' => 'Ц.с ддс',
            'batch' => 'Партида',
            'certificate' => 'Cертификат',
            'expiryDate' => 'Ср.г.',
            'pharmacyPrice' => 'Ц.Апт.',
            'limitPrice' => 'Пред.цена',
            'limitPriceType' => 'Пред.цена тип',
            'INN' => 'INN',
        ];
    }
}