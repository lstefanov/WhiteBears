<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\ProvidersModel;

class Partners extends BaseController
{
    private array $viewData = [];

    public function providers(): string
    {
        $this->viewData['assets']['js'] = 'partners/providers.js';

        $providersModel = new ProvidersModel();
        $providers = $providersModel->orderBy('name', 'asc')->findAll();

        foreach ($providers as $key => $provider) {
            $providers[$key]['businesses'] = $providersModel->getBusinesses($provider['id']);
        }

        $this->viewData['providers'] = $providers;

        return view('partners/providers', $this->viewData);
    }

    public function businesses(): string
    {
        $this->viewData['assets']['js'] = 'partners/businesses.js';

        //Filter by Providers
        $selectedProviderId = (int)$this->request->getGet('provider_id');
        $this->viewData['selectedProviderId'] = $selectedProviderId;


        //Get all providers for filter section
        $providersModel = new ProvidersModel();
        $this->viewData['providers'] = $providersModel->orderBy('name', 'asc')->findAll();

        $businessesModel = new BusinessesModel();

        //Get businesses for selected provider
        if($selectedProviderId !== 0){
            $businesses = $providersModel->getBusinesses($selectedProviderId);
        } else {
            $businesses = $businessesModel->orderBy('name', 'asc')->findAll();
        }

        foreach ($businesses as $key => $business){
            $businesses[$key]['companies'] = $businessesModel->getCompanies($business['id']);
        }

        $this->viewData['businesses'] = $businesses;

        return view('partners/businesses', $this->viewData);
    }

    public function companies(): string
    {
        $this->viewData['assets']['js'] = 'partners/companies.js';

        //Filter by Businesses
        $selectedBusinessId = (int)$this->request->getGet('business_id');
        $this->viewData['selectedBusinessId'] = $selectedBusinessId;

        $businessesModel = new BusinessesModel();
        $this->viewData['businesses'] = $businessesModel->orderBy('name', 'asc')->findAll();

        if($selectedBusinessId !== 0){
            $companies = $businessesModel->getCompanies($selectedBusinessId);
        } else {
            $companiesModel = new CompaniesModel();
            $companies = $companiesModel->orderBy('name', 'asc')->findAll();
        }

        $this->viewData['companies'] = $companies;

        return view('partners/companies', $this->viewData);
    }
}