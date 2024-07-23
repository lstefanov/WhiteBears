<?php

namespace App\Controllers\Fixes;

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
use Config\Database;
use DateTime;

class PurchaseByDocumentPrices extends BaseController
{
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct() {
        // Create a new connection
        $this->db = Database::connect();
    }


    public function export_to_files()
    {
        //get all providers
        $providers = $this->db->query("SELECT id, name FROM providers")->getResult();

        //get all business
        $businesses = $this->db->query("SELECT id, name FROM businesses")->getResult();

        //get invoices with payment === 0 -> this mean that they are inserted incorrectly
        $results = $this->db->query("SELECT id, provider_id, business_id, invoice_number, source_content FROM purchase_by_document WHERE payment_amount = 0")->getResult();

        $mainFolder = WRITEPATH . 'tmp/export_to_files/';
        foreach($results as $counter => $result){

            //check for main provider folder
            $providerFolder = $mainFolder . $this->getProviderNameById($result->provider_id, $providers) . '/';
            if(!is_dir($providerFolder)){
                mkdir($providerFolder, 0777, true);
            }

            //Check for business folder
            $businessFolder = $providerFolder . $this->getBusinessNameById($result->business_id, $businesses) . '/';
            if(!is_dir($businessFolder)){
                mkdir($businessFolder, 0777, true);
            }

            if((int) $result->provider_id === 1){
                $encodedContent = mb_convert_encoding($result->source_content, 'Windows-1251');
                file_put_contents($businessFolder . $result->invoice_number . '.html', $encodedContent);
            } else {
                file_put_contents($businessFolder . $result->invoice_number . '.txt', $result->source_content);
            }

        }

        echo 'done';
        exit();
    }



    public function delete_invalids()
    {
        //get all providers
        $providers = $this->db->query("SELECT id, name FROM providers")->getResult();

        //get all business
        $businesses = $this->db->query("SELECT id, name FROM businesses")->getResult();

        //get invoices with payment === 0 -> this mean that they are inserted incorrectly
        $results = $this->db->query("SELECT id, provider_id, business_id, invoice_number FROM purchase_by_document WHERE payment_amount = 0")->getResult();

        foreach($results as $counter => $result){
            $pbdId = $result->id;

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
        }

        echo 'done';
        exit();
    }



    private function getProviderNameById(int $providerId, array $providers): string
    {
        $providerName = '';
        foreach ($providers as $provider) {
            if ((int)$provider->id == (int)$providerId) {
                $providerName = $provider->name;
                break;
            }
        }

        return $providerName;
    }

    private function getBusinessNameById(int $businessId, array $businesses): string
    {
        $businessName = '';
        foreach ($businesses as $business) {
            if ((int)$business->id == (int)$businessId) {
                $businessName = $business->name;
                break;
            }
        }

        return $businessName;
    }
}