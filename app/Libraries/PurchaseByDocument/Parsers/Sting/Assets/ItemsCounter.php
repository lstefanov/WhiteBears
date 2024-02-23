<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Sting\Assets;

class ItemsCounter
{
    private array $fileContentByLines;

    private int $totalItemsCount = 0;

    public function __construct(array $fileContentByLines)
    {
        $this->fileContentByLines = $fileContentByLines;
    }

    public function getTotalItemsCount(): int
    {
        return $this->totalItemsCount;
    }

    public function execute()
    {
        //search for items count
        $foundedStartLine = false; //search for 'Лекарствено средство' in array sub elements
        foreach ($this->fileContentByLines as $itemKey => $item) {

            if($foundedStartLine === true){
                if(isset($item[4])) {
                    $this->totalItemsCount += (int)$item[4];
                }

            }

            foreach ($item as $subItem) {
                if (strpos($subItem, 'Лекарствено средство') !== false) {
                    $foundedStartLine = true;
                    break;
                }
            }

            foreach ($item as $subItem) {
                if (strpos($subItem, 'Всичко :') !== false) {
                    $foundedStartLine = false;
                    //$this->totalItemsCount--;
                    break;
                }
            }
        }
    }
}