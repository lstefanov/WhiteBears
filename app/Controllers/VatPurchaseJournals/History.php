<?php

namespace App\Controllers\VatPurchaseJournals;

use App\Controllers\BaseController;
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
        $this->viewData['assets']['js'] = 'VatPurchaseJournals/history.js';

        $vatPurchaseJournalsModel = new VatPurchaseJournalsModel();
        $history = $vatPurchaseJournalsModel->getHistory();

        //echo '<pre>'; print_r($history); echo '</pre>'; die();

        $this->viewData['history'] = $history;

        return view('VatPurchaseJournals/history', $this->viewData);
    }

    public function view(int $vpjId): string
    {
        $this->viewData['assets']['js'] = 'VatPurchaseJournals/history-view.js';

        $vatPurchaseJournalsModel = new VatPurchaseJournalsModel();
        $vpJDetails = $vatPurchaseJournalsModel->find($vpjId);

        if((int)$vpJDetails['provider_id'] === 1){
            $this->viewData['vpjDetails'] = $vpJDetails;

            $vpjStingModel = new VPJStingEntitiesModel();
            $this->viewData['vpjStingEntities'] = $vpjStingModel->where('vat_purchase_journals_id', $vpjId)->orderBy('id', 'asc')->findAll();

            return view('VatPurchaseJournals/History/history-view-sting', $this->viewData);
        } elseif((int)$vpJDetails['provider_id'] === 2){
            $this->viewData['vpjDetails'] = $vpJDetails;

            $vpjFioniksFarmaEntitiesModel = new VPJFioniksFarmaEntitiesModel();
            $this->viewData['vpjFioniksFarmaEntities'] = $vpjFioniksFarmaEntitiesModel->where('vat_purchase_journals_id', $vpjId)->orderBy('id', 'asc')->findAll();

            return view('VatPurchaseJournals/History/history-view-fioniks-farma', $this->viewData);
        } elseif((int)$vpJDetails['provider_id'] === 3){
            $this->viewData['vpjDetails'] = $vpJDetails;

            $vpjAsterEntitiesModel = new VPJAsterEntitiesModel();
            $this->viewData['vpjAsterEntitiesModel'] = $vpjAsterEntitiesModel->where('vat_purchase_journals_id', $vpjId)->orderBy('id', 'asc')->findAll();

            return view('VatPurchaseJournals/History/history-view-aster', $this->viewData);
        }

        die('Invalid provider!');
    }

    public function delete(int $vpjId): \CodeIgniter\HTTP\RedirectResponse
    {
        $vatPurchaseJournalsModel = new VatPurchaseJournalsModel();
        $vpJDetails = $vatPurchaseJournalsModel->find($vpjId);

        $uploadedFileDirRootLocation = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR;
        @unlink( $uploadedFileDirRootLocation . $vpJDetails['file_location']);

        if((int)$vpJDetails['provider_id'] === 1){
            $vpjStingEntitiesModel = new VPJStingEntitiesModel();
            $vpjStingEntitiesModel->where('vat_purchase_journals_id', $vpjId)->delete();
        } elseif((int)$vpJDetails['provider_id'] === 2){
            $vpjFioniksFarmaEntitiesModel = new VPJFioniksFarmaEntitiesModel();
            $vpjFioniksFarmaEntitiesModel->where('vat_purchase_journals_id', $vpjId)->delete();
        } elseif((int)$vpJDetails['provider_id'] === 3){
            $vpjAsterEntitiesModel = new VPJAsterEntitiesModel();
            $vpjAsterEntitiesModel->where('vat_purchase_journals_id', $vpjId)->delete();
        }

        $vatPurchaseJournalsModel->delete($vpjId);

        return redirect()->to('/vat-purchase-journals/history');
    }

    public function download(int $vpjId)
    {
        $vatPurchaseJournalsModel = new VatPurchaseJournalsModel();
        $vpJDetails = $vatPurchaseJournalsModel->find($vpjId);

        $uploadedFileDirRootLocation = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR;
        $fileLocation = $uploadedFileDirRootLocation . $vpJDetails['file_location'];

        //Write me headers that will download this file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $vpJDetails['file_name'] . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($fileLocation));
        readfile($fileLocation);
        exit;


    }
}