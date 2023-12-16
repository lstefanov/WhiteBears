<?php

namespace App\Controllers\Test;

use App\Controllers\BaseController;

class Sss extends BaseController
{
    public function parse()
    {

        $filePath = WRITEPATH . 'test/SSS/FA0204871679.S$$';

        $arrayData = $this->parseWindows1251EncodedFile($filePath);
        print_r2($arrayData);

        die();
    }


    private function parseWindows1251EncodedFile($filePath) {
        // Check if the file exists
        if (!file_exists($filePath)) {
            return "File not found";
        }

        // Read the file content
        $contentBytes = file_get_contents($filePath);

        // Decode the content from Windows-1251 to UTF-8
        // Adjusted to handle Bulgarian text
        $decodedContent = mb_convert_encoding($contentBytes, 'UTF-8', 'Windows-1251');

        // Split the content into lines
        $lines = explode("\n", $decodedContent);

        // Array to hold all entities
        $entities = [];

        // Process each line
        foreach ($lines as $line) {
            // Trim the line and check if it is not empty
            $trimmedLine = trim($line);
            if (!empty($trimmedLine)) {
                // Split the line into parts based on tabs
                $parts = explode("\t", $trimmedLine);
                // Add the parts to the entities array
                $entities[] = $parts;
            }
        }

        return $entities;
    }
}