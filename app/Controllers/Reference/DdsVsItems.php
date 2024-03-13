<?php

namespace App\Controllers\Reference;

use App\Controllers\BaseController;
use App\Models\BusinessesModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\ProvidersModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJAsterEntitiesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
use Config\Database;
use Config\Services;

class DdsVsItems extends BaseController
{
    private \CodeIgniter\Database\BaseConnection $db;

    private array $viewData = [];

    private \CodeIgniter\Session\Session $session;

    public function __construct()
    {
        $this->session = Services::session();

        $this->db = Database::connect();
    }


    /**
     * @throws \Exception
     */
    public function view(): string
    {
        $this->viewData['assets']['js'] = 'Reference/dds-vs-items-view.js';

        $selectedProviderId = $this->request->getGet('provider_id') ?? 0;
        $selectedBusinessId = $this->request->getGet('business_id') ?? 0;
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('first day of this month'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');
        $invoiceNumber = $this->request->getGet('invoice_number') ?? '';
        $priceFrom = $this->request->getGet('price_from') ?? '';
        $priceTo = $this->request->getGet('price_to') ?? '';
        $selectedDocumentType = $this->request->getGet('document_type') ?? 0;
        $matchStatus = $this->request->getGet('match_status') ?? 0;

        $data = $this->generateData($selectedProviderId, $selectedBusinessId, $dateFrom, $dateTo, $invoiceNumber,
            $priceFrom, $priceTo, $selectedDocumentType, $matchStatus);

        //print_r2($data, 1);

        $this->viewData['dateFrom'] = $dateFrom;
        $this->viewData['dateTo'] = $dateTo;
        $this->viewData['selectedBusinessId'] = $selectedBusinessId;
        $this->viewData['selectedProviderId'] = $selectedProviderId;
        $this->viewData['invoiceNumber'] = $invoiceNumber;
        $this->viewData['priceFrom'] = $priceFrom;
        $this->viewData['priceTo'] = $priceTo;
        $this->viewData['selectedDocumentType'] = $selectedDocumentType;
        $this->viewData['matchStatus'] = $matchStatus;
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

        return view('Reference/dds-vs-items-view', $this->viewData);
    }


    /**
     * @throws \Exception
     */
    private function generateData(
        int $selectedProviderId,
        int $selectedBusinessId,
        string $dateFrom,
        string $dateTo,
        string $invoiceNumber,
        string $priceFrom,
        string $priceTo,
        int $selectedDocumentType,
        int $matchStatus
    ): array {
        $data = [];

        if ($selectedProviderId === 0 || $selectedBusinessId === 0) {
            return $data;
        }


        if ($selectedProviderId === 1) {
            $vpjStingEntitiesModel = new VPJStingEntitiesModel();
            $entitiesQuery = $vpjStingEntitiesModel->where('status', 'success')
                ->where('business_id', $selectedBusinessId)
                ->where('provider_id', $selectedProviderId)
                ->orderBy('doc_date', 'asc');

            if ($dateFrom !== '') {
                $entitiesQuery->where('export_date_full >=', $dateFrom);
            }
            if ($dateTo !== '') {
                $entitiesQuery->where('export_date_full <=', $dateTo);
            }
            if ($invoiceNumber !== '') {
                $entitiesQuery->where('doc_n', $invoiceNumber);
            }
            if ($priceFrom !== '') {
                $entitiesQuery->where('payment_summary >=', $priceFrom);
            }
            if ($priceTo !== '') {
                $entitiesQuery->where('payment_summary <=', $priceTo);
            }

            $entities = $entitiesQuery->findAll();


            foreach ($entities as $entityKey => $entity) {

                if($selectedDocumentType !== 0){
                    $h_doc_type = 901;
                    if (strpos(mb_strtolower($entity['doc_type']),
                            '3096') !== false || strpos(mb_strtolower($entity['doc_type']), '3090') !== false) {
                        $h_doc_type = 904;
                    }

                    if($selectedDocumentType !== $h_doc_type){
                        continue;
                    }
                }

                $entity['doc_n'] = '0' . $entity['doc_n'];

                //Search for purchase_by_document that match invoice id
                $purchaseByDocumentResult = $this->db->query("
                    SELECT id, payment_amount 
                    FROM purchase_by_document WHERE invoice_number = ?",
                    [$entity['doc_n']])
                    ->getResult();

                $status = 3; // default status is no found

                //Change Status to  found
                if(!empty($purchaseByDocumentResult)){
                    $status = 1;

                    $purchaseByDocumentResult[0]->payment_amount = number_format($purchaseByDocumentResult[0]->payment_amount, 2, '.', '');
                    $entity['purchase_by_document_data'] = (array) $purchaseByDocumentResult[0];

                }

                //if founded check if payment amount is the same
                if($status === 1){
                    $paymentAmount = $purchaseByDocumentResult[0]->payment_amount;
                    $entity['payment_summary'] = number_format($entity['payment_summary'], 2, '.', '');

                    if($paymentAmount !== $entity['payment_summary']){
                        $status = 2;
                    }
                }

                $entity['status'] = $status;

                if($matchStatus !== 0 && $status !== $matchStatus){
                    continue;
                }

                $data[] = $entity;
            }

        }

        if ($selectedProviderId === 2) {
            $vpjFioniksFarmaEntitiesModel = new VPJFioniksFarmaEntitiesModel();
            $entitiesQuery = $vpjFioniksFarmaEntitiesModel->where('status', 'success')
                ->where('business_id', $selectedBusinessId)
                ->where('provider_id', $selectedProviderId)
                ->orderBy('invoice_date', 'asc');

            if ($dateFrom !== '') {
                $entitiesQuery->where('invoice_date >=', $dateFrom);
            }
            if ($dateTo !== '') {
                $entitiesQuery->where('invoice_date <=', $dateTo);
            }
            if ($invoiceNumber !== '') {
                $entitiesQuery->where('invoice', $invoiceNumber);
            }
            if ($priceFrom !== '') {
                $entitiesQuery->where('payment_summary >=', $priceFrom);
            }
            if ($priceTo !== '') {
                $entitiesQuery->where('payment_summary <=', $priceTo);
            }

            $entities = $entitiesQuery->findAll();

            foreach ($entities as $entityKey => $entity) {

                if($selectedDocumentType !== 0){
                    $h_doc_type = 901;
                    if (strpos(mb_strtolower($entity['invoice_type']), 'корекционна') !== false ||
                        strpos(mb_strtolower($entity['invoice_type']), 'финансов рабат') !== false ||
                        strpos(mb_strtolower($entity['invoice_type']), 'цен.разлики') !== false) {
                        $h_doc_type = 904;
                    }

                    if($selectedDocumentType !== $h_doc_type){
                        continue;
                    }
                }

                //Fix for UI view
                $entity['doc_n'] = $entity['invoice'];
                $entity['export_date_full'] = $entity['invoice_date'];

                //Search for purchase_by_document that match invoice id
                $purchaseByDocumentResult = $this->db->query("
                    SELECT id, payment_amount 
                    FROM purchase_by_document WHERE invoice_number = ?",
                    [$entity['invoice']])
                    ->getResult();

                $status = 3; // default status is no found

                //Change Status to  found
                if(!empty($purchaseByDocumentResult)){
                    $status = 1;

                    $purchaseByDocumentResult[0]->payment_amount = number_format($purchaseByDocumentResult[0]->payment_amount, 2, '.', '');
                    $entity['purchase_by_document_data'] = (array) $purchaseByDocumentResult[0];
                }

                //if founded check if payment amount is the same
                if($status === 1){
                    $paymentAmount = $purchaseByDocumentResult[0]->payment_amount;
                    $entity['payment_summary'] = number_format($entity['payment_summary'], 2, '.', '');
                    if($paymentAmount !== $entity['payment_summary']){
                        $status = 2;
                    } else {
                        $purchaseByDocumentInvoicePriceResult = $this->db->query("
                            SELECT id
                            FROM pbd_fioniks_farma_invoice_price WHERE purchase_by_document_id = ? AND (tax_base_9 > 0 OR tax_base_0 > 0)",
                            [$purchaseByDocumentResult[0]->id])
                            ->getRow();
                        if($purchaseByDocumentInvoicePriceResult){
                            $status = 4;
                        }
                    }
                }

                $entity['status'] = $status;

                if( ($matchStatus !== 0 && $status !== $matchStatus) || ($matchStatus === 1 AND !in_array($status, [1,4])) ){
                    continue;
                }

                $data[] = $entity;
            }
        }



        if ($selectedProviderId === 3) {
            $vpjAsterEntitiesModel = new VPJAsterEntitiesModel();
            $entitiesQuery = $vpjAsterEntitiesModel->where('status', 'success')
                ->where('business_id', $selectedBusinessId)
                ->where('provider_id', $selectedProviderId)
                ->orderBy('invoice_date', 'asc');

            if ($dateFrom !== '') {
                $entitiesQuery->where('export_date_full >=', $dateFrom);
            }
            if ($dateTo !== '') {
                $entitiesQuery->where('export_date_full <=', $dateTo);
            }
            if ($invoiceNumber !== '') {
                $entitiesQuery->where('invoice', $invoiceNumber);
            }
            if ($priceFrom !== '') {
                $entitiesQuery->where('payment_summary >=', $priceFrom);
            }
            if ($priceTo !== '') {
                $entitiesQuery->where('payment_summary <=', $priceTo);
            }

            $entities = $entitiesQuery->findAll();

            foreach ($entities as $entityKey => $entity) {

                if($selectedDocumentType !== 0){

                    $h_doc_type = '901';
                    if ($entity['total_price_inc_vat'] < 0) {
                        $h_doc_type = 904;
                    }

                    if($selectedDocumentType !== $h_doc_type){
                        continue;
                    }
                }

                //Fix for UI view
                $entity['doc_n'] = $entity['invoice'];
                $entity['export_date_full'] = $entity['invoice_date'];
                $entity['payment_summary'] = $entity['price_without_vat'];

                //Search for purchase_by_document that match invoice id
                $purchaseByDocumentResult = $this->db->query("
                    SELECT id, payment_amount 
                    FROM purchase_by_document WHERE invoice_number = ?",
                    [$entity['invoice']])
                    ->getResult();

                $status = 3; // default status is no found

                //Change Status to  found
                if(!empty($purchaseByDocumentResult)){
                    $status = 1;

                    $entity['purchase_by_document_data'] = (array) $purchaseByDocumentResult[0];
                }

                //if founded check if payment amount is the same
                if($status === 1){
                    $paymentAmount = $purchaseByDocumentResult[0]->payment_amount;
                    if($paymentAmount !== $entity['price_without_vat']){
                        if($paymentAmount - $entity['price_without_vat'] >= 0.06 || $paymentAmount - $entity['price_without_vat'] <= -0.06){
                            $status = 2;
                        }
                    }
                }

                $entity['status'] = $status;

                if($matchStatus !== 0 && $status !== $matchStatus){
                    continue;
                }

                $data[] = $entity;
            }
        }


        return $data;
    }
}