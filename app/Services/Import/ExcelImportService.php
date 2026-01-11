<?php

namespace App\Services\Import;

use Generator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Excel Import Service
 *
 * Handles Excel file (.xlsx, .xls) parsing:
 * - Multi-sheet support
 * - Date conversion
 * - Formula evaluation
 */
class ExcelImportService
{
    private ?Spreadsheet $spreadsheet = null;

    /**
     * Analyze an Excel file.
     */
    public function analyze(string $filePath): array
    {
        $this->spreadsheet = IOFactory::load($filePath);
        $worksheet = $this->spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Read headers (first row)
        $headers = [];
        $headerRow = $worksheet->rangeToArray("A1:{$highestColumn}1", null, true, true, false)[0];
        foreach ($headerRow as $cell) {
            $headers[] = $this->cleanValue($cell);
        }

        // Read sample rows
        $sampleRows = [];
        $maxSamples = min(10, $highestRow - 1);

        for ($row = 2; $row <= $maxSamples + 1; $row++) {
            $rowData = $worksheet->rangeToArray("A{$row}:{$highestColumn}{$row}", null, true, true, false)[0];
            $sampleRows[] = array_map(fn ($v) => $this->cleanValue($v), $rowData);
        }

        return [
            'headers' => $headers,
            'sample_rows' => $sampleRows,
            'total_rows' => $highestRow - 1, // Exclude header row
            'sheets' => $this->spreadsheet->getSheetNames(),
            'active_sheet' => $worksheet->getTitle(),
        ];
    }

    /**
     * Stream rows from Excel file.
     */
    public function streamRows(string $filePath, ?string $sheetName = null): Generator
    {
        if (! $this->spreadsheet) {
            $this->spreadsheet = IOFactory::load($filePath);
        }

        $worksheet = $sheetName
            ? $this->spreadsheet->getSheetByName($sheetName)
            : $this->spreadsheet->getActiveSheet();

        if (! $worksheet) {
            throw new \RuntimeException("Sheet not found: {$sheetName}");
        }

        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Read headers
        $headers = [];
        $headerRow = $worksheet->rangeToArray("A1:{$highestColumn}1", null, true, true, false)[0];
        foreach ($headerRow as $cell) {
            $headers[] = $this->cleanValue($cell);
        }

        // Stream data rows
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = $worksheet->rangeToArray("A{$row}:{$highestColumn}{$row}", null, true, true, false)[0];

            $data = [];
            foreach ($headers as $index => $header) {
                $value = $rowData[$index] ?? null;
                $data[$header] = $this->cleanValue($value);
            }

            yield $row - 1 => $data;
        }
    }

    /**
     * Import Excel to array with column mapping.
     */
    public function import(string $filePath, array $columnMapping, ?string $sheetName = null): array
    {
        $rows = [];

        foreach ($this->streamRows($filePath, $sheetName) as $rowNumber => $row) {
            $mappedRow = [];
            foreach ($columnMapping as $targetField => $sourceColumn) {
                $mappedRow[$targetField] = $row[$sourceColumn] ?? null;
            }
            $mappedRow['_row_number'] = $rowNumber;
            $rows[] = $mappedRow;
        }

        return $rows;
    }

    /**
     * Clean and convert cell value.
     */
    private function cleanValue(mixed $value): mixed
    {
        if ($value === null) {
            return '';
        }

        // Handle DateTime objects (Excel dates)
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // Handle numeric values
        if (is_numeric($value)) {
            return $value;
        }

        // Handle strings
        if (is_string($value)) {
            return trim($value);
        }

        return (string) $value;
    }

    /**
     * Get sheet names from file.
     */
    public function getSheetNames(string $filePath): array
    {
        if (! $this->spreadsheet) {
            $this->spreadsheet = IOFactory::load($filePath);
        }

        return $this->spreadsheet->getSheetNames();
    }
}
