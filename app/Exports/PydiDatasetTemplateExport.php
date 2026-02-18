<?php

namespace App\Exports;

use App\Models\PhilippineRegions;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PydiDatasetTemplateExport implements WithMultipleSheets
{
    protected $dimensionId;
    protected $indicatorId;

    public function __construct($dimensionId, $indicatorId = null)
    {
        $this->dimensionId = $dimensionId;
        $this->indicatorId = $indicatorId;
    }

    public function sheets(): array
    {
        return [
            new PydiTemplateDataSheet(),
            new RegionReferenceSheet(),
        ];
    }
}

class PydiTemplateDataSheet implements FromCollection, WithHeadings, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Data Entry';
    }

    public function collection()
    {
        return new Collection([
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
        ]);
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sex = ['Male', 'Female', 'Others'];

                // Reference the regions from the second sheet
                $regionCount = PhilippineRegions::count();
                $this->addDropdownFromSheet($sheet, 'A', 'Regions!$A$2:$A$' . ($regionCount + 1), 2, 100);
                
                // Sex dropdown (short list - inline is fine)
                $this->addSimpleDropdown($sheet, 'B', $sex, 2, 100);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(50);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(15);

                // Bold headers
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);
            }
        ];
    }

    private function addDropdownFromSheet($sheet, $column, $range, $startRow, $endRow)
    {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $validation = $sheet->getCell("{$column}{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Region Selection');
            $validation->setError('Please select a valid region from the dropdown or type to search.');
            $validation->setPromptTitle('Philippine Region');
            $validation->setFormula1($range); // Reference to other sheet
        }
    }

    private function addSimpleDropdown($sheet, $column, $options, $startRow, $endRow)
    {
        $optionsString = '"' . implode(',', $options) . '"';

        for ($row = $startRow; $row <= $endRow; $row++) {
            $validation = $sheet->getCell("{$column}{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1($optionsString);
        }
    }
}

class RegionReferenceSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Regions';
    }

    public function collection()
    {
        return PhilippineRegions::orderBy('region_description')
            ->pluck('region_description')
            ->map(function($region) {
                return [$region];
            });
    }

    public function headings(): array
    {
        return ['Available Philippine Regions'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                
                $sheet->getStyle('A1')->getFont()->setBold(true);
                
                $sheet->getColumnDimension('A')->setWidth(50);
            }
        ];
    }
}