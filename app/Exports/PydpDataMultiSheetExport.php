<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\PydpDatasetEntry;

class PydpDataMultiSheetExport implements WithMultipleSheets
{
    private $userId;
    private $selectedLevels;

    public function __construct($userId, $selectedLevels = [])
    {
        $this->userId = $userId;
        $this->selectedLevels = $selectedLevels;
    }

    /**
     * Use array KEYS to set sheet names
     * This is more reliable than title() method
     */
    public function sheets(): array
    {
        $sheets = [];
        
        // Create a sheet for each year (2023-2028)
        // KEY = sheet name, VALUE = sheet object
        for ($year = 2023; $year <= 2028; $year++) {
            $sheets[(string)$year] = new PydpYearSheet($year, $this->userId, $this->selectedLevels);
        }

        return $sheets;
    }
}

class PydpYearSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    private $year;
    private $userId;
    private $selectedLevels;

    public function __construct($year, $userId, $selectedLevels = [])
    {
        $this->year = (int)$year;
        $this->userId = $userId;
        $this->selectedLevels = $selectedLevels;
    }

    public function collection()
    {
        // Query data for THIS year only
        $query = \DB::table('pydp_dataset_entries')
            ->join('pydp_indicators', 'pydp_dataset_entries.pydp_indicator_id', '=', 'pydp_indicators.id')
            ->join('pydp_levels', 'pydp_indicators.pydp_level_id', '=', 'pydp_levels.id')
            ->where('pydp_levels.user_id', $this->userId)
            ->where('pydp_dataset_entries.year', $this->year); // Filter by specific year

        // Filter by selected levels if provided
        if (!empty($this->selectedLevels) && is_array($this->selectedLevels)) {
            $query->whereIn('pydp_levels.id', $this->selectedLevels);
        }

        $entries = $query->select(
            'pydp_levels.id as level_id',
            'pydp_levels.title as level',
            'pydp_levels.content as level_description',
            'pydp_indicators.id as indicator_id',
            'pydp_indicators.title as indicator',
            'pydp_indicators.content as indicator_description',
            'pydp_indicators.data_sources',
            'pydp_indicators.frequency',
            'pydp_indicators.responsible',
            'pydp_indicators.validation',
            'pydp_indicators.data_sharing',
            'pydp_indicators.measurement_unit',
            'pydp_dataset_entries.year',
            'pydp_dataset_entries.baseline',
            'pydp_dataset_entries.physical_target_male',
            'pydp_dataset_entries.physical_target_female',
            'pydp_dataset_entries.physical_target_total',
            'pydp_dataset_entries.physical_actual_male',
            'pydp_dataset_entries.physical_actual_female',
            'pydp_dataset_entries.physical_actual_total',
            'pydp_dataset_entries.financial_allotted',
            'pydp_dataset_entries.financial_spent',
            'pydp_dataset_entries.remarks'
        )
        ->orderBy('pydp_levels.title')
        ->orderBy('pydp_indicators.title')
        ->get();

        // Map the data
        $data = $entries->map(function ($row) {
            return [
                $row->level,
                $row->level_description ?? '',
                $row->indicator,
                $row->indicator_description ?? '',
                $row->measurement_unit ?? '',
                $row->baseline ?? '',
                $row->physical_target_male ?? '',
                $row->physical_target_female ?? '',
                $row->physical_target_total ?? '',
                $row->physical_actual_male ?? '',
                $row->physical_actual_female ?? '',
                $row->physical_actual_total ?? '',
                $row->financial_allotted ?? '',
                $row->financial_spent ?? '',
                $row->data_sources ?? '',
                $row->frequency ?? '',
                $row->responsible ?? '',
                $row->validation ?? '',
                $row->data_sharing ?? '',
                $row->remarks ?? '',
            ];
        });

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Level of Result',
            'Level Description',
            'Indicator',
            'Indicator Description',
            'Measurement Unit',
            'Baseline',
            'Physical Target - Male',
            'Physical Target - Female',
            'Physical Target - Total',
            'Physical Actual - Male',
            'Physical Actual - Female',
            'Physical Actual - Total',
            'Financial - Allotted',
            'Financial - Spent',
            'Data Sources',
            'Frequency',
            'Responsible',
            'Validation & Reporting',
            'Data Sharing',
            'Remarks',
        ];
    }

    /**
     * NOTE: Sheet name is now controlled by the ARRAY KEY in sheets() method
     * Not by this title() method. But keeping it for reference.
     */
    public function title(): string
    {
        return (string)$this->year;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling - Professional blue theme
        $sheet->getStyle('1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'], // Professional blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Content styling
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            // Alternate row colors for better readability
            for ($row = 2; $row <= $highestRow; $row++) {
                $bgColor = ($row % 2 == 0) ? 'F2F2F2' : 'FFFFFF'; // Light gray for even rows
                
                $sheet->getStyle('A' . $row . ':T' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $bgColor],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3'],
                        ],
                    ],
                ]);
            }
        }

        // Set column widths for better visibility
        $columnWidths = [
            'A' => 18, // Level of Result
            'B' => 20, // Level Description
            'C' => 22, // Indicator
            'D' => 20, // Indicator Description
            'E' => 14, // Measurement Unit
            'F' => 12, // Baseline
            'G' => 15, // Physical Target - Male
            'H' => 15, // Physical Target - Female
            'I' => 15, // Physical Target - Total
            'J' => 15, // Physical Actual - Male
            'K' => 15, // Physical Actual - Female
            'L' => 15, // Physical Actual - Total
            'M' => 15, // Financial - Allotted
            'N' => 15, // Financial - Spent
            'O' => 18, // Data Sources
            'P' => 12, // Frequency
            'Q' => 14, // Responsible
            'R' => 16, // Validation & Reporting
            'S' => 14, // Data Sharing
            'T' => 20, // Remarks
        ];

        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Header row height for readability
        $sheet->getRowDimension(1)->setRowHeight(40);

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }
}