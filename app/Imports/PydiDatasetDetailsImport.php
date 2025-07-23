<?php

namespace App\Imports;

use App\Models\PydiDatasetDetail;
use App\Models\Dimension;
use App\Models\Indicator;
use App\Models\PhilippineRegions;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class PydiDatasetDetailsImport implements ToModel, WithHeadingRow
{
    protected $datasetId;

    public function __construct($datasetId)
    {
        $this->datasetId = $datasetId;
    }

    public function model(array $row)
    {
        try {
            // Log the incoming row for debugging
            Log::debug('Processing import row:', $row);

            // Validate required fields exist in the row
            if (empty($row['dimension']) || empty($row['indicator']) || empty($row['philippine_region']) || empty($row['content'])) {
                throw new \Exception("Missing required fields in row");
            }

            // Get dimension or fail
            $dimension = Dimension::where('name', $row['dimension'])->first();
            if (!$dimension) {
                throw new \Exception("Dimension not found: {$row['dimension']}");
            }

            // Get indicator that belongs to the dimension or fail
            $indicator = Indicator::where('name', $row['indicator'])
                ->where('dimension_id', $dimension->id)
                ->first();
            if (!$indicator) {
                throw new \Exception("Indicator '{$row['indicator']}' not found for dimension '{$dimension->name}'");
            }

            // Get region or fail
            $region = PhilippineRegions::where('region_description', $row['philippine_region'])->first();
            if (!$region) {
                throw new \Exception("Region not found: {$row['philippine_region']}");
            }

            return new PydiDatasetDetail([
                'pydi_dataset_id'       => $this->datasetId,
                'dimension_id'          => $dimension->id,
                'indicator_id'          => $indicator->id,
                'philippine_region_id'  => $region->id,
                'sex'                   => $row['sex'] ?? null,
                'age'                   => $row['age'] ?? null,
                'content'               => $row['content'],
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to import row: " . $e->getMessage(), [
                'row_data' => $row,
                'error' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
