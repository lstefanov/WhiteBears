<?php

namespace App\Controllers\Test;

use App\Controllers\BaseController;
use Config\Services;

class Txt extends BaseController
{

    /**
     * @var false|string
     */
    private $fileContent = "";

    /**
     * @var false|string[]
     */
    private array $fileContentByLines = [];

    private array $invoiceItemsText = [];


    public function __construct()
    {
        //get file content from writable folder
        $this->fileContent = file_get_contents(WRITEPATH . 'test/txt/2.txt');
        $this->fileContentByLines = explode("\n", $this->fileContent);
    }


    public function parse()
    {
        //$this->getRecipientAndSupplierInfo(); //DONE
        //$this->getInvoiceInfo(); // Done
        //$this->getDeliveryInfo(); //Done
        //$this->getInvoiceDetails(); //Done

        $this->parseInvoiceItems();
        $this->getInvoiceItems();
    }


    private function getInvoiceItems()
    {
        $itemNumberMatrix = [0, 7];
        $currentItemNumber = 0;
        $items = [];

        $itemDetailsMatrix = [
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

        /**
         * Get item number and matrix
         */
        foreach ($this->invoiceItemsText as $lineNumber => $line){
            $invoiceItemNumberMatrixForLine = mb_substr($line, $itemNumberMatrix[0], $itemNumberMatrix[1]);

            /*
             * Validate for item number
             */

            //empty matrix
            if(empty($invoiceItemNumberMatrixForLine)){
                continue;
            }

            //INN: string ???
            if(mb_strpos(mb_strtolower($invoiceItemNumberMatrixForLine), 'inn:') !== false ){
                continue;
            }

            //is numeric
            if(!is_numeric($invoiceItemNumberMatrixForLine)){
                continue;
            }


            //set invoice number that we found in the line
            $invoiceItemNumber = (int) $invoiceItemNumberMatrixForLine;

            if($invoiceItemNumber > $currentItemNumber){
                $currentItemNumber = $invoiceItemNumber;

                if($currentItemNumber > 1){
                    $items[$currentItemNumber - 1]['matrix'][1] = $lineNumber - 1;
                }

                $items[$invoiceItemNumber] = [
                    'matrix' => [$lineNumber, 0],
                    'details' => [],
                ];
            }
        }

        //Set last element matrix (line end
        $items[$currentItemNumber]['matrix'][1] = count($this->invoiceItemsText) - 1;




        /**
         * Get item content
         */
        foreach ($items as $itemNumber => $item){
            foreach ($this->invoiceItemsText as $lineNumber => $line){
                if($lineNumber >= $item['matrix'][0] && $lineNumber <= $item['matrix'][1]){
                    $items[$itemNumber]['content'][] = $line;
                }
            }
        }




        /**
         * Get item details
         */
        foreach ($items as $itemNumber => $item){
            $itemDetails = [];

            foreach ($item['content'] as $lineNumber => $line){
                if(mb_strpos(mb_strtolower($line), 'inn:') !== false ){
                    $items[$itemNumber]['details']['INN'] = $line;
                    continue;
                }

                //check if this is the main line
                $invoiceItemNumberMatrixForLine = mb_substr($line, $itemNumberMatrix[0], $itemNumberMatrix[1]);
                if(!empty($invoiceItemNumberMatrixForLine) && is_numeric($invoiceItemNumberMatrixForLine)){
                    //$items[$itemNumber]['mainLine'] = $line;

                    foreach ($itemDetailsMatrix as $key => $matrix){
                        $itemDetailsMatrixValue = mb_substr($line, $matrix[0], $matrix[1]);
                        $items[$itemNumber]['details'][$key] = $itemDetailsMatrixValue;
                    }
                } else {
                    //other lines (not main and not INN)
                    foreach ($itemDetailsMatrix as $key => $matrix){
                        $itemDetailsMatrixValue = mb_substr($line, $matrix[0], $matrix[1]);
                        $items[$itemNumber]['details'][$key] .= " " . $itemDetailsMatrixValue;
                    }
                }
            }
        }


        //Clear item content from whitespaces
        foreach ($items as $itemNumber => $item){
            foreach ($item['details'] as $key => $value){
                $items[$itemNumber]['details'][$key] = trim($value);
            }
        }


        print_r2($items,1);
    }


    private function parseInvoiceItems()
    {
        $invoiceItemsLines = [0, 0]; //Start line and end line

        $patternItemSeparator = '--------------------------------------------------------------------------------------------------------------------------';
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


        $invoiceItemsInfo = [];

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if($lineCounter >= $invoiceItemsLines[0] && $lineCounter <= $invoiceItemsLines[1]) {
                $invoiceItemsInfo[] = $line;
            }
        }

        $this->invoiceItemsText = $invoiceItemsInfo;
    }


    private function getInvoiceDetails()
    {
        $invoiceDetailsLines = [0, 0]; //Start line and end line
        $invoicePaymentLines = [0, 0]; //Start line and end line

        $patternBegin = '--------------------------------------------------------------------------------------------------------------------------';
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



        $invoiceDetailsInfo = "";

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if($lineCounter >= $invoiceDetailsLines[0] && $lineCounter <= $invoiceDetailsLines[1]) {
                $invoiceDetailsInfo .= $line . "\n";
            }
        }

        print_r2($invoiceDetailsInfo);

        // Define an associative array to store the extracted values
        $pattern = [
            'ОБЩА СТОЙНОСТ'               => '/ОБЩА СТОЙНОСТ\s*:\s*([^\n\r]+)/',
            'ТЪРГОВСКА ОТСТЪПКА'           => '/ТЪРГОВСКА ОТСТЪПКА\s*:\s*([^\n\r]+)/',
            'ДАНЪЧНА ОСНОВА ЗА  9 % ДДС'   => '/ДАНЪЧНА ОСНОВА ЗА  9 % ДДС\s*:\s*([^\n\r]+)/',
            'НАЧИСЛЕН ДДС ЗА  9 % ДДС'     => '/НАЧИСЛЕН ДДС ЗА  9 % ДДС\s*:\s*([^\n\r]+)/',
            'ДАНЪЧНА ОСНОВА ЗА 20 % ДДС'   => '/ДАНЪЧНА ОСНОВА ЗА 20 % ДДС\s*:\s*([^\n\r]+)/',
            'НАЧИСЛЕН ДДС ЗА 20 % ДДС'     => '/НАЧИСЛЕН ДДС ЗА 20 % ДДС\s*:\s*([^\n\r]+)/',
            'ДАНЪЧНА ОСНОВА ЗА  0 % ДДС'      => '/ДАНЪЧНА ОСНОВА ЗА  0 % ДДС\s*:\s*([^\n\r]+)/',
            'СУМА ЗА ПЛАЩАНЕ'              => '/СУМА ЗА ПЛАЩАНЕ\s*:\s*([^\n\r]+)/',
            'Словом'                      => '/Словом\s*:\s*([^\n\r]+)/',
            'Забележка'                   => '/Забележка:\s*([^\n\r]+)/',
        ];

        // Extract information using regular expressions
        $result = [];
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $invoiceDetailsInfo, $matches)) {
                $result[$key] = trim(preg_replace('/\s+/', ' ', $matches[1]));
            }
        }

        // Output the result
        print_r2($result);





        echo "<br />----------------------------------<br />";


        $invoicePaymentLines[0] = $invoiceDetailsLines[1] - 1;
        $invoicePaymentLines[1] = $invoiceDetailsLines[1];
        $invoicePaymentDetailsInfo = "";

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if($lineCounter >= $invoicePaymentLines[0] && $lineCounter <= $invoicePaymentLines[1]) {
                $invoicePaymentDetailsInfo .= $line . "\n";
            }
        }

        print_r2($invoicePaymentDetailsInfo);



        // Define an associative array to store the extracted values
        $pattern = [
            'Плащане'               => '/Плащане\s*:\s*([^\n\r]+?)\s*Дата на плащане\s*:/',
            'Дата на плащане'       => '/Дата на плащане\s*:\s*([^\n\r]+?)\s*Дата на данъчно събитие\s*:/',
            'Дата на данъчно събитие' => '/Дата на данъчно събитие\s*:\s*([^\n\r]+)/',
            'Раэплащателна с-ка BIC' => '/Раэплащателна с-ка BIC\s*:\s*([^\n\r]+?)\s*IBAN\s*:/',
            'IBAN'                  => '/IBAN\s*:\s*([^\n\r]+?)\s*Банка\s*:/',
            'Банка'                 => '/Банка\s*:\s*([^\n\r]+)/',
        ];

// Extract information using regular expressions
        $result = [];
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $invoicePaymentDetailsInfo, $matches)) {
                $result[$key] = trim(preg_replace('/\s+/', ' ', $matches[1]));
            }
        }


        // Output the result
        print_r2($result);
    }

    private function getDeliveryInfo()
    {
        $deliveryInfoLines = [9, 15]; //Start line and end line
        $deliveryInfoMatrix = [95, 34]; //Start position and length

        $deliveryInfo = "";

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if($lineCounter >= $deliveryInfoLines[0] && $lineCounter <= $deliveryInfoLines[1]) {
                $deliveryInfo .= mb_substr($line, $deliveryInfoMatrix[0], $deliveryInfoMatrix[1]) . "\n";
            }
        }


        print_r2($deliveryInfo);


        // Define an associative array to store the extracted values
        $pattern = [
            'Ср.на доставка' => '/Ср\.на доставка:\s*([^\n\r]+)/',
            'Място'          => '/Място:\s*([^\n\r]+)/',
            'Адрес'          => '/УЛ\.\s*([^\n\r]+)[\s\S]*?([^\n\r]+)/',
            'Маршрут'        => '/Маршрут:\s*([^\n\r]+)/',
        ];

// Extract information using regular expressions
        $result = [];
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $deliveryInfo, $matches)) {
                $result[$key] = trim(preg_replace('/\s+/', ' ', implode(' ', array_slice($matches, 1))));
            }
        }

        // Output the result
        print_r2($result);
    }

    private function getInvoiceInfo()
    {
        $invoiceInfoLines = [18, 18]; //Start line and end line
        $recipientContentMatrix = [0, 120]; //Start position and length

        $invoiceInfo = "";

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if($lineCounter >= $invoiceInfoLines[0] && $lineCounter <= $invoiceInfoLines[1]) {
                $invoiceInfo .= mb_substr($line, $recipientContentMatrix[0], $recipientContentMatrix[1]) . "\n";
            }
        }

        echo $invoiceInfo;


        $pattern = [
            'NO.'             => '/NO\.\s*([^\/\n\r]+)/',
            'Дата на издаване' => '/Дата на издаване\s*([^\/\n\r]+)/',
        ];

        // Extract information using regular expressions
        $result = [];
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $invoiceInfo, $matches)) {
                $result[$key] = trim($matches[1]);
            }
        }

        // Output the result
        print_r2($result);
    }

    private function getRecipientAndSupplierInfo()
    {
        $recipientAndSupplierInfoLines = [8, 16]; //Start line and end line
        $recipientContentMatrix = [0, 43]; //Start position and length
        $supplierContentMatrix = [43, 43]; //Start position and length

        $recipientInfo = "";
        $supplierInfo = "";

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if($lineCounter >= $recipientAndSupplierInfoLines[0] && $lineCounter <= $recipientAndSupplierInfoLines[1]) {
                $recipientInfo .= mb_substr($line, $recipientContentMatrix[0], $recipientContentMatrix[1]) . "\n";
                $supplierInfo .= mb_substr($line, $supplierContentMatrix[0], $supplierContentMatrix[1]) . "\n";
            }
        }

        print_r2($recipientInfo);

        // Define an associative array to store the extracted values
        $pattern = [
            'Име'      => '/Име:\s*([^\n\r]+)/',
            'Адрес'    => '/Адрес:\s*([\s\S]+?)\s*Ид.н.допк:/',
            'Ид.н.допк' => '/Ид.н.допк:\s*([^\n\r]+)/',
            'Ид.н.зддс' => '/Ид.н.зддс:\s*([^\n\r]+)/',
            'Лиценз'   => '/Лиценз:\s*([^\n\r]+)/',
            'Л.опиати' => '/Л.опиати:\s*([^\n\r]+)/',
            'МОЛ'      => '/МОЛ:\s*([^\n\r]+)/',
            'Телефон'  => '/Телефон:\s*([^\n\r]+)/',
        ];

        // Extract information using regular expressions
        $result = [];
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $recipientInfo, $matches)) {
                $result[$key] = trim($matches[1]);
            }
        }
        print_r2($result);

        echo "<br />----------------------------------<br />";

        print_r2($supplierInfo);

        $result = [];
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $supplierInfo, $matches)) {
                $result[$key] = trim($matches[1]);
            }
        }
        print_r2($result);
    }

    private function getRecipientAndSupplierInfo_bu()
    {
        $startContentToParse = false;
        $startLineContentToSearch = 'П О Л У Ч А Т Е Л';
        $endLineContentToSearch = '----------------------------------------------------------------------';

        $recipientContent = "";
        $supplierContent = "";

        $recipientAndSupplierInfoLines = [8, 17];
        $recipientContentMatrix = [0, 43];
        $supplierContentMatrix = [43, 86];


        die();
        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if (strpos($line, $startLineContentToSearch) !== false) {
                $startContentToParse = true;
                continue;
            }

            if (strpos($line, $endLineContentToSearch) !== false) {
                $startContentToParse = false;
                continue;
            }

            if (!$startContentToParse) {
                continue;
            }


            //Search for name
            if (strpos($line, 'Име: ') !== false) {
                echo $lineCounter . ' - ' . $line . '<br>';

                $pattern = '/Име:\s*([^\d]+)\s*Име:\s*(.+?)(?:\s*-+\s*|$)/u';

                if (preg_match($pattern, $line, $matches)) {
                    $firstValue = trim($matches[1]);
                    $secondValue = trim($matches[2]);

                    echo "First Value: $firstValue<br />";
                    echo "Second Value: $secondValue<br />";
                } else {
                    echo "No match found.\n";
                }
            }


        }
    }
}