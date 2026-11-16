<?php
// ── TEMPORARY DEBUG FILE ──────────────────────────────────────────────────
// Place this file at: C:\xampp\htdocs\YOUR_PROJECT\public\drive_debug.php
// Then open in browser: http://localhost:8000/drive_debug.php
// DELETE this file after debugging!

// Load Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<pre style='font-family:monospace;font-size:13px;padding:20px;'>";
echo "=== Google Drive Debug ===\n\n";

// 1. Check env values
$keyFile  = env('GOOGLE_SERVICE_ACCOUNT_JSON');
$folderId = env('GOOGLE_DRIVE_FOLDER_ID');

echo "GOOGLE_SERVICE_ACCOUNT_JSON = " . var_export($keyFile, true) . "\n";
echo "GOOGLE_DRIVE_FOLDER_ID      = " . var_export($folderId, true) . "\n\n";

// 2. Check file exists
if (!$keyFile) {
    echo "❌ GOOGLE_SERVICE_ACCOUNT_JSON is empty in .env\n";
    die("</pre>");
}

if (!file_exists($keyFile)) {
    echo "❌ File does NOT exist at that path!\n";
    echo "   PHP is looking for: " . $keyFile . "\n";
    echo "\n   Try these paths:\n";
    echo "   " . storage_path('app/google-service-account.json') . "\n";
    die("</pre>");
}

echo "✅ File exists!\n\n";

// 3. Try to decode JSON
$keyData = json_decode(file_get_contents($keyFile), true);
if (!$keyData) {
    echo "❌ JSON is invalid or corrupted\n";
    die("</pre>");
}
echo "✅ JSON is valid\n";
echo "   client_email = " . ($keyData['client_email'] ?? 'MISSING') . "\n\n";

// 4. Try to get access token
$clientEmail = $keyData['client_email'];
$privateKey  = $keyData['private_key'];

$now     = time();
$exp     = $now + 3600;
$header  = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
$payload = rtrim(strtr(base64_encode(json_encode([
    'iss'   => $clientEmail,
    'scope' => 'https://www.googleapis.com/auth/drive.file',
    'aud'   => 'https://oauth2.googleapis.com/token',
    'iat'   => $now,
    'exp'   => $exp,
])), '+/', '-_'), '=');

$sigInput = $header . '.' . $payload;
openssl_sign($sigInput, $signature, $privateKey, 'SHA256');
$sig = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
$jwt = $sigInput . '.' . $sig;

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $jwt,
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
]);
$tokenResponse = json_decode(curl_exec($ch), true);
$tokenHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Token HTTP Code: " . $tokenHttpCode . "\n";
echo "Token Response: " . json_encode($tokenResponse, JSON_PRETTY_PRINT) . "\n\n";

if (empty($tokenResponse['access_token'])) {
    echo "❌ Failed to get access token\n";
    die("</pre>");
}
echo "✅ Got access token!\n\n";

// 5. Try to list files in folder (simple test)
$accessToken = $tokenResponse['access_token'];
$ch = curl_init('https://www.googleapis.com/drive/v3/files?q=parents+in+\'' . $folderId . '\'');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
]);
$listResponse = json_decode(curl_exec($ch), true);
$listHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "List Folder HTTP Code: " . $listHttpCode . "\n";
echo "List Response: " . json_encode($listResponse, JSON_PRETTY_PRINT) . "\n\n";

if ($listHttpCode === 200) {
    echo "✅ Google Drive folder is accessible!\n";
    echo "   Files in folder: " . count($listResponse['files'] ?? []) . "\n";
} else {
    echo "❌ Cannot access folder\n";
}

echo "</pre>";