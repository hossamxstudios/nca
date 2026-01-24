<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Services\ActivityLogger;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BackupController extends Controller
{
    /**
     * Show backup history page
     */
    public function index()
    {
        $backups = Backup::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.backup.index', compact('backups'));
    }

    /**
     * Create and download a full backup (database + uploaded files)
     */
    public function download(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $backupRecord = null;

        try {
            $backupName = 'backup_' . date('Y-m-d_H-i-s');
            $savePath = storage_path('app/backups');

            if (!file_exists($savePath)) {
                mkdir($savePath, 0755, true);
            }

            $backupPath = "{$savePath}/{$backupName}";
            $zipPath = "{$savePath}/{$backupName}.zip";

            // Create backup record
            $backupRecord = Backup::create([
                'user_id' => Auth::id(),
                'filename' => "{$backupName}.zip",
                'path' => $zipPath,
                'status' => 'pending',
            ]);

            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // 1. Export database
            $this->exportDatabase($backupPath);

            // 2. Copy uploaded files
            $this->copyUploadedFiles($backupPath);

            // 3. Create ZIP archive
            $this->createZipArchive($backupPath, $zipPath);

            // 4. Clean up temp folder
            $this->deleteDirectory($backupPath);

            // 5. Update backup record
            $fileSize = file_exists($zipPath) ? filesize($zipPath) : 0;
            $backupRecord->update([
                'status' => 'completed',
                'size' => $fileSize,
            ]);

            // 6. Log activity
            ActivityLogger::make()
                ->action(ActivityLog::ACTION_CREATE, ActivityLog::GROUP_SYSTEM)
                ->description("إنشاء نسخة احتياطية: {$backupName}.zip (" . $this->formatBytes($fileSize) . ")")
                ->log();

            // 7. Return download
            return response()->download($zipPath, "{$backupName}.zip")->deleteFileAfterSend(false);

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());

            if ($backupRecord) {
                $backupRecord->update([
                    'status' => 'failed',
                    'notes' => $e->getMessage(),
                ]);
            }

            ActivityLogger::make()
                ->action(ActivityLog::ACTION_CREATE, ActivityLog::GROUP_SYSTEM)
                ->description("فشل إنشاء نسخة احتياطية: " . $e->getMessage())
                ->log();

            return response()->json(['error' => 'فشل إنشاء النسخة الاحتياطية'], 500);
        }
    }

    private function formatBytes($bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }

    /**
     * Export database to SQL file
     */
    private function exportDatabase(string $backupPath): void
    {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', 3306);
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $sqlFile = "{$backupPath}/database.sql";

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            // Fallback: Export using PHP if mysqldump fails
            $this->exportDatabasePHP($sqlFile);
        }
    }

    /**
     * Fallback: Export database using PHP (for environments without mysqldump)
     */
    private function exportDatabasePHP(string $sqlFile): void
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');

        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$dbName}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            // Get create table statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $columns = array_keys((array)$rows->first());
                $columnList = '`' . implode('`, `', $columns) . '`';

                foreach ($rows as $row) {
                    $values = array_map(function($value) {
                        if ($value === null) return 'NULL';
                        return "'" . addslashes($value) . "'";
                    }, (array)$row);

                    $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($sqlFile, $sql);
    }

    /**
     * Copy project code (excluding vendor, node_modules, etc.)
     */
    private function copyProjectCode(string $backupPath): void
    {
        $codePath = "{$backupPath}/code";
        if (!file_exists($codePath)) {
            mkdir($codePath, 0755, true);
        }

        $projectRoot = base_path();
        $this->copyDirectoryWithExclusions($projectRoot, $codePath, $this->excludeFolders);
    }

    /**
     * Copy directory with exclusions
     */
    private function copyDirectoryWithExclusions(string $source, string $destination, array $excludes): void
    {
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;

            $srcPath = $source . DIRECTORY_SEPARATOR . $file;
            $destPath = $destination . DIRECTORY_SEPARATOR . $file;

            // Check if this path should be excluded
            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $srcPath);
            $shouldExclude = false;
            foreach ($excludes as $exclude) {
                if (strpos($relativePath, $exclude) === 0 || $file === $exclude) {
                    $shouldExclude = true;
                    break;
                }
            }

            if ($shouldExclude) continue;

            if (is_dir($srcPath)) {
                $this->copyDirectoryWithExclusions($srcPath, $destPath, $excludes);
            } else {
                copy($srcPath, $destPath);
            }
        }
        closedir($dir);
    }

    /**
     * Copy uploaded files from storage
     */
    private function copyUploadedFiles(string $backupPath): void
    {
        $filesPath = "{$backupPath}/files";
        if (!file_exists($filesPath)) {
            mkdir($filesPath, 0755, true);
        }

        // Copy media library files (Spatie Media Library stores files here)
        $mediaPath = storage_path('app/public');
        if (file_exists($mediaPath)) {
            $this->copyDirectory($mediaPath, "{$filesPath}/public");
        }

        // Also backup the media folder if exists
        $mediaFolder = storage_path('app/media');
        if (file_exists($mediaFolder)) {
            $this->copyDirectory($mediaFolder, "{$filesPath}/media");
        }
    }

    /**
     * Create ZIP archive from backup folder
     */
    private function createZipArchive(string $sourcePath, string $zipPath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Could not create ZIP archive');
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourcePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }

    /**
     * Recursively copy a directory
     */
    private function copyDirectory(string $source, string $destination): void
    {
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $directory = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            $subPath = $iterator->getSubPathName();
            $destPath = $destination . DIRECTORY_SEPARATOR . $subPath;
            if ($item->isDir()) {
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
            } else {
                copy($item->getRealPath(), $destPath);
            }
        }
    }

    /**
     * Recursively delete a directory
     */
    private function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) return;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }
}
