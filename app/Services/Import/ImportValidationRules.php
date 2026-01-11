<?php

namespace App\Services\Import;

use Carbon\Carbon;

/**
 * Import Validation Rules
 *
 * Validates imported data:
 * - Date format validation
 * - Amount parsing and validation
 * - Required fields check
 * - Data type validation
 */
class ImportValidationRules
{
    /**
     * Validate sample data against column mapping.
     */
    public function validateSample(
        array $sampleRows,
        array $columnMapping,
        array $requiredColumns
    ): array {
        $errors = [];
        $warnings = [];
        $validCount = 0;
        $invalidCount = 0;

        foreach ($sampleRows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $rowErrors = [];
            $rowWarnings = [];

            // Map columns
            $mappedRow = [];
            foreach ($columnMapping as $field => $sourceColumn) {
                $columnIndex = array_search($sourceColumn, array_keys($row));
                $mappedRow[$field] = $row[$columnIndex] ?? null;
            }

            // Validate each field
            foreach ($requiredColumns as $field => $config) {
                $value = $mappedRow[$field] ?? null;

                // Required check
                if ($config['required'] && $this->isEmpty($value)) {
                    $rowErrors[] = __('Row :row: Missing required field ":field"', [
                        'row' => $rowNumber,
                        'field' => $config['label'],
                    ]);

                    continue;
                }

                // Skip validation if empty and not required
                if ($this->isEmpty($value)) {
                    continue;
                }

                // Type validation
                $typeError = $this->validateType($value, $config['type'], $config['label']);
                if ($typeError) {
                    $rowErrors[] = __('Row :row: :error', [
                        'row' => $rowNumber,
                        'error' => $typeError,
                    ]);
                }
            }

            // Specific validations
            if (! empty($mappedRow['amount'])) {
                $amount = $this->parseNumber($mappedRow['amount']);
                if ($amount === null) {
                    $rowErrors[] = __('Row :row: Invalid amount format', ['row' => $rowNumber]);
                }
            }

            if (! empty($mappedRow['date'])) {
                $date = $this->parseDate($mappedRow['date']);
                if (! $date) {
                    $rowErrors[] = __('Row :row: Invalid date format', ['row' => $rowNumber]);
                } elseif ($date->isFuture()) {
                    $rowWarnings[] = __('Row :row: Date is in the future', ['row' => $rowNumber]);
                }
            }

            if (empty($rowErrors)) {
                $validCount++;
            } else {
                $invalidCount++;
                $errors = array_merge($errors, $rowErrors);
            }

            $warnings = array_merge($warnings, $rowWarnings);
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'valid_count' => $validCount,
            'invalid_count' => $invalidCount,
        ];
    }

    /**
     * Validate a single row.
     */
    public function validateRow(array $row, array $requiredColumns): array
    {
        $errors = [];
        $warnings = [];

        foreach ($requiredColumns as $field => $config) {
            $value = $row[$field] ?? null;

            if ($config['required'] && $this->isEmpty($value)) {
                $errors[] = __('Missing required field: :field', ['field' => $config['label']]);

                continue;
            }

            if ($this->isEmpty($value)) {
                continue;
            }

            $typeError = $this->validateType($value, $config['type'], $config['label']);
            if ($typeError) {
                $errors[] = $typeError;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Validate value type.
     */
    private function validateType(mixed $value, string $type, string $label): ?string
    {
        return match ($type) {
            'date' => $this->parseDate($value) ? null : __(':field has invalid date format', ['field' => $label]),
            'number' => $this->parseNumber($value) !== null ? null : __(':field has invalid number format', ['field' => $label]),
            'string' => is_string($value) || is_numeric($value) ? null : __(':field must be text', ['field' => $label]),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : __(':field has invalid email format', ['field' => $label]),
            default => null,
        };
    }

    /**
     * Check if value is empty.
     */
    private function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || (is_string($value) && trim($value) === '');
    }

    /**
     * Parse number from various formats.
     */
    public function parseNumber(mixed $value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        // Remove currency symbols
        $value = preg_replace('/[€$£¥]/', '', $value);

        // Remove spaces
        $value = str_replace(' ', '', $value);

        // Handle negative in parentheses: (100.00)
        if (preg_match('/^\((.+)\)$/', $value, $matches)) {
            $value = '-' . $matches[1];
        }

        // Detect format: 1.234,56 (EU) vs 1,234.56 (US)
        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                // EU format: 1.234,56
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                // US format: 1,234.56
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            // Could be EU decimal or US thousands
            $afterComma = substr($value, $lastComma + 1);
            if (strlen($afterComma) === 2 || strlen($afterComma) === 1) {
                // Likely EU decimal
                $value = str_replace(',', '.', $value);
            } else {
                // Likely US thousands
                $value = str_replace(',', '', $value);
            }
        }

        if (! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * Parse date from various formats.
     */
    public function parseDate(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if (empty($value)) {
            return null;
        }

        // Common formats to try
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y',
            'Y/m/d',
            'd.m.Y',
            'Ymd',
            'd M Y',
            'M d, Y',
            'Y-m-d H:i:s',
            'd/m/Y H:i:s',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date && $date->format($format) === $value) {
                    return $date;
                }
            } catch (\Exception) {
                continue;
            }
        }

        // Try Carbon parse as fallback
        try {
            return Carbon::parse($value);
        } catch (\Exception) {
            return null;
        }
    }
}
