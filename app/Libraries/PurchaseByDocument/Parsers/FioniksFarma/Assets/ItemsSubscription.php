<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

use App\Helpers\NumberFormat;
use Faker\Core\Number;

class ItemsSubscription
{
    private array $fileContentByLines;

    private bool $invoiceItemsWithCodeField = false;

    private array $rawInfo = [];

    private array $parsedInfo = [];

    private array $itemDetailsMatrix = [
        'Наименование' => [6, 39],
        'М-ка' => [46, 5],
        'Кол.' => [52, 5],
        'Ед.цен' => [58, 9],
        'ТО' => [68, 3],
        'Стойност' => [72, 10],
        'Ц.с ддс' => [83, 10],
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


    public function execute()
    {
        $this->parseInvoiceItems();
        $this->getInvoiceItems();
    }

    public function getIsNZOK(): int
    {
        return 0;
    }

    private function parseInvoiceItems()
    {
        $invoiceItemsLines = [0, 0]; //Start line and end line

        $patternItemSeparator = '-------------------------------------------------------------------------------------------';
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
        $itemNumberMatrix = [0, 6];
        $currentItemNumber = 0;

        $itemDetailsMatrix = $this->itemDetailsMatrix;

        /**
         * Get item number and matrix
         */
        foreach ($this->rawInfo as $lineNumber => $line) {
            $invoiceItemNumberMatrixForLine = mb_substr($line, $itemNumberMatrix[0], $itemNumberMatrix[1]);
            echo $invoiceItemNumberMatrixForLine;

            /*
             * Validate for item number
             */

            //empty matrix
            if (empty($invoiceItemNumberMatrixForLine)) {
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
                'designation' => $parsedItem['details']['Наименование'] ?? '',
                'manufacturer' => $parsedItem['details']['М-ка'] ?? '',
                'quantity' => $parsedItem['details']['Кол.'] ?? '',
                'basePrice' => $parsedItem['details']['Ед.цен'] ?? '',
                'tradeDiscount' => $parsedItem['details']['ТО'] ?? '',
                'value' => $parsedItem['details']['Стойност'] ?? '',
                'priceWithVAT' => $parsedItem['details']['Ц.с ддс'] ?? '',
            ];


            //Fix price to be able to fit in database
            $itemDetails['basePrice'] = NumberFormat::formatPrice($itemDetails['basePrice']);
            $itemDetails['value'] = NumberFormat::formatPrice($itemDetails['value']);
            $itemDetails['priceWithVAT'] = NumberFormat::formatPrice($itemDetails['priceWithVAT']);

            $this->result[] = $itemDetails;
        }
    }


    private function setAlias()
    {
        $this->alias = [
            'itemNumber' => 'Номер на артикул',
            'designation' => 'Наименование',
            'manufacturer' => 'М-ка',
            'quantity' => 'Кол.',
            'basePrice' => 'Ед.цена',
            'tradeDiscount' => 'ТО',
            'value' => 'Стойност',
            'priceWithVAT' => 'Ц.с ддс',
        ];
    }
}