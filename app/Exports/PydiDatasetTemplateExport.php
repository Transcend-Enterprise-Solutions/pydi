<?php

namespace App\Exports;

use App\Models\Dimension;
use App\Models\PhilippineRegions;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PydiDatasetTemplateExport implements FromCollection, WithHeadings, WithEvents
{
    protected $dimensionId;

    public function __construct($dimensionId)
    {
        $this->dimensionId = $dimensionId;
    }

    public function collection()
    {
        return new Collection([
            ['', '', '', '', ''] // Empty row for user input
        ]);
    }

    public function headings(): array
    {
        return [
            'Indicator',
            'Philippine Region',
            'Sex',
            'Age',
            'Value',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $spreadsheet = $sheet->getParent();

                $dimension = Dimension::with('indicators')->findOrFail($this->dimensionId);
                $indicators = $dimension->indicators->pluck('name')->toArray();
                $regions = PhilippineRegions::pluck('region_description')->toArray();
                $sex = ['Male', 'Female', 'Others'];

                // Add dropdowns
                $this->addDropdown($spreadsheet, $sheet, 'A', $indicators, 2, 100);
                $this->addDropdown($spreadsheet, $sheet, 'B', $regions, 2, 100);
                $this->addDropdown($spreadsheet, $sheet, 'C', $sex, 2, 100);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(10);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(10);
            }
        ];
    }

    private function addDropdown($spreadsheet, $sheet, $column, $options, $startRow, $endRow)
    {
        // Handle long lists or large strings by using a helper sheet
        if (strlen(implode(',', $options)) > 250 || count($options) > 50) {
            $helperSheetName = "Dropdown_{$column}";

            // Check if helper sheet already exists
            $helperSheet = null;
            foreach ($spreadsheet->getWorksheetIterator() as $ws) {
                if ($ws->getTitle() === $helperSheetName) {
                    $helperSheet = $ws;
                    break;
                }
            }

            if (!$helperSheet) {
                $helperSheet = $spreadsheet->createSheet();
                $helperSheet->setTitle($helperSheetName);
                $helperSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
            }

            // Fill helper sheet
            foreach ($options as $i => $option) {
                $helperSheet->setCellValue("A" . ($i + 1), $option);
            }

            $highestRow = count($options);
            $rangeName = "list_{$column}";

            $spreadsheet->addNamedRange(
                new NamedRange($rangeName, $helperSheet, "A1:A{$highestRow}")
            );

            $formula = "={$rangeName}";
        } else {
            $formula = '"' . implode(',', $options) . '"';
        }

        // Apply validation to each row
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1($formula);

        for ($row = $startRow; $row <= $endRow; $row++) {
            $sheet->getCell("{$column}{$row}")->setDataValidation(clone $validation);
        }
    }
}
