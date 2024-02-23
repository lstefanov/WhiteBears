<?php

namespace App\Controllers\Test;

use App\Controllers\BaseController;
use DOMDocument;
use DOMXPath;

class Sting extends BaseController
{
    /**
     * @throws \Exception
     */
    public function parse()
    {
        return view('Test/Sting/form');
    }


    public function execute()
    {

        $content = $_POST['message'];

        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new DOMXPath($dom);
        $rows = $xpath->query('//table//tr');

        $data = [];
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');
            $rowData = [];
            foreach ($cells as $cell) {
                // Capture the cell's text, ensuring any further encoding needs are addressed
                $rowData[] = trim($cell->textContent);
            }
            $data[] = $rowData;
        }


        print_r2($data,1);
    }
}