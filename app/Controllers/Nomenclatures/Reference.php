<?php

namespace App\Controllers\Nomenclatures;

use App\Controllers\BaseController;
use App\Controllers\Partners;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\NomenclaturesEntitiesModel;
use App\Models\PBDAsterInvoiceItemsModel;
use App\Models\PBDFioniksFarmaDeliveryModel;
use App\Models\PBDFioniksFarmaInvoiceItemsModel;
use App\Models\PBDFioniksFarmaRecipientModel;
use App\Models\PBDStingInvoiceItemsModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\ProvidersModel;
use App\Models\PurchaseByDocumentDataModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJAsterEntitiesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
use CodeIgniter\Events\Events;
use Collator;
use Config\Services;

class Reference extends BaseController
{
    private array $viewData = [];

    private \CodeIgniter\Session\Session $session;
    private array $businesses;

    public function __construct()
    {
        $this->session = Services::session();
    }


    /**
     * @throws \Exception
     */
    public function view(): string
    {
        $this->viewData['assets']['js'] = 'Nomenclatures/reference.js';

        $selectedProviderId = (int)$this->request->getGet('provider_id') ?? 0;
        $selectedBusinessId = (int)$this->request->getGet('business_id') ?? 0;
        $selectedCompanyId = (int)$this->request->getGet('company_id') ?? 0;
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $data = $this->generateData($selectedProviderId, $selectedBusinessId, $selectedCompanyId, $dateFrom, $dateTo);

        //print_r2($data, 1);

        $this->viewData['dateFrom'] = $dateFrom;
        $this->viewData['dateTo'] = $dateTo;
        $this->viewData['selectedBusinessId'] = $selectedBusinessId;
        $this->viewData['selectedProviderId'] = $selectedProviderId;
        $this->viewData['selectedCompanyId'] = $selectedCompanyId;
        $this->viewData['data'] = $data;

        $providersModel = new ProvidersModel();
        $providers = $providersModel->orderBy('name', 'ASC')->findAll();
        $this->viewData['providers'] = $providers;

        $providersBusinessesModel = new ProvidersBusinessesModel();
        $businessesModel = new BusinessesModel();
        $businesses = $businessesModel->orderBy('name', 'ASC')->findAll();
        foreach ($businesses as $businessKey => $business) {
            $providers = $providersBusinessesModel->where('business_id',
                $business['id'])->findAll();

            $businesses[$businessKey]['providers'] = array_column($providers, 'provider_id');
        }
        $this->viewData['businesses'] = $businesses;

        $companiesModel = new CompaniesModel();
        $businessCompaniesModel = new \App\Models\BusinessesCompaniesModel();
        $companies = $companiesModel->orderBy('name', 'ASC')->findAll();

        foreach ($companies as $companyKey => $company) {
            $businesses = $businessCompaniesModel->where('company_id',
                $company['id'])->findAll();

            $companies[$companyKey]['businesses'] = array_column($businesses, 'business_id');
        }
        $this->viewData['companies'] = $companies;

        return view('Nomenclatures/references', $this->viewData);
    }

    private function generateData(
        $selectedProviderId,
        $selectedBusinessId,
        $selectedCompaniesId,
        $dateFrom,
        $dateTo
    ): array {

        $data = [];

        if ($selectedProviderId === 0 || $selectedBusinessId === 0 || $selectedCompaniesId === 0) {
            return $data;
        }

        //Store companies in array for easier access
        $companiesModel = new CompaniesModel();
        $companies = $companiesModel->orderBy('name', 'ASC')->findAll();

        $selectedCompanyNames = [];
        foreach ($companies as $company) {
            if ($company['id'] == $selectedCompaniesId) {
                $selectedCompanyNames[] = mb_strtolower($company['name']);
                for($i = 1; $i <= 10; $i++) {
                    if(!empty($company['alias_' . $i])) {
                        $selectedCompanyNames[] = mb_strtolower($company['alias_' . $i]);
                    }
                }
            }
        }


        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $invoices = $purchaseByDocumentDataModel->select('id')
            ->where('provider_id', $selectedProviderId)
            ->where('business_id', $selectedBusinessId)
            ->where('invoice_date >=', $dateFrom)
            ->where('invoice_date <=', $dateTo)
            ->findAll();

        // Use whereIn with an array of invoice IDs
        $invoiceIds = array_column($invoices, 'id');


        $invoicesItems = [];
        if ($selectedProviderId === 2) {
            $invoiceEntitiesModel = new PBDFioniksFarmaInvoiceItemsModel();
            $pBDFioniksFarmaDeliveryModel = new PBDFioniksFarmaDeliveryModel();

            $filteredInvoices = $pBDFioniksFarmaDeliveryModel->select('purchase_by_document_id')
                ->whereIn('purchase_by_document_id', $invoiceIds)
                ->whereIn('LOWER(place)', $selectedCompanyNames)
                ->findAll();

            foreach($filteredInvoices as $invoiceId){
                $items = $invoiceEntitiesModel->where('purchase_by_document_id', $invoiceId['purchase_by_document_id'])->findAll();

                foreach($items as $item){
                    $invoicesItems[] = [
                        'invoice_id' => $invoiceId['purchase_by_document_id'],
                        'name' => trim(str_replace('ОТСТЪПКА', '', $item['designation'])),
                        'quantity' => $item['quantity'],
                        'price' => $item['value'],
                        'single_item_price' => $item['wholesaler_price']
                    ];
                }
            }
        }


        $groupedItems = [];
        foreach($invoicesItems as $item){
            $elementName = $item['name']  . ' ||| ' . $item['single_item_price'];

            //No exist in list yet
            if(!isset($groupedItems[$elementName])){
                $groupedItems[$elementName] = [
                    'name' => $item['name'],
                    'invoices' => [$item['invoice_id']],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'single_item_price' => $item['single_item_price']
                ];
            } else {
                $groupedItems[$elementName]['invoices'][] = $item['invoice_id'];
                $groupedItems[$elementName]['quantity'] += $item['quantity'];
                $groupedItems[$elementName]['price'] += $item['price'];
            }
        }
        $groupedFilteredItems = $this->mergeDuplicateEntries($groupedItems);

        //When we have  generated  data, we need to check in category it will take based on price
        $nomenclaturesEntitiesModel = new NomenclaturesEntitiesModel();
        $nomenclaturesEntities = $nomenclaturesEntitiesModel->findAll();

        $nomenclaturesSyncModel = new \App\Models\NomenclaturesSyncModel();
        $lastItem = $nomenclaturesSyncModel->orderBy('id', 'DESC')->first();
        $nomenclaturesSyncEntitiesModel = new \App\Models\NomenclaturesSyncEntitiesModel();
        $nomenclaturesSyncEntities = $nomenclaturesSyncEntitiesModel->where('nomenclatures_sync_id', $lastItem['id'])->findAll();

        $finalData = [
            'elements' => [],
            'missing' => []
        ];

        foreach ($groupedFilteredItems as $groupedFilteredItem){

            //based on $groupedFilteredItem['name'], search in $nomenclaturesSyncEntities
            $nomenclatureSyncEntity = null;
            foreach($nomenclaturesSyncEntities as $nomenclaturesSyncEntity){
                if(mb_strtolower($nomenclaturesSyncEntity['name']) === mb_strtolower($groupedFilteredItem['name'])){
                    $nomenclatureSyncEntity = $nomenclaturesSyncEntity;
                    break;
                }
            }

            if(!$nomenclatureSyncEntity){
                $finalData['missing'][] = $groupedFilteredItem;
                continue;
            }
            $finalData['elements'][] = [
                'name' => $groupedFilteredItem['name'],
                'invoices' => $groupedFilteredItem['invoices'],
                'quantity' => $groupedFilteredItem['quantity'],
                'price' => $groupedFilteredItem['price'],
                'single_item_price' => $groupedFilteredItem['single_item_price'],
                'code_name' => $nomenclatureSyncEntity['code_name'],
            ];
        }

        foreach ($finalData['elements'] as $key => $row) {

            $foundedCodes = [];
            foreach($nomenclaturesEntities as $nomenclaturesEntity){
                if(mb_strtolower($nomenclaturesEntity['code_name']) === mb_strtolower($row['code_name'])){
                    $foundedCodes[] = $nomenclaturesEntity;
                }
            }

            foreach($foundedCodes as $foundedCode){
                $rowPrice = doubleval($row['single_item_price']);
                $codePriceFrom = doubleval($foundedCode['price_from']);
                $codePriceTo = doubleval($foundedCode['price_to']);

                if($rowPrice >= $codePriceFrom && $rowPrice <= $codePriceTo){
                    $finalData['elements'][$key]['code_number'] = $foundedCode['code_number'];
                    break;
                }
            }
        }

        print_r2($finalData,1);

        return $finalData;
    }

    private function mergeDuplicateEntries($array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            // Extract the product name from the key (before the |||)
            $productName = explode(' ||| ', $key)[0];

            // If this product already exists in the result, merge the quantities and prices
            if (isset($result[$productName])) {
                $result[$productName]['quantity'] += $value['quantity'];
                $result[$productName]['price'] += $value['price'];
            } else {
                // Initialize the result with the current product
                $result[$productName] = $value;
            }
        }

        // Now reconstruct the array with the original keys (productName ||| single_item_price)
        $finalResult = [];
        foreach ($result as $productName => $details) {
            $key = $productName . ' ||| ' . number_format($details['single_item_price'], 2);
            $finalResult[$key] = $details;
        }

        return $finalResult;
    }
}