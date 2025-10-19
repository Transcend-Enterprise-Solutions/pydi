<?php

namespace App\Exports;

use App\Models\{PydiDatasetDetail, PydiDataset};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;

class PydiDatasetDetailsExport implements FromCollection, WithHeadings, WithEvents, WithStyles, WithTitle
{
    protected $datasetId;
    protected $indicatorName;

    public function __construct($datasetId)
    {
        $this->datasetId = $datasetId;
        
        // Get the indicator name for the filename
        $dataset = PydiDataset::with('indicator')->find($datasetId);
        $this->indicatorName = $dataset && $dataset->indicator 
            ? $dataset->indicator->name 
            : 'dataset';
    }

    /**
     * Get the sanitized indicator name for filename
     */
    public function getIndicatorSlug(): string
    {
        // Convert to lowercase and replace spaces with underscores
        $slug = strtolower($this->indicatorName);
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
        $slug = trim($slug, '_');
        
        return $slug . '_template';
    }

    public function collection()
    {
        Log::info("Exporting dataset ID: {$this->datasetId}");
        
        return PydiDatasetDetail::with(['dimension', 'indicator', 'region'])
            ->where('pydi_dataset_id', $this->datasetId)
            ->get()
            ->map(function ($detail) {
                return [
                    'Philippine Region' => $detail->region->region_description ?? '',
                    'Sex'               => $detail->sex ?? '',
                    'Age'               => $detail->age ?? '',
                    'Value'             => $detail->value ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Philippine Region',
            'Sex',
            'Age',
            'Value',
        ];
    }

    public function title(): string
    {
        return 'Dataset';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo color
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Set column widths for better readability
                $sheet->getColumnDimension('A')->setWidth(40); // Philippine Region
                $sheet->getColumnDimension('B')->setWidth(15); // Sex
                $sheet->getColumnDimension('C')->setWidth(15); // Age
                $sheet->getColumnDimension('D')->setWidth(20); // Value

                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Apply borders to all cells with data
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ];

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray($styleArray);

                // Center align all cells
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Left align Philippine Region column (except header)
                if ($highestRow > 1) {
                    $sheet->getStyle("A2:A{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                // Apply alternating row colors for better readability
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F9FAFB'); // Light gray
                    }
                }

                // Freeze the header row
                $sheet->freezePane('A2');

                // Add autofilter to header row
                $sheet->setAutoFilter("A1:{$highestColumn}1");
            }
        ];
    }
}