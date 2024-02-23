<?php

namespace App\Models;

use CodeIgniter\Model;

class PBDStingInvoiceItemsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'pbd_sting_invoice_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'purchase_by_document_id',
        'number',
        'designation',
        'manufacturer',
        'batch',
        'quantity',
        'expiry_date',
        'certificate',
        'base_price',
        'trade_markup',
        'trade_discount',
        'wholesaler_price',
        'value',
        'price_with_vat',
        'recommended_price',
        'limit_price',
        'percent_a',
        'nzok'
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
