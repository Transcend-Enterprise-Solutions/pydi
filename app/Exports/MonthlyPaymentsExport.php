<?php

namespace App\Exports;

use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MonthlyPaymentsExport
{
    use Exportable;

    protected $filters;
    protected $rowNumber = 0;
    protected $currentRow = 7;

    public function __construct($filters){
        $this->filters = $filters;
    }

    public function export(){
        try {
            $spreadsheet = IOFactory::load(storage_path('app/templates/payments_export_template.xlsx'));
            $sheet = $spreadsheet->getSheetByName(worksheetName: 'Sheet1');
           
            $this->AddData($sheet);

            $writer = new Xlsx($spreadsheet);
            $filename = "Assoc_Dues.xlsx";
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
        $sheet->setCellValue("A5", $this->filters['title']);
        $this->DataRows($sheet);
    }

    private function getData(){
        $query = $this->filters['query'];
    
        $data = $query->get()->map(function ($homeowner) {
            $this->rowNumber++;
            return [
                0 => $this->rowNumber,
                1 => $homeowner->name,
                2 => $homeowner->block,
                3 => $homeowner->lot,
                4 => $homeowner->payment_mode ?: '-',
                5 => $homeowner->reference_number ?: '-',
                6 => $homeowner->payment_mode && $homeowner->reference_number ? Carbon::parse($homeowner->date_paid)->format('m/d/Y') : '-',
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

