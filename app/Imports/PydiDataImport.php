<?php

namespace App\Imports;

use App\Models\PydiDataRecord;
use App\Models\PhilippineRegions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class PydiDataImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithEvents,
    SkipsEmptyRows,
    SkipsOnError,
    SkipsOnFailure
{
    protected $uploadSessionId;
    protected $dimensionId;
    protected $indicatorId;
    protected $userId;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];
    protected $validRegions = [];
    protected $validSexOptions = ['Male', 'Female', 'Both'];

    public function __construct($uploadSessionId, $dimensionId, $indicatorId, $userId)
    {
        $this->uploadSessionId = $uploadSessionId;
        $this->dimensionId = $dimensionId;
        $this->indicatorId = $indicatorId;
        $this->userId = $userId;
        $this->validRegions = PhilippineRegions::pluck('region_description')->toArray();
    }

    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }

        $normalizedRow = $this->normalizeRowData($row);

        $validator = Validator::make($normalizedRow, [
            'region' => 'required|string|in:' . implode(',', $this->validRegions),
            'sex' => 'required|in:Male,Female,Both',
            'age' => 'required|string',
            'value' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $normalizedRow,
                'errors' => $validator->errors()->toArray()
            ];
            return null;
        }

        try {
            $this->importedCount++;

            return new PydiDataRecord([
                'upload_session_id' => $this->uploadSessionId,
                'dimension_id' => $this->dimensionId,
                'indicator_id' => $this->indicatorId,
                'user_id' => $this->userId,
                'region' => $normalizedRow['region'],
                'sex' => $normalizedRow['sex'],
                'age' => $normalizedRow['age'],
                'value' => $normalizedRow['value'],
                'remarks' => $normalizedRow['remarks'] ?? null,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $normalizedRow,
                'errors' => ['general' => $e->getMessage()]
            ];
            return null;
        }
    }

    private function normalizeRowData(array $row): array
    {
        $normalized = [];
        $columnMappings = [
            'region' => ['region', 'region_description', 'area', 'location'],
            'sex' => ['sex', 'gender'],
            'age' => ['age', 'age_group', 'age_range'],
            'value' => ['value', 'value_of_indicator', 'indicator_value', 'data_value'],
            'remarks' => ['remarks', 'notes', 'comments', 'description'],
        ];

        foreach ($columnMappings as $standardKey => $possibleKeys) {
            foreach ($possibleKeys as $possibleKey) {
                if (isset($row[$possibleKey]) && !empty($row[$possibleKey])) {
                    $normalized[$standardKey] = trim($row[$possibleKey]);
                    break;
                }
            }
        }

        if (isset($normalized['sex'])) {
            $normalized['sex'] = $this->normalizeSexValue($normalized['sex']);
        }

        if (isset($normalized['value'])) {
            $normalized['value'] = $this->normalizeNumericValue($normalized['value']);
        }

        return $normalized;
    }

    private function normalizeSexValue(string $value): string
    {
        $value = strtolower(trim($value));

        return match ($value) {
            'm', 'male', 'males' => 'Male',
            'f', 'female', 'females' => 'Female',
            default => 'Both'
        };
    }

    private function normalizeNumericValue($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) str_replace([',', ' '], '', $value);
    }

    public function onError(Throwable $e): void
    {
        Log::error('PydiDataImport error: ' . $e->getMessage(), [
            'upload_session_id' => $this->uploadSessionId,
            'trace' => $e->getTraceAsString()
        ]);

        $this->errors[] = ['general_error' => $e->getMessage()];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ];
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                Log::info('Starting import', [
                    'session' => $this->uploadSessionId,
                    'user' => $this->userId
                ]);
            },
            AfterImport::class => function (AfterImport $event) {
                Log::info('Import completed', [
                    'session' => $this->uploadSessionId,
                    'imported' => $this->importedCount,
                    'skipped' => $this->skippedCount
                ]);
            },
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportSummary(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'error_count' => count($this->errors),
            'has_errors' => !empty($this->errors),
        ];
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrorReport(): string
    {
        if (!$this->hasErrors()) {
            return 'No errors encountered during import.';
        }

        $report = "Import Error Report\n";
        $report .= "==================\n";
        $report .= "Total Errors: " . count($this->errors) . "\n\n";

        foreach ($this->errors as $index => $error) {
            $report .= sprintf("Error #%d:\n", $index + 1);

            if (isset($error['general_error'])) {
                $report .= "  - General Error: " . $error['general_error'] . "\n";
            }

            if (isset($error['row'])) {
                $report .= "  - Row Data: " . json_encode($error['row']) . "\n";
            }

            if (isset($error['errors'])) {
                $report .= "  - Validation Errors:\n";
                foreach ($error['errors'] as $field => $messages) {
                    $report .= sprintf("    - %s: %s\n", $field, implode(', ', $messages));
                }
            }

            $report .= "\n";
        }

        return $report;
    }
}
