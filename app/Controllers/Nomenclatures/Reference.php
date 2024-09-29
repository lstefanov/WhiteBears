<?php

namespace App\Controllers\Nomenclatures;

use App\Controllers\BaseController;
use App\Controllers\Partners;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\PBDAsterInvoiceItemsModel;
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

        $selectedProviderId = $this->request->getGet('provider_id') ?? 0;
        $selectedBusinessId = $this->request->getGet('business_id') ?? 0;
        $selectedCompanyId = $this->request->getGet('company_id') ?? 0;
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

        $selectedCompanyName = '';
        foreach ($companies as $company) {
            if ($company['id'] == $selectedCompaniesId) {
                $selectedCompanyName = mb_strtolower($company['name']);
            }
        }

        //echo $selectedCompanyName;
        //print_r2($companies,1);

        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $invoices = $purchaseByDocumentDataModel->select('id')
            ->where('provider_id', $selectedProviderId)
            ->where('business_id', $selectedBusinessId)
            ->where('invoice_date >=', $dateFrom)
            ->where('invoice_date <=', $dateTo)
            ->findAll();

        //print_r2($invoices,1);

        $filteredInvoices = [];
        if ($selectedProviderId === 2) {
            $pBDFioniksFarmaRecipientModel = new PBDFioniksFarmaRecipientModel();

            // Use whereIn with an array of invoice IDs
            $invoiceIds = array_column($invoices, 'id');

            // Construct the query with LOWER for case insensitive search in Cyrillic
            $filteredInvoices = $pBDFioniksFarmaRecipientModel->select('invoice_id')
                ->whereIn('invoice_id', $invoiceIds)
                ->where('LOWER(name)', $selectedCompanyName)
                ->findAll();
        }

        print_r2($filteredInvoices,1);

        return [];
    }
}