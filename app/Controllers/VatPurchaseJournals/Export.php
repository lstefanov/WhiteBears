<?php

namespace App\Controllers\VatPurchaseJournals;

use App\Controllers\BaseController;
use App\Models\BusinessesModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJAsterEntitiesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
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
        $selectedBusinessId = $this->request->getGet('business_id') ?? 0;
        $data = $this->generateData($selectedBusinessId, $date);

        //dd($data);

        $this->viewData['date'] = $date;
        $this->viewData['selectedBusinessId'] = $selectedBusinessId;
        $this->viewData['data'] = $data;

        $businessesModel = new BusinessesModel();
        $businesses = $businessesModel->findAll();
        $this->viewData['businesses'] = $businesses;

        return view('VatPurchaseJournals/export-view', $this->viewData);
    }


    /**
     * @throws \Exception
     */
    public function export()
    {
        $date = $this->request->getGet('date') ?? date('Y-m');
        $selectedBusinessId = $this->request->getGet('business_id') ?? 0;
        $data = $this->generateData($selectedBusinessId, $date);

        //Get business details
        $businessModel = new BusinessesModel();
        $business = $businessModel->find($selectedBusinessId);
        $fileName = "{$date}-{$business['name']}-vpj.xlsx";

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(ROOTPATH . 'public/templates/vat-purchase-journals-template.xlsx');
        $sheet = $spreadsheet->getActiveSheet();

        //write data to the template starting at column A row 2
        $sheet->fromArray($data, null, 'A3');


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($fileName);

        return $this->response->download($fileName, null, true);
    }


    /**
     * @throws \Exception
     */
    private function generateData(int $selectedBusinessId, string $date): array
    {
        $data = [];

        if ($selectedBusinessId === 0) {
            return $data;
        }

        $businessModel = new BusinessesModel();

        //get last day of the month for param $date
        $last = new \DateTime($date);
        $last->modify('last day of this month');
        $dateLastDayOfTheMonth = $last->format('Y-m-d');


        //get first day of the month for param $date
        $first = new \DateTime($date);
        $first->modify('first day of this month');
        $dateFirstDayOfTheMonth = $first->format('Y-m-d');

        $vpjFioniksFarmaEntitiesModel = new VPJFioniksFarmaEntitiesModel();
        $vpjFioniksFarmaEntities = $vpjFioniksFarmaEntitiesModel->where('export_date', $date)->where('status',
            'success')->where('business_id', $selectedBusinessId
        )->orderBy('invoice_date', 'asc')->findAll();

        foreach ($vpjFioniksFarmaEntities as $vpjFioniksFarmaEntity) {
            $h_doc_type = '901';
            if (strpos(mb_strtolower($vpjFioniksFarmaEntity['invoice_type']), 'корекционна') !== false) {
                $h_doc_type = 904;
            }


            $entity = [
                'h_doc_type' => $h_doc_type,
                'h_doc_no' => $vpjFioniksFarmaEntity['invoice'],
                'h_doc_date' => date('d-m-Y', strtotime($vpjFioniksFarmaEntity['invoice_date'])),
                'h_doc_valeur' => date('d-m-Y', strtotime($dateLastDayOfTheMonth)),
                'd_net_value' => number_format(($vpjFioniksFarmaEntity['payment_summary'] * 100 / 120), 2, '.', ''),
                'd_vat_value' => number_format(($vpjFioniksFarmaEntity['payment_summary'] * 20 / 120), 2, '.', ''),
                'd_partner_type' => 11,
                'd_partner' => 502,
                'd_23' => 305,
                'd_24' => $vpjFioniksFarmaEntity['business_name'],
                'd_$18' => mb_strtolower($vpjFioniksFarmaEntity['payment_type']) === 'банка' ? '02' : '01',
            ];

            $data[] = $entity;
        }


        $vpjStingEntitiesModel = new VPJStingEntitiesModel();
        $vpjStingEntities = $vpjStingEntitiesModel->where('export_date', $date)->where('status',
            'success')->where('business_id', $selectedBusinessId
        )->orderBy('doc_date', 'asc')->findAll();

        foreach ($vpjStingEntities as $vpjStingEntity) {
            $businessEntity = $businessModel->find($vpjStingEntity['business_id']);

            $h_doc_type = '901';
            if (strpos(mb_strtolower($vpjStingEntity['doc_type']),
                    '3096') !== false || strpos(mb_strtolower($vpjStingEntity['doc_type']), '3090') !== false) {
                $h_doc_type = 904;
            }

            $h_doc_date = str_replace('г.', '', $vpjStingEntity['doc_date']);
            $h_doc_date = date('d-m-Y', strtotime($h_doc_date));

            $entity = [
                'h_doc_type' => $h_doc_type,
                'h_doc_no' => $vpjStingEntity['doc_n'],
                'h_doc_date' => $h_doc_date,
                'h_doc_valeur' => date('d-m-Y', strtotime($dateLastDayOfTheMonth)),
                'd_net_value' => number_format(($vpjStingEntity['payment_summary'] * 100 / 120), 2, '.', ''),
                'd_vat_value' => number_format(($vpjStingEntity['payment_summary'] * 20 / 120), 2, '.', ''),
                'd_partner_type' => 11,
                'd_partner' => 501,
                'd_23' => 405,
                'd_24' => $businessEntity['name'],
                'd_$18' => '02',
            ];

            $data[] = $entity;
        }


        $vpjAsterEntitiesModel = new VPJAsterEntitiesModel();
        $vpjAsterEntities = $vpjAsterEntitiesModel
            ->where('export_date', $date)
            ->where('status', 'success')
            ->where('business_id', $selectedBusinessId)
            ->orderBy('invoice_date', 'asc')
            ->findAll();

        foreach ($vpjAsterEntities as $vpjAsterEntity) {
            $businessEntity = $businessModel->find($vpjAsterEntity['business_id']);

            $h_doc_type = '901';
            if ($vpjAsterEntity['total_price_inc_vat'] < 0) {
                $h_doc_type = 904;
            }

            //create date from format m/d/Y
            $h_doc_date = date('Y-m-d', strtotime($vpjAsterEntity['invoice_date']));
            if($h_doc_date < $dateFirstDayOfTheMonth){
                $h_doc_date = $dateLastDayOfTheMonth;
            }


            $entity = [
                'h_doc_type' => $h_doc_type,
                'h_doc_no' => $vpjAsterEntity['invoice'],
                'h_doc_date' => date('d-m-Y', strtotime($h_doc_date)),
                'h_doc_valeur' => date('d-m-Y', strtotime($dateLastDayOfTheMonth)),
                'd_net_value' => number_format($vpjAsterEntity['price_without_vat'], 2, '.', ''),
                'd_vat_value' => number_format($vpjAsterEntity['price_vat'], 2, '.', ''),
                'd_partner_type' => 13,
                'd_partner' => 500,
                'd_23' => '000',
                'd_24' => $businessEntity['name'],
                'd_$18' => '02',
            ];

            $data[] = $entity;
        }


        return $data;
    }
}