<?php

namespace App\Libraries\Parsers;

use App\Models\BusinessesCompaniesModel;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AsterParser
{

    private array $sheetData = [];

    private array $businesses = [];

    public array $errors = [];

    public array $entitiesStatistics = [
        'total' => 0,
        'success' => 0,
        'error' => 0
    ];

    /**
     * @var int
     * 1 - mean it has file error
     * 2 - mean there is no valid rows
     */
    public int $errorType = 1;


    public function execute(string $file, array &$parsedInvoicesNumbers): array
    {
        //collect businesses linked with this provider
        $this->getBusinessesDetails(2);

        //Parse file
        $spreadsheet = IOFactory::load($file);
        $this->sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        //echo '<pre>'; print_r($this->sheetData); die();

        //Validate file header
        $this->validateParsedFileHeaders();

        //Validate file data
        if (empty($this->errors)) {
            $this->validateParsedFileData($parsedInvoicesNumbers);

            $this->validateForValidRows();
        }


        return $this->sheetData;
    }

    private function validateForValidRows()
    {
        $validRows = 0;
        $invalidRows = 0;
        foreach ($this->sheetData as $rowKey => $row) {
            if ($rowKey < 7 || count($this->sheetData) === $rowKey) { // skip header data
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

        $headers = $this->sheetData[1];

        if (!isset($headers['A'])) {
            $errors[] = 'Колона А "АСТЕР РУСЕ ЕООД" липсва !';
        }
        if (!isset($headers['F'])) {
            $errors[] = 'Колона F "Дневник на продажбите" липсва !';
        }
        if (!isset($headers['O'])) {
            $errors[] = 'Колона O "Дата и час на разпечатване" липсва !';
        }

        foreach ($headers as $headerKey => $header) {
            $header2 = mb_strtolower($header);

            if ($headerKey === 'A' && $header2 !== 'астер русе еоод') {
                $errors[] = 'Колона А трябва да съдържа "АСТЕР РУСЕ ЕООД"';
            }

            if ($headerKey === 'F' && $header2 !== 'дневник на продажбите') {
                $errors[] = 'Колона B трябва да съдържа "Дневник на продажбите"';
            }

            if ($headerKey === 'O' && $header2 !== 'дата и час на разпечатване') {
                $errors[] = 'Колона O трябва да съдържа "Дата и час на разпечатване"';
            }
        }

        $this->errors = array_merge($this->errors, $errors);
    }

    private function validateParsedFileData(&$parsedInvoicesNumbers)
    {
        foreach ($this->sheetData as $rowKey => $row) {
            if ($rowKey < 7 || count($this->sheetData) === $rowKey) { // skip header data
                continue;
            }

            $this->entitiesStatistics['total']++;

            $this->sheetData[$rowKey]['business_id'] = 0;
            $this->sheetData[$rowKey]['status'] = 'success';
            $this->sheetData[$rowKey]['status_details'] = [];

            //Base Validation
            $basicValidation = $this->baseValidation($row);
            if ($basicValidation !== true) {
                $this->sheetData[$rowKey]['status'] = 'error';
                $this->sheetData[$rowKey]['status_details'][] = $basicValidation;
                $this->entitiesStatistics['error']++;
                continue;
            }


            //Validate business
            $businessName = $row['I'];
            $businessId = $this->getBusinessIdByName($businessName);
            if (!$businessId) {
                $this->sheetData[$rowKey]['status'] = 'error';
                $this->sheetData[$rowKey]['status_details'][] = "Фирма $businessName не е свързана с този доставчик";
                $this->entitiesStatistics['error']++;
                continue;
            }
            $this->sheetData[$rowKey]['business_id'] = $businessId;


            //Validate invoice duplicate
            $invoiceNumbers1 = $row['D'];
            $invoiceNumbers2 = $row['E'];

            if(!empty($invoiceNumbers1)){
                $check = explode('/', $invoiceNumbers1);
                $invoiceNumbers = trim($invoiceNumbers1);
            }else{
                $invoiceNumbers = $invoiceNumbers2;
            }

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

    private function getBusinessesDetails(int $providerId)
    {
        //Get all linked business for this provider
        $providersBusinessesModel = new ProvidersBusinessesModel();
        $businessForThisProvider = $providersBusinessesModel->where('provider_id', $providerId)->findAll();

        //Get details for all businesses
        $businessesModel = new BusinessesModel();
        $businesses = $businessesModel->whereIn('id', array_column($businessForThisProvider, 'business_id'))->findAll();

        $businessesCompaniesModel = new BusinessesCompaniesModel();
        $companiesModel = new CompaniesModel();
        foreach ($businesses as $businessKey => $business) {
            //Predefine names for validation
            $businesses[$businessKey]['names_for_validation'] = [
                'name' => str_replace([' ', '"', "'"], '', mb_strtolower($business['name'])),
                'alias_1' => $business['alias_1'] ? str_replace([' ', '"', "'"], '', mb_strtolower($business['alias_1'])) : false,
                'alias_2' => $business['alias_2'] ? str_replace([' ', '"', "'"], '', mb_strtolower($business['alias_2'])) : false
            ];

            $companies = $businessesCompaniesModel->where('business_id', $business['id'])->findAll();

            foreach ($companies as $company) {
                $businesses[$businessKey]['companies'][] = $companiesModel->find($company['company_id']);
            }
        }

        $this->businesses = $businesses;
    }

    private function getBusinessIdByName(string $businessName)
    {
        foreach ($this->businesses as $business) {
            $businessName = str_replace([' ', '"', "'"], '', mb_strtolower($businessName));

            foreach($business['names_for_validation'] as $businessNameForValidation){
                if($businessNameForValidation === $businessName){
                    return $business['id'];
                }
            }
        }

        return false;
    }


    private function getCompanyIdByName(int $businessId, string $companyName)
    {
        //search business in businesses array by id
        $businessIndex = array_search($businessId, array_column($this->businesses, 'id'));
        if ($businessIndex === false) {
            return false;
        }

        $companiesForBusiness = array_column($this->businesses[$businessIndex]['companies'], 'name');
        foreach ($companiesForBusiness as $company) {
            $company = str_replace(' ', '', mb_strtolower($company));
            $companyName = str_replace(' ', '', mb_strtolower($companyName));

            if ($company === $companyName) {
                return $company['id'];
            }
        }

        return false;
    }


    private function getCompanyIdByClientNumber(int $businessId, string $companyClientNumber)
    {
        //search business in businesses array by id
        $businessIndex = array_search($businessId, array_column($this->businesses, 'id'));
        if ($businessIndex === false) {
            return false;
        }

        if(!is_array(is_array($this->businesses[$businessIndex]))){
            return false;
        }

        foreach ($this->businesses[$businessIndex]['companies'] as $businessCompany) {

            if ($companyClientNumber === $businessCompany['client_number']) {
                return $businessCompany['id'];
            }
        }

        return false;
    }


    private function validateInvoiceNumberForDuplication(string $invoiceNumber): bool
    {
        $vpjAsterEntitiesModel = new VPJFioniksFarmaEntitiesModel();
        $vpjAsterEntity = $vpjAsterEntitiesModel->where('invoice', $invoiceNumber)->first();
        if ($vpjAsterEntity) {
            return false;
        }
        return true;
    }

    private function baseValidation($row)
    {
        if (empty($row['A'])) {
            return "Липсва 'номер на ред'";
        }
        if (empty($row['B'])) {
            return "Липсва 'Вид на документа'";
        }
        if (empty($row['D']) && empty($row['E'])) {
            return "Липсва 'Номер на документа'";
        }
        if (empty($row['F'])) {
            return "Липсва 'дата'";
        }
        if (empty($row['H'])) {
            return "Липсва 'ЕИК на контрагента'";
        }
        if (empty($row['I'])) {
            return "Липсва 'Име на контрагента'";
        }
        if (empty($row['L'])) {
            return "Липсва 'Предмет на сделката'";
        }
        if (empty($row['M'])) {
            return "Липсва 'Обща с-ст на сделката (вкл. ДДС)'";
        }
        if (empty($row['N'])) {
            return "Липсва 'Ст-ст на облаг. сделки'";
        }
        if (empty($row['O'])) {
            return "Липсва 'ДДС на облаг. сделки'";
        }
        if (empty($row['P'])) {
            return "Липсва 'Ст-ст по пок. цени'";
        }

        return true;
    }
}