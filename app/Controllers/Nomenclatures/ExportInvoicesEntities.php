<?php

namespace App\Controllers\Nomenclatures;

use App\Controllers\BaseController;
use App\Controllers\Partners;
use App\Models\BusinessesModel;
use App\Models\PBDAsterInvoiceItemsModel;
use App\Models\PBDFioniksFarmaInvoiceItemsModel;
use App\Models\PBDStingInvoiceItemsModel;
use App\Models\PurchaseByDocumentDataModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJAsterEntitiesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
use CodeIgniter\Events\Events;
use Collator;
use Config\Services;

class ExportInvoicesEntities extends BaseController
{
    private array $viewData = [];

    private \CodeIgniter\Session\Session $session;
    private array $businesses;

    public function __construct()
    {
        $this->session = Services::session();
    }


    /**
     * @throws \Exception
     */
    public function view(): string
    {
        $this->viewData['assets']['js'] = 'Nomenclatures/export-invoices-entities.js';

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');
        $preview = (int)$this->request->getGet('preview') ?? 0;
        $data = $preview === 1 ? $this->generateData($dateFrom, $dateTo) : [];

        //dd($data);

        $this->viewData['preview'] = $preview;
        $this->viewData['dateFrom'] = $dateFrom;
        $this->viewData['dateTo'] = $dateTo;
        $this->viewData['data'] = $data;

        return view('Nomenclatures/export-invoices-entities', $this->viewData);
    }


    /**
     * @throws \Exception
     */
    public function export(): ?\CodeIgniter\HTTP\DownloadResponse
    {
        error_reporting(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');
        $data = $this->generateData($dateFrom, $dateTo,true);
        $fileName = "Spisak_artikuli - {$dateFrom} - {$dateTo}.xlsx";
        $filePath = WRITEPATH . 'tmp/' . $fileName;

        // If file already exists delete it
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // Create a new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Set header
        $spreadsheet->getActiveSheet()
            ->setCellValue('A1', 'N')
            ->setCellValue('B1', 'Име на лекарствения продукт')
            ->setCellValue('C1', 'група');
        // Populate the rows with data
        $dataIndex = 2;
        foreach ($data as $index => $value) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$dataIndex, $index + 1)
                ->setCellValue('B'.$dataIndex, mb_strtoupper($value))
                ->setCellValue('C'.$dataIndex, '');
            $dataIndex++;
        }
        // Write the file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);

        // Now let's download the file
        $file = $this->response->download($filePath, null);
        $file->setFileName($fileName);

        return $file;
    }

    public function export_bu()
    {
        error_reporting(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);

        ob_start(); // Start output buffering to prevent accidental output

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');
        $data = $this->generateData($dateFrom, $dateTo,true);

        $fileName = "Списък артикули - {$dateFrom} - {$dateTo}.xlsx";

        // Create a new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Set the headers for the columns
        $spreadsheet->getActiveSheet()
            ->setCellValue('A1', 'N')
            ->setCellValue('B1', 'Име на лекарствения продукт')
            ->setCellValue('C1', 'група');

        // Populate the rows with data
        $dataIndex = 2; // Start from the second row as we have headers on the first row
        foreach ($data as $index => $value) {
            $value = iconv(mb_detect_encoding($value, mb_detect_order(), true), "UTF-8//IGNORE", $value);
            $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$dataIndex, $index + 1) // Add 1 to $index since array is 0-indexed
                ->setCellValue('B'.$dataIndex, $value)
                ->setCellValue('C'.$dataIndex, ''); // This remains empty
            $dataIndex++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_end_clean(); // End output buffering
        $writer->save('php://output');
    }


    /**
     * @throws \Exception
     */
    private function generateData(string $dateFrom, string $dateTo, bool $forExport = false): array
    {
        $purchaseByDocumentModel = new PurchaseByDocumentDataModel();

        $purchases = $purchaseByDocumentModel
            ->select('id, provider_id')
            ->where('invoice_date >=', $dateFrom)
            ->where('invoice_date <=', $dateTo)
            ->findAll();

        $provider1 = [];
        $provider2 = [];
        $provider3 = [];

        // Iterate over purchases and separate them based on provider_id
        foreach($purchases as $purchase) {
            switch($purchase['provider_id']) {
                case 1:
                    $provider1[] = $purchase['id'];
                    break;
                case 2:
                    $provider2[] = $purchase['id'];
                    break;
                case 3:
                    $provider3[] = $purchase['id'];
                    break;
            }
        }

        //Sting
        $pBDStingInvoiceItemsModel = new PBDStingInvoiceItemsModel();
        $stingData = $pBDStingInvoiceItemsModel
            ->distinct()
            ->select('designation AS name')
            ->whereIn('purchase_by_document_id', $provider1)
            ->findAll();

        $pBDFioniksFarmaInvoiceItemsModel = new PBDFioniksFarmaInvoiceItemsModel();
        $fioniksFarmaData = $pBDFioniksFarmaInvoiceItemsModel
            ->distinct()
            ->select('designation AS name')
            ->whereIn('purchase_by_document_id', $provider2)
            ->findAll();

        $pBDAsterInvoiceItemsModel = new PBDAsterInvoiceItemsModel();
        $asterData = $pBDAsterInvoiceItemsModel
            ->distinct()
            ->select('product_name AS name')
            ->whereIn('purchase_by_document_id', $provider3)
            ->findAll();


        // Merge all three arrays
        $mergedArray = array_merge($stingData, $fioniksFarmaData, $asterData);

        // Get 'name' column from merged array
        $names = array_column($mergedArray, 'name');

        // Change all names to lowercase
        $lowercaseNames = array_map('mb_strtolower', $names);

        // Get unique names
        $uniqueNames = array_unique($lowercaseNames);

        // Exclude names that start with '*' or are only numeric
        $filteredNames = array_filter($uniqueNames, function($name) {
            return $name[0] !== '*' && !is_numeric($name);
        });

        $collator = new Collator('bg_BG');
        $collator->sort($filteredNames);

        return $filteredNames;
    }
}