<?php

namespace App\Models;

use CodeIgniter\Model;

class PBDFioniksFarmaInvoiceItemsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'pbd_fioniks_farma_invoice_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'purchase_by_document_id',
        'number',
        'code',
        'designation',
        'manufacturer',
        'quantity',
        'base_price',
        'trade_markup',
        'trade_discount',
        'wholesaler_price',
        'value',
        'price_with_vat',
        'batch',
        'certificate',
        'expiry_date',
        'pharmacy_price',
        'limit_price',
        'limit_price_type',
        'inn'
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
