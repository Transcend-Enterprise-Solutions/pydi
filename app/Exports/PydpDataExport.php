<?php

namespace App\Exports;

use App\Models\PydpLevel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PydpDataExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    private $year;
    private $userId;
    private $userName;
    private $selectedLevelIds;

    public function __construct($year, $userId, $selectedLevelIds = null)
    {
        $this->year = $year;
        $this->userId = $userId;
        $this->selectedLevelIds = $selectedLevelIds;
        
        // Get user name for sheet title
        $user = \App\Models\User::find($userId);
        if ($user && $user->userData) {
            $this->userName = $user->userData->first_name . ' ' . $user->userData->last_name;
        } else {
            $this->userName = 'User_' . $userId;
        }
    }

    public function collection()
    {
        $query = \DB::table('pydp_dataset_entries')
            ->join('pydp_indicators', 'pydp_dataset_entries.pydp_indicator_id', '=', 'pydp_indicators.id')
            ->join('pydp_levels', 'pydp_indicators.pydp_level_id', '=', 'pydp_levels.id')
            ->where('pydp_dataset_entries.year', $this->year)
            ->where('pydp_dataset_entries.submitted_by', $this->userId)
            ->select(
                'pydp_levels.title as level',
                'pydp_indicators.title as indicator',
                'pydp_dataset_entries.physical_target_male',
                'pydp_dataset_entries.physical_target_female',
                'pydp_dataset_entries.physical_target_total',
                'pydp_dataset_entries.physical_actual_male',
                'pydp_dataset_entries.physical_actual_female',
                'pydp_dataset_entries.physical_actual_total',
                'pydp_dataset_entries.financial_allotted',
                'pydp_dataset_entries.financial_spent',
                'pydp_dataset_entries.remarks',
                'pydp_dataset_entries.submission_status'
            );

        // Filter by selected levels if provided
        if (!empty($this->selectedLevelIds)) {
            $query->whereIn('pydp_levels.id', $this->selectedLevelIds);
        }

        $entries = $query->orderBy('pydp_indicators.title')->get();

        return collect($entries->map(function ($row) {
            return [
                $row->level,
                $row->indicator,
                $row->physical_target_male ?? '',
                $row->physical_target_female ?? '',
                $row->physical_target_total ?? '',
                $row->physical_actual_male ?? '',
                $row->physical_actual_female ?? '',
                $row->physical_actual_total ?? '',
                $row->financial_allotted ?? '',
                $row->financial_spent ?? '',
                $row->remarks ?? '',
                $row->submission_status ?? '',
            ];
        }));
    }

    public function headings(): array
    {
        return [
            'Level',
            'Indicator',
            'Physical Target - Male',
            'Physical Target - Female',
            'Physical Target - Total',
            'Physical Actual - Male',
            'Physical Actual - Female',
            'Physical Actual - Total',
            'Financial - Allotted',
            'Financial - Spent',
            'Remarks',
            'Status',
        ];
    }

    public function title(): string
    {
        // Make safe sheet name (max 31 characters)
        return substr(str_replace(['/', '\\', '?', '*', '[', ']', ':'], '', $this->userName), 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Content styling
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A2:L' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D3D3D3'],
                    ],
                ],
            ]);
        }

        // Auto height for header
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(15);

        return [];
    }
}