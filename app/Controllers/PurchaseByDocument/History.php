<?php

namespace App\Controllers\PurchaseByDocument;

use App\Controllers\BaseController;
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
            $pbdStingRecipientModel = new PBDStingRecipientModel();
            $pbdStingSupplierModel = new PBDStingSupplierModel();
            $pbdStingDeliveryModel = new PBDStingDeliveryModel();
            $pbdStingInvoicePriceyModel = new PBDStingInvoicePriceModel();
            $pbdStingInvoicePaymentModel = new PBDStingInvoicePaymentModel();
            $pbdStingInvoiceItemsModel = new PBDStingInvoiceItemsModel();

            $data = [
                'data' => $pbdDetails,
                'recipient' => $pbdStingRecipientModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'supplier' => $pbdStingSupplierModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'delivery' => $pbdStingDeliveryModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'invoice_price' => $pbdStingInvoicePriceyModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'invoice_payment' => $pbdStingInvoicePaymentModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'invoice_items' => $pbdStingInvoiceItemsModel->where('purchase_by_document_id', $pbdId)->findAll(),
            ];
            //print_r2($data,1);
            $this->viewData['data'] = $data;

            return view('PurchaseByDocument/History/Sting/View', $this->viewData);
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

            return view('PurchaseByDocument/History/FioniksFarma/View', $this->viewData);
        } elseif((int)$pbdDetails['provider_id'] === 3){
            $pbdAsterRecipientModel = new PBDAsterRecipientModel();
            $pbdAsterInvoiceItemsModel = new PBDAsterInvoiceItemsModel();

            $data = [
                'data' => $pbdDetails,
                'recipient' => $pbdAsterRecipientModel->where('purchase_by_document_id', $pbdId)->find()[0],
                'invoice_items' => $pbdAsterInvoiceItemsModel->where('purchase_by_document_id', $pbdId)->findAll(),
            ];
            $this->viewData['data'] = $data;

            return view('PurchaseByDocument/History/Aster/View', $this->viewData);
        }

        die('Invalid provider!');
    }


    public function delete(int $pbdId): \CodeIgniter\HTTP\RedirectResponse
    {

        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdDetails = $purchaseByDocumentDataModel->find($pbdId);

        if((int)$pbdDetails['provider_id'] === 1){

            $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
            $pbdStingRecipientModel = new PBDStingRecipientModel();
            $pbdStingSupplierModel = new PBDStingSupplierModel();
            $pbdStingDeliveryModel = new PBDStingDeliveryModel();
            $pbdStingInvoicePriceyModel = new PBDStingInvoicePriceModel();
            $pbdStingInvoicePaymentModel = new PBDStingInvoicePaymentModel();
            $pbdStingInvoiceItemsModel = new PBDStingInvoiceItemsModel();

            $pbdStingRecipientModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdStingSupplierModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdStingDeliveryModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdStingInvoicePriceyModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdStingInvoicePaymentModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdStingInvoiceItemsModel->where('purchase_by_document_id', $pbdId)->delete();

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
            $pbdAsterRecipientModel = new PBDAsterRecipientModel();
            $pbdAsterInvoiceItemsModel = new PBDAsterInvoiceItemsModel();

            $pbdAsterRecipientModel->where('purchase_by_document_id', $pbdId)->delete();
            $pbdAsterInvoiceItemsModel->where('purchase_by_document_id', $pbdId)->delete();
        }

        $purchaseByDocumentDataModel->delete($pbdId);

        return redirect()->to('/purchase-by-document//history');
    }

    public function download(int $pbdId)
    {
        $purchaseByDocumentDataModel = new PurchaseByDocumentDataModel();
        $pbdDetails = $purchaseByDocumentDataModel->find($pbdId);

        // Set headers for file download
        if((int)$pbdDetails['provider_id'] === 1){

            //in $pbdDetails['source_content'] find meta tag with meta http-equiv="Content-Type" and remove it
            $pbdDetails['source_content'] = preg_replace('/<meta http-equiv="Content-Type" content="text\/html; charset=Windows-1251">/', '', $pbdDetails['source_content']);

            header('Content-Type: text/html; charset=Windows-1251');
            header('Content-Disposition: attachment; filename="'.$pbdDetails['invoice_number'].'.html"');
            //set header encoding to be windows-1251
        } else {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="'.$pbdDetails['invoice_number'].'.txt"');
        }

        // Use output buffering to capture content
        ob_start();
        echo $pbdDetails['source_content'];
        $fileContent = ob_get_clean();

        // Output the file content
        echo $fileContent;
        exit();
    }
}