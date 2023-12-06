<?php

namespace App\Controllers\PurchaseByDocument;

use App\Controllers\BaseController;
use App\Models\BusinessesModel;
use App\Models\PBDFioniksFarmaDeliveryModel;
use App\Models\PBDFioniksFarmaInvoiceItemsModel;
use App\Models\PBDFioniksFarmaInvoicePaymentModel;
use App\Models\PBDFioniksFarmaInvoicePriceModel;
use App\Models\PBDFioniksFarmaRecipientModel;
use App\Models\PBDFioniksFarmaSupplierModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\ProvidersModel;
use App\Models\PurchaseByDocumentDataModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

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


    public function submit(): RedirectResponse
    {
        $parsedData = [];
        $providerId = (int)$this->request->getPost('providers');


        if ($providerId === 1) {
            //$parsedData = $this->parseSting();
        } elseif ($providerId === 2) {
            $parsedData = $this->parseFioniksFarma();
            $this->validateFioniksFarma($parsedData);
        } elseif ($providerId === 3) {
            //$parsedData = $this->parseAster();
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


        $this->session->set('PbParsedDataProvider', $providerId);
        $this->session->set('PbParsedData', $parsedData);
        $this->session->set('PbParsedDataStatistics', $parsedDataStatistics);

        return redirect()->to('/purchase-by-document/submit-preview');
    }

    public function submit_preview(): string
    {
        $this->viewData['assets']['js'] = 'PurchaseByDocument/submit-preview.js';

        $this->viewData['parsedData'] = $this->session->get('PbParsedData');
        $this->viewData['parsedDataStatistics'] = $this->session->get('PbParsedDataStatistics');
        $provider = $this->session->get('PbParsedDataProvider');

        if ($provider === 1) {
            //return view('VatPurchaseJournals/SubmitPreview/Sting', $this->viewData);
        } elseif ($provider === 2) {
            return view('PurchaseByDocument/SubmitPreview/FioniksFarma', $this->viewData);
        } elseif ($provider === 3) {
            //return view('VatPurchaseJournals/SubmitPreview/Aster', $this->viewData);
        }

        die('Invalid provider !');
    }


    public function finish(): RedirectResponse
    {
        $provider = $this->session->get('PbParsedDataProvider');

        if ($provider === 1) {
            //$this->finishSting();
        } elseif ($provider === 2) {
            $this->finishFioniksFarma();
        } elseif ($provider === 3) {
            //$this->finishAster();
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
                    'trade_markup' => $invoiceItem['tradeMarkup'],
                    'trade_discount' => $invoiceItem['tradeDiscount'],
                    'wholesaler_price' => $invoiceItem['wholesalerPrice'],
                    'value' => $invoiceItem['value'],
                    'price_with_vat' => $invoiceItem['priceWithVAT'],
                    'batch' => $invoiceItem['batch'],
                    'certificate' => $invoiceItem['certificate'],
                    'expiry_date' => $invoiceItem['expiryDate'],
                    'pharmacy_price' => $invoiceItem['pharmacyPrice'],
                    'limit_price' => $invoiceItem['limitPrice'],
                    'limit_price_type' => $invoiceItem['limitPriceType'],
                    'inn' => $invoiceItem['INN']
                ];
                $pbdFioniksFarmaInvoiceItemsModel->insert($invoiceItemData);
            }
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
                'parsed' => $fioniksFarmaParser->getResult()
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
                    'parsed' => $fioniksFarmaParser->getResult()
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

            $items[$itemKey]['success'] = true;
        }
    }
}