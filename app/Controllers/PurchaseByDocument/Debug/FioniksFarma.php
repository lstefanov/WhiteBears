<?php

namespace App\Controllers\PurchaseByDocument\Debug;

use App\Controllers\BaseController;
use Config\Services;

class FioniksFarma extends BaseController
{
    public function __construct()
    {
        $this->session = Services::session();
    }

    public function display()
    {
        $parserResult = $this->session->get('PbParsedData');

        if(!$parserResult){
            die('No SESSION EXISTS !');
        }

        $itemToDisplay = $this->request->getGet('item');
        if($itemToDisplay){
            die('No SESSION EXISTS !');
        }

        if(!isset($parserResult[$itemToDisplay])){
            die('No such item !');
        }

        $data = $parserResult[$itemToDisplay]['parsed'];
        $originalContent = $parserResult[$itemToDisplay]['originalContent'] ?? '';

        return view('Test/PurchaseByDocument/debug', ['data' => $data, 'originalContent' => $originalContent]);
    }

}