<?php

namespace App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Assets;

class Delivery
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
        $deliveryInfoLines = [9, 15]; //Start line and end line
        $deliveryInfoMatrix = [95, 34]; //Start position and length

        foreach ($this->fileContentByLines as $lineCounter => $line) {
            if ($lineCounter >= $deliveryInfoLines[0] && $lineCounter <= $deliveryInfoLines[1]) {
                $this->rawInfo .= mb_substr($line, $deliveryInfoMatrix[0], $deliveryInfoMatrix[1]) . "\n";
            }
        }

        $pattern = [
            'Ср.на доставка' => '/Ср\.на доставка:\s*([^\n\r]+)/',
            'Място' => '/Място:\s*([^\n\r]+)/',
            'Адрес' => '/УЛ\.\s*([^\n\r]+)[\s\S]*?([^\n\r]+)/',
            'Маршрут' => '/Маршрут:\s*([^\n\r]+)/',
        ];

        // Extract information using regular expressions
        foreach ($pattern as $key => $regex) {
            if (preg_match($regex, $this->rawInfo, $matches)) {
                $this->parsedInfo[$key] = trim(preg_replace('/\s+/', ' ', implode(' ', array_slice($matches, 1))));
            }
        }
    }

    private function fixResult()
    {
        $this->result = [
            'averageDelivery' => $this->parsedInfo['Ср.на доставка'] ?? '',
            'place' => $this->parsedInfo['Място'] ?? '',
            'address' => $this->parsedInfo['Адрес'] ?? '',
            'route' => $this->parsedInfo['Маршрут'] ?? '',
        ];

        //Convert Ср.на доставка
        try{
            $averageDeliveryDate = \DateTime::createFromFormat('d.m.y', $this->result['averageDelivery']);
            if(!$averageDeliveryDate){ Throw new \Exception('Invalid date format'); }
            $this->result['averageDelivery'] = $averageDeliveryDate->format('Y-m-d');
        } catch (\Exception $e) {
            $this->result['averageDelivery'] = null;
        }
    }

    private function setAlias()
    {
        $this->alias = [
            'averageDelivery' => 'Ср.на доставка',
            'place' => 'Място',
            'address' => 'Адрес',
            'route' => 'Маршрут',
        ];
    }
}