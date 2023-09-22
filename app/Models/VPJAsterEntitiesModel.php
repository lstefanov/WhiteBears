<?php

namespace App\Models;

use CodeIgniter\Model;

class VPJAsterEntitiesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'vpj_aster_entities';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'vat_purchase_journals_id',
        'provider_id',
        'business_id',
        'status',
        'status_details',
        'export_date',
        'invoice',
        'invoice_date',
        'eik',
        'business_name',
        'subject_of_the_transaction',
        'total_price_inc_vat',
        'price_without_vat',
        'price_vat',
        'price_purchase'
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
