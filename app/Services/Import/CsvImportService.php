<?php

namespace App\Services\Import;

use Generator;

/**
 * CSV Import Service
 *
 * Handles CSV file parsing and import:
 * - Auto-detection of delimiter and encoding
 * - Streaming for large files
 * - Column analysis
 */
class CsvImportService
{
    private string $delimiter = ',';

    private string $enclosure = '"';

    private string $encoding = 'UTF-8';

    /**
     * Analyze a CSV file.
     */
    public function analyze(string $filePath): array
    {
        $this->detectFormat($filePath);

        $handle = $this->openFile($filePath);

        // Read headers
        $headers = fgetcsv($handle, 0, $this->delimiter, $this->enclosure);
        $headers = array_map(fn ($h) => $this->cleanHeader($h), $headers);

        // Read sample rows
        $sampleRows = [];
        $rowCount = 0;
        $maxSamples = 10;

        while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure)) !== false) {
            $rowCount++;
            if (count($sampleRows) < $maxSamples) {
                $sampleRows[] = array_map(fn ($v) => $this->cleanValue($v), $row);
            }
        }

        fclose($handle);

        return [
            'headers' => $headers,
            'sample_rows' => $sampleRows,
            'total_rows' => $rowCount,
            'delimiter' => $this->delimiter,
            'encoding' => $this->encoding,
        ];
    }

    /**
     * Stream rows from CSV file.
     */
    public function streamRows(string $filePath): Generator
    {
        $this->detectFormat($filePath);

        $handle = $this->openFile($filePath);

        // Skip header row
        $headers = fgetcsv($handle, 0, $this->delimiter, $this->enclosure);
        $headers = array_map(fn ($h) => $this->cleanHeader($h), $headers);

        $rowNumber = 0;
        while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure)) !== false) {
            $rowNumber++;

            // Combine headers with values
            $data = [];
            foreach ($headers as $index => $header) {
                $data[$header] = $this->cleanValue($row[$index] ?? '');
            }

            yield $rowNumber => $data;
        }

        fclose($handle);
    }

    /**
     * Import CSV to array with column mapping.
     */
    public function import(string $filePath, array $columnMapping): array
    {
        $rows = [];

        foreach ($this->streamRows($filePath) as $rowNumber => $row) {
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
     * Detect CSV format (delimiter, encoding).
     */
    private function detectFormat(string $filePath): void
    {
        $content = file_get_contents($filePath, false, null, 0, 8192);

        // Detect encoding
        $detected = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        $this->encoding = $detected ?: 'UTF-8';

        // Detect delimiter
        $delimiters = [',', ';', "\t", '|'];
        $delimiterCounts = [];

        foreach ($delimiters as $delimiter) {
            $delimiterCounts[$delimiter] = substr_count($content, $delimiter);
        }

        $this->delimiter = array_keys($delimiterCounts, max($delimiterCounts))[0];
    }

    /**
     * Open file with proper encoding.
     *
     * @return resource
     */
    private function openFile(string $filePath)
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open file: {$filePath}");
        }

        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        return $handle;
    }

    /**
     * Clean header string.
     */
    private function cleanHeader(string $header): string
    {
        // Convert encoding if needed
        if ($this->encoding !== 'UTF-8') {
            $header = mb_convert_encoding($header, 'UTF-8', $this->encoding);
        }

        return trim($header);
    }

    /**
     * Clean value string.
     */
    private function cleanValue(string $value): string
    {
        // Convert encoding if needed
        if ($this->encoding !== 'UTF-8') {
            $value = mb_convert_encoding($value, 'UTF-8', $this->encoding);
        }

        return trim($value);
    }
}
