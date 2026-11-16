<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BackupController extends Controller
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    // ─── List Backups ────────────────────────────────────────────────────────
    public function index()
    {
        $files = [];

        if (is_dir($this->backupPath)) {
            foreach (scandir($this->backupPath) as $item) {
                if (!str_ends_with($item, '.tar.gz')) continue;

                $fullPath = $this->backupPath . '/' . $item;
                $info     = $this->readBackupInfo($fullPath);

                $files[] = [
                    'name'        => $item,
                    'size'        => $this->formatSize(filesize($fullPath)),
                    'size_bytes'  => filesize($fullPath),
                    'created_at'  => date('Y-m-d H:i:s', filemtime($fullPath)),
                    'label'       => $info['label']       ?? '',
                    'tables'      => $info['table_count']  ?? 0,
                    'has_db'      => $info['has_db']       ?? false,
                    'has_images'  => $info['has_images']   ?? false,
                    'has_storage' => $info['has_storage']  ?? false,
                ];
            }
            usort($files, fn($a, $b) => $b['created_at'] <=> $a['created_at']);
        }

        // Table list for the table picker
        $tableRows = DB::select('SHOW TABLES');
        $tables    = array_map(fn($r) => array_values((array) $r)[0], $tableRows);

        return view('admin.backup', compact('files', 'tables'));
    }

    // ─── Create Backup ───────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $request->validate([
            'backup_name' => 'required|regex:/^[a-zA-Z0-9_\-]+$/|max:60',
        ], [
            'backup_name.required' => 'Please enter a backup name.',
            'backup_name.regex'    => 'Name may only contain letters, numbers, - and _.',
            'backup_name.max'      => 'Name must be 60 characters or less.',
        ]);

        $saveServer     = $request->boolean('save_server');
        $saveDownload   = $request->boolean('save_download');
        $saveDrive      = $request->boolean('save_drive');
        $saveTelegram   = $request->boolean('save_telegram');
        $saveEmail      = $request->boolean('save_email');
        $selectedTables = $request->input('tables', []);

        if (empty($selectedTables)) {
            return back()->with('error', 'Please select at least one table to backup.');
        }
        if (!$saveServer && !$saveDownload && !$saveDrive && !$saveTelegram && !$saveEmail) {
            return back()->with('error', 'Please choose at least one save location.');
        }

        try {
            $timestamp  = now()->format('Y-m-d_H-i-s');
            $customName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('backup_name'));
            $backupName = "backup_{$customName}_{$timestamp}.tar.gz";
            $backupFile = $this->backupPath . '/' . $backupName;

            $stageDir = storage_path('app/backup_stage_' . time());
            mkdir($stageDir . '/database', 0755, true);

            // 1. Export selected tables
            $sqlContent = $this->exportDatabase($selectedTables);
            file_put_contents($stageDir . "/database/database_{$timestamp}.sql", $sqlContent);
            $tableCount = count($selectedTables);

            // 2. Backup info JSON
            file_put_contents($stageDir . '/backup_info.json', json_encode([
                'label'           => $customName,
                'created_at'      => now()->toDateTimeString(),
                'has_db'          => true,
                'has_images'      => false,
                'has_storage'     => false,
                'table_count'     => $tableCount,
                'tables'          => $selectedTables,
                'save_server'     => $saveServer,
                'save_download'   => $saveDownload,
                'save_drive'      => $saveDrive,
                'app_name'        => config('app.name'),
                'php_version'     => PHP_VERSION,
                'laravel_version' => app()->version(),
            ], JSON_PRETTY_PRINT));

            // 4. Pack into .tar.gz using PharData (built-in PHP, no extension needed)
            $tarOnly = str_replace('.tar.gz', '.tar', $backupFile);
            $phar = new \PharData($tarOnly);
            $phar->buildFromDirectory($stageDir);
            $phar->compress(\Phar::GZ);
            unlink($tarOnly);
            $this->deleteDirectory($stageDir);

            $size = $this->formatSize(filesize($backupFile));

            // 5. Send to Telegram
            $telegramMsg = '';
            if ($saveTelegram) {
                $sent = $this->sendToTelegram($backupFile, $backupName, $tableCount, $size, $customName);
                $telegramMsg = $sent ? ' 📨 Sent to Telegram!' : ' ⚠️ Telegram send failed.';
            }

            // 6. Send to Email
            $emailMsg = '';
            if ($saveEmail) {
                $sent = $this->sendToEmail($backupFile, $backupName, $tableCount, $size, $customName);
                $emailMsg = $sent ? ' 📧 Sent to Email!' : ' ⚠️ Email send failed.';
            }

            // 7. Send to Google Drive
            $driveMsg = '';
            if ($saveDrive) {
                $sent = $this->sendToDrive($backupFile, $backupName);
                $driveMsg = $sent ? ' ☁️ Uploaded to Google Drive!' : ' ⚠️ Google Drive upload failed.';
            }

            // 8. Download response (takes priority over redirect)
            if ($saveDownload) {
                session()->flash('success', "✅ Backup '{$customName}' created! ({$tableCount} tables, {$size}){$telegramMsg}{$emailMsg}{$driveMsg}");
                return response()->download($backupFile, $backupName, [
                    'Content-Type' => 'application/x-tar',
                ]);
            }

            $msg = "✅ Backup '{$customName}' saved! ({$tableCount} tables, {$size}){$telegramMsg}{$emailMsg}{$driveMsg}";

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            if (isset($stageDir) && is_dir($stageDir)) $this->deleteDirectory($stageDir);
            if (isset($tarOnly)   && file_exists($tarOnly))   unlink($tarOnly);
            if (isset($backupFile) && file_exists($backupFile)) unlink($backupFile);
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    // ─── Download Existing Backup ─────────────────────────────────────────────
    public function download(string $filename)
    {
        $filename = basename($filename);
        $filePath = $this->backupPath . '/' . $filename;

        if (!file_exists($filePath) || !str_ends_with($filename, '.tar.gz')) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/x-tar',
        ]);
    }

    // ─── Delete ──────────────────────────────────────────────────────────────
    public function delete(string $filename)
    {
        $filename = basename($filename);
        $filePath = $this->backupPath . '/' . $filename;

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        unlink($filePath);
        return back()->with('success', "🗑️ Backup '{$filename}' deleted.");
    }

    // ─── Restore ─────────────────────────────────────────────────────────────
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:524288',
        ]);

        try {
            $uploadedPath = $request->file('backup_file')->getRealPath();
            $extractDir   = storage_path('app/restore_tmp_' . time());
            mkdir($extractDir, 0755, true);

            $phar = new \PharData($uploadedPath);
            $phar->extractTo($extractDir);

            $sqlFiles = glob($extractDir . '/database/*.sql');
            if (empty($sqlFiles)) {
                $this->deleteDirectory($extractDir);
                return back()->with('error', 'No SQL file found in the backup archive.');
            }

            $this->importDatabase($sqlFiles[0]);
            $this->deleteDirectory($extractDir);

            return back()->with('success', '✅ Database restored successfully!');

        } catch (\Exception $e) {
            if (isset($extractDir) && is_dir($extractDir)) $this->deleteDirectory($extractDir);
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function exportDatabase(array $selectedTables = []): string
    {
        // If no specific tables given, export all tables
        if (empty($selectedTables)) {
            $allRows        = DB::select('SHOW TABLES');
            $selectedTables = array_map(fn($r) => array_values((array) $r)[0], $allRows);
        }

        $sql  = "-- Database Backup\n";
        $sql .= "-- Created: " . now() . "\n";
        $sql .= "-- App: " . config('app.name') . "\n";
        $sql .= "-- Tables: " . implode(', ', $selectedTables) . "\n";
        $sql .= "-- -----------------------------------------------\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($selectedTables as $tableName) {
            $createResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql    = $createResult[0]->{'Create Table'};

            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createSql . ";\n\n";

            $rows = DB::table($tableName)->get();
            if ($rows->isEmpty()) continue;

            $columns = array_keys((array) $rows->first());
            $colList = '`' . implode('`, `', $columns) . '`';
            $sql    .= "INSERT INTO `{$tableName}` ({$colList}) VALUES\n";

            $valueRows = [];
            foreach ($rows as $row) {
                $vals = array_map(function ($val) {
                    if ($val === null) return 'NULL';
                    return "'" . addslashes((string) $val) . "'";
                }, array_values((array) $row));
                $valueRows[] = '(' . implode(', ', $vals) . ')';
            }
            $sql .= implode(",\n", $valueRows) . ";\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    private function importDatabase(string $sqlFile): void
    {
        $sql        = file_get_contents($sqlFile);
        $statements = array_filter(
            array_map('trim', explode(";\n", $sql)),
            fn($s) => $s !== '' && !str_starts_with($s, '--')
        );

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($statements as $statement) {
            if (trim($statement) !== '') {
                DB::statement($statement);
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function uploadToDrive(string $filePath, string $fileName): ?string
    {
        // Google Drive integration requires:
        // composer require google/apiclient
        // Set up OAuth2 credentials in config/services.php
        // For now, returns null — coming soon
        return null;
    }

    private function updateBackupInfoDriveLink(string $tarGzPath, string $link): void
    {
        try {
            // Read existing info, add drive_link, repack — skipped for simplicity
            // The link is shown in the success message instead
        } catch (\Exception $e) {}
    }

    private function readBackupInfo(string $tarGzPath): array
    {
        try {
            $phar = new \PharData($tarGzPath);
            foreach ($phar as $file) {
                if ($file->getFilename() === 'backup_info.json') {
                    return json_decode(file_get_contents($file->getPathname()), true) ?? [];
                }
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }
        rmdir($dir);
    }

    // ─── Send Backup to Telegram ─────────────────────────────────────────────
    private function sendToTelegram(string $filePath, string $fileName, int $tableCount, string $size, string $label): bool
    {
        $token  = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            \Illuminate\Support\Facades\Log::warning('[Telegram] TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID not set in .env');
            return false;
        }

        try {
            $caption = "🗄 *Database Backup*\n"
                . " Name: `{$label}`\n"
                . " Tables: {$tableCount}\n"
                . " Size: {$size}\n"
                . " Time: " . now()->format('d M Y H:i') . "\n"
                . " App: " . config('app.name');

            $url  = "https://api.telegram.org/bot{$token}/sendDocument";
            $data = [
                'chat_id'    => $chatId,
                'caption'    => $caption,
                'parse_mode' => 'Markdown',
            ];

            // Use cURL to send the file
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($data, [
                'document' => new \CURLFile($filePath, 'application/x-tar', $fileName),
            ]));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode === 200 && ($result['ok'] ?? false)) {
                \Illuminate\Support\Facades\Log::info('[Telegram] Backup sent successfully: ' . $fileName);
                return true;
            }

            \Illuminate\Support\Facades\Log::error('[Telegram] Failed: ' . ($result['description'] ?? $response));
            return false;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[Telegram] Exception: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Send Backup via Email ───────────────────────────────────────────────
    private function sendToEmail(string $filePath, string $fileName, int $tableCount, string $size, string $label): bool
    {
        $toEmail = env('BACKUP_EMAIL_TO', env('MAIL_FROM_ADDRESS'));

        if (!$toEmail) {
            \Illuminate\Support\Facades\Log::warning('[Email] BACKUP_EMAIL_TO not set in .env');
            return false;
        }

        try {
            Mail::send([], [], function ($message) use ($filePath, $fileName, $tableCount, $size, $label, $toEmail) {
                $message->to($toEmail)
                    ->subject('🗄 Database Backup — ' . $label . ' — ' . config('app.name'))
                    ->html('
                        <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
                            <div style="background:#0f172a;padding:24px;border-radius:12px 12px 0 0;">
                                <h2 style="color:#fff;margin:0;">🗄 Database Backup</h2>
                                <p style="color:#94a3b8;margin:4px 0 0;">'.config('app.name').'</p>
                            </div>
                            <div style="background:#f8fafc;padding:24px;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 12px 12px;">
                                <table style="width:100%;border-collapse:collapse;">
                                    <tr><td style="padding:8px 0;color:#6b7280;font-size:13px;">📛 Backup Name</td><td style="padding:8px 0;font-weight:700;font-size:13px;">'.$label.'</td></tr>
                                    <tr><td style="padding:8px 0;color:#6b7280;font-size:13px;">📊 Tables</td><td style="padding:8px 0;font-weight:700;font-size:13px;">'.$tableCount.' tables</td></tr>
                                    <tr><td style="padding:8px 0;color:#6b7280;font-size:13px;">📦 File Size</td><td style="padding:8px 0;font-weight:700;font-size:13px;">'.$size.'</td></tr>
                                    <tr><td style="padding:8px 0;color:#6b7280;font-size:13px;">🕒 Created</td><td style="padding:8px 0;font-weight:700;font-size:13px;">'.now()->format('d M Y H:i').'</td></tr>
                                    <tr><td style="padding:8px 0;color:#6b7280;font-size:13px;">📁 File</td><td style="padding:8px 0;font-family:monospace;font-size:12px;color:#3b82f6;">'.$fileName.'</td></tr>
                                </table>
                                <div style="margin-top:16px;padding:12px;background:#dcfce7;border-radius:8px;font-size:12px;color:#15803d;">
                                    ✅ Backup file is attached to this email.
                                </div>
                            </div>
                        </div>
                    ')
                    ->attach($filePath, [
                        'as'   => $fileName,
                        'mime' => 'application/x-tar',
                    ]);
            });

            \Illuminate\Support\Facades\Log::info('[Email] Backup sent to: ' . $toEmail);
            return true;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[Email] Failed: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Send Backup to Google Drive (OAuth2 Refresh Token) ─────────────────
    private function sendToDrive(string $filePath, string $fileName): bool
    {
        $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');
        $clientId     = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $folderId     = env('GOOGLE_DRIVE_FOLDER_ID', '');

        if (!$refreshToken) {
            \Illuminate\Support\Facades\Log::warning('[Drive] Not connected. Visit /admin/backup/drive/authorize to connect Google Drive.');
            return false;
        }

        try {
            // ── 1. Get fresh access token using refresh token ────────────────
            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => http_build_query([
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'refresh_token' => $refreshToken,
                    'grant_type'    => 'refresh_token',
                ]),
            ]);
            $tokenResponse = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $accessToken = $tokenResponse['access_token'] ?? null;
            if (!$accessToken) {
                \Illuminate\Support\Facades\Log::error('[Drive] Token refresh failed: ' . json_encode($tokenResponse));
                return false;
            }

            // ── 2. Initiate resumable upload session ─────────────────────────
            $metadata = json_encode(array_filter([
                'name'    => $fileName,
                'parents' => $folderId ? [$folderId] : null,
            ]));
            $fileSize = filesize($filePath);

            // Step A: start session
            $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => true,
                CURLOPT_POSTFIELDS     => $metadata,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json; charset=UTF-8',
                    'X-Upload-Content-Type: application/x-tar',
                    'X-Upload-Content-Length: ' . $fileSize,
                ],
            ]);
            $initiateRaw  = curl_exec($ch);
            $initiateCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $headerSize   = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            curl_close($ch);

            if ($initiateCode !== 200) {
                \Illuminate\Support\Facades\Log::error('[Drive] Initiate failed HTTP ' . $initiateCode . ': ' . substr($initiateRaw, $headerSize));
                return false;
            }

            // Extract Location (upload URI) from response headers
            $uploadUri = null;
            foreach (explode("\r\n", substr($initiateRaw, 0, $headerSize)) as $line) {
                if (stripos($line, 'Location:') === 0) {
                    $uploadUri = trim(substr($line, 9));
                    break;
                }
            }
            if (!$uploadUri) {
                \Illuminate\Support\Facades\Log::error('[Drive] No Location header in initiate response.');
                return false;
            }

            // Step B: upload the actual file bytes
            $fh = fopen($filePath, 'rb');
            $ch = curl_init($uploadUri);
            curl_setopt_array($ch, [
                CURLOPT_PUT            => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_INFILE         => $fh,
                CURLOPT_INFILESIZE     => $fileSize,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/x-tar',
                    'Content-Length: ' . $fileSize,
                ],
            ]);
            $uploadResponse = json_decode(curl_exec($ch), true);
            $httpCode       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fh);

            if ($httpCode === 200 || $httpCode === 201) {
                $fileId = $uploadResponse['id'] ?? 'unknown';
                \Illuminate\Support\Facades\Log::info('[Drive] Uploaded: ' . $fileName . ' (id=' . $fileId . ')');
                return true;
            }

            \Illuminate\Support\Facades\Log::error('[Drive] Upload failed HTTP ' . $httpCode . ': ' . json_encode($uploadResponse));
            return false;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[Drive] Exception: ' . $e->getMessage());
            return false;
        }
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2)    . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2)       . ' KB';
        return $bytes . ' B';
    }

    // ─── Auto Backup (called by scheduler) ──────────────────────────────────
    // ─── Google Drive OAuth: Step 1 — Redirect to Google ────────────────────
    public function driveAuthorize()
    {
        $clientId    = env('GOOGLE_CLIENT_ID');
        $redirectUri = url('/admin/backup/drive/callback');

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/drive.file',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    // ─── Google Drive OAuth: Step 2 — Handle Callback & Save Refresh Token ──
    public function driveCallback(Request $request)
    {
        $code        = $request->input('code');
        $clientId    = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri = url('/admin/backup/drive/callback');

        if (!$code) {
            return redirect()->route('backup.index')
                ->with('error', '❌ Google Drive authorization failed or was cancelled.');
        }

        // Exchange code for tokens
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ]),
        ]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $refreshToken = $response['refresh_token'] ?? null;

        if (!$refreshToken) {
            \Illuminate\Support\Facades\Log::error('[Drive OAuth] No refresh token: ' . json_encode($response));
            return redirect()->route('admin.backup.index')
                ->with('error', '❌ Could not get refresh token. Make sure you clicked "Allow".');
        }

        // Save refresh token to .env file
        $envPath    = base_path('.env');
        $envContent = file_get_contents($envPath);

        if (str_contains($envContent, 'GOOGLE_DRIVE_REFRESH_TOKEN=')) {
            $envContent = preg_replace('/^GOOGLE_DRIVE_REFRESH_TOKEN=.*/m', 'GOOGLE_DRIVE_REFRESH_TOKEN=' . $refreshToken, $envContent);
        } else {
            $envContent .= "\nGOOGLE_DRIVE_REFRESH_TOKEN=" . $refreshToken . "\n";
        }

        file_put_contents($envPath, $envContent);

        \Illuminate\Support\Facades\Log::info('[Drive OAuth] Refresh token saved to .env');

        return redirect()->route('backup.index')
            ->with('success', '✅ Google Drive connected successfully! You can now backup to Google Drive.');
    }

    public function autoBackup(): void
    {
        try {
            // Get all tables
            $allRows   = DB::select('SHOW TABLES');
            $allTables = array_map(fn($r) => array_values((array) $r)[0], $allRows);

            $timestamp  = now()->format('Y-m-d_H-i-s');
            $backupName = "backup_auto_{$timestamp}.tar.gz";
            $backupFile = $this->backupPath . '/' . $backupName;

            $stageDir = storage_path('app/backup_stage_' . time());
            mkdir($stageDir . '/database', 0755, true);

            // Export all tables
            $sqlContent = $this->exportDatabase($allTables);
            file_put_contents($stageDir . "/database/database_{$timestamp}.sql", $sqlContent);

            // Backup info
            file_put_contents($stageDir . '/backup_info.json', json_encode([
                'label'           => 'auto_' . now()->format('Y-m-d'),
                'created_at'      => now()->toDateTimeString(),
                'has_db'          => true,
                'has_images'      => false,
                'has_storage'     => false,
                'table_count'     => count($allTables),
                'tables'          => $allTables,
                'save_server'     => true,
                'save_download'   => false,
                'save_drive'      => false,
                'auto'            => true,
                'app_name'        => config('app.name'),
                'php_version'     => PHP_VERSION,
                'laravel_version' => app()->version(),
            ], JSON_PRETTY_PRINT));

            // Pack into .tar.gz
            $tarOnly = str_replace('.tar.gz', '.tar', $backupFile);
            $phar = new \PharData($tarOnly);
            $phar->buildFromDirectory($stageDir);
            $phar->compress(\Phar::GZ);
            unlink($tarOnly);
            $this->deleteDirectory($stageDir);

            // Keep only the 7 most recent auto backups (auto-cleanup)
            $autoFiles = glob($this->backupPath . '/backup_auto_*.tar.gz') ?: [];
            if (count($autoFiles) > 7) {
                usort($autoFiles, fn($a, $b) => filemtime($a) - filemtime($b)); // oldest first
                foreach (array_slice($autoFiles, 0, count($autoFiles) - 7) as $old) {
                    unlink($old);
                }
            }

            $autoSize  = $this->formatSize(filesize($backupFile));
            $autoLabel = 'auto_' . now()->format('Y-m-d');

            // Send to Telegram if configured
            $this->sendToTelegram($backupFile, $backupName, count($allTables), $autoSize, $autoLabel);

            // Send to Email if configured
            $this->sendToEmail($backupFile, $backupName, count($allTables), $autoSize, $autoLabel);

            // Send to Google Drive if configured
            $this->sendToDrive($backupFile, $backupName);

            \Illuminate\Support\Facades\Log::info('[AutoBackup] Created: ' . $backupName . ' (' . count($allTables) . ' tables)');

        } catch (\Exception $e) {
            if (isset($stageDir) && is_dir($stageDir)) $this->deleteDirectory($stageDir);
            if (isset($tarOnly)   && file_exists($tarOnly))   unlink($tarOnly);
            if (isset($backupFile) && file_exists($backupFile)) unlink($backupFile);
            \Illuminate\Support\Facades\Log::error('[AutoBackup] Failed: ' . $e->getMessage());
        }
    }
}