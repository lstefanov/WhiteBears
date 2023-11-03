<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'businesses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'code',
        'in_number',
        'alias_1',
        'alias_2',
        'alias_3',
        'alias_4',
        'alias_5',
        'active',
        'deleted',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getCompanies(int $businessId): array
    {
        $businessesCompaniesModel = new BusinessesCompaniesModel();
        $companiesIds = $businessesCompaniesModel->where('business_id', $businessId)->findAll();
        $companiesIds = array_column($companiesIds, 'company_id');

        //get companies details based on $companiesIds
        $companies = [];
        if (!empty($companiesIds)) {
            $companiesModel = new CompaniesModel();
            $companies = $companiesModel->whereIn('id', $companiesIds)->orderBy('name', 'asc')->findAll();
        }

        return $companies;
    }


    /**
     * @throws \ReflectionException
     */
    public function addProviders(int $businessId, array $providers): bool
    {
        $providersBusinessesModel = new ProvidersBusinessesModel();
        $providersBusinessesModel->where('business_id', $businessId)->delete();

        $data = [];
        foreach ($providers as $provider) {
            $data[] = [
                'business_id' => $businessId,
                'provider_id' => $provider,
            ];
        }

        $providersBusinessesModel->insertBatch($data);

        return true;
    }


    public function getProvidersByBusinessId(int $businessId): array
    {
        $providersBusinessesModel = new ProvidersBusinessesModel();
        $providersIds = $providersBusinessesModel->where('business_id', $businessId)->findAll();
        $providersIds = array_column($providersIds, 'provider_id');

        return $providersIds;
    }
}
