<?php

namespace App\Imports;

use App\Models\PydiDatasetDetail;
use App\Models\Indicator;
use App\Models\PhilippineRegions;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class PydiDatasetDetailsImport implements ToModel, WithHeadingRow
{
    protected $datasetId;
    protected $dimensionId; // passed from controller
    public $errors = [];

    public function __construct($datasetId, $dimensionId)
    {
        $this->datasetId = $datasetId;
        $this->dimensionId = $dimensionId;
    }

    public function model(array $row)
    {
        try {
            // Required fields (Dimension removed since it's pre-selected)
            if (!isset($row['indicator'], $row['philippine_region'], $row['sex'], $row['value'])) {
                return null;
            }

            if (empty($row['sex']) || $row['value'] === null || $row['value'] === '') {
                return null;
            }

            // Allow numeric string but enforce integer value
            if (!is_numeric($row['value']) || floor($row['value']) != $row['value']) {
                throw new \Exception("Value must be an integer, got '{$row['value']}'");
            }

            // Validate `age` as an integer
            if (!is_numeric($row['age']) || floor($row['age']) != $row['age']) {
                throw new \Exception("Age must be an integer, got '{$row['age']}'");
            }

            // Lookups
            $indicator = Indicator::where('name', $row['indicator'])
                ->where('dimension_id', $this->dimensionId)
                ->first();
            $region = PhilippineRegions::where('region_description', $row['philippine_region'])->first();

            if (!$indicator || !$region) {
                return null;
            }

            return new PydiDatasetDetail([
                'pydi_dataset_id'       => $this->datasetId,
                'dimension_id'          => $this->dimensionId,
                'indicator_id'          => $indicator->id,
                'philippine_region_id'  => $region->id,
                'sex'                   => $row['sex'],
                'age'                   => intval($row['age']),
                'content'               => intval($row['value']),
            ]);
        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $row,
                'message' => $e->getMessage()
            ];

            Log::error("Failed to import row: " . $e->getMessage(), [
                'row_data' => $row,
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
