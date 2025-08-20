<?php

namespace App\Exports;

use App\Models\PydiDatasetDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\Log;

class PydiDatasetDetailsExport implements FromCollection, WithHeadings, WithEvents
{
    protected $datasetId;

    public function __construct($datasetId)
    {
        $this->datasetId = $datasetId;
    }

    public function collection()
    {
        Log::info($this->datasetId);
        return PydiDatasetDetail::with(['dimension', 'indicator', 'region'])
            ->where('pydi_dataset_id', $this->datasetId)
            ->get()
            ->map(function ($detail) {
                return [
                    'Dimension'         => $detail->dimension->name ?? '',
                    'Indicator'         => $detail->indicator->name ?? '',
                    'Philippine Region' => $detail->region->region_description ?? '',
                    'Sex'               => $detail->sex ?? '',
                    'Age'               => $detail->age ?? '',
                    'Content'           => $detail->value ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Dimension',
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

                // Set column widths for readability
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(10);
                $sheet->getColumnDimension('F')->setWidth(10);

                // Allow free text for "Sex" column (remove validation)
                $validation = new DataValidation();
                $validation->setType(DataValidation::TYPE_NONE);
                $validation->setAllowBlank(true);

                for ($row = 2; $row <= 100; $row++) {
                    $sheet->getCell("D{$row}")->setDataValidation(clone $validation);
                }
            }
        ];
    }
}
