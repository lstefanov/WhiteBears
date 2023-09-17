<?php

namespace App\Controllers\VatPurchaseJournals;

use App\Controllers\BaseController;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use Config\Services;

class Export extends BaseController
{
    private array $viewData = [];

    private \CodeIgniter\Session\Session $session;

    public function __construct()
    {
        $this->session = Services::session();
    }


    /**
     * @throws \Exception
     */
    public function view(): string
    {
        $this->viewData['assets']['js'] = 'VatPurchaseJournals/export-view.js';

        $date = $this->request->getGet('date') ?? date('Y-m');
        $data = $this->generateData($date);

        //dd($data);


        $this->viewData['data'] = $data;

        return view('VatPurchaseJournals/export-view', $this->viewData);
    }


    /**
     * @throws \Exception
     */
    private function generateData(string $date): array
    {
        $data = [];

        //get last day of the month for param $date
        $last = new \DateTime($date);
        $last->modify('last day of this month');
        $dateLastDayOfTheMonth = $last->format('Y-m-d');

        $vpjFioniksFarmaEntitiesModel = new VPJFioniksFarmaEntitiesModel();
        $vpjFioniksFarmaEntities = $vpjFioniksFarmaEntitiesModel->where('export_date', $date)->where('status', 'success')->orderBy('invoice_date', 'asc')->findAll();

        foreach ($vpjFioniksFarmaEntities as $vpjFioniksFarmaEntity) {
            $entity = [
                'h_doc_type' => '901',
                'h_doc_no' => $vpjFioniksFarmaEntity['invoice'],
                'h_doc_date' => date('d/m/Y', strtotime($vpjFioniksFarmaEntity['invoice_date'])),
                'h_doc_valeur' => $dateLastDayOfTheMonth,
                'd_net_value' => number_format( ($vpjFioniksFarmaEntity['payment_summary'] * 100 / 120), 2, '.', ''),
                'd_vat_value' => number_format( ($vpjFioniksFarmaEntity['payment_summary'] * 20 / 120), 2, '.', ''),
                'd_partner_type' => 11,
                'd_partner' => 502,
                'd_23' => 305,
                'd_24' => $vpjFioniksFarmaEntity['business_name'],
                'd_$18' => 2
            ];

            $data[] = $entity;
        }

        return $data;
    }
}