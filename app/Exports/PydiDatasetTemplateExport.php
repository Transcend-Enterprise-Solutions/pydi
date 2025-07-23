<?php

namespace App\Exports;

use App\Models\Dimension;
use App\Models\Indicator;
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
    public function collection()
    {
        return new Collection([
            ['', '', '', '', '', '']
        ]);
    }

    public function headings(): array
    {
        return [
            'Dimension',
            'Indicator',
            'Philippine Region',
            'Sex',
            'Age',
            'Content',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $spreadsheet = $sheet->getParent();

                // Get all dimensions and indicators with relationships
                $dimensions = Dimension::with('indicators')->get();
                $regions = PhilippineRegions::pluck('region_description')->toArray();
                $sex = ['Male', 'Female', 'Others'];

                // Create helper sheets for dimensions and indicators
                $this->createDimensionHelperSheet($spreadsheet, $dimensions);
                $this->createIndicatorHelperSheet($spreadsheet, $dimensions);

                // Add dimension dropdown
                $this->addDropdown(
                    $spreadsheet,
                    $sheet,
                    'A',
                    $dimensions->pluck('name')->toArray(),
                    2,
                    100
                );

                // Add region dropdown
                $this->addDropdown(
                    $spreadsheet,
                    $sheet,
                    'C',
                    $regions,
                    2,
                    100
                );

                // Add sex dropdown
                $this->addDropdown(
                    $spreadsheet,
                    $sheet,
                    'D', // Column D for Sex
                    $sex,
                    2,
                    100
                );

                // Set up dependent dropdown for indicators
                $this->setupDependentDropdown(
                    $spreadsheet,
                    $sheet,
                    'B', // Indicator column
                    'A', // Dimension column
                    2,
                    100
                );

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(10);
                $sheet->getColumnDimension('F')->setWidth(40);
            }
        ];
    }

    private function createDimensionHelperSheet($spreadsheet, $dimensions)
    {
        $helperSheet = $spreadsheet->createSheet();
        $helperSheet->setTitle('Dimensions');
        $helperSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // Populate dimension names
        foreach ($dimensions as $i => $dimension) {
            $helperSheet->setCellValue("A" . ($i + 1), $dimension->name);
        }

        // Create named range for dimensions
        $spreadsheet->addNamedRange(
            new NamedRange('dimensions', $helperSheet, "A1:A" . $dimensions->count())
        );
    }

    private function createIndicatorHelperSheet($spreadsheet, $dimensions)
    {
        $helperSheet = $spreadsheet->createSheet();
        $helperSheet->setTitle('Indicators');
        $helperSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // Create a mapping of dimension names to their indicators
        $row = 1;
        foreach ($dimensions as $dimension) {
            $helperSheet->setCellValue("A{$row}", $dimension->name);
            $col = 'B';
            foreach ($dimension->indicators as $indicator) {
                $helperSheet->setCellValue("{$col}{$row}", $indicator->name);
                $col++;
            }
            $row++;
        }

        // Create named ranges for each dimension's indicators
        foreach ($dimensions as $i => $dimension) {
            $rangeName = 'indicators_' . preg_replace('/[^a-zA-Z0-9]/', '_', $dimension->name);
            $startCol = 'B';
            $endCol = chr(ord($startCol) + $dimension->indicators->count() - 1);
            $range = "{$startCol}" . ($i + 1) . ":{$endCol}" . ($i + 1);

            $spreadsheet->addNamedRange(
                new NamedRange($rangeName, $helperSheet, $range)
            );
        }
    }

    private function setupDependentDropdown($spreadsheet, $sheet, $targetCol, $sourceCol, $startRow, $endRow)
    {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $validation = new DataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('=INDIRECT("indicators_" & SUBSTITUTE(' . $sourceCol . $row . ', " ", "_"))');

            $sheet->getCell($targetCol . $row)->setDataValidation($validation);
        }
    }

    private function addDropdown($spreadsheet, $sheet, $column, $options, $startRow, $endRow)
    {
        if (strlen(implode(',', $options)) > 250 || count($options) > 50) {
            $helperSheetName = "Dropdown_{$column}";

            if ($spreadsheet->sheetNameExists($helperSheetName)) {
                $helperSheet = $spreadsheet->getSheetByName($helperSheetName);
            } else {
                $helperSheet = $spreadsheet->createSheet();
                $helperSheet->setTitle($helperSheetName);
                $helperSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
            }

            foreach ($options as $i => $option) {
                $helperSheet->setCellValue("A" . ($i + 1), $option);
                $helperSheet->setCellValue("C" . ($i + 1), $option);
                $helperSheet->setCellValue("D" . ($i + 1), $option);
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
