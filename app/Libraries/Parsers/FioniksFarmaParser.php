<?php

namespace App\Libraries\Parsers;

use App\Models\BusinessesCompaniesModel;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FioniksFarmaParser
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


    /**
     * @throws Exception
     */
    public function execute(string $file, array &$parsedInvoicesNumbers): array
    {
        //collect businesses linked with this provider
        $this->getBusinessesDetails(2);

        //Parse file
        $spreadsheet = IOFactory::load($file);
        $this->sheetData = $spreadsheet->getSheet(0)->toArray(null, true, true, true);

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
            if ($rowKey === 1) { // skip header data
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
            $errors[] = 'Колона А "склад" липсва !';
        }
        if (!isset($headers['B'])) {
            $errors[] = 'Колона B "фирма" липсва !';
        }
        if (!isset($headers['C'])) {
            $errors[] = 'Колона C "клиентски номер" липсва !';
        }
        if (!isset($headers['D'])) {
            $errors[] = 'Колона D "клиент" липсва !';
        }
        if (!isset($headers['E'])) {
            $errors[] = 'Колона E "фактура" липсва !';
        }
        if (!isset($headers['F'])) {
            $errors[] = 'Колона F "дата" липсва !';
        }
        if (!isset($headers['G'])) {
            $errors[] = 'Колона G "тип" липсва !';
        }
        if (!isset($headers['H'])) {
            $errors[] = 'Колона H "падеж" липсва !';
        }
        if (!isset($headers['I'])) {
            $errors[] = 'Колона I "вид плащане" липсва !';
        }
        if (!isset($headers['J'])) {
            $errors[] = 'Колона J "стойност" липсва !';
        }
        if (!isset($headers['K'])) {
            $errors[] = 'Колона K "платено" липсва !';
        }


        foreach ($headers as $headerKey => $header) {
            $header2 = mb_strtolower($header);

            if ($headerKey === 'A' && $header2 !== 'склад') {
                $errors[] = 'Колона А трябва да съдържа "склад"';
            }

            if ($headerKey === 'B' && $header2 !== 'фирма') {
                $errors[] = 'Колона B трябва да съдържа "фирма"';
            }

            if ($headerKey === 'C' && $header2 !== 'клиентски номер') {
                $errors[] = 'Колона C трябва да съдържа "клиентски номер"';
            }

            if ($headerKey === 'D' && $header2 !== 'клиент') {
                $errors[] = 'Колона D трябва да съдържа "клиент"';
            }

            if ($headerKey === 'E' && $header2 !== 'фактура') {
                $errors[] = 'Колона E трябва да съдържа "фактура"';
            }

            if ($headerKey === 'F' && $header2 !== 'дата') {
                $errors[] = 'Колона F трябва да съдържа "дата"';
            }

            if ($headerKey === 'G' && $header2 !== 'тип') {
                $errors[] = 'Колона G трябва да съдържа "тип"';
            }

            if ($headerKey === 'H' && $header2 !== 'падеж') {
                $errors[] = 'Колона H трябва да съдържа "падеж"';
            }

            if ($headerKey === 'I' && $header2 !== 'вид плащане') {
                $errors[] = 'Колона I трябва да съдържа "вид плащане"';
            }

            if ($headerKey === 'J' && $header2 !== 'стойност') {
                $errors[] = 'Колона J трябва да съдържа "стойност"';
            }

            if ($headerKey === 'K' && $header2 !== 'платено') {
                $errors[] = 'Колона K трябва да съдържа "платено"';
            }
        }

        $this->errors = array_merge($this->errors, $errors);
    }

    private function validateParsedFileData(&$parsedInvoicesNumbers)
    {
        foreach ($this->sheetData as $rowKey => $row) {
            if ($rowKey === 1) { // skip header data
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
            $businessName = $row['B'];
            $businessId = $this->getBusinessIdByName($businessName);
            if (!$businessId) {
                $this->sheetData[$rowKey]['status'] = 'error';
                $this->sheetData[$rowKey]['status_details'][] = "Фирма $businessName не е свързана с този доставчик";
                $this->entitiesStatistics['error']++;
                continue;
            }
            $this->sheetData[$rowKey]['business_id'] = $businessId;


            //Validate company
            /*
            $companyClientNumber = $row['C'];
            $companyName = $row['D'];
            $companyId = $this->getCompanyIdByClientNumber($businessId, $companyClientNumber);
            if (!$companyId) {
                $this->sheetData[$rowKey]['status'] = 'error';
                $this->sheetData[$rowKey]['status_details'][] = "Аптека $companyName ($companyClientNumber) не е свързана с фирма: $businessName";
                continue;
            }
            $this->sheetData[$rowKey]['company_id'] = $companyId;
            */

            //Validate invoice duplicate
            $invoiceNumbers = $row['E'];
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
                //'name' => str_replace(' ', '', mb_strtolower($business['name'])),
                //'alias_1' => $business['alias_1'] ? str_replace(' ', '', mb_strtolower($business['alias_1'])) : false,
                //'alias_2' => $business['alias_2'] ? str_replace(' ', '', mb_strtolower($business['alias_2'])) : false
                'name' => str_replace(' ', '', mb_strtolower($business['name'])),
                'alias_1' => str_replace(' ', '', mb_strtolower($business['alias_1'])),
                'alias_2' => str_replace(' ', '', mb_strtolower($business['alias_2'])),
                'alias_3' => str_replace(' ', '', mb_strtolower($business['alias_3'])),
                'alias_4' => str_replace(' ', '', mb_strtolower($business['alias_4'])),
                'alias_5' => str_replace(' ', '', mb_strtolower($business['alias_5']))
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
            $businessName = str_replace(' ', '', mb_strtolower($businessName));

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
        $vpjFioniksFarmaEntitiesModel = new VpjFioniksFarmaEntitiesModel();
        $vpjFioniksFarmaEntity = $vpjFioniksFarmaEntitiesModel->where('invoice', $invoiceNumber)->first();
        if ($vpjFioniksFarmaEntity) {
            return false;
        }
        return true;
    }

    private function baseValidation($row)
    {
        if (empty($row['A'])) {
            return "Липсва склад";
        }
        if (empty($row['B'])) {
            return "Липсва фирма";
        }
        if (empty($row['C'])) {
            return "Липсва клиентски номер";
        }
        if (empty($row['D'])) {
            return "Липсва клиент";
        }
        if (empty($row['E'])) {
            return "Липсва фактура";
        }
        if (empty($row['F'])) {
            return "Липсва дата";
        }
        if (empty($row['G'])) {
            return "Липсва тип";
        }
        if (empty($row['H'])) {
            return "Липсва падеж";
        }
        if (empty($row['I'])) {
            return "Липсва вид плащане";
        }
        if (empty($row['J'])) {
            return "Липсва стойност";
        }

        return true;
    }
}