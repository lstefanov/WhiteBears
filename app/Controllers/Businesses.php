<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\ProvidersModel;

class Businesses extends BaseController
{
    private array $viewData = [];

    public function manage(): string
    {
        $this->viewData['assets']['js'] = 'businesses/manage.js';

        $action = $this->request->getGet('action');
        if($action === 'edit'){
            $businessesModel = new BusinessesModel();
            $business = $businessesModel->find($this->request->getGet('id'));
            $this->viewData['business'] = $business;

            //get providers for this business
            $businessProviders = $businessesModel->getProvidersByBusinessId($business['id']);
            $this->viewData['businessProviders'] = $businessProviders;
        }

        //Get providers
        $providersModel = new ProvidersModel();
        $providers = $providersModel->orderBy('name', 'asc')->findAll();
        $this->viewData['providers'] = $providers;

        $this->viewData['action'] = $action;
        return view('businesses/manage', $this->viewData);
    }


    /**
     * @throws \ReflectionException
     */
    public function save(): \CodeIgniter\HTTP\RedirectResponse
    {
        $businessesModel = new BusinessesModel();

        $businessId = (int)$this->request->getPost('id');
        $action = $this->request->getPost('action');
        $providers = $this->request->getPost('providers');

        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'in_number' => $this->request->getPost('in_number'),
            'alias_1' => $this->request->getPost('alias_1'),
            'alias_2' => $this->request->getPost('alias_2'),
            'alias_3' => $this->request->getPost('alias_3'),
            'alias_4' => $this->request->getPost('alias_4'),
            'alias_5' => $this->request->getPost('alias_5'),
        ];

        //check for business with same name if already exist
        $business = $businessesModel->where('name', $data['name'])->first();
        if($business && (int)$business['id'] !== $businessId){
            return redirect()->to("businesses/manage?action={$action}&id=$businessId")->withInput()->with('errors', 'Фирма с такова име вече същестува!');
        }

        //check for business with same code if already exist
        $business = $businessesModel->where('code', $data['code'])->first();
        if($business && (int)$business['id'] !== $businessId){
            return redirect()->to("businesses/manage?action=edit&id=$businessId")->withInput()->with('errors', 'Фирма с такъв код вече същестува!');
        }

        //check for business with same in_number if already exist
        $business = $businessesModel->where('in_number', $data['in_number'])->first();
        if($business && (int)$business['id'] !== $businessId){
            return redirect()->to("businesses/manage?action=edit&id=$businessId")->withInput()->with('errors', 'Фирма с такъв ИН номер вече същестува!');
        }


        if($action === 'add'){
            $businessesModel->insert($data);
            $businessId = $businessesModel->getInsertID();
        }else{
            $businessesModel->update($businessId, $data);
        }

        //Add providers
        $businessesModel->addProviders($businessId, $providers);


        return redirect()->to("businesses/manage?action={$action}&id=$businessId")->withInput()->with('success', $action === 'add' ? 'Фирмата е добавена успешно!' : 'Фирмата е редактирана успешно!');
    }


    /**
     * @throws \ReflectionException
     */
    public function change_status()
    {
        $businessesModel = new BusinessesModel();

        $businessId = (int)$this->request->getPost('business_id');
        $activeStatus = $this->request->getPost('active_status');

        $businessesModel->update($businessId, ['active' => $activeStatus]);

        return $this->response->setJSON(['success' => true]);
    }
}