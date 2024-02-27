<?php

namespace App\Controllers\PurchaseByDocument;

use App\Controllers\BaseController;
use App\Helpers\NumberFormat;
use App\Models\BusinessesModel;
use App\Models\PBDAsterInvoiceItemsModel;
use App\Models\PBDAsterRecipientModel;
use App\Models\PBDFioniksFarmaDeliveryModel;
use App\Models\PBDFioniksFarmaInvoiceItemsModel;
use App\Models\PBDFioniksFarmaInvoicePaymentModel;
use App\Models\PBDFioniksFarmaInvoicePriceModel;
use App\Models\PBDFioniksFarmaRecipientModel;
use App\Models\PBDFioniksFarmaSupplierModel;
use App\Models\PBDStingDeliveryModel;
use App\Models\PBDStingInvoiceItemsModel;
use App\Models\PBDStingInvoicePaymentModel;
use App\Models\PBDStingInvoicePriceModel;
use App\Models\PBDStingRecipientModel;
use App\Models\PBDStingSupplierModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\ProvidersModel;
use App\Models\PurchaseByDocumentDataModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;
use PhpOffice\PhpSpreadsheet\Exception;

class Add extends BaseController
{
    private array $viewData = [];

    private \CodeIgniter\Session\Session $session;

    public function __construct()
    {
        $this->session = Services::session();
    }

    public function index(): string
    {
        $this->viewData['assets']['js'] = 'PurchaseByDocument/add.js';

        $providersModel = new ProvidersModel();
        $this->viewData['providers'] = $providersModel->orderBy('name', 'asc')->findAll();


        return view('PurchaseByDocument/add', $this->viewData);
    }


    /**
     * @throws Exception
     */
    public function submit(): RedirectResponse
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


        $parsedData = [];
        $providerId = (int)$this->request->getPost('providers');

        if ($providerId === 1) {
            $parsedData = $this->parseSting();
            $this->validateSting($parsedData);
        } elseif ($providerId === 2) {
            $parsedData = $this->parseFioniksFarma();
            $this->validateFioniksFarma($parsedData);
        } elseif ($providerId === 3) {
            $parsedData = $this->parseAster();
        }


        //@todo redirect to error page
        if (empty($parsedData)) {
            die('Invalid provider !');
        }

        $parsedDataStatistics = [
            'total' => 0,
            'success' => 0,
            'errors' => 0,
        ];

        foreach ($parsedData as $parsedDataKey => $parsedDataValue) {
            $parsedDataStatistics['total']++;
            if (isset($parsedDataValue['error'])) {
                $parsedDataStatistics['errors']++;
            } else {
                $parsedDataStatistics['success']++;
            }
        }

        //echo 'DEBUGGGG!!!'; print_r2($parsedDataStatistics); print_r2($parsedData,1);

        $this->session->set('PbParsedDataProvider', $providerId);
        $this->session->set('PbParsedData', $parsedData);
        $this->session->set('PbParsedDataStatistics', $parsedDataStatistics);

        return redirect()->to('/purchase-by-document/submit-preview');
    }

    public function submit_preview(): string
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

        $this->viewData['assets']['js'] = 'PurchaseByDocument/submit-preview.js';

        $this->viewData['parsedData'] = $this->session->get('PbParsedData');
        $this->viewData['parsedDataStatistics'] = $this->session->get('PbParsedDataStatistics');
        $provider = $this->session->get('PbParsedDataProvider');

        if ($provider === 1) {
            return view('PurchaseByDocument/SubmitPreview/Sting/Preview', $this->viewData);
        } elseif ($provider === 2) {
            return view('PurchaseByDocument/SubmitPreview/FioniksFarma/Preview', $this->viewData);
        } elseif ($provider === 3) {
            return view('PurchaseByDocument/SubmitPreview/Aster/Preview', $this->viewData);
        }

        die('Invalid provider !');
    }


    /**
     * @throws \ReflectionException
     */
    public function finish(): RedirectResponse
    {
        $provider = $this->session->get('PbParsedDataProvider');

        if ($provider === 1) {
            $this->finishSting();
        } elseif ($provider === 2) {
            $this->finishFioniksFarma();
        } elseif ($provider === 3) {
            $this->finishAster();
        }

        return redirect()->to('/purchase-by-document/done');
    }


    public function done(): string
    {
        $this->viewData['parsedDataStatistics'] = $this->session->get('PbParsedDataStatistics');
        return view('PurchaseByDocument/done', $this->viewData);
    }


    /**
     * @throws \ReflectionException
     */
    private function finishFioniksFarma()
    {
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdFioniksFarmaRecipientModel = new PBDFioniksFarmaRecipientModel();
        $pbdFioniksFarmaSupplierModel = new PBDFioniksFarmaSupplierModel();
        $pbdFioniksFarmaDeliveryModel = new PBDFioniksFarmaDeliveryModel();
        $pbdFioniksFarmaInvoicePriceyModel = new PBDFioniksFarmaInvoicePriceModel();
        $pbdFioniksFarmaInvoicePaymentModel = new PBDFioniksFarmaInvoicePaymentModel();
        $pbdFioniksFarmaInvoiceItemsModel = new PBDFioniksFarmaInvoiceItemsModel();

        $parsedData = $this->session->get('PbParsedData');

        foreach ($parsedData as $data) {

            //Save base data for added document for "Покупка по документ"
            $purchaseByDocumentData = [
                'provider_id' => 2,
                'business_id' => $data['business']['id'],
                'document_type' => $data['invoiceType'],
                'invoice_number' => $data['parsed']['invoiceInfo']['result']['number'],
                'invoice_date' => $data['parsed']['invoiceInfo']['result']['date'],
                'amount' => $data['parsed']['invoicePrice']['result']['totalPrice'],
                'payment_amount' => $data['parsed']['invoicePrice']['result']['totalPriceWithTax'],
                'items' => count($data['parsed']['invoiceItems']['result']),
                'entities' => $data['parsed']['itemsCount'],
                'nzok' => $data['parsed']['nzok'] ?? 0,
                'source_type' => $data['type'],
                'source_name' => $data['name'],
                'source_content' => $data['originalContent'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            $purchaseByDocumentDataModel->insert($purchaseByDocumentData);
            $purchaseByDocumentId = $purchaseByDocumentDataModel->getInsertID();


            //Save recipient data
            $recipientData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'name' => $data['parsed']['recipient']['result']['name'],
                'address' => $data['parsed']['recipient']['result']['address'],
                'in_number' => $data['parsed']['recipient']['result']['idNumber'],
                'vat_number' => $data['parsed']['recipient']['result']['vatNumber'],
                'license' => $data['parsed']['recipient']['result']['license'],
                'opiates_license' => $data['parsed']['recipient']['result']['opiatesLicense'],
                'mol' => $data['parsed']['recipient']['result']['mol'],
                'phone' => $data['parsed']['recipient']['result']['phone'],
            ];
            $pbdFioniksFarmaRecipientModel->insert($recipientData);


            //Save supplier data
            $supplierData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'name' => $data['parsed']['supplier']['result']['name'],
                'address' => $data['parsed']['supplier']['result']['address'],
                'in_number' => $data['parsed']['supplier']['result']['idNumber'],
                'vat_number' => $data['parsed']['supplier']['result']['vatNumber'],
                'license' => $data['parsed']['supplier']['result']['license'],
                'opiates_license' => $data['parsed']['supplier']['result']['opiatesLicense'],
                'mol' => $data['parsed']['supplier']['result']['mol'],
                'phone' => $data['parsed']['supplier']['result']['phone'],
            ];
            $pbdFioniksFarmaSupplierModel->insert($supplierData);


            //Save delivery data
            $deliveryData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'average_delivery' => $data['parsed']['delivery']['result']['averageDelivery'],
                'place' => $data['parsed']['delivery']['result']['place'],
                'address' => $data['parsed']['delivery']['result']['address'],
                'route' => $data['parsed']['delivery']['result']['route'],
            ];
            $pbdFioniksFarmaDeliveryModel->insert($deliveryData);


            //Save invoice price
            $invoicePriceData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'taxable_value' => $data['parsed']['invoicePrice']['result']['taxableValue'],
                'total_price' => $data['parsed']['invoicePrice']['result']['totalPrice'],
                'total_price_from_supplier' => $data['parsed']['invoicePrice']['result']['totalPriceFromSupplier'],
                'trade_discount' => $data['parsed']['invoicePrice']['result']['tradeDiscount'],
                'tax_base_9' => $data['parsed']['invoicePrice']['result']['taxBase9'],
                'tax_9' => $data['parsed']['invoicePrice']['result']['tax9'],
                'tax_base_20' => $data['parsed']['invoicePrice']['result']['taxBase20'],
                'tax_20' => $data['parsed']['invoicePrice']['result']['tax20'],
                'tax_base_0' => $data['parsed']['invoicePrice']['result']['taxBase0'],
                'total_price_with_tax' => $data['parsed']['invoicePrice']['result']['totalPriceWithTax'],
                'total_price_with_tax_in_words' => $data['parsed']['invoicePrice']['result']['totalPriceWithTaxInWords'],
                'note' => $data['parsed']['invoicePrice']['result']['note']
            ];
            $pbdFioniksFarmaInvoicePriceyModel->insert($invoicePriceData);


            //Save invoice payment
            $invoicePaymentData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'payment_info' => $data['parsed']['invoicePayment']['result']['paymentInfo'],
                'payment_date' => $data['parsed']['invoicePayment']['result']['paymentDate'],
                'tax_event_date' => $data['parsed']['invoicePayment']['result']['taxEventDate'],
                'payer_bic' => $data['parsed']['invoicePayment']['result']['payerBIC'],
                'payer_iban' => $data['parsed']['invoicePayment']['result']['payerIBAN'],
                'payer_bank' => $data['parsed']['invoicePayment']['result']['payerBank']
            ];
            $pbdFioniksFarmaInvoicePaymentModel->insert($invoicePaymentData);


            //Save invoice items
            foreach ($data['parsed']['invoiceItems']['result'] as $invoiceItem) {
                $invoiceItemData = [
                    'purchase_by_document_id' => $purchaseByDocumentId,
                    'number' => $invoiceItem['itemNumber'],
                    'code' => $invoiceItem['code'] ?? '',
                    'designation' => $invoiceItem['designation'],
                    'manufacturer' => $invoiceItem['manufacturer'],
                    'quantity' => $invoiceItem['quantity'],
                    'base_price' => $invoiceItem['basePrice'],
                    'trade_markup' => $invoiceItem['tradeMarkup'] ?? 0,
                    'trade_discount' => $invoiceItem['tradeDiscount'],
                    'wholesaler_price' => $invoiceItem['wholesalerPrice'] ?? 0,
                    'value' => $invoiceItem['value'],
                    'price_with_vat' => $invoiceItem['priceWithVAT'],
                    'batch' => $invoiceItem['batch'] ?? '',
                    'certificate' => $invoiceItem['certificate'] ?? '',
                    'expiry_date' => $invoiceItem['expiryDate'] ?? '',
                    'pharmacy_price' => $invoiceItem['pharmacyPrice'] ?? 0,
                    'limit_price' => $invoiceItem['limitPrice'] ?? 0,
                    'limit_price_type' => $invoiceItem['limitPriceType'] ?? '',
                    'inn' => $invoiceItem['INN'] ?? '',
                ];
                $pbdFioniksFarmaInvoiceItemsModel->insert($invoiceItemData);
            }
        }
    }


    /**
     * @throws \ReflectionException
     */
    private function finishSting()
    {
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdStingRecipientModel = new PBDStingRecipientModel();
        $pbdStingSupplierModel = new PBDStingSupplierModel();
        $pbdStingDeliveryModel = new PBDStingDeliveryModel();
        $pbdStingInvoicePriceyModel = new PBDStingInvoicePriceModel();
        $pbdStingInvoicePaymentModel = new PBDStingInvoicePaymentModel();
        $pbdStingInvoiceItemsModel = new PBDStingInvoiceItemsModel();

        $parsedData = $this->session->get('PbParsedData');

        foreach ($parsedData as $data) {

            //Save base data for added document for "Покупка по документ"
            $purchaseByDocumentData = [
                'provider_id' => 1,
                'business_id' => $data['business']['id'],
                'document_type' => $data['invoiceType'],
                'invoice_number' => $data['parsed']['invoiceInfo']['result']['number'],
                'invoice_date' => $data['parsed']['invoiceInfo']['result']['date'],
                'amount' => $data['parsed']['invoicePrice']['result']['totalPrice'],
                'payment_amount' => $data['parsed']['invoicePrice']['result']['totalPriceWithTax'],
                'items' => count($data['parsed']['invoiceItems']['result']),
                'entities' => $data['parsed']['itemsCount'],
                'nzok' => $data['parsed']['nzok'] ?? 0,
                'source_type' => $data['type'],
                'source_name' => $data['name'],
                'source_content' => $data['originalContent'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            $purchaseByDocumentDataModel->insert($purchaseByDocumentData);
            $purchaseByDocumentId = $purchaseByDocumentDataModel->getInsertID();


            //Save recipient data
            $recipientData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'name' => $data['parsed']['recipient']['result']['name'],
                'address' => $data['parsed']['recipient']['result']['address'],
                'address_reg' => $data['parsed']['recipient']['result']['address_reg'],
                'in_number' => $data['parsed']['recipient']['result']['idNumber'],
                'vat_number' => $data['parsed']['recipient']['result']['vatNumber'],
                'license' => $data['parsed']['recipient']['result']['license'],
                'opiates_license' => $data['parsed']['recipient']['result']['opiatesLicense'],
                'mol' => $data['parsed']['recipient']['result']['mol'],
                'phone' => $data['parsed']['recipient']['result']['phone'],
                'client_id' => $data['parsed']['recipient']['result']['client_id'],
            ];
            $pbdStingRecipientModel->insert($recipientData);


            //Save supplier data
            $supplierData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'name' => $data['parsed']['supplier']['result']['name'],
                'address' => $data['parsed']['supplier']['result']['address'],
                'in_number' => $data['parsed']['supplier']['result']['idNumber'],
                'vat_number' => $data['parsed']['supplier']['result']['vatNumber'],
                'license' => $data['parsed']['supplier']['result']['license'],
                'opiates_license' => $data['parsed']['supplier']['result']['opiatesLicense'],
                'phone' => $data['parsed']['supplier']['result']['phone'],
                'controlling_person' => $data['parsed']['supplier']['result']['controlling_person'],
            ];
            $pbdStingSupplierModel->insert($supplierData);


            //Save delivery data
            $deliveryData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'pharmacist_in_charge' => $data['parsed']['delivery']['result']['pharmacistInCharge'],
                'place' => $data['parsed']['delivery']['result']['place'],
                'address' => $data['parsed']['delivery']['result']['address'],
                'route' => $data['parsed']['delivery']['result']['route'],
                'date' => $data['parsed']['delivery']['result']['date'],
            ];
            $pbdStingDeliveryModel->insert($deliveryData);

            //Save invoice price
            $invoicePriceData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'total_price' => $data['parsed']['invoicePrice']['result']['totalPrice'],
                'total_price_from_supplier' => $data['parsed']['invoicePrice']['result']['totalPriceFromSupplier'],
                'trade_discount_percent' => $data['parsed']['invoicePrice']['result']['tradeDiscountPercent'],
                'value_of_the_deal' => $data['parsed']['invoicePrice']['result']['valueOfTheDeal'],
                'tax_20' => $data['parsed']['invoicePrice']['result']['tax20'],
                'total_price_with_tax' => $data['parsed']['invoicePrice']['result']['totalPriceWithTax'],
                'total_price_with_tax_in_words' => $data['parsed']['invoicePrice']['result']['totalPriceWithTaxInWords'],
                'tax_base' => $data['parsed']['invoicePrice']['result']['taxBase'],
                'trade_discount' => $data['parsed']['invoicePrice']['result']['tradeDiscount'],
                'doc_number' => $data['parsed']['invoicePrice']['result']['docNumber'],
                'note' => $data['parsed']['invoicePrice']['result']['note']
            ];
            $pbdStingInvoicePriceyModel->insert($invoicePriceData);


            //Save invoice payment
            $invoicePaymentData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'payment_info' => $data['parsed']['invoicePayment']['result']['paymentInfo'],
                'tax_event_date' => $data['parsed']['invoicePayment']['result']['taxEventDate'],
                'payer_bic' => $data['parsed']['invoicePayment']['result']['payerBIC'],
                'payer_iban' => $data['parsed']['invoicePayment']['result']['payerIBAN'],
                'payer_bank' => $data['parsed']['invoicePayment']['result']['payerBank']
            ];
            $pbdStingInvoicePaymentModel->insert($invoicePaymentData);


            //Save invoice items
            foreach ($data['parsed']['invoiceItems']['result'] as $invoiceItem) {
                $invoiceItemData = [
                    'purchase_by_document_id' => $purchaseByDocumentId,
                    'number' => $invoiceItem['itemNumber'],
                    'designation' => $invoiceItem['designation'],
                    'manufacturer' => $invoiceItem['manufacturer'],
                    'batch' => $invoiceItem['batch'] ?? '',
                    'quantity' => $invoiceItem['quantity'],
                    'expiry_date' => $invoiceItem['expiryDate'] ?? '',
                    'certificate' => $invoiceItem['certificate'] ?? '',
                    'base_price' => $invoiceItem['basePrice'],
                    'trade_markup' => $invoiceItem['tradeMarkup'] ?? 0,
                    'trade_discount' => $invoiceItem['tradeDiscount'],
                    'wholesaler_price' => $invoiceItem['wholesalerPrice'] ?? 0,
                    'value' => $invoiceItem['value'],
                    'price_with_vat' => $invoiceItem['priceWithVAT'],
                    'recommended_price' => $invoiceItem['recommendedPrice'] ?? 0,
                    'limit_price' => $invoiceItem['limitPrice'] ?? '',
                    'percent_a' => $invoiceItem['percent_a'] ?? '',
                    'nzok' => $invoiceItem['nzok'] ?? '',
                ];
                $pbdStingInvoiceItemsModel->insert($invoiceItemData);
            }
        }
    }


    /**
     * @throws \ReflectionException
     */
    private function finishAster()
    {
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdAsterRecipientModel = new PBDAsterRecipientModel();
        $pbdAsterInvoiceItemsModel = new PBDAsterInvoiceItemsModel();

        $parsedData = $this->session->get('PbParsedData');

        foreach ($parsedData as $data) {

            if(isset($data['error'])){ continue; }

            //Save base data for added document for "Покупка по документ"
            $purchaseByDocumentData = [
                'provider_id' => 3,
                'business_id' => $data['parsed']['founded_business_id'],
                'document_type' => 'file',
                'invoice_number' => $data['parsed']['invoice_number'],
                'invoice_date' => $data['parsed']['invoice_date'],
                'amount' => $data['parsed']['totalPrice'],
                'payment_amount' => $data['parsed']['totalPrice'],
                'items' => count($data['parsed']['items']),
                'entities' => $data['parsed']['itemsCount'],
                'nzok' => 0,
                'source_type' => $data['type'],
                'source_name' => $data['name'],
                'source_content' => $data['originalContent'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            $purchaseByDocumentDataModel->insert($purchaseByDocumentData);
            $purchaseByDocumentId = $purchaseByDocumentDataModel->getInsertID();


            //Save recipient data
            $recipientData = [
                'purchase_by_document_id' => $purchaseByDocumentId,
                'company_id' => $data['parsed']['founded_company_id'],
                'company_name' => $data['parsed']['company_name'],
                'company_name_real' => $data['parsed']['founded_company_name'],
                'business_name' => $data['parsed']['founded_business_name'],
                'business_id' => $data['parsed']['founded_business_id'],
                'business_in_number' => $data['parsed']['founded_business_in_number'],
                'address' => $data['parsed']['address'],
            ];
            $pbdAsterRecipientModel->insert($recipientData);


            //Save invoice items
            foreach ($data['parsed']['items'] as $invoiceItem) {
                $invoiceItemData = [
                    'purchase_by_document_id' => $purchaseByDocumentId,
                    'product_code' => $invoiceItem['product_code'],
                    'product_name' => $invoiceItem['product_name'],
                    'pharmacy_code' => $invoiceItem['pharmacy_code'],
                    'quantity' => $invoiceItem['quantity'],
                    'totalValue' => $invoiceItem['totalValue'],
                    'price_per_item' => $invoiceItem['price_per_item'],
                ];
                $pbdAsterInvoiceItemsModel->insert($invoiceItemData);
            }
        }
    }


    /**
     * @throws Exception
     */
    private function parseAster(): array
    {
        $items = [];
        $files = $this->request->getFiles();

        //Parse attached files
        if (!empty($files['files'])) {
            foreach ($files['files'] as $file) {

                if (empty($file->getName())) {
                    continue;
                }

                //Parse content
                $asterParser = new \App\Libraries\PurchaseByDocument\Parsers\Aster\Parser();
                $asterParser->execute($file->getTempName());

                $uuID = uniqid();
                $uploadedFileDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
                if (!is_dir($uploadedFileDir)) {
                    mkdir($uploadedFileDir, 0777, true);
                }
                $uploadedFileLocation = $uploadedFileDir . $uuID . '.' . pathinfo($file->getName(), PATHINFO_EXTENSION);
                move_uploaded_file($file->getTempName(), $uploadedFileLocation);

                $parsedResult = $asterParser->getResult();

                foreach ($parsedResult as $parsedResultValue) {
                    $items[] = [
                        'type' => 'file',
                        'name' => $file->getName(),
                        'originalContent' => '',
                        'parsed' => $parsedResultValue,
                        'invoiceType' => $asterParser->getInvoiceType(),
                        'fileTmpName' => $uploadedFileLocation
                    ];

                    if(isset($parsedResultValue['error'])){
                        $items[count($items)-1]['error'] = $parsedResultValue['error'];
                    }
                }

            }
        }

        return $items;
    }



    private function parseSting(): array
    {
        $items = [];
        $files = $this->request->getFiles();
        $texts = $this->request->getPost('texts') ?? [];


        //Parse attached texts
        foreach ($texts as $textElementKey => $text) {

            //Parse content
            $stingParser = new \App\Libraries\PurchaseByDocument\Parsers\Sting\Parser();
            $stingParser->execute($text);

            //Prepare data
            $items[] = [
                'type' => 'text',
                'name' => "Текст " . ($textElementKey + 1),
                'originalContent' => $text,
                'parsed' => $stingParser->getResult(),
                'invoiceType' => $stingParser->getInvoiceType()
            ];
        }


        //Parse attached files
        if (!empty($files['files'])) {
            foreach ($files['files'] as $file) {

                if (empty($file->getName())) {
                    continue;
                }

                //Parse content
                $stingParser = new \App\Libraries\PurchaseByDocument\Parsers\Sting\Parser();
                $stingParser->execute(file_get_contents($file->getTempName()));

                $fileContent = file_get_contents($file->getTempName());
                $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'Windows-1251');

                //Prepare data
                $items[] = [
                    'type' => 'file',
                    'name' => $file->getName(),
                    'originalContent' => $fileContent,
                    'parsed' => $stingParser->getResult(),
                    'invoiceType' => $stingParser->getInvoiceType()
                ];
            }
        }

        return $items;
    }

    private function validateSting(array &$items)
    {
        $businessesModel = new BusinessesModel();
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();

        foreach ($items as $itemKey => $item) {

            //Check if itemsCount is more than 0 // Общо количество: [n] бр.
            if ($item['parsed']['itemsCount'] === 0) {
                $items[$itemKey]['error'] = 'Общо количество: е 0';
                continue;
            }

            //Check recipient text
            if (empty($item['parsed']['recipient']['result']['name'])) {
                $items[$itemKey]['error'] = 'Не е намерен получател';
                continue;
            }

            //Search for recipient number
            $recipientNumber = $item['parsed']['recipient']['result']['idNumber'];
            if (empty($recipientNumber)) {
                $items[$itemKey]['error'] = 'Не е намерен ИН Номер на получател';
                continue;
            }

            //Search for business by recipient number
            $business = $businessesModel->where('in_number', $recipientNumber)->first();
            if (!$business) {
                $items[$itemKey]['error'] = 'Не е намерен бизнес с ИН Номер: ' . $recipientNumber;
                continue;
            }
            $items[$itemKey]['business'] = $business;

            //Search for at least one (not empty) item from invoiceItems['results']
            $validInvoiceItems = 0;
            foreach ($item['parsed']['invoiceItems']['result'] as $invoiceItem) {
                if (!empty($invoiceItem['designation'])) {
                    $validInvoiceItems++;
                }
            }
            if ($validInvoiceItems === 0) {
                $items[$itemKey]['error'] = 'Не е намерен нито един артикул';
                continue;
            }


            //Validate invoice number
            if (empty($item['parsed']['invoiceInfo']['result']['number'])) {
                $items[$itemKey]['error'] = 'Не е намерен номер на фактура';
                continue;
            }

            //Validate if this invoice number is already added
            $invoiceNumber = $item['parsed']['invoiceInfo']['result']['number'];
            $invoiceExists = $purchaseByDocumentDataModel->where('invoice_number', $invoiceNumber)->first();
            if ($invoiceExists) {
                $items[$itemKey]['error'] = 'Фактура с номер: ' . $invoiceNumber . ' вече е добавена';
                continue;
            }


            $items[$itemKey]['success'] = true;
        }
    }




    private function parseFioniksFarma(): array
    {
        $items = [];
        $files = $this->request->getFiles();
        $texts = $this->request->getPost('texts') ?? [];


        //Parse attached texts
        foreach ($texts as $textElementKey => $text) {

            //Parse content
            $fioniksFarmaParser = new \App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Parser();
            $fioniksFarmaParser->execute($text);

            //Prepare data
            $items[] = [
                'type' => 'text',
                'name' => "Текст " . ($textElementKey + 1),
                'originalContent' => $text,
                'parsed' => $fioniksFarmaParser->getResult(),
                'invoiceType' => $fioniksFarmaParser->getInvoiceType()
            ];
        }


        //Parse attached files
        if (!empty($files['files'])) {
            foreach ($files['files'] as $file) {

                if (empty($file->getName())) {
                    continue;
                }

                //Parse content
                $fioniksFarmaParser = new \App\Libraries\PurchaseByDocument\Parsers\FioniksFarma\Parser();
                $fioniksFarmaParser->execute(file_get_contents($file->getTempName()));

                //Prepare data
                $items[] = [
                    'type' => 'file',
                    'name' => $file->getName(),
                    'originalContent' => file_get_contents($file->getTempName()),
                    'parsed' => $fioniksFarmaParser->getResult(),
                    'invoiceType' => $fioniksFarmaParser->getInvoiceType()
                ];
            }
        }

        return $items;
    }

    private function validateFioniksFarma(array &$items)
    {
        $businessesModel = new BusinessesModel();
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();

        foreach ($items as $itemKey => $item) {

            //Check if itemsCount is more than 0 // Общо количество: [n] бр.
            if ($item['parsed']['itemsCount'] === 0) {
                $items[$itemKey]['error'] = 'Общо количество: е 0';
                continue;
            }

            //Check recipient text
            if (empty($item['parsed']['recipient']['result']['name'])) {
                $items[$itemKey]['error'] = 'Не е намерен получател';
                continue;
            }

            //Search for recipient number
            $recipientNumber = $item['parsed']['recipient']['result']['idNumber'];
            if (empty($recipientNumber)) {
                $items[$itemKey]['error'] = 'Не е намерен ИН Номер на получател';
                continue;
            }

            //Search for business by recipient number
            $business = $businessesModel->where('in_number', $recipientNumber)->first();
            if (!$business) {
                $items[$itemKey]['error'] = 'Не е намерен бизнес с ИН Номер: ' . $recipientNumber;
                continue;
            }
            $items[$itemKey]['business'] = $business;

            //Search for at least one (not empty) item from invoiceItems['results']
            $validInvoiceItems = 0;
            foreach ($item['parsed']['invoiceItems']['result'] as $invoiceItem) {
                if (!empty($invoiceItem['designation'])) {
                    $validInvoiceItems++;
                }
            }
            if ($validInvoiceItems === 0) {
                $items[$itemKey]['error'] = 'Не е намерен нито един артикул';
                continue;
            }


            //Validate invoice number
            if (empty($item['parsed']['invoiceInfo']['result']['number'])) {
                $items[$itemKey]['error'] = 'Не е намерен номер на фактура';
                continue;
            }

            //Validate if this invoice number is already added
            $invoiceNumber = $item['parsed']['invoiceInfo']['result']['number'];
            $invoiceExists = $purchaseByDocumentDataModel->where('invoice_number', $invoiceNumber)->first();
            if ($invoiceExists) {
                $items[$itemKey]['error'] = 'Фактура с номер: ' . $invoiceNumber . ' вече е добавена';
                continue;
            }

            //Validate total price
            if ($item['invoiceType'] === 1) {
                $totalPriceFromItems = 0;
                foreach ($item['parsed']['invoiceItems']['result'] as $invoiceItem) {
                    $totalPriceFromItems += $invoiceItem['value'];
                }
                $totalPriceFromItems = NumberFormat::formatPrice($totalPriceFromItems);
                $totalPriceFromInvoice = $item['parsed']['invoicePrice']['result']['totalPrice'];
                if ($totalPriceFromItems != $totalPriceFromInvoice) {
                    $items[$itemKey]['error'] = "Сумата от елементите: {$totalPriceFromItems} е различна от сумата от фактурата: {$totalPriceFromInvoice}";
                    continue;
                }
            } elseif ($item['invoiceType'] === 2) {
                $totalPriceFromItems = 0;
                foreach ($item['parsed']['invoiceItems']['result'] as $invoiceItem) {
                    $totalPriceFromItems += $invoiceItem['priceWithVAT'];
                }
                $totalPriceFromItems = NumberFormat::formatPrice($totalPriceFromItems);
                $totalPriceFromInvoice = $item['parsed']['invoicePrice']['result']['totalPriceWithTax'];
                if ($totalPriceFromItems != $totalPriceFromInvoice) {
                    $items[$itemKey]['error'] = "Сумата от елементите: {$totalPriceFromItems} е различна от сумата от фактурата: {$totalPriceFromInvoice}";
                    continue;
                }
            }


            //Validate price discount
            if ($item['invoiceType'] === 1) {
                $sum1ToValidate = $item['parsed']['invoicePrice']['result']['totalPrice'] - $item['parsed']['invoicePrice']['result']['tradeDiscount'];
                $sum2ToValidate = $item['parsed']['invoicePrice']['result']['taxBase9'] + $item['parsed']['invoicePrice']['result']['taxBase20'] + $item['parsed']['invoicePrice']['result']['taxBase0'];

                $sum1ToValidate = NumberFormat::formatPrice($sum1ToValidate);
                $sum2ToValidate = NumberFormat::formatPrice($sum2ToValidate);

                if ($sum1ToValidate != $sum2ToValidate) {
                    $items[$itemKey]['error'] = "Общата стойност (без търговската отстъпка): {$sum1ToValidate} е различна от сбора на данъчните основи: {$sum2ToValidate}";
                    continue;
                }
            }

            $items[$itemKey]['success'] = true;
        }
    }
}