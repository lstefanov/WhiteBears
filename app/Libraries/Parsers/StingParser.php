<?php

namespace App\Libraries\Parsers;

use App\Models\BusinessesCompaniesModel;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StingParser
{
    private array $sheetData = [];


    public array $errors = [];

    /**
     * @var int
     * 1 - mean it has file error
     * 2 - mean there is no valid rows
     */
    public int $errorType = 1;

    public array $entitiesStatistics = [
        'total' => 0,
        'success' => 0,
        'error' => 0
    ];


    public function execute(string $file, array &$parsedInvoicesNumbers, int $businessId): array
    {
        //Parse file
        $spreadsheet = IOFactory::load($file);
        $this->sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        //Validate file header
        $this->validateParsedFileHeaders();

        //Validate file data
        if (empty($this->errors)) {
            $this->validateParsedFileData($parsedInvoicesNumbers, $businessId);

            $this->validateForValidRows();
        }

        return $this->sheetData;
    }

    private function validateForValidRows()
    {
        $validRows = 0;
        $invalidRows = 0;
        foreach ($this->sheetData as $rowKey => $row) {
            if ($rowKey < 5) { // skip header data
                continue;
            }

            if (count($this->sheetData) === $rowKey) { // skip footer data
                continue;
            }


            if ($row['status'] === 'success') {
                $validRows++;
            } else {
                $invalidRows++;
            }
        }

        if(empty($this->errors) && $validRows === 0){
            $this->errorType = 2;
            $this->errors = ['Няма валидни редове за запис !'];
        }
    }

    private function validateParsedFileHeaders()
    {
        $errors = [];

        if(!isset($this->sheetData[4])){
            $this->errors[] = 'Невалиден документ !';
            return;
        }

        $headers = $this->sheetData[4];

        if (!isset($headers['A'])) {
            $errors[] = 'Колона А "No Док" липсва !';
        }
        if (!isset($headers['B'])) {
            $errors[] = 'Колона B "Тип Док" липсва !';
        }
        if (!isset($headers['C'])) {
            $errors[] = 'Колона C "Дата на Док" липсва !';
        }
        if (!isset($headers['D'])) {
            $errors[] = 'Колона D "Начин на плащане" липсва !';
        }
        if (!isset($headers['E'])) {
            $errors[] = 'Колона E "Сума с ДДС" липсва !';
        }

        foreach ($headers as $headerKey => $header) {
            $header2 = mb_strtolower($header);

            if ($headerKey === 'A' && $header2 !== 'no док') {
                $errors[] = 'Колона А трябва да съдържа "No Док"';
            }

            if ($headerKey === 'B' && $header2 !== 'тип док') {
                $errors[] = 'Колона B трябва да съдържа "Тип Док"';
            }

            if ($headerKey === 'C' && $header2 !== 'дата на док') {
                $errors[] = 'Колона C трябва да съдържа "Дата на Док"';
            }

            if ($headerKey === 'D' && $header2 !== 'начин на плащане') {
                $errors[] = 'Колона D трябва да съдържа "Начин на плащане"';
            }

            if ($headerKey === 'E' && $header2 !== 'сума с ддс') {
                $errors[] = 'Колона E трябва да съдържа "Сума с ДДС"';
            }
        }

        $this->errors = array_merge($this->errors, $errors);
    }

    private function validateParsedFileData(&$parsedInvoicesNumbers, int $businessId)
    {
        foreach ($this->sheetData as $rowKey => $row) {
            if ($rowKey < 5) { // skip header data
                continue;
            }

            if (count($this->sheetData) === $rowKey) { // skip footer data
                continue;
            }

            $this->entitiesStatistics['total']++;

            $this->sheetData[$rowKey]['status'] = 'success';
            $this->sheetData[$rowKey]['status_details'] = [];
            $this->sheetData[$rowKey]['business_id'] = $businessId;

            //Base Validation
            $basicValidation = $this->baseValidation($row);
            if ($basicValidation !== true) {
                $this->sheetData[$rowKey]['status'] = 'error';
                $this->sheetData[$rowKey]['status_details'][] = $basicValidation;
                $this->entitiesStatistics['error']++;
                continue;
            }


            //Validate invoice duplicate
            $invoiceNumbers = $row['A'];
            $invoiceValidation = $this->validateInvoiceNumberForDuplication($invoiceNumbers);
            if (!$invoiceValidation || in_array($invoiceNumbers, $parsedInvoicesNumbers)) {
                $this->sheetData[$rowKey]['status'] = 'error';
                $this->sheetData[$rowKey]['status_details'][] = "Фактура с номер: {$invoiceNumbers} вече съществува";
                $this->entitiesStatistics['error']++;
                continue;
            } else {
                $parsedInvoicesNumbers[] = $invoiceNumbers;
            }

            $this->entitiesStatistics['success']++;
        }
    }


    private function validateInvoiceNumberForDuplication(string $invoiceNumber): bool
    {
        $vpjStingEntitiesModel = new VPJStingEntitiesModel();
        $vpjStingEntity = $vpjStingEntitiesModel->where('doc_n', $invoiceNumber)->first();
        if ($vpjStingEntity) {
            return false;
        }
        return true;
    }

    private function baseValidation($row)
    {
        if (empty($row['A'])) {
            return "Липсва No Док";
        }
        if (empty($row['B'])) {
            return "Липсва Тип Док";
        }
        if (empty($row['C'])) {
            return "Липсва Дата на Док";
        }
        if (empty($row['D'])) {
            return "Липсва Начин на плащане";
        }
        if (empty($row['E'])) {
            return "Липсва Сума с ДДС";
        }

        return true;
    }
}