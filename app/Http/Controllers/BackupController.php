<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Display a listing of the backups.
     */
    public function index()
    {
        $backups = $this->getBackupsList();
        
        // إحصائيات النسخ الاحتياطية
        $statistics = [
            'total_backups' => count($backups),
            'total_size' => collect($backups)->sum('size'),
            'latest_backup' => collect($backups)->first()['created_at'] ?? null,
            'disk_usage' => $this->getDiskUsage()
        ];

        return view('backups.index', compact('backups', 'statistics'));
    }

    /**
     * Create a new backup.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:full,database,files',
            'description' => 'nullable|string|max:255',
            'include_uploads' => 'boolean'
        ]);

        try {
            $backupInfo = $this->createBackup(
                $validated['type'],
                $validated['description'] ?? null,
                $request->boolean('include_uploads')
            );

            return redirect()->route('backups.index')
                ->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح: ' . $backupInfo['filename']);

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل في إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download($filename)
    {
        $backupPath = $this->getBackupPath($filename);
        
        if (!Storage::disk('local')->exists($backupPath)) {
            abort(404, 'ملف النسخة الاحتياطية غير موجود');
        }

        return Storage::disk('local')->download($backupPath, $filename);
    }

    /**
     * Delete a backup file.
     */
    public function delete($filename)
    {
        try {
            $backupPath = $this->getBackupPath($filename);
            
            if (Storage::disk('local')->exists($backupPath)) {
                Storage::disk('local')->delete($backupPath);
                
                return redirect()->route('backups.index')
                    ->with('success', 'تم حذف النسخة الاحتياطية بنجاح');
            }

            return redirect()->route('backups.index')
                ->with('error', 'ملف النسخة الاحتياطية غير موجود');

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل في حذف النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * Restore from backup.
     */
    public function restore(Request $request)
    {
        $validated = $request->validate([
            'backup_file' => 'required|file|mimes:zip,sql',
            'restore_type' => 'required|in:full,database,files'
        ]);

        try {
            $backupFile = $request->file('backup_file');
            $tempPath = $backupFile->store('temp/restore');
            
            $result = $this->restoreFromBackup(
                Storage::disk('local')->path($tempPath),
                $validated['restore_type']
            );

            // تنظيف الملف المؤقت
            Storage::disk('local')->delete($tempPath);

            if ($result) {
                return redirect()->route('backups.index')
                    ->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
            } else {
                return redirect()->route('backups.index')
                    ->with('error', 'فشل في استعادة النسخة الاحتياطية');
            }

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل في استعادة النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * Create backup based on type.
     */
    private function createBackup($type, $description = null, $includeUploads = true)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$type}_{$timestamp}.zip";
        $backupPath = $this->getBackupPath($filename);
        
        $zip = new ZipArchive();
        
        if ($zip->open(Storage::disk('local')->path($backupPath), ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('لا يمكن إنشاء ملف النسخة الاحتياطية');
        }

        // إضافة معلومات النسخة الاحتياطية
        $backupInfo = [
            'type' => $type,
            'description' => $description,
            'created_at' => now()->toISOString(),
            'include_uploads' => $includeUploads,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ];
        
        $zip->addFromString('backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT));

        switch ($type) {
            case 'full':
                $this->addDatabaseToZip($zip);
                $this->addFilesToZip($zip, $includeUploads);
                break;
            case 'database':
                $this->addDatabaseToZip($zip);
                break;
            case 'files':
                $this->addFilesToZip($zip, $includeUploads);
                break;
        }

        $zip->close();

        return [
            'filename' => $filename,
            'path' => $backupPath,
            'size' => Storage::disk('local')->size($backupPath),
            'type' => $type
        ];
    }

    /**
     * Add database dump to zip.
     */
    private function addDatabaseToZip(ZipArchive $zip)
    {
        $databaseName = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $dumpFile = storage_path('app/temp/database_dump.sql');
        
        // إنشاء مجلد temp إذا لم يكن موجوداً
        if (!File::exists(dirname($dumpFile))) {
            File::makeDirectory(dirname($dumpFile), 0755, true);
        }

        // تصدير قاعدة البيانات
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($databaseName),
            escapeshellarg($dumpFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !File::exists($dumpFile)) {
            throw new \Exception('فشل في تصدير قاعدة البيانات');
        }

        $zip->addFile($dumpFile, 'database.sql');
        
        // حذف الملف المؤقت بعد الإضافة
        register_shutdown_function(function() use ($dumpFile) {
            if (File::exists($dumpFile)) {
                File::delete($dumpFile);
            }
        });
    }

    /**
     * Add application files to zip.
     */
    private function addFilesToZip(ZipArchive $zip, $includeUploads = true)
    {
        $filesToBackup = [
            'app/',
            'config/',
            'database/migrations/',
            'database/seeders/',
            'resources/',
            'routes/',
            '.env',
            'composer.json',
            'composer.lock'
        ];

        if ($includeUploads) {
            $filesToBackup[] = 'storage/app/public/';
        }

        foreach ($filesToBackup as $path) {
            $fullPath = base_path($path);
            
            if (File::isDirectory($fullPath)) {
                $this->addDirectoryToZip($zip, $fullPath, $path);
            } elseif (File::exists($fullPath)) {
                $zip->addFile($fullPath, $path);
            }
        }
    }

    /**
     * Add directory to zip recursively.
     */
    private function addDirectoryToZip(ZipArchive $zip, $sourcePath, $zipPath)
    {
        $files = File::allFiles($sourcePath);
        
        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $relativePath);
        }
    }

    /**
     * Restore from backup file.
     */
    private function restoreFromBackup($backupFilePath, $restoreType)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($backupFilePath) !== TRUE) {
            throw new \Exception('لا يمكن فتح ملف النسخة الاحتياطية');
        }

        $tempDir = storage_path('app/temp/restore_' . time());
        File::makeDirectory($tempDir, 0755, true);

        $zip->extractTo($tempDir);
        $zip->close();

        try {
            switch ($restoreType) {
                case 'full':
                    $this->restoreDatabase($tempDir . '/database.sql');
                    $this->restoreFiles($tempDir);
                    break;
                case 'database':
                    $this->restoreDatabase($tempDir . '/database.sql');
                    break;
                case 'files':
                    $this->restoreFiles($tempDir);
                    break;
            }

            return true;

        } finally {
            // تنظيف المجلد المؤقت
            File::deleteDirectory($tempDir);
        }
    }

    /**
     * Restore database from SQL file.
     */
    private function restoreDatabase($sqlFile)
    {
        if (!File::exists($sqlFile)) {
            throw new \Exception('ملف قاعدة البيانات غير موجود في النسخة الاحتياطية');
        }

        $databaseName = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($databaseName),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('فشل في استعادة قاعدة البيانات');
        }
    }

    /**
     * Restore files from backup.
     */
    private function restoreFiles($tempDir)
    {
        $filesToRestore = [
            'app/' => base_path('app/'),
            'config/' => base_path('config/'),
            'resources/' => base_path('resources/'),
            'storage/app/public/' => storage_path('app/public/')
        ];

        foreach ($filesToRestore as $source => $destination) {
            $sourceDir = $tempDir . '/' . $source;
            
            if (File::isDirectory($sourceDir)) {
                File::copyDirectory($sourceDir, $destination);
            }
        }
    }

    /**
     * Get list of available backups.
     */
    private function getBackupsList()
    {
        $backupFiles = Storage::disk('local')->files('backups');
        $backups = [];

        foreach ($backupFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => Storage::disk('local')->size($file),
                    'created_at' => Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file)),
                    'type' => $this->getBackupType(basename($file))
                ];
            }
        }

        return collect($backups)->sortByDesc('created_at')->values()->all();
    }

    /**
     * Get backup type from filename.
     */
    private function getBackupType($filename)
    {
        if (Str::contains($filename, '_full_')) {
            return 'full';
        } elseif (Str::contains($filename, '_database_')) {
            return 'database';
        } elseif (Str::contains($filename, '_files_')) {
            return 'files';
        }
        
        return 'unknown';
    }

    /**
     * Get backup file path.
     */
    private function getBackupPath($filename)
    {
        return 'backups/' . $filename;
    }

    /**
     * Get disk usage information.
     */
    private function getDiskUsage()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $totalSpace = disk_total_space($backupPath);
        $freeSpace = disk_free_space($backupPath);
        $usedSpace = $totalSpace - $freeSpace;

        return [
            'total' => $totalSpace,
            'used' => $usedSpace,
            'free' => $freeSpace,
            'usage_percentage' => round(($usedSpace / $totalSpace) * 100, 1)
        ];
    }

    /**
     * Schedule automatic backups.
     */
    public function scheduleAutoBackup()
    {
        // يمكن تنفيذ جدولة النسخ الاحتياطية التلقائية هنا
        // باستخدام Laravel Scheduler
        
        return response()->json([
            'message' => 'ميزة الجدولة التلقائية قيد التطوير'
        ]);
    }

    /**
     * Clean old backups.
     */
    public function cleanOldBackups(Request $request)
    {
        $daysToKeep = $request->input('days', 30);
        $cutoffDate = now()->subDays($daysToKeep);
        
        $backups = $this->getBackupsList();
        $deletedCount = 0;

        foreach ($backups as $backup) {
            if ($backup['created_at']->lt($cutoffDate)) {
                $backupPath = $this->getBackupPath($backup['filename']);
                
                if (Storage::disk('local')->exists($backupPath)) {
                    Storage::disk('local')->delete($backupPath);
                    $deletedCount++;
                }
            }
        }

        return redirect()->route('backups.index')
            ->with('success', "تم حذف {$deletedCount} نسخة احتياطية قديمة");
    }
}