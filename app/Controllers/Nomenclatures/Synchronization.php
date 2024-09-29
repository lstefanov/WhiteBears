<?php

namespace App\Controllers\Nomenclatures;

use App\Controllers\BaseController;
use App\Models\NomenclaturesSyncEntitiesModel;
use App\Models\NomenclaturesSyncModel;
use Config\Services;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Synchronization extends BaseController
{
    private array $viewData = [];

    public function __construct()
    {
        $this->session = Services::session();
    }


    /**
     * @throws \Exception
     */
    public function view(): string
    {
        $this->viewData['assets']['js'] = 'Nomenclatures/synchronization-view.js';

        $nomenclaturesSyncModel = new NomenclaturesSyncModel();


        $data = $nomenclaturesSyncModel->orderBy('id', 'DESC')->findAll();

        $this->viewData['data'] = $data;

        return view('Nomenclatures/synchronization-view', $this->viewData);
    }


    public function add(): string
    {
        $this->viewData['assets']['js'] = 'Nomenclatures/synchronization-add.js';

        return view('Nomenclatures/synchronization-add', $this->viewData);
    }


    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function submit(): \CodeIgniter\HTTP\RedirectResponse
    {
        //Add record to main table
        $nomenclaturesSyncModel = new NomenclaturesSyncModel();
        $nomenclaturesSyncModel->insert(['date' => date('Y-m-d')]);
        $syncFileId = $nomenclaturesSyncModel->getInsertID();

        $nomenclaturesSyncEntitiesModel = new NomenclaturesSyncEntitiesModel();

        $file = $this->request->getFiles()['file'];

        //Parse file
        $spreadsheet = IOFactory::load($file->getTempName());

        //parse first sheet
        $sheetData = $spreadsheet->getSheet(0)->toArray(null, true, true, true);

        foreach ($sheetData as $counter => $row) {
            if ($counter === 1) {
                continue;
            }

            $data = [
                'nomenclatures_sync_id' => $syncFileId,
                'code_name' => $row['C'],
                'name' => $row['B']
            ];

            $nomenclaturesSyncEntitiesModel->insert($data);
        }

        return redirect()->to('/nomenclatures/add-sync-file-finalize');
    }

    public function submit_finalize(): string
    {
        $this->viewData['assets']['js'] = 'Nomenclatures/synchronization-finalize.js';

        return view('Nomenclatures/synchronization-finalize', $this->viewData);
    }

    public function delete(int $fileId): \CodeIgniter\HTTP\RedirectResponse
    {
        $nomenclaturesSyncModel = new NomenclaturesSyncModel();
        $nomenclaturesSyncModel->delete($fileId);

        $nomenclaturesSyncEntitiesModel = new NomenclaturesSyncEntitiesModel();
        $nomenclaturesSyncEntitiesModel->where('nomenclatures_sync_id', $fileId)->delete();

        return redirect()->to('/nomenclatures/synchronization');
    }


    public function view_file(int $fileId): string
    {
        $this->viewData['assets']['js'] = 'Nomenclatures/synchronization-view-file.js';

        $nomenclaturesSyncEntitiesModel = new NomenclaturesSyncEntitiesModel();
        $data = $nomenclaturesSyncEntitiesModel->where('nomenclatures_sync_id', $fileId)->findAll();

        $this->viewData['data'] = $data;

        return view('Nomenclatures/synchronization-view-file', $this->viewData);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function download_file(int $fileId)
    {
        $nomenclaturesSyncModel = new NomenclaturesSyncModel();
        $file = $nomenclaturesSyncModel->where('id', $fileId)->first();
        $fileName = 'Синхронизиращ файл ' . $file['date'] . '.xlsx';

        $nomenclaturesSyncEntitiesModel = new NomenclaturesSyncEntitiesModel();
        $data = $nomenclaturesSyncEntitiesModel->where('nomenclatures_sync_id', $fileId)->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Номер');
        $sheet->setCellValue('B1', 'Име на лекарствения продукт');
        $sheet->setCellValue('C1', 'Група');

        $counter = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $counter, $counter - 1);
            $sheet->setCellValue('B' . $counter, $item['name']);
            $sheet->setCellValue('C' . $counter, $item['code_name']);
            $counter++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Set the appropriate headers to force download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit(); // Make sure the script stops after sending the file
    }
}