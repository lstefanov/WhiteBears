<?php

namespace App\Controllers\Nomenclatures;

use App\Controllers\BaseController;
use App\Controllers\Partners;
use App\Models\BusinessesModel;
use App\Models\CompaniesModel;
use App\Models\NomenclaturesEntitiesModel;
use App\Models\NomenclaturesSyncEntitiesModel;
use App\Models\NomenclaturesSyncModel;
use App\Models\PBDAsterInvoiceItemsModel;
use App\Models\PBDFioniksFarmaDeliveryModel;
use App\Models\PBDFioniksFarmaInvoiceItemsModel;
use App\Models\PBDFioniksFarmaRecipientModel;
use App\Models\PBDStingInvoiceItemsModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\ProvidersModel;
use App\Models\PurchaseByDocumentDataModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJAsterEntitiesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
use CodeIgniter\Events\Events;
use Collator;
use Config\Services;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class Reference extends BaseController
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
        //set memory limit to 0
        ini_set('memory_limit', '-1');
        //set max execution time to unlimited
        set_time_limit(0);

        // Adjust maximum file size to 100MB
        ini_set('upload_max_filesize', '100M');
        // Adjust maximum size of POST data to 101MB
        ini_set('post_max_size', '101M');
        // Increase the maximum number of files allowed to be uploaded simultaneously
        ini_set('max_file_uploads', '1000');

        //display all errors
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        $this->viewData['assets']['js'] = 'Nomenclatures/reference.js';

        $selectedProviderId = (int)$this->request->getGet('provider_id') ?? 0;
        $selectedBusinessId = (int)$this->request->getGet('business_id') ?? 0;
        $selectedCompanyId = (int)$this->request->getGet('company_id') ?? 0;
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $data = $this->generateData($selectedProviderId, $selectedBusinessId, $selectedCompanyId, $dateFrom, $dateTo);

        $this->viewData['dateFrom'] = $dateFrom;
        $this->viewData['dateTo'] = $dateTo;
        $this->viewData['selectedBusinessId'] = $selectedBusinessId;
        $this->viewData['selectedProviderId'] = $selectedProviderId;
        $this->viewData['selectedCompanyId'] = $selectedCompanyId;
        $this->viewData['data'] = $data;

        $providersModel = new ProvidersModel();
        $providers = $providersModel->orderBy('name', 'ASC')->findAll();
        $this->viewData['providers'] = $providers;

        $providersBusinessesModel = new ProvidersBusinessesModel();
        $businessesModel = new BusinessesModel();
        $businesses = $businessesModel->orderBy('name', 'ASC')->findAll();
        foreach ($businesses as $businessKey => $business) {
            $providers = $providersBusinessesModel->where('business_id',
                $business['id'])->findAll();

            $businesses[$businessKey]['providers'] = array_column($providers, 'provider_id');
        }
        $this->viewData['businesses'] = $businesses;

        $companiesModel = new CompaniesModel();
        $businessCompaniesModel = new \App\Models\BusinessesCompaniesModel();
        $companies = $companiesModel->orderBy('name', 'ASC')->findAll();

        foreach ($companies as $companyKey => $company) {
            $businesses = $businessCompaniesModel->where('company_id',
                $company['id'])->findAll();

            $companies[$companyKey]['businesses'] = array_column($businesses, 'business_id');
        }
        $this->viewData['companies'] = $companies;

        return view('Nomenclatures/references', $this->viewData);
    }


    /**
     * @throws Exception
     */
    public function export()
    {
        $selectedProviderId = (int)$this->request->getGet('provider_id') ?? 0;
        $selectedBusinessId = (int)$this->request->getGet('business_id') ?? 0;
        $selectedCompanyId = (int)$this->request->getGet('company_id') ?? 0;
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $data = $this->generateData($selectedProviderId, $selectedBusinessId, $selectedCompanyId, $dateFrom, $dateTo);

        $fileName = 'Справка за  закупена стока.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Група');
        $sheet->setCellValue('B1', 'Код');
        $sheet->setCellValue('C1', 'Наименование');
        $sheet->setCellValue('D1', 'Брой');
        $sheet->setCellValue('E1', 'Обща цена');
        $sheet->setCellValue('F1', 'Средна цена');
        $sheet->setCellValue('G1', 'Фактури');

        $counter = 2;
        foreach ($data['elements'] as $item) {
            $averagePrice = intval($item['quantity']) !== 0 ? doubleval($item['price']) / intval($item['quantity']) : 0;

            $invoicesElements = [];
            foreach ($item['invoices'] as $invoice) {
                $invoicesElements[] = $invoice['number'];
            }
            $invoices = implode(', ', $invoicesElements);

            if(!isset($item['code_number'])){
                $item['code_number'] = '-';
            }

            $sheet->setCellValue('A' . $counter, $item['code_name']);
            $sheet->setCellValue('B' . $counter, $item['code_number']);
            $sheet->setCellValue('C' . $counter, $item['name']);
            $sheet->setCellValue('D' . $counter, $item['quantity']);
            $sheet->setCellValue('E' . $counter, number_format($item['price'], 2, '.', ''));
            $sheet->setCellValue('F' . $counter, number_format($averagePrice, 2, '.', ''));
            $sheet->setCellValue('G' . $counter, $invoices);
            $counter++;
        }

        // Add discount to the end of the file
        if(in_array($selectedProviderId, [1, 2])) {
            // Define yellow fill style
            $yellowFill = [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f5db47'],
            ];

            // Add 3 empty rows
            for ($i = 0; $i < 3; $i++) {
                $sheet->setCellValue('A' . $counter, '');
                $counter++;
            }

            // Add header for the discount section
            $sheet->setCellValue('A' . $counter, 'Отстъпки');
            $sheet->getStyle('A' . $counter . ':G' . $counter)->getFill()->applyFromArray($yellowFill);
            $counter++;

            // Add discount headers
            $sheet->setCellValue('A' . $counter, 'Фактура');
            $sheet->setCellValue('B' . $counter, 'Отстъпка');
            $sheet->getStyle('A' . $counter . ':G' . $counter)->getFill()->applyFromArray($yellowFill);
            $counter++;

            foreach ($data['discounts'] as $discount) {
                $sheet->setCellValue('A' . $counter, $discount['number']);
                $sheet->setCellValue('B' . $counter, number_format($discount['discount'], 2, '.', ''));
                $sheet->getStyle('A' . $counter . ':G' . $counter)->getFill()->applyFromArray($yellowFill);
                $counter++;
            }
        }

        // Align column G to left
        $sheet->getStyle('G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Set the appropriate headers to force download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit(); // Make sure the script stops after sending the file
    }


    public function export_invalid()
    {
        $selectedProviderId = (int)$this->request->getGet('provider_id') ?? 0;
        $selectedBusinessId = (int)$this->request->getGet('business_id') ?? 0;
        $selectedCompanyId = (int)$this->request->getGet('company_id') ?? 0;
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $data = $this->generateData($selectedProviderId, $selectedBusinessId, $selectedCompanyId, $dateFrom, $dateTo);

        $fileName = 'Справка за  закупена стока - грешни.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Група');
        $sheet->setCellValue('B1', 'Код');
        $sheet->setCellValue('C1', 'Наименование');
        $sheet->setCellValue('D1', 'Брой');
        $sheet->setCellValue('E1', 'Обща цена');
        $sheet->setCellValue('F1', 'Средна цена');
        $sheet->setCellValue('G1', 'Фактури');

        $counter = 2;
        foreach ($data['missing'] as $item) {
            $averagePrice = intval($item['quantity']) !== 0 ? doubleval($item['price']) / intval($item['quantity']) : 0;

            $invoicesElements = [];
            foreach ($item['invoices'] as $invoice) {
                $invoicesElements[] = $invoice['number'];
            }
            $invoices = implode(', ', $invoicesElements);

            $sheet->setCellValue('A' . $counter, '');
            $sheet->setCellValue('B' . $counter, '');
            $sheet->setCellValue('C' . $counter, $item['name']);
            $sheet->setCellValue('D' . $counter, $item['quantity']);
            $sheet->setCellValue('E' . $counter, number_format($item['price'], 2, '.', ''));
            $sheet->setCellValue('F' . $counter, number_format($averagePrice, 2, '.', ''));
            $sheet->setCellValue('G' . $counter, $invoices);
            $counter++;
        }

        // Align column G to left
        $sheet->getStyle('G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Set the appropriate headers to force download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit(); // Make sure the script stops after sending the file
    }


    private function generateData(
        $selectedProviderId,
        $selectedBusinessId,
        $selectedCompaniesId,
        $dateFrom,
        $dateTo
    ): array {
        $data = [];

        if ($selectedProviderId === 0 || $selectedBusinessId === 0) {
            return $data;
        }

        // Store companies in an array for easier access
        $companiesModel = new CompaniesModel();
        $companies = $companiesModel->orderBy('name', 'ASC')->findAll();

        $selectedCompanyNames = [];

        if($selectedCompaniesId !== 0){
            foreach ($companies as $company) {
                if ($company['id'] == $selectedCompaniesId) {
                    $selectedCompanyNames[] = mb_strtolower($company['name']);
                    for ($i = 1; $i <= 10; $i++) {
                        if (!empty($company['alias_' . $i])) {
                            $selectedCompanyNames[] = mb_strtolower($company['alias_' . $i]);
                        }
                    }
                }
            }
        } else {{
            //Get all companies based on Business ID
            $businessCompaniesModel = new \App\Models\BusinessesCompaniesModel();
            $companiesIds = $businessCompaniesModel->where('business_id', $selectedBusinessId)->findAll();

            foreach ($companiesIds as $company) {
                $company = $companiesModel->where('id', $company['company_id'])->first();
                $selectedCompanyNames[] = mb_strtolower($company['name']);
                for ($i = 1; $i <= 10; $i++) {
                    if (!empty($company['alias_' . $i])) {
                        $selectedCompanyNames[] = mb_strtolower($company['alias_' . $i]);
                    }
                }
            }
        }}


        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $invoices = $purchaseByDocumentDataModel->select('id, invoice_number')
            ->where('provider_id', $selectedProviderId)
            ->where('business_id', $selectedBusinessId)
            ->where('invoice_date >=', $dateFrom)
            ->where('invoice_date <=', $dateTo)
            ->findAll();

        // Initialize an empty array to store unique invoice IDs.
        $uniqueInvoices = [];

        // Loop through the original array.
        foreach ($invoices as $invoice) {
            // Use invoice_number as the key, this ensures only the first occurrence is kept.
            $uniqueInvoices[$invoice['invoice_number']] = $invoice['id'];
        }

        // Get only the unique IDs.
        $invoiceIds = array_values($uniqueInvoices);

        //$invoiceIds = array_column($invoices, 'id');

        if (empty($invoiceIds)) {
            return [
                'elements' => [],
                'missing' => [],
                'discounts' => []
            ];
        }

        $invoicesItems = [];
        if ($selectedProviderId === 1) {
            $invoiceEntitiesModel = new PBDStingInvoiceItemsModel();
            $pBDStingRecipientModel = new \App\Models\PBDStingRecipientModel();

            $filteredInvoices = $pBDStingRecipientModel->select('purchase_by_document_id')
                ->whereIn('purchase_by_document_id', $invoiceIds)
                ->whereIn('LOWER(name)', $selectedCompanyNames)
                ->findAll();

            foreach ($filteredInvoices as $invoiceId) {
                $items = $invoiceEntitiesModel->where('purchase_by_document_id',
                    $invoiceId['purchase_by_document_id'])->findAll();
                foreach ($items as $item) {
                    $invoicesItems[] = [
                        'invoice_id' => $invoiceId['purchase_by_document_id'],
                        'name' => trim($item['designation']),
                        'quantity' => $item['quantity'],
                        'price' => $item['value'],
                        'single_item_price' => $item['wholesaler_price'],
                        'credit_notice' => (int)$item['credit_notice'] ?? 0
                    ];
                }
            }
        } elseif ($selectedProviderId === 2) {
            $invoiceEntitiesModel = new PBDFioniksFarmaInvoiceItemsModel();
            $pBDFioniksFarmaDeliveryModel = new PBDFioniksFarmaDeliveryModel();

            $filteredInvoices = $pBDFioniksFarmaDeliveryModel->select('purchase_by_document_id')
                ->whereIn('purchase_by_document_id', $invoiceIds)
                ->whereIn('LOWER(place)', $selectedCompanyNames)
                ->findAll();

            foreach ($filteredInvoices as $invoiceId) {
                $items = $invoiceEntitiesModel->where('purchase_by_document_id',
                    $invoiceId['purchase_by_document_id'])->findAll();
                foreach ($items as $item) {
                    $invoicesItems[] = [
                        'invoice_id' => $invoiceId['purchase_by_document_id'],
                        'name' => trim(str_replace('ОТСТЪПКА', '', $item['designation'])),
                        'quantity' => $item['quantity'],
                        'price' => $item['value'],
                        'single_item_price' => $item['wholesaler_price'],
                        'credit_notice' => $item['credit_notice'] ?? 0
                    ];
                }
            }
        } elseif ($selectedProviderId === 3) {
            $invoiceEntitiesModel = new PBDAsterInvoiceItemsModel();
            $pBDAsterRecipientModel = new \App\Models\PBDAsterRecipientModel();

            $filteredInvoices = $pBDAsterRecipientModel->select('purchase_by_document_id')
                ->whereIn('purchase_by_document_id', $invoiceIds)
                ->whereIn('LOWER(company_name)', $selectedCompanyNames)
                ->findAll();

            foreach ($filteredInvoices as $invoiceId) {
                $items = $invoiceEntitiesModel->where('purchase_by_document_id',
                    $invoiceId['purchase_by_document_id'])->findAll();
                foreach ($items as $item) {
                    $invoicesItems[] = [
                        'invoice_id' => $invoiceId['purchase_by_document_id'],
                        'name' => trim($item['product_name']),
                        'quantity' => $item['quantity'],
                        'price' => $item['totalValue'],
                        'single_item_price' => $item['price_per_item'],
                        'credit_notice' => $item['credit_notice'] ?? 0
                    ];
                }
            }
        }

        $groupedItems = [];
        foreach ($invoicesItems as $item) {
            $elementName = $item['name'] . ' ||| ' . $item['single_item_price'];

            //Get invoice number by id
            $invoiceDetails = $purchaseByDocumentDataModel->select('invoice_number')
                ->where('id', $item['invoice_id'])
                ->first();
            $invoice = [
                'id' => $item['invoice_id'],
                'number' => $invoiceDetails['invoice_number']
            ];

            $quantity = $item['quantity'];
            $price = $item['price'];

            if($item['credit_notice'] === 1){
                $quantity = -$quantity;
                $price = -$price;
            }

            if (!isset($groupedItems[$elementName])) {
                $groupedItems[$elementName] = [
                    'name' => $item['name'],
                    'invoices' => [$invoice],
                    'quantity' => $quantity,
                    'price' => $price,
                    'single_item_price' => $item['single_item_price']
                ];
            } else {
                $groupedItems[$elementName]['invoices'][] = $invoice;
                $groupedItems[$elementName]['quantity'] += $quantity;
                $groupedItems[$elementName]['price'] += $price;
            }
        }
        
        $groupedFilteredItems = $this->mergeDuplicateEntries($groupedItems);

        // Fetch only relevant nomenclaturesSyncEntities based on names in groupedFilteredItems
        $names = array_map(function ($item) {
            return mb_strtolower($item['name']);
        }, $groupedFilteredItems);

        if(empty($names)){
            return [
                'elements' => [],
                'missing' => [],
                'discounts' => []
            ];
        }

        $nomenclaturesSyncEntitiesModel = new \App\Models\NomenclaturesSyncEntitiesModel();
        $nomenclaturesSyncEntities = $nomenclaturesSyncEntitiesModel
            ->whereIn('LOWER(name)', $names)
            ->findAll();

        // Build an associative array (hash map) for quick lookups
        $nomenclatureSyncEntityMap = [];
        foreach ($nomenclaturesSyncEntities as $nomenclaturesSyncEntity) {
            $nomenclatureSyncEntityMap[mb_strtolower($nomenclaturesSyncEntity['name'])] = $nomenclaturesSyncEntity;
        }

        $finalData = [
            'elements' => [],
            'missing' => [],
            'discounts' => []
        ];

        foreach ($groupedFilteredItems as $groupedFilteredItem) {
            $name = mb_strtolower($groupedFilteredItem['name']);
            if (isset($nomenclatureSyncEntityMap[$name])) {
                $nomenclatureSyncEntity = $nomenclatureSyncEntityMap[$name];

                $uniqueInvoices = array_unique($groupedFilteredItem['invoices'], SORT_REGULAR);

                $finalData['elements'][] = [
                    'name' => $groupedFilteredItem['name'],
                    'invoices' => $uniqueInvoices,
                    'quantity' => $groupedFilteredItem['quantity'],
                    'price' => $groupedFilteredItem['price'],
                    'single_item_price' => $groupedFilteredItem['single_item_price'],
                    'code_name' => $nomenclatureSyncEntity['code_name']
                ];
            } else {
                $finalData['missing'][] = $groupedFilteredItem;
            }
        }

        // Fetch all nomenclaturesEntities and map them based on code_name
        $nomenclaturesEntitiesModel = new NomenclaturesEntitiesModel();
        $nomenclaturesEntities = $nomenclaturesEntitiesModel->findAll();

        // Mapping nomenclatureEntities for quick lookup
        $nomenclaturesEntityMap = [];
        foreach ($nomenclaturesEntities as $nomenclaturesEntity) {
            $nomenclaturesEntityMap[mb_strtolower($nomenclaturesEntity['code_name'])][] = $nomenclaturesEntity;
        }

        // Match the finalData elements to the correct nomenclaturesEntities based on code_name and price range
        foreach ($finalData['elements'] as $key => $row) {
            $codeName = mb_strtolower($row['code_name']);
            $tokensToReplace = ['а' => 'a', 'в' => 'b', 'с' => 'c'];
            $codeName = str_replace(array_keys($tokensToReplace), array_values($tokensToReplace), $codeName);
            $foundedCodes = $nomenclaturesEntityMap[$codeName] ?? [];

            foreach ($foundedCodes as $foundedCode) {
                $rowPrice = intval($row['quantity']) !== 0 ? doubleval($row['price']) / intval($row['quantity']) : 0.00;
                $rowPrice = round($rowPrice, 2);
                $codePriceFrom = round(doubleval($foundedCode['price_from']), 2);
                $codePriceTo = round(doubleval($foundedCode['price_to']), 2);

                if ($rowPrice >= $codePriceFrom && $rowPrice <= $codePriceTo) {
                    $finalData['elements'][$key]['code_number'] = $foundedCode['code_number'];
                    break;
                }
            }
        }

        //Search for discounts
        $uniqueInvoicesForDiscounts = [];
        if ($selectedProviderId === 1) {
            foreach ($finalData['elements'] as $element) {
                foreach ($element['invoices'] as $invoice) {
                    $uniqueInvoicesForDiscounts[$invoice['id']]['id'] = $invoice['id'];
                    $uniqueInvoicesForDiscounts[$invoice['id']]['number'] = $invoice['number'];
                }
            }

            //For all invoices ids get them from pbd_sting_invoice_price
            $pBDStingInvoicePriceModel = new \App\Models\PBDStingInvoicePriceModel();
            $discounts = $pBDStingInvoicePriceModel->select('purchase_by_document_id, trade_discount')
                ->whereIn('purchase_by_document_id', array_column($uniqueInvoicesForDiscounts, 'id'))
                ->findAll();

            foreach ($discounts as $discount) {
                $uniqueInvoicesForDiscounts[$discount['purchase_by_document_id']]['discount'] = $discount['trade_discount'];
            }

            //From $uniqueInvoicesForDiscounts remove all elements that discount is < 0
            $uniqueInvoicesForDiscounts = array_filter($uniqueInvoicesForDiscounts, function ($invoice) {
                return $invoice['discount'] > 0;
            });

            $finalData['discounts'] = $uniqueInvoicesForDiscounts;
        } elseif ($selectedProviderId === 2) {
            foreach ($finalData['elements'] as $element) {
                foreach ($element['invoices'] as $invoice) {
                    $uniqueInvoicesForDiscounts[$invoice['id']]['id'] = $invoice['id'];
                    $uniqueInvoicesForDiscounts[$invoice['id']]['number'] = $invoice['number'];
                }
            }

            //For all invoices ids get them from pbd_sting_invoice_price
            $pBDFioniksFarmaInvoicePriceModel = new \App\Models\PBDFioniksFarmaInvoicePriceModel();
            $discounts = $pBDFioniksFarmaInvoicePriceModel->select('purchase_by_document_id, trade_discount')
                ->whereIn('purchase_by_document_id', array_column($uniqueInvoicesForDiscounts, 'id'))
                ->findAll();

            foreach ($discounts as $discount) {
                $uniqueInvoicesForDiscounts[$discount['purchase_by_document_id']]['discount'] = $discount['trade_discount'];
            }

            //From $uniqueInvoicesForDiscounts remove all elements that discount is < 0
            $uniqueInvoicesForDiscounts = array_filter($uniqueInvoicesForDiscounts, function ($invoice) {
                return $invoice['discount'] > 0;
            });

            $finalData['discounts'] = $uniqueInvoicesForDiscounts;
        }

        return $finalData;
    }

    private function mergeDuplicateEntries($array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            // Use both product name and single_item_price to create a unique identifier
            $productKey = $value['name'] . ' ||| ' . number_format($value['single_item_price'], 2);

            if (isset($result[$productKey])) {
                // Merge quantities, prices, and invoices if the product already exists
                $result[$productKey]['quantity'] += $value['quantity'];
                $result[$productKey]['price'] += $value['price'];
                $result[$productKey]['invoices'] = array_merge($result[$productKey]['invoices'], $value['invoices']);
            } else {
                // Add the product to the result
                $result[$productKey] = $value;
            }
        }

        // Remove duplicate invoices (if any) from merged entries
        foreach ($result as &$product) {
            $product['invoices'] = array_unique($product['invoices'], SORT_REGULAR);
        }

        return $result;
    }
}