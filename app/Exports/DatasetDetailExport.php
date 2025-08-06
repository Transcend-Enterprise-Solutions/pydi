<?php

namespace App\Exports;

use App\Models\PydpDatasetDetail;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class DatasetDetailExport implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    protected $yearRange;
    protected $datasetId;

    public function __construct($yearRange, $datasetId)
    {
        $this->yearRange = $yearRange;
        $this->datasetId = $datasetId;
    }

    public function collection()
    {
        return PydpDatasetDetail::with(['dimension', 'indicator', 'years'])
            ->where('pydp_dataset_id', $this->datasetId)
            ->get();
    }

    public function map($row): array
    {
        $mapped = [
            $row->dimension->name,
            $row->indicator->content,
        ];

        foreach ($this->yearRange as $year) {
            $yearData = $row->years->firstWhere('year', $year);
            $mapped[] = $yearData->target_physical ?? '';
            $mapped[] = $yearData->target_financial ?? '';
            $mapped[] = $yearData->actual_physical ?? '';
            $mapped[] = $yearData->actual_financial ?? '';
        }

        return $mapped;
    }

    public function headings(): array
    {
        $row1 = ['Dimension', 'Indicator'];
        $row2 = ['', ''];
        $row3 = ['', ''];

        foreach ($this->yearRange as $year) {
            $row1 = array_merge($row1, [$year, '', '', '']);
            $row2 = array_merge($row2, ['Target', '', 'Actual', '']);
            $row3 = array_merge($row3, ['Physical', 'Financial', 'Physical', 'Financial']);
        }

        return [$row1, $row2, $row3];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();

                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(2 + count($this->yearRange) * 4);
                $lastRow = $sheet->getHighestRow();

                $fullRange = "A1:{$lastColumn}{$lastRow}";

                // Style: Center alignment and bold header
                $sheet->getStyle("A1:{$lastColumn}3")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A1:{$lastColumn}3")->getFont()->setBold(true);

                // Merge static headers
                $sheet->mergeCells('A1:A3'); // PPA
                $sheet->mergeCells('B1:B3'); // Indicator

                $colIndex = 3; // C column index

                foreach ($this->yearRange as $year) {
                    $start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $end = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 3);

                    $sheet->mergeCells("{$start}1:{$end}1"); // Year
                    $sheet->mergeCells("{$start}2:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . "2"); // Target
                    $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2) . "2:{$end}2"); // Actual

                    $colIndex += 4;
                }

                // Freeze the top 3 header rows
                $sheet->freezePane('C4');

                // ✅ Apply Border to All Used Cells
                $sheet->getStyle($fullRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        ];
    }
}
