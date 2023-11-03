<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvidersModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'providers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [];

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


    public function getBusinesses(int $providerId ): array
    {
        $providersBusinessesModel = new ProvidersBusinessesModel();
        $businessesIds = $providersBusinessesModel->where('provider_id', $providerId)->findAll();
        $businessesIds = array_column($businessesIds, 'business_id');

        //get businesses details based on $businessesIds
        $businesses = [];
        if (!empty($businessesIds)) {
            $businessesModel = new BusinessesModel();
            $businesses = $businessesModel->whereIn('id', $businessesIds)->orderBy('name', 'asc')->findAll();
        }

        return $businesses;
    }
}
