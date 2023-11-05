<?php

namespace App\Controllers\VatPurchaseJournals;

use App\Controllers\BaseController;
use App\Controllers\Partners;
use App\Models\BusinessesModel;
use App\Models\VatPurchaseJournalsModel;
use App\Models\VPJAsterEntitiesModel;
use App\Models\VPJFioniksFarmaEntitiesModel;
use App\Models\VPJStingEntitiesModel;
use Config\Services;

class ExportAster extends BaseController
{
    private array $viewData = [];

    private \CodeIgniter\Session\Session $session;
    private array $businesses;

    public function __construct()
    {
        $this->session = Services::session();
    }


    /**
     * @throws \Exception
     */
    public function view(): string
    {
        $this->viewData['assets']['js'] = 'VatPurchaseJournals/export-aster-view.js';

        $date = $this->request->getGet('date') ?? date('Y-m');
        $data = $this->generateData($date);

        //dd($data);

        $this->viewData['date'] = $date;
        $this->viewData['data'] = $data;

        $businessesModel = new BusinessesModel();
        $businesses = $businessesModel->findAll();
        $this->viewData['businesses'] = $businesses;

        return view('VatPurchaseJournals/export-aster-view', $this->viewData);
    }


    /**
     * @throws \Exception
     */
    public function export()
    {
        $date = $this->request->getGet('date') ?? date('Y-m');
        $data = $this->generateData($date, true);

        //Get business details
        $businessModel = new BusinessesModel();
        $fileName = "{$date} - Дневник на продажбите на Астер Русе-vpj.xlsx";

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(ROOTPATH . 'public/templates/aster-vat-purchase-journals-template.xlsx');
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
    private function generateData(string $date, bool $forExport = false): array
    {
        $data = [];

        $businessModel = new BusinessesModel();

        $dateFormat = $forExport ? 'd/m/Y' : 'd-m-Y';

        //get last day of the month for param $date
        $last = new \DateTime($date);
        $last->modify('last day of this month');
        $dateLastDayOfTheMonth = $last->format('Y-m-d');


        //get first day of the month for param $date
        $first = new \DateTime($date);
        $first->modify('first day of this month');
        $dateFirstDayOfTheMonth = $first->format('Y-m-d');


        $vpjAsterEntitiesModel = new VPJAsterEntitiesModel();
        $vpjAsterEntities = $vpjAsterEntitiesModel
            ->where('export_date', $date)
            ->where('status', 'success')
            ->orderBy('invoice_date', 'asc')
            ->orderBy('invoice', 'asc')
            ->findAll();

        //Get all active businesses
        $this->businesses = $businessModel->where('active', 1)->findAll();

        foreach ($vpjAsterEntities as $vpjAsterEntity) {

            $h_doc_type = '901';
            if ($vpjAsterEntity['total_price_inc_vat'] < 0) {
                $h_doc_type = 904;
            }

            //create date from format m/d/Y
            $h_doc_date = date('Y-m-d', strtotime($vpjAsterEntity['invoice_date']));
            if ($h_doc_date < $dateFirstDayOfTheMonth) {
                $h_doc_date = $dateLastDayOfTheMonth;
            }

            //get business details
            $businessDetails = $this->searchForBusiness($vpjAsterEntity['business_id']);
            if(!$businessDetails){
                continue;
            }

            $entity = [
                'h_doc_type' => $h_doc_type,
                'h_doc_no' => $this->fixDocNo((string)$vpjAsterEntity['invoice']),
                'h_doc_date' => date($dateFormat, strtotime($h_doc_date)),
                'h_doc_valeur' => date($dateFormat, strtotime($dateLastDayOfTheMonth)),
                'd_net_value' => number_format($vpjAsterEntity['price_without_vat'], 2, '.', ''),
                'd_vat_value' => number_format($vpjAsterEntity['price_vat'], 2, '.', ''),
                'd_partner_type' => 13,
                'd_partner' => $businessDetails['code'],
                'd_partner_id' => $businessDetails['in_number'],
                'd_partner_name' => $businessDetails['name'],
                'd_$12' => $this->fixDocNo((string)$vpjAsterEntity['invoice']),
                'h_12' => date($dateFormat, strtotime($h_doc_date)),
                'd_$18' => '02',
                'd_777' => '001',
            ];

            $data[] = $entity;
        }


        return $data;
    }

    private function searchForBusiness(int $businessId): array
    {
        foreach ($this->businesses as $business) {
            if ((int)$business['id'] === $businessId) {
                return $business;
            }
        }

        return [];
    }


    private function fixDocNo(string $docNo = ''): string
    {
        $fixedDocNo = $docNo;
        if (strlen($docNo) < 10) {
            $fixedDocNo = str_pad($docNo, 10, '0', STR_PAD_LEFT);
        }

        return $fixedDocNo;
    }
}