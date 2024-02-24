<?php

namespace App\Controllers\Fixes;

use App\Controllers\BaseController;
use Config\Database;

class VatPurchaseJournals extends BaseController
{

    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct() {
        // Create a new connection
        $this->db = Database::connect();
    }

    /**
     * Fix date for already uploaded documents for Sting in VatPurchaseJournals
     */
    public function stingExportDateFull()
    {
        $results = $this->db->query("SELECT * FROM vpj_sting_entities WHERE export_date_full IS NULL AND export_date != '' ")->getResult();

        foreach($results as $result){
            $exportDate = str_replace('г.', '', $result->doc_date);
            $exportDateFull = date('Y-m-d', strtotime($exportDate));
            $this->db->query("UPDATE vpj_sting_entities SET export_date_full = ? WHERE id = ?", [$exportDateFull, $result->id]);
        }

        echo 'done';
        exit();
    }
}