<?php

namespace App\Models;

use CodeIgniter\Model;

class VPJFioniksFarmaEntitiesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'vpj_fioniks_farma_entities';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'vat_purchase_journals_id',
        'provider_id',
        'business_id',
        'company_id',
        'status',
        'status_details',
        'export_date',
        'warehouse',
        'business_name',
        'client_number',
        'company_name',
        'invoice',
        'invoice_date',
        'invoice_type',
        'due_date',
        'payment_type',
        'payment_summary',
        'payment_payed',
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
}
