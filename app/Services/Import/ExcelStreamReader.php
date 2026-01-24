<?php

namespace App\Services\Import;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Generator;

/**
 * Memory-efficient Excel reader that streams data in chunks.
 *
 * Why: Loading 12,000+ rows into memory at once causes memory exhaustion.
 * This reader uses PhpSpreadsheet's chunk reading filter to process
 * the file in configurable batch sizes, keeping memory usage constant.
 */
class ExcelStreamReader
{
    private string $filePath;
    private int $chunkSize;
    private array $headers = [];
    private int $totalRows = 0;
    private int $startRow = 2; // Skip header row

    public function __construct(string $filePath, int $chunkSize = 100)
    {
        $this->filePath = $filePath;
        $this->chunkSize = $chunkSize;
    }

    /**
     * Get total row count without loading all data into memory.
     * Uses a lightweight read to count rows.
     */
    public function getTotalRows(): int
    {
        if ($this->totalRows > 0) {
            return $this->totalRows;
        }

        $reader = IOFactory::createReaderForFile($this->filePath);
        $reader->setReadDataOnly(true);

        // Read only first column to count rows efficiently
        $filter = new class implements IReadFilter {
            public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
            {
                return $columnAddress === 'A';
            }
        };

        $reader->setReadFilter($filter);
        $spreadsheet = $reader->load($this->filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $this->totalRows = $worksheet->getHighestDataRow() - 1; // Exclude header
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $this->totalRows;
    }

    /**
     * Get headers from the first row.
     */
    public function getHeaders(): array
    {
        if (!empty($this->headers)) {
            return $this->headers;
        }

        $reader = IOFactory::createReaderForFile($this->filePath);
        $reader->setReadDataOnly(true);

        // Only read header row
        $filter = new class implements IReadFilter {
            public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
            {
                return $row === 1;
            }
        };

        $reader->setReadFilter($filter);
        $spreadsheet = $reader->load($this->filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $headerRow = $worksheet->toArray()[0] ?? [];
        $this->headers = array_filter($headerRow, fn($h) => !empty(trim($h ?? '')));

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $this->headers;
    }

    /**
     * Stream rows in chunks using a Generator.
     *
     * Why Generator: Allows processing one chunk at a time without
     * loading entire file. Each yield returns a batch of rows,
     * and memory is freed before the next chunk is loaded.
     *
     * @return Generator<int, array{chunkIndex: int, rows: array, startRow: int, endRow: int}>
     */
    public function streamChunks(): Generator
    {
        $totalRows = $this->getTotalRows();
        $headers = $this->getHeaders();
        $chunkIndex = 0;

        for ($startRow = $this->startRow; $startRow <= $totalRows + 1; $startRow += $this->chunkSize) {
            $endRow = min($startRow + $this->chunkSize - 1, $totalRows + 1);

            // Create chunk filter
            $filter = new ChunkReadFilter($startRow, $endRow);

            $reader = IOFactory::createReaderForFile($this->filePath);
            $reader->setReadDataOnly(true);
            $reader->setReadFilter($filter);

            $spreadsheet = $reader->load($this->filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = [];

            // Use toArray() like the old working code
            $allRows = $worksheet->toArray();

            for ($row = $startRow; $row <= $endRow; $row++) {
                $rowIndex = $row - 1; // toArray is 0-indexed
                if (!isset($allRows[$rowIndex])) {
                    continue;
                }

                $rowData = [];
                $hasData = false;

                foreach ($headers as $colIndex => $header) {
                    $cellValue = $allRows[$rowIndex][$colIndex] ?? null;
                    $rowData[$header] = $this->cleanValue($cellValue);

                    if (!empty($rowData[$header])) {
                        $hasData = true;
                    }
                }

                // Only include rows with data
                if ($hasData) {
                    $rowData['_excel_row'] = $row;
                    $rows[] = $rowData;
                }
            }

            // Free memory immediately
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $worksheet, $reader);
            gc_collect_cycles();

            if (!empty($rows)) {
                yield [
                    'chunkIndex' => $chunkIndex,
                    'rows' => $rows,
                    'startRow' => $startRow,
                    'endRow' => $endRow,
                ];
            }

            $chunkIndex++;
        }
    }

    /**
     * Convert column index to letter (0 = A, 1 = B, etc.)
     */
    private function getColumnLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intval($index / 26) - 1;
        }
        return $letter;
    }

    /**
     * Clean and normalize cell values.
     */
    private function cleanValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        // Handle merged cells that might have weird values
        if ($value === '' || $value === 'NULL' || $value === '#N/A') {
            return null;
        }

        return $value;
    }
}

/**
 * PhpSpreadsheet read filter for chunk-based reading.
 */
class ChunkReadFilter implements IReadFilter
{
    private int $startRow;
    private int $endRow;

    public function __construct(int $startRow, int $endRow)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        // Always read header row
        if ($row === 1) {
            return true;
        }

        return $row >= $this->startRow && $row <= $this->endRow;
    }
}
