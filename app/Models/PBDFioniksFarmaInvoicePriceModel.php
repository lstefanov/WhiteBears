<?php

namespace App\Models;

use CodeIgniter\Model;

class PBDFioniksFarmaInvoicePriceModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'pbd_fioniks_farma_invoice_price';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'purchase_by_document_id',
        'taxable_value',
        'total_price',
        'total_price_from_supplier',
        'trade_discount',
        'tax_base_9',
        'tax_9',
        'tax_base_20',
        'tax_20',
        'tax_base_0',
        'total_price_with_tax',
        'total_price_with_tax_in_words',
        'note'
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
