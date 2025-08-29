<?php

namespace App\Exports;

use App\Models\PydpDatasetDetail;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Log;

class DatasetDetailExport implements FromArray, WithEvents
{
    protected $yearRange;
    protected $datasetId;

    public function __construct($yearRange, $datasetId)
    {
        $this->yearRange = $yearRange;
        $this->datasetId = $datasetId;
    }

    public function array(): array
    {
        Log::info('Exporting dataset ID: ' . $this->datasetId);

        $details = PydpDatasetDetail::with(['dimension', 'indicator.level', 'years'])
            ->where('pydp_dataset_id', $this->datasetId)
            ->get();

        // Build the header structure
        $mainHeaders = ['Dimension', 'Level', 'Indicator'];
        $subHeaders = ['', '', ''];

        // Add year headers
        foreach ($this->yearRange as $year) {
            // Main header spans 7 columns for each year
            $mainHeaders = array_merge($mainHeaders, [$year, '', '', '', '', '', '']);

            // Sub headers for each year's data
            $subHeaders = array_merge($subHeaders, [
                'Baseline',
                'Total',
                'Remarks',
                'Target Physical',
                'Target Financial',
                'Actual Physical',
                'Actual Financial'
            ]);
        }

        $exportData = [$mainHeaders, $subHeaders];

        // Add data rows
        foreach ($details as $detail) {
            $yearsData = $detail->years->keyBy('year');

            $row = [
                $detail->dimension->name ?? '',
                $detail->indicator->level->title ?? '',
                $detail->indicator->title ?? ''
            ];

            foreach ($this->yearRange as $year) {
                $yearData = $yearsData->get($year);

                if ($yearData) {
                    $row[] = $yearData->baseline ?? '';
                    $row[] = $yearData->total ?? '';
                    $row[] = $yearData->remarks ?? '';
                    $row[] = $yearData->target_physical ?? '';
                    $row[] = $yearData->target_financial ?? '';
                    $row[] = $yearData->actual_physical ?? '';
                    $row[] = $yearData->actual_financial ?? '';
                } else {
                    $row = array_merge($row, ['', '', '', '', '', '', '']);
                }
            }

            $exportData[] = $row;
        }

        return $exportData;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $totalColumns = 3 + (count($this->yearRange) * 7);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(25); // Dimension
                $sheet->getColumnDimension('B')->setWidth(15); // Level
                $sheet->getColumnDimension('C')->setWidth(45); // Indicator

                // Set year column widths
                $colIndex = 4;
                foreach ($this->yearRange as $year) {
                    $sheet->getColumnDimensionByColumn($colIndex)->setWidth(12);     // Baseline
                    $sheet->getColumnDimensionByColumn($colIndex + 1)->setWidth(12); // Total
                    $sheet->getColumnDimensionByColumn($colIndex + 2)->setWidth(25); // Remarks
                    $sheet->getColumnDimensionByColumn($colIndex + 3)->setWidth(13); // Target Physical
                    $sheet->getColumnDimensionByColumn($colIndex + 4)->setWidth(13); // Target Financial
                    $sheet->getColumnDimensionByColumn($colIndex + 5)->setWidth(13); // Actual Physical
                    $sheet->getColumnDimensionByColumn($colIndex + 6)->setWidth(13); // Actual Financial
                    $colIndex += 7;
                }

                // Merge cells for year headers
                $colIndex = 4;
                foreach ($this->yearRange as $year) {
                    $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 6);
                    $sheet->mergeCells($startCol . '1:' . $endCol . '1');
                    $colIndex += 7;
                }

                // Style year headers (main header row)
                $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns) . '1';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 13,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2F5597'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Style sub headers
                $subHeaderRange = 'A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns) . '2';
                $sheet->getStyle($subHeaderRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 10,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '5B9BD5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $highestRow = $sheet->getHighestRow();

                // Apply borders to all data
                if ($highestRow > 2) {
                    $dataRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns) . $highestRow;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                    // Style data rows with alternating colors
                    for ($row = 3; $row <= $highestRow; $row++) {
                        $rowColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
                        $rowRange = 'A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns) . $row;
                        $sheet->getStyle($rowRange)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $rowColor],
                            ],
                        ]);
                    }

                    // Left align text columns
                    $sheet->getStyle('A3:C' . $highestRow)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ]);

                    // Style year data columns
                    $colIndex = 4;
                    foreach ($this->yearRange as $year) {
                        // Center align numeric columns
                        for ($i = 0; $i < 7; $i++) {
                            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + $i);
                            if ($i == 2) { // Remarks column
                                $sheet->getStyle($colLetter . '3:' . $colLetter . $highestRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_TOP,
                                        'wrapText' => true,
                                    ],
                                ]);
                            } else { // Numeric columns
                                $sheet->getStyle($colLetter . '3:' . $colLetter . $highestRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                    ],
                                ]);
                            }
                        }
                        $colIndex += 7;
                    }
                }

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(25);

                // Auto-size data rows
                for ($row = 3; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                // Freeze panes
                $sheet->freezePane('D3');
            }
        ];
    }
}
