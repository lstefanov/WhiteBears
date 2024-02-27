<?php

namespace App\Libraries\PurchaseByDocument\Parsers\Aster;


use App\Helpers\NumberFormat;
use App\Models\BusinessesCompaniesModel;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\PurchaseByDocumentDataModel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Parser
{
    public array $result = [];


    private int $invoiceType = 1; //1- Фактура, 2 - Абонамент

    private array $sheetData;

    /**
     * A] => Продуктов код
     * [B] => Име на лекарствения продукт
     * [C] => Код на аптеката
     * [D] => Име на фирмата
     * [E] => Адрес
     * [F] => Продадено количество
     * [G] => Стойност на продаденото количество без ДДС
     * [H] => Единична цена без ддс
     * [I] => Номер на фактура
     * [J] => Дата на фактура
     * [K] => Име на търговеца на едро
     *
     * @throws Exception
     */
    public function execute(string $file)
    {

        $this->result = [];


        //Parse file
        $spreadsheet = IOFactory::load($file);
        $this->sheetData = $spreadsheet->getSheet(0)->toArray(null, true, true, true);


        $invoicesData = [];
        foreach ($this->sheetData as $rowKey => $row) {
            if ($rowKey === 1) {
                continue;
            }

            if(empty($row['I'])){
                continue;
            }

            if (!isset($invoicesData[$row['I']])) {
                try {
                    $date = \DateTime::createFromFormat('d.m.Y', $row['J']);
                    if (!$date) {
                        throw new \Exception('Invalid date format');
                    }
                    $invoiceDate = $date->format('Y-m-d');
                } catch (\Exception $e) {
                    $invoiceDate = null;
                }

                $invoicesData[$row['I']] = [
                    'invoice_number' => $row['I'],
                    'invoice_date' => $invoiceDate,
                    'company_name' => $row['D'],
                    'address' => $row['E'],
                    'itemsCount' => 0,
                    'totalPrice' => 0,
                    'items' => []
                ];
            }

            $item = [
                'product_code' => $row['A'],
                'product_name' => $row['B'],
                'pharmacy_code' => $row['C'],
                'quantity' => $row['F'],
                'totalValue' => NumberFormat::formatPrice($row['G']),
                'price_per_item' => NumberFormat::formatPrice($row['H']),
            ];

            $invoicesData[$row['I']]['items'][] = $item;
            $invoicesData[$row['I']]['itemsCount'] += $item['quantity'];
            $invoicesData[$row['I']]['totalPrice'] += $item['totalValue'];
        }


        //get all companies
        $companies = (new CompaniesModel())->where('active', 1)->findAll();
        foreach ($companies as $companyKey => $company) {
            $companies[$companyKey]['name_checker'] = mb_strtolower($company['name']);
            $companies[$companyKey]['alias_1_checker'] = mb_strtolower($company['alias_1']);
            $companies[$companyKey]['alias_2_checker'] = mb_strtolower($company['alias_2']);
        }

        //get all businesses
        $businesses = (new BusinessesModel())->where('active', 1)->findAll();
        foreach ($businesses as $businessKey => $business) {
            //get all businesses companies for this business
            $businesses[$businessKey]['companies'] = (new BusinessesCompaniesModel())->where('business_id',
                $business['id'])->findAll();
        }

        foreach ($invoicesData as $rowKey => $row) {
            $error = '';

            $companyNameFromSheet = mb_strtolower($row['company_name']);
            $foundedCompanyId = 0;
            $foundedCompanyName = '';
            $foundedBusinessId = 0;
            $foundedBusinessName = '';
            $foundedBusinessInNumber = '';

            //search for company
            foreach ($companies as $company) {
                if ($companyNameFromSheet === $company['name_checker'] || $companyNameFromSheet === $company['alias_1_checker'] || $companyNameFromSheet === $company['alias_2_checker']) {
                    $foundedCompanyId = $company['id'];
                    $foundedCompanyName = $company['name'];
                    break;
                }
            }

            if ($foundedCompanyId === 0) {
                $error = 'Не е намерена фирма';
            } else {
                //search for business
                foreach ($businesses as $business) {
                    foreach ($business['companies'] as $businessCompany) {
                        if ($businessCompany['company_id'] === $foundedCompanyId) {
                            $foundedBusinessId = $business['id'];
                            $foundedBusinessName = $business['name'];
                            $foundedBusinessInNumber = $business['in_number'];
                            break 2;
                        }
                    }
                }
            }

            if ($foundedCompanyId === 1 && $foundedBusinessId === 0) {
                $error = 'Не е намерен бизнес';
            }

            //Validate if invoice number already exist
            $invoiceExistCheck = (new PurchaseByDocumentDataModel())->where('invoice_number', $row['invoice_number'])->first();
            if($invoiceExistCheck){
                $error = 'Фактурата вече е добавена';
            }

            $invoicesData[$rowKey]['founded_company_id'] = $foundedCompanyId;
            $invoicesData[$rowKey]['founded_company_name'] = $foundedCompanyName;
            $invoicesData[$rowKey]['founded_business_id'] = $foundedBusinessId;
            $invoicesData[$rowKey]['founded_business_name'] = $foundedBusinessName;
            $invoicesData[$rowKey]['founded_business_in_number'] = $foundedBusinessInNumber;

            if ($error !== '') {
                $invoicesData[$rowKey]['error'] = $error;
            }
        }

        $this->result = $invoicesData;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getInvoiceType(): int
    {
        return $this->invoiceType;
    }
}