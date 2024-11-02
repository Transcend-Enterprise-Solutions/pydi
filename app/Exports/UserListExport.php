<?php

namespace App\Exports;

use App\Models\User;
use Exception;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserListExport
{
    use Exportable;

    protected $filters;
    protected $rowNumber = 0;
    protected $currentRow = 6;

    public function __construct($filters){
        $this->filters = $filters;
    }

    public function export(){
        try {
            $spreadsheet = IOFactory::load(storage_path('app/templates/list_export_template.xlsx'));
            $sheet = $spreadsheet->getSheetByName(worksheetName: 'Sheet1');
           
            $this->AddData($sheet);

            $writer = new Xlsx($spreadsheet);
            $filename = "Homeowners_List.xlsx";
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);
            $fileContent = file_get_contents($tempFile);
            unlink($tempFile);
            return [
                'content' => $fileContent,
                'filename' => $filename
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }


    private function AddData($sheet){
        $this->DataRows($sheet);
    }

    private function formatCurrency($value) {
        if($value == 0 || $value == null){
            return "-";
        }
        return number_format((double)$value, 2, '.', ',');
    }

    private function getData(){
        $query = User::select(
            'users.id as user_id',
            'users.name',
            'users.email',
            'users.profile_photo_path',
            'users.user_role',
            'positions.position',
            'user_data.*'
            )->join('user_data', 'user_data.user_id', 'users.id')
            ->where('users.user_role', 'homeowner')
            ->leftJoin('positions', 'positions.id', 'users.position_id')
            ->orderByRaw('CAST(user_data.block AS UNSIGNED) ASC')
            ->orderByRaw('CAST(user_data.lot AS UNSIGNED) ASC');

        if (isset($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('users.name', 'LIKE', '%' . $this->filters['search'] . '%');
            });
        }

        if (isset($this->filters['activeStatus'])) {
            $query->where(function ($q) {
                $q->where('users.active_status', '=', $this->filters['activeStatus']);
            });
        }
    
        $data = $query->get()->map(function ($homeowner) {
            $this->rowNumber++;
            return [
                0 => $this->rowNumber,
                1 => $homeowner->name,
                2 => $homeowner->block,
                3 => $homeowner->lot,
                4 => $homeowner->street,
                5 => $homeowner->email,
                6 => $homeowner->tel_number ?: $homeowner->mobile_number,
            ];
        });
    
        $this->rowNumber = 0;
        return $data;
    }

    private function DataRows($sheet){
        $data = $this->getData();
        foreach ($data as $row) {
            $sheet->setCellValue("A{$this->currentRow}", $row[0]);
            $sheet->mergeCells("B{$this->currentRow}:C{$this->currentRow}");
            $sheet->setCellValue("B{$this->currentRow}", $row[1]);
            $sheet->setCellValue("D{$this->currentRow}", $row[2]);
            $sheet->setCellValue("E{$this->currentRow}", $row[3]);
            $sheet->setCellValue("F{$this->currentRow}", $row[4]);
            $sheet->setCellValue("G{$this->currentRow}", $row[5]);
            $sheet->setCellValue("H{$this->currentRow}", $row[6]);

            $sheet->getStyle("A{$this->currentRow}:H{$this->currentRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
            $this->currentRow++;
        }
    }
}

