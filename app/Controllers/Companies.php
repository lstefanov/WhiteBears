<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\ProvidersModel;

class Companies extends BaseController
{
    private array $viewData = [];

    public function manage(): string
    {
        $this->viewData['assets']['js'] = 'companies/manage.js';

        $action = $this->request->getGet('action');
        if($action === 'edit'){
            $companiesModel = new CompaniesModel();
            $company = $companiesModel->find($this->request->getGet('id'));
            $this->viewData['company'] = $company;

            //get businesses for this company
            $companyBusinesses = $companiesModel->getBusinessesByCompanyId($company['id']);
            $this->viewData['companyBusinesses'] = $companyBusinesses;
        }

        //Get providers
        $businessesModel = new BusinessesModel();
        $businesses = $businessesModel->orderBy('name', 'asc')->findAll();
        $this->viewData['businesses'] = $businesses;

        $this->viewData['action'] = $action;
        return view('companies/manage', $this->viewData);
    }


    /**
     * @throws \ReflectionException
     */
    public function save(): \CodeIgniter\HTTP\RedirectResponse
    {
        $companiesModel = new CompaniesModel();

        $companyId = (int)$this->request->getPost('id');
        $action = $this->request->getPost('action');
        $businesses = $this->request->getPost('businesses');

        $data = [
            'name' => $this->request->getPost('name'),
            'alias_1' => $this->request->getPost('alias_1'),
            'alias_2' => $this->request->getPost('alias_2'),
            'client_number' => $this->request->getPost('client_number')
        ];

        //check  for company with same name
        $company = $companiesModel->where('name', $data['name'])->first();
        if($company && (int)$company['id'] !== $companyId){
            return redirect()->to("companies/manage?action={$action}&id=$companyId")->withInput()->with('errors', 'Вече съществува обект с това име!');
        }

        if($action === 'add'){
            $companiesModel->insert($data);
            $companyId = $companiesModel->getInsertID();
        }else{
            $companiesModel->update($companyId, $data);
        }

        //Add businesses
        $companiesModel->addBusinesses($companyId, $businesses);


        return redirect()->to("companies/manage?action={$action}&id=$companyId")->withInput()->with('success', $action === 'add' ? 'Обекта е добавен успешно!' : 'Обекта е редактиран успешно!');
    }


    /**
     * @throws \ReflectionException
     */
    public function change_status()
    {
        $companiesModel = new CompaniesModel();

        $companyId = (int)$this->request->getPost('company_id');
        $activeStatus = $this->request->getPost('active_status');

        $companiesModel->update($companyId, ['active' => $activeStatus]);

        return $this->response->setJSON(['success' => true]);
    }
}