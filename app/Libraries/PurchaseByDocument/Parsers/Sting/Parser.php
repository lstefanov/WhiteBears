<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting;

use DOMDocument;
use DOMXPath;

class Parser
{
    public array $result = [];

    /**
     * @var false|string[]
     */
    private $fileContentByLines;

    private int $invoiceType = 1; //1- Фактура, 2 - Абонамент

    public function execute(string $fileContent)
    {
        //Check for Invoice Type
        $this->checkInvoiceType($fileContent);

        //FIX encoding and headers in HTML content
        //search for meta charset=Windows-1251 header
        if(strpos($fileContent, 'charset=windows-1251') !== false){
            $encoding = mb_detect_encoding($fileContent, "UTF-8, Windows-1251, ASCII", true);
            if($encoding === 'UTF-8' ){
                //convert $fileContent to Windows-1251
                $fileContent = mb_convert_encoding($fileContent, 'Windows-1251', 'UTF-8');
            }
        } else {
            $searchMetaTag = '<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">';

            $pattern = '/<head>/i';

            // Define the replacement string
            $replacement = "<head>\n    " . $searchMetaTag;

            // Replace the first occurrence of the <head> tag with the <head> tag followed by the meta tag
            $fileContent = preg_replace($pattern, $replacement, $fileContent, 1);
            $fileContent = mb_convert_encoding($fileContent, 'Windows-1251', 'UTF-8');
        }



        $dom = new DOMDocument();
        @$dom->loadHTML($fileContent);

        $xpath = new DOMXPath($dom);
        $rows = $xpath->query('//table//tr');

        $this->fileContentByLines = [];
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');
            $rowData = [];
            foreach ($cells as $cell) {
                // Capture the cell's text, ensuring any further encoding needs are addressed
                $rowData[] = trim($cell->textContent);
            }
            $this->fileContentByLines[] = $rowData;
        }


        //Get items count
        $itemsCounter = new Assets\ItemsCounter($this->fileContentByLines);
        $itemsCounter->execute();
        $this->result['itemsCount'] = $itemsCounter->getTotalItemsCount();

        //Get recipient info
        $recipientParser = new Assets\Recipient($this->fileContentByLines);
        $recipientParser->execute();
        $this->result['recipient']['parsed'] = $recipientParser->getParsedInfo();
        $this->result['recipient']['raw'] = $recipientParser->getRawInfo();
        $this->result['recipient']['result'] = $recipientParser->getResult();
        $this->result['recipient']['alias'] = $recipientParser->getAlias();


        //Get supplier info
        $supplierParser = new Assets\Supplier($this->fileContentByLines);
        $supplierParser->execute();
        $this->result['supplier']['parsed'] = $supplierParser->getParsedInfo();
        $this->result['supplier']['raw'] = $supplierParser->getRawInfo();
        $this->result['supplier']['result'] = $supplierParser->getResult();
        $this->result['supplier']['alias'] = $supplierParser->getAlias();


        //Get Delivery info
        $deliveryParser = new Assets\Delivery($this->fileContentByLines);
        $deliveryParser->execute();
        $this->result['delivery']['parsed'] = $deliveryParser->getParsedInfo();
        $this->result['delivery']['raw'] = $deliveryParser->getRawInfo();
        $this->result['delivery']['result'] = $deliveryParser->getResult();
        $this->result['delivery']['alias'] = $deliveryParser->getAlias();

        //Get invoice info
        $invoiceInfoParser = new Assets\InvoiceInfo($this->fileContentByLines);
        $invoiceInfoParser->execute();
        $this->result['invoiceInfo']['parsed'] = $invoiceInfoParser->getParsedInfo();
        $this->result['invoiceInfo']['raw'] = $invoiceInfoParser->getRawInfo();
        $this->result['invoiceInfo']['result'] = $invoiceInfoParser->getResult();
        $this->result['invoiceInfo']['alias'] = $invoiceInfoParser->getAlias();


        //get invoice price
        $invoicePriceParser = new Assets\InvoicePrice($this->fileContentByLines);
        $invoicePriceParser->execute();
        $this->result['invoicePrice']['parsed'] = $invoicePriceParser->getParsedInfo();
        $this->result['invoicePrice']['raw'] = $invoicePriceParser->getRawInfo();
        $this->result['invoicePrice']['result'] = $invoicePriceParser->getResult();
        $this->result['invoicePrice']['alias'] = $invoicePriceParser->getAlias();

        //get invoice payment
        $invoicePaymentParser = new Assets\InvoicePayment($this->fileContentByLines);
        $invoicePaymentParser->execute();
        $this->result['invoicePayment']['parsed'] = $invoicePaymentParser->getParsedInfo();
        $this->result['invoicePayment']['raw'] = $invoicePaymentParser->getRawInfo();
        $this->result['invoicePayment']['result'] = $invoicePaymentParser->getResult();
        $this->result['invoicePayment']['alias'] = $invoicePaymentParser->getAlias();


        //get invoice items
        if($this->invoiceType === 1){
            $invoiceItemsParser = new Assets\Items($this->fileContentByLines);
        } else {
            //skip
        }

        $invoiceItemsParser->execute();
        $this->result['invoiceItems']['parsed'] = $invoiceItemsParser->getParsedInfo();
        $this->result['invoiceItems']['raw'] = $invoiceItemsParser->getRawInfo();
        $this->result['invoiceItems']['result'] = $invoiceItemsParser->getResult();
        $this->result['invoiceItems']['alias'] = $invoiceItemsParser->getAlias();
        $this->result['nzok'] = $invoiceItemsParser->getIsNZOK();
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getInvoiceType(): int
    {
        return $this->invoiceType;
    }

    private function checkInvoiceType(string $fileContent): void
    {
        $this->invoiceType = 1;
    }
}