<?php
// ── TEMPORARY DEBUG FILE v2 ───────────────────────────────────────────────
// Place at: C:\xampp\htdocs\robotech-academy\robotech-academy\public\drive_debug2.php
// Open:     http://localhost:8000/drive_debug2.php
// DELETE after debugging!

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<pre style='font-family:monospace;font-size:13px;padding:20px;'>";
echo "=== Google Drive Upload Debug v2 ===\n\n";

$keyFile  = env('GOOGLE_SERVICE_ACCOUNT_JSON');
$folderId = env('GOOGLE_DRIVE_FOLDER_ID', '');

// ── Step 1: Get Token ─────────────────────────────────────────────────────
$keyData     = json_decode(file_get_contents($keyFile), true);
$clientEmail = $keyData['client_email'];
$privateKey  = $keyData['private_key'];

$now     = time();
$header  = rtrim(strtr(base64_encode(json_encode(['alg'=>'RS256','typ'=>'JWT'])),'+/','-_'),'=');
$payload = rtrim(strtr(base64_encode(json_encode([
    'iss'   => $clientEmail,
    'scope' => 'https://www.googleapis.com/auth/drive.file',
    'aud'   => 'https://oauth2.googleapis.com/token',
    'iat'   => $now,
    'exp'   => $now + 3600,
])),'+/','-_'),'=');
$sigInput = $header.'.'.$payload;
openssl_sign($sigInput, $sig, $privateKey, 'SHA256');
$jwt = $sigInput.'.'.rtrim(strtr(base64_encode($sig),'+/','-_'),'=');

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POSTFIELDS=>http_build_query(['grant_type'=>'urn:ietf:params:oauth:grant-type:jwt-bearer','assertion'=>$jwt]),
    CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded']]);
$tokenResponse = json_decode(curl_exec($ch), true);
curl_close($ch);
$accessToken = $tokenResponse['access_token'];
echo "✅ Token OK\n\n";

// ── Step 2: Create a small test file ─────────────────────────────────────
$testFile = storage_path('app/drive_test.tar.gz');
file_put_contents($testFile, 'TEST BACKUP CONTENT ' . date('Y-m-d H:i:s'));
$fileSize = filesize($testFile);
$fileName = 'drive_test_' . date('His') . '.tar.gz';
echo "Test file: $testFile ($fileSize bytes)\n\n";

// ── Step 3: Initiate resumable upload ────────────────────────────────────
$metadata = json_encode(array_filter([
    'name'    => $fileName,
    'parents' => $folderId ? [$folderId] : null,
]));

echo "Metadata: $metadata\n\n";

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
$curlError    = curl_error($ch);
curl_close($ch);

echo "Initiate HTTP Code: $initiateCode\n";
if ($curlError) echo "cURL Error: $curlError\n";
echo "Response Headers:\n" . substr($initiateRaw, 0, $headerSize) . "\n";
echo "Response Body:\n" . substr($initiateRaw, $headerSize) . "\n\n";

if ($initiateCode !== 200) {
    echo "❌ Initiate failed\n";
    @unlink($testFile);
    die("</pre>");
}

// Extract Location
$uploadUri = null;
foreach (explode("\r\n", substr($initiateRaw, 0, $headerSize)) as $line) {
    if (stripos($line, 'Location:') === 0) {
        $uploadUri = trim(substr($line, 9));
        break;
    }
}
echo "Upload URI: " . ($uploadUri ?: 'NOT FOUND') . "\n\n";

if (!$uploadUri) {
    echo "❌ No Location header\n";
    @unlink($testFile);
    die("</pre>");
}

// ── Step 4: Upload file ───────────────────────────────────────────────────
$fh = fopen($testFile, 'rb');
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
    CURLOPT_VERBOSE        => true,
]);
$verboseLog = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verboseLog);

$uploadRaw  = curl_exec($ch);
$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError2 = curl_error($ch);
curl_close($ch);
fclose($fh);

rewind($verboseLog);
$verboseOutput = stream_get_contents($verboseLog);
fclose($verboseLog);

echo "Upload HTTP Code: $httpCode\n";
if ($curlError2) echo "cURL Error: $curlError2\n";
echo "Upload Response: $uploadRaw\n\n";
echo "cURL Verbose:\n$verboseOutput\n";

if ($httpCode === 200 || $httpCode === 201) {
    $resp = json_decode($uploadRaw, true);
    echo "\n✅ SUCCESS! File uploaded to Drive!\n";
    echo "   File ID: " . ($resp['id'] ?? 'unknown') . "\n";
    echo "   Name: " . ($resp['name'] ?? 'unknown') . "\n";
} else {
    echo "\n❌ Upload failed\n";
}

@unlink($testFile);
echo "</pre>";