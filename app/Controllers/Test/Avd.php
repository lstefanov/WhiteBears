<?php

namespace App\Controllers\Test;

use App\Controllers\BaseController;

class Avd extends BaseController
{
    public function parse()
    {
        $filePath = WRITEPATH . 'test/avd/3005165588.AVD';

        $arrayData = $this->readCyrillicXmlFile($filePath);
        print_r2($arrayData);

        die();
    }


    private function readCyrillicXmlFile($filePath) {
        // Check if the file exists
        if (!file_exists($filePath)) {
            return "File not found";
        }

        // Read the content of the file
        $content = file_get_contents($filePath);

        // Convert from Windows-1251 to UTF-8
        $contentUtf8 = iconv('Windows-1251', 'UTF-8', $content);

        // Load the XML content
        $xml = simplexml_load_string($contentUtf8);
        if ($xml === false) {
            return "Failed to parse XML";
        }

        // Convert the SimpleXMLElement object to an array
        $result = json_decode(json_encode($xml), true);

        return $result;
    }
}