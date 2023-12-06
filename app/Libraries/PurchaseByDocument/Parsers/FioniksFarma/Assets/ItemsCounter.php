<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

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
        $searchPattern = '/Общо\s+кол\.\s+(\d+)\s+бр\./';

        foreach ($this->fileContentByLines as $line) {
            if (preg_match($searchPattern, $line, $matches)) {
                $this->totalItemsCount = (int)$matches[1];
            }
        }
    }
}