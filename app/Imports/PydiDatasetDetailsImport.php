<?php

namespace App\Imports;

use App\Models\PydiDatasetDetail;
use App\Models\PhilippineRegions;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Illuminate\Support\Facades\Log;

class PydiDatasetDetailsImport implements ToModel, WithHeadingRow, WithLimit
{
    protected $datasetId;
    protected $dimensionId;
    protected $indicatorId;
    public $errors = [];

    public function __construct($datasetId, $dimensionId, $indicatorId)
    {
        $this->datasetId = $datasetId;
        $this->dimensionId = $dimensionId;
        $this->indicatorId = $indicatorId;
    }

    public function limit(): int
    {
        return 1000; // Limit to first 1000 rows
    }

    public function model(array $row)
    {
        try {
            if (!isset($row['philippine_region'], $row['sex'], $row['value'])) {
                return null;
            }

            if (empty($row['sex']) || $row['value'] === null || $row['value'] === '') {
                return null;
            }

            // Allow numeric string but enforce integer value
            if (!is_numeric($row['value']) || floor($row['value']) != $row['value']) {
                throw new \Exception("Value must be an integer, got '{$row['value']}'");
            }

            if (isset($row['age']) && $row['age'] !== '' && $row['age'] !== null) {
                if (!is_numeric($row['age']) || floor($row['age']) != $row['age']) {
                    throw new \Exception("Age must be an integer, got '{$row['age']}'");
                }
            }

            // Lookups
            $region = PhilippineRegions::where('region_description', $row['philippine_region'])->first();

            if (!$region) {
                throw new \Exception("Region '{$row['philippine_region']}' not found. Please use the exact region name from the database.");
            }

            return new PydiDatasetDetail([
                'pydi_dataset_id'       => $this->datasetId,
                'dimension_id'          => $this->dimensionId,
                'indicator_id'          => $this->indicatorId,
                'philippine_region_id'  => $region->id,
                'sex'                   => $row['sex'],
                'age'                   => isset($row['age']) && $row['age'] !== '' ? intval($row['age']) : null,
                'value'                 => intval($row['value']),
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
