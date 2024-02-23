<?php

namespace App\Models;

use CodeIgniter\Model;

class PBDStingInvoicePriceModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'pbd_sting_invoice_price';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'purchase_by_document_id',
        'total_price',
        'total_price_from_supplier',
        'trade_discount',
        'trade_discount_percent',
        'value_of_the_deal',
        'tax_20',
        'total_price_with_tax',
        'total_price_with_tax_in_words',
        'tax_base',
        'doc_number',
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
