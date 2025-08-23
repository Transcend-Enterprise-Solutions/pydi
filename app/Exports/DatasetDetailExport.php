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
            $row->baseline ?? '',
        ];

        foreach ($this->yearRange as $year) {
            $yearData = $row->years->firstWhere('year', $year);
            $mapped[] = $yearData->target_physical ?? '';
            $mapped[] = $yearData->target_financial ?? '';
            $mapped[] = $yearData->actual_physical ?? '';
            $mapped[] = $yearData->actual_financial ?? '';
        }

        $mapped[] = $row->total ?? '';
        $mapped[] = $row->remarks ?? '';

        return $mapped;
    }

    public function headings(): array
    {
        $row1 = ['Dimension', 'Indicator', 'Baseline'];
        $row2 = ['', '', ''];
        $row3 = ['', '', ''];

        foreach ($this->yearRange as $year) {
            $row1 = array_merge($row1, [$year, '', '', '']);
            $row2 = array_merge($row2, ['Target', '', 'Actual', '']);
            $row3 = array_merge($row3, ['Physical', 'Financial', 'Physical', 'Financial']);
        }

        $row1[] = 'Total';
        $row2[] = '';
        $row3[] = '';

        $row1[] = 'Remarks';
        $row2[] = '';
        $row3[] = '';

        return [$row1, $row2, $row3];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $staticColumns = 3; // Dimension, Indicator, Baseline
                $yearColumns = count($this->yearRange) * 4;
                $extraColumns = 2; // Total + Remarks
                $lastColumnIndex = $staticColumns + $yearColumns + $extraColumns;

                $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);
                $lastRow = $sheet->getHighestRow();

                $fullRange = "A1:{$lastColumn}{$lastRow}";

                // Style headers
                $sheet->getStyle("A1:{$lastColumn}3")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A1:{$lastColumn}3")->getFont()->setBold(true);

                // Merge static headers
                $sheet->mergeCells('A1:A3'); // Dimension
                $sheet->mergeCells('B1:B3'); // Indicator
                $sheet->mergeCells('C1:C3'); // Baseline

                // Merge Total column
                $totalColIndex = $lastColumnIndex - 1; // before Remarks
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($totalColIndex) . "1:" . Coordinate::stringFromColumnIndex($totalColIndex) . "3");

                // Merge Remarks column
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($lastColumnIndex) . "1:" . Coordinate::stringFromColumnIndex($lastColumnIndex) . "3");

                // Merge year headers
                $colIndex = 4; // Start at column D
                foreach ($this->yearRange as $year) {
                    $start = Coordinate::stringFromColumnIndex($colIndex);
                    $end = Coordinate::stringFromColumnIndex($colIndex + 3);

                    $sheet->mergeCells("{$start}1:{$end}1"); // Year
                    $sheet->mergeCells("{$start}2:" . Coordinate::stringFromColumnIndex($colIndex + 1) . "2"); // Target
                    $sheet->mergeCells(Coordinate::stringFromColumnIndex($colIndex + 2) . "2:{$end}2"); // Actual

                    $colIndex += 4;
                }

                // Freeze first 3 rows + first 3 columns
                $sheet->freezePane(Coordinate::stringFromColumnIndex(4) . "4");

                // Borders
                $sheet->getStyle($fullRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Wrap text for Remarks
                $sheet->getStyle(Coordinate::stringFromColumnIndex($lastColumnIndex) . "4:" . Coordinate::stringFromColumnIndex($lastColumnIndex) . $lastRow)
                    ->getAlignment()->setWrapText(true);
            }
        ];
    }
}
