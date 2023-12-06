<?php

namespace App\Controllers\Test;

use App\Controllers\BaseController;

class Txt2 extends BaseController
{
    public function parse()
    {
        $fileContent = file_get_contents(WRITEPATH . 'test/txt/4.txt');

        $fioniksFarmaParser = new \App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Parser();
        $fioniksFarmaParser->execute($fileContent);
        $parserResult = $fioniksFarmaParser->getResult();

        return view('Test/PurchaseByDocument/debug', ['data' => $parserResult]);
    }

}