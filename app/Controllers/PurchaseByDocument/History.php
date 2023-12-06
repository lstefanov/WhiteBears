<?php

namespace App\Controllers\PurchaseByDocument;

use App\Controllers\BaseController;
use App\Models\PBDFioniksFarmaDeliveryModel;
use App\Models\PBDFioniksFarmaInvoiceItemsModel;
use App\Models\PBDFioniksFarmaInvoicePaymentModel;
use App\Models\PBDFioniksFarmaInvoicePriceModel;
use App\Models\PBDFioniksFarmaRecipientModel;
use App\Models\PBDFioniksFarmaSupplierModel;
use App\Models\PurchaseByDocumentDataModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJAsterEntitiesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
use Config\Services;

class History extends BaseController
{
    private array $viewData = [];

    private \CodeIgniter\Session\Session $session;

    public function __construct()
    {
        $this->session = Services::session();
    }

    public function index(): string
    {
        $this->viewData['assets']['js'] = 'PurchaseByDocument/history.js';

        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $history = $purchaseByDocumentDataModel->getHistory();

        $this->viewData['history'] = $history;

        return view('PurchaseByDocument/history', $this->viewData);
    }

    public function view(int $pbdId): string
    {
        $this->viewData['assets']['js'] = 'PurchaseByDocument/history-view.js';

        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdDetails = $purchaseByDocumentDataModel->find($pbdId);

        if((int)$pbdDetails['provider_id'] === 1){

        } elseif((int)$pbdDetails['provider_id'] === 2){
            $pbdFioniksFarmaRecipientModel = new PBDFioniksFarmaRecipientModel();
            $pbdFioniksFarmaSupplierModel = new PBDFioniksFarmaSupplierModel();
            $pbdFioniksFarmaDeliveryModel = new PBDFioniksFarmaDeliveryModel();
            $pbdFioniksFarmaInvoicePriceyModel = new PBDFioniksFarmaInvoicePriceModel();
            $pbdFioniksFarmaInvoicePaymentModel = new PBDFioniksFarmaInvoicePaymentModel();
            $pbdFioniksFarmaInvoiceItemsModel = new PBDFioniksFarmaInvoiceItemsModel();

            $data = [
                'data' => $pbdDetails,
                'recipient' => $pbdFioniksFarmaRecipientModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'supplier' => $pbdFioniksFarmaSupplierModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'delivery' => $pbdFioniksFarmaDeliveryModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'invoice_price' => $pbdFioniksFarmaInvoicePriceyModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'invoice_payment' => $pbdFioniksFarmaInvoicePaymentModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'invoice_items' => $pbdFioniksFarmaInvoiceItemsModel->where('purchase_by_document_id', $pbdId)->findAll(),
            ];
            $this->viewData['data'] = $data;

            return view('PurchaseByDocument/History/history-view-fioniks-farma', $this->viewData);
        } elseif((int)$pbdDetails['provider_id'] === 3){

        }

        die('Invalid provider!');
    }


    public function delete(int $pbdId): \CodeIgniter\HTTP\RedirectResponse
    {
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdDetails = $purchaseByDocumentDataModel->find($pbdId);

        if((int)$pbdDetails['provider_id'] === 1){

        } elseif((int)$pbdDetails['provider_id'] === 2){
            $pbdFioniksFarmaRecipientModel = new PBDFioniksFarmaRecipientModel();
            $pbdFioniksFarmaSupplierModel = new PBDFioniksFarmaSupplierModel();
            $pbdFioniksFarmaDeliveryModel = new PBDFioniksFarmaDeliveryModel();
            $pbdFioniksFarmaInvoicePriceyModel = new PBDFioniksFarmaInvoicePriceModel();
            $pbdFioniksFarmaInvoicePaymentModel = new PBDFioniksFarmaInvoicePaymentModel();
            $pbdFioniksFarmaInvoiceItemsModel = new PBDFioniksFarmaInvoiceItemsModel();

            $pbdFioniksFarmaRecipientModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdFioniksFarmaSupplierModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdFioniksFarmaDeliveryModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdFioniksFarmaInvoicePriceyModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdFioniksFarmaInvoicePaymentModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdFioniksFarmaInvoiceItemsModel->where('purchase_by_document_id', $pbdId)->delete();
        } elseif((int)$pbdDetails['provider_id'] === 3){

        }

        $purchaseByDocumentDataModel->delete($pbdId);

        return redirect()->to('/purchase-by-document//history');
    }

    public function download(int $pbdId)
    {
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdDetails = $purchaseByDocumentDataModel->find($pbdId);

        // Set headers for file download
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="'.$pbdDetails['invoice_number'].'.txt"');

        // Use output buffering to capture content
        ob_start();
        echo $pbdDetails['source_content'];
        $fileContent = ob_get_clean();

        // Output the file content
        echo $fileContent;
        exit();
    }
}