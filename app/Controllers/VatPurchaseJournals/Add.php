<?php

namespace App\Controllers\VatPurchaseJournals;

use App\Controllers\BaseController;
use App\Libraries\Parsers\AsterParser;
use App\Libraries\Parsers\FioniksFarmaParser;
use App\Libraries\Parsers\StingParser;
use App\Models\BusinessesModel;
use App\Models\ProvidersBusinessesModel;
use App\Models\ProvidersModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
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
        $this->viewData['assets']['js'] = 'VatPurchaseJournals/add.js';

        $providersModel = new ProvidersModel();
        $this->viewData['providers'] = $providersModel->orderBy('name', 'asc')->findAll();

        $businessesModel = new BusinessesModel();
        $businesses = $businessesModel->orderBy('name', 'asc')->findAll();

        $providersBusinessesModel = new ProvidersBusinessesModel();
        foreach ($businesses as $businessKey => $business) {
            $businesses[$businessKey]['providers'] = $providersBusinessesModel->where('business_id',
                $business['id'])->findAll();
        }

        $this->viewData['businesses'] = $businesses;

        return view('VatPurchaseJournals/add', $this->viewData);
    }


    public function submit(): RedirectResponse
    {
        $parsedData = [];
        $providerId = (int)$this->request->getPost('providers');

        if ($providerId === 1) {
            $parsedData = $this->parseSting();
        } elseif ($providerId === 2) {
            $parsedData = $this->parseFioniksFarma();
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
            if (!empty($parsedDataValue['errors'])) {
                $parsedDataStatistics['errors']++;
            } else {
                $parsedDataStatistics['success']++;
            }
        }
        //echo '<pre>'; print_r($parsedData); echo '</pre>'; die();

        $this->session->set('parsedDataProvider', $providerId);
        $this->session->set('parsedDataStatistics', $parsedDataStatistics);
        $this->session->set('parsedData', $parsedData);

        return redirect()->to('/vat-purchase-journals/submit-preview');
    }

    public function submit_preview(): string
    {
        $this->viewData['assets']['js'] = 'VatPurchaseJournals/submit-preview.js';

        $this->viewData['parsedData'] = $this->session->get('parsedData');
        $this->viewData['parsedDataStatistics'] = $this->session->get('parsedDataStatistics');
        $provider = $this->session->get('parsedDataProvider');

        //echo '<pre>'; print_r($this->viewData['parsedData']); echo '</pre>'; die();

        if ($provider === 1) {
            return view('VatPurchaseJournals/SubmitPreview/Sting', $this->viewData);
        } elseif ($provider === 2) {
            return view('VatPurchaseJournals/SubmitPreview/FioniksFarma', $this->viewData);
        } elseif ($provider === 3) {
            return view('VatPurchaseJournals/SubmitPreview/Aster', $this->viewData);
        }

        die('Invalid provider !');
    }


    /**
     * @throws \ReflectionException
     */
    public function finish(): RedirectResponse
    {
        $provider = $this->session->get('parsedDataProvider');

        if ($provider === 1) {
            $this->finishSting();
        } elseif ($provider === 2) {
            $this->finishFioniksFarma();
        }

        return redirect()->to('/vat-purchase-journals/done');
    }

    public function done(): string
    {
        $this->viewData['parsedDataStatistics'] = $this->session->get('parsedDataStatistics');
        return view('VatPurchaseJournals/done', $this->viewData);
    }

    private function parseFioniksFarma(): array
    {
        $parsedData = [];
        $parsedInvoicesNumbers = [];
        foreach ($_FILES['files']['name'] as $fileKey => $fileName) {

            //@todo check for files errors $_FILES['files']['error'][$fileKey]
            $parser = new FioniksFarmaParser();
            $parsedData[$fileKey]['parsedData'] = $parser->execute($_FILES['files']['tmp_name'][$fileKey], $parsedInvoicesNumbers);
            $parsedData[$fileKey]['errors'] = $parser->errors;
            $parsedData[$fileKey]['entities_statistics'] = $parser->entitiesStatistics;
            $parsedData[$fileKey]['errorType'] = $parser->errorType;
            $parsedData[$fileKey]['fileName'] = $fileName;
            $parsedData[$fileKey]['fileType'] = $_FILES['files']['type'][$fileKey];
            $parsedData[$fileKey]['fileSize'] = $_FILES['files']['size'][$fileKey];

            $parsedData[$fileKey]['uuid'] = uniqid();


            $uploadedFileDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadedFileDir)) {
                mkdir($uploadedFileDir, 0777, true);
            }
            $uploadedFileLocation = $uploadedFileDir . $parsedData[$fileKey]['uuid'] . '.' . pathinfo($fileName,
                    PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['files']['tmp_name'][$fileKey], $uploadedFileLocation);
            $parsedData[$fileKey]['fileTmpName'] = $uploadedFileLocation;
        }

        return $parsedData;
    }


    private function parseAster(): array
    {
        $parsedData = [];
        $parsedInvoicesNumbers = [];
        foreach ($_FILES['files']['name'] as $fileKey => $fileName) {

            //@todo check for files errors $_FILES['files']['error'][$fileKey]
            $parser = new AsterParser();
            $parsedData[$fileKey]['parsedData'] = $parser->execute($_FILES['files']['tmp_name'][$fileKey], $parsedInvoicesNumbers);
            $parsedData[$fileKey]['errors'] = $parser->errors;
            $parsedData[$fileKey]['entities_statistics'] = $parser->entitiesStatistics;
            $parsedData[$fileKey]['errorType'] = $parser->errorType;
            $parsedData[$fileKey]['fileName'] = $fileName;
            $parsedData[$fileKey]['fileType'] = $_FILES['files']['type'][$fileKey];
            $parsedData[$fileKey]['fileSize'] = $_FILES['files']['size'][$fileKey];

            $parsedData[$fileKey]['uuid'] = uniqid();


            $uploadedFileDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadedFileDir)) {
                mkdir($uploadedFileDir, 0777, true);
            }
            $uploadedFileLocation = $uploadedFileDir . $parsedData[$fileKey]['uuid'] . '.' . pathinfo($fileName,
                    PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['files']['tmp_name'][$fileKey], $uploadedFileLocation);
            $parsedData[$fileKey]['fileTmpName'] = $uploadedFileLocation;
        }

        return $parsedData;
    }

    private function parseSting(): array
    {
        $parsedData = [];
        $parsedInvoicesNumbers = [];
        foreach ($_FILES['files']['name'] as $fileKey => $fileName) {

            //@todo check for files errors $_FILES['files']['error'][$fileKey]
            $parser = new StingParser();
            $parsedData[$fileKey]['parsedData'] = $parser->execute($_FILES['files']['tmp_name'][$fileKey], $parsedInvoicesNumbers, $_POST['businesses']);
            $parsedData[$fileKey]['errors'] = $parser->errors;
            $parsedData[$fileKey]['entities_statistics'] = $parser->entitiesStatistics;
            $parsedData[$fileKey]['errorType'] = $parser->errorType;
            $parsedData[$fileKey]['fileName'] = $fileName;
            $parsedData[$fileKey]['fileType'] = $_FILES['files']['type'][$fileKey];
            $parsedData[$fileKey]['fileSize'] = $_FILES['files']['size'][$fileKey];

            $parsedData[$fileKey]['uuid'] = uniqid();


            $uploadedFileDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadedFileDir)) {
                mkdir($uploadedFileDir, 0777, true);
            }
            $uploadedFileLocation = $uploadedFileDir . $parsedData[$fileKey]['uuid'] . '.' . pathinfo($fileName,
                    PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['files']['tmp_name'][$fileKey], $uploadedFileLocation);
            $parsedData[$fileKey]['fileTmpName'] = $uploadedFileLocation;
        }

        return $parsedData;
    }

    /**
     * @throws \ReflectionException
     */
    private function finishFioniksFarma()
    {

        $vatPurchaseJournalsModel = new VatPurchaseJournalsModel();
        $vPJFioniksFarmaEntitiesModel = new VPJFioniksFarmaEntitiesModel();
        $parsedData = $this->session->get('parsedData');

        foreach ($parsedData as $data) {
            if (!empty($data['errors'])) {
                continue;
            }

            //Set uploaded file paths
            $uploadFileName = $data['uuid'] . '.' . pathinfo($data['fileName'], PATHINFO_EXTENSION);
            $uploadedFileDirRootLocation = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR;
            $uploadedFileDirSubLocation = 'vat-purchase-journals' . DIRECTORY_SEPARATOR . date('Y-m') . DIRECTORY_SEPARATOR;

            if (!is_dir($uploadedFileDirRootLocation . $uploadedFileDirSubLocation)) {
                mkdir($uploadedFileDirRootLocation . $uploadedFileDirSubLocation, 0777, true);
            }

            //Move file
            @rename($data['fileTmpName'], $uploadedFileDirRootLocation . $uploadedFileDirSubLocation . $uploadFileName);

            //Create record in invoice table
            $invoiceData = [
                'provider_id' => 2,
                'uuid' => $data['uuid'],
                'file_name' => $data['fileName'],
                'file_type' => $data['fileType'],
                'file_size' => $data['fileSize'],
                'file_location' => $uploadedFileDirSubLocation . $uploadFileName,
                'entities_total' => $data['entities_statistics']['total'],
                'entities_success' => $data['entities_statistics']['success'],
                'entities_error' => $data['entities_statistics']['error'],
                'created_at' => date('Y-m-d H:i:s'),

            ];
            $vatPurchaseJournalsModel->insert($invoiceData);
            $vatPurchaseJournalId = $vatPurchaseJournalsModel->getInsertID();

            foreach ($data['parsedData'] as $parsedDataKey => $parsedDataValue) {
                if ($parsedDataKey === 1) {
                    continue;
                }
                if(!isset($parsedDataValue['business_id'])){
                    dd($parsedDataValue);
                }
                $parsedDataEntity = [
                    'vat_purchase_journals_id' => $vatPurchaseJournalId,
                    'provider_id' => 2,
                    'business_id' => $parsedDataValue['business_id'],
                    'company_id' => $parsedDataValue['company_id'] ?? 0,
                    'status' => $parsedDataValue['status'],
                    'status_details' => implode(',', $parsedDataValue['status_details']),
                    'export_date' => date('Y-m', strtotime($parsedDataValue['F'])),
                    'warehouse' => $parsedDataValue['A'],
                    'business_name' => $parsedDataValue['B'],
                    'client_number' => $parsedDataValue['C'],
                    'company_name' => $parsedDataValue['D'],
                    'invoice' => $parsedDataValue['E'],
                    'invoice_date' => $parsedDataValue['F'],
                    'invoice_type' => $parsedDataValue['G'],
                    'due_date' => $parsedDataValue['H'],
                    'payment_type' => $parsedDataValue['I'],
                    'payment_summary' => $parsedDataValue['J'],
                    'payment_payed' => $parsedDataValue['K'],
                ];
                $vPJFioniksFarmaEntitiesModel->insert($parsedDataEntity);
            }

        }
    }


    private function finishSting()
    {
        $vatPurchaseJournalsModel = new VatPurchaseJournalsModel();
        $vPJStingEntitiesModel = new VPJStingEntitiesModel();
        $parsedData = $this->session->get('parsedData');

        foreach ($parsedData as $data) {
            if (!empty($data['errors'])) {
                continue;
            }

            //Set uploaded file paths
            $uploadFileName = $data['uuid'] . '.' . pathinfo($data['fileName'], PATHINFO_EXTENSION);
            $uploadedFileDirRootLocation = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR;
            $uploadedFileDirSubLocation = 'vat-purchase-journals' . DIRECTORY_SEPARATOR . date('Y-m') . DIRECTORY_SEPARATOR;

            if (!is_dir($uploadedFileDirRootLocation . $uploadedFileDirSubLocation)) {
                mkdir($uploadedFileDirRootLocation . $uploadedFileDirSubLocation, 0777, true);
            }

            //Move file
            @rename($data['fileTmpName'], $uploadedFileDirRootLocation . $uploadedFileDirSubLocation . $uploadFileName);

            //Create record in invoice table
            $invoiceData = [
                'provider_id' => 1,
                'uuid' => $data['uuid'],
                'file_name' => $data['fileName'],
                'file_type' => $data['fileType'],
                'file_size' => $data['fileSize'],
                'file_location' => $uploadedFileDirSubLocation . $uploadFileName,
                'created_at' => date('Y-m-d H:i:s'),

            ];
            $vatPurchaseJournalsModel->insert($invoiceData);
            $vatPurchaseJournalId = $vatPurchaseJournalsModel->getInsertID();

            foreach ($data['parsedData'] as $parsedDataKey => $parsedDataValue) {
                if ($parsedDataKey < 5 || count($data['parsedData']) === $parsedDataKey) {
                    continue;
                }

                $exportDate = str_replace('г.', '', $parsedDataValue['C']);
                $exportDate = date('Y-m', strtotime($exportDate));

                $parsedDataEntity = [
                    'vat_purchase_journals_id' => $vatPurchaseJournalId,
                    'provider_id' => 2,
                    'business_id' => $parsedDataValue['business_id'],
                    'company_id' => $parsedDataValue['company_id'] ?? 0,
                    'status' => $parsedDataValue['status'],
                    'status_details' => implode(',', $parsedDataValue['status_details']),
                    'export_date' => $exportDate,
                    'doc_n' => $parsedDataValue['A'],
                    'doc_type' => $parsedDataValue['B'],
                    'doc_date' => $parsedDataValue['C'],
                    'payment_type' => $parsedDataValue['D'],
                    'payment_summary' => $parsedDataValue['E'],
                ];
                $vPJStingEntitiesModel->insert($parsedDataEntity);
            }

        }
    }
}