<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseByDocumentDataModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'purchase_by_document';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'provider_id',
        'business_id',
        'document_type',
        'invoice_number',
        'invoice_date',
        'amount',
        'payment_amount',
        'items',
        'entities',
        'nzok',
        'source_type',
        'source_name',
        'source_content',
        'created_at',
        'credit_notice'
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
        $this->select('purchase_by_document.id, purchase_by_document.invoice_number, purchase_by_document.invoice_date, purchase_by_document.payment_amount, purchase_by_document.items, purchase_by_document.entities, purchase_by_document.provider_id, purchase_by_document.created_at, providers.name as provider_name, businesses.name as business_name');
        $this->join('providers', 'providers.id = purchase_by_document.provider_id');
        $this->join('businesses', 'businesses.id = purchase_by_document.business_id');
        $this->limit(10000);
        $this->orderBy('purchase_by_document.created_at', 'desc');
        $query = $this->get();
        return $query->getResultArray();

        //echo $this->getLastQuery(); die();
    }
}
