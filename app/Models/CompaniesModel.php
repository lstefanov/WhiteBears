<?php

namespace App\Models;

use CodeIgniter\Model;

class CompaniesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'alias_1',
        'alias_2',
        'alias_3',
        'alias_4',
        'alias_5',
        'alias_6',
        'alias_7',
        'alias_8',
        'alias_9',
        'alias_10',
        'client_number',
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


    /**
     * @throws \ReflectionException
     */
    public function addBusinesses(int $companyId, array $businesses): bool
    {
        $businessesCompaniesModel = new BusinessesCompaniesModel();
        $businessesCompaniesModel->where('company_id', $companyId)->delete();

        $data = [];
        foreach ($businesses as $business) {
            $data[] = [
                'business_id' => $business,
                'company_id' => $companyId,
            ];
        }

        $businessesCompaniesModel->insertBatch($data);

        return true;
    }

    public function getBusinessesByCompanyId(int $companyId): array
    {
        $businessesCompaniesModel = new BusinessesCompaniesModel();
        $businessesIds = $businessesCompaniesModel->where('company_id', $companyId)->findAll();
        $businessesIds = array_column($businessesIds, 'business_id');

        return $businessesIds;
    }
}
