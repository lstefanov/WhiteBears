<?php

namespace App\Models;

use CodeIgniter\Model;

class VatPurchaseJournalsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'vat_purchase_journals';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'provider_id',
        'uuid',
        'file_name',
        'file_type',
        'file_size',
        'file_location',
        'created_at',
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

    public function getHistory(): array
    {
        $this->select('vat_purchase_journals.*, providers.name as provider_name');
        $this->join('providers', 'providers.id = vat_purchase_journals.provider_id');
        //$this->where(...);
        $this->orderBy('vat_purchase_journals.created_at', 'desc');
        return $this->findAll();

    }
}
