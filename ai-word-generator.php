<?php
/**
 * DEVTRONIX ARCADE PORTAL - GEMINI AI UNDERCOVER WORD GENERATOR
 * File: ai-word-generator.php
 * Description: Secure server-side proxy to communicate with Google Gemini 1.5 Flash API
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Hanya terima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method Not Allowed. Gunakan POST request.'
    ]);
    exit;
}

// Ambil input JSON
$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

$apiKey = isset($input['apiKey']) ? trim($input['apiKey']) : '';
$category = isset($input['category']) ? trim($input['category']) : 'Acak';

if (empty($apiKey)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Google Gemini API Key belum dimasukkan! Silakan isi di menu pengaturan AI.'
    ]);
    exit;
}

if (empty($category)) {
    $category = 'Acak / Seru';
}

// Bikin system instruction & prompt yang super ketat
$prompt = "Anda adalah AI pembuat kata untuk permainan pesta Undercover dalam Bahasa Indonesia.
Tugas Anda adalah menghasilkan pasangan kata yang sangat mirip/berkaitan erat tetapi memiliki perbedaan yang jelas secara konseptual.
Satu kata diberikan kepada Warga (Citizen) dan kata lainnya diberikan kepada Penyusup (Undercover).

Kategori yang diinginkan pengguna: \"" . $category . "\"

Panduan Pembuatan Kata:
1. Kata harus dalam bahasa Indonesia yang lazim digunakan (boleh kata gaul/santai asal seru dan dimengerti).
2. Kata harus berupa kata tunggal atau frasa 2-kata pendek (misal: \"Bakso\" vs \"Mie Ayam\").
3. Pasangan kata harus memiliki keterkaitan yang sangat kuat sehingga Penyusup tidak mudah menyadari kata mereka berbeda di awal game, tetapi tetap memiliki perbedaan karakter/sifat sehingga bisa didiskusikan (misal: \"Es Teh\" vs \"Es Kopi\", \"Instagram\" vs \"TikTok\", \"Kereta\" vs \"KRL\").
4. Jangan pernah membuat pasangan kata yang terlalu jauh perbedaannya (misal: \"Batu\" vs \"Kucing\") karena itu merusak permainan.
5. Anda wajib mengembalikan output dalam format JSON mentah dengan struktur berikut:
{
  \"citizen\": \"<kata_warga>\",
  \"undercover\": \"<kata_penyusup>\",
  \"category\": \"<nama_kategori>\"
}";

// Bangun payload untuk endpoint API Gemini
$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'responseMimeType' => 'application/json',
        'temperature' => 0.85
    ]
];

// Fungsi pembantu untuk memanggil API Gemini secara terisolasi
function callGeminiAPI($apiKey, $apiVersion, $modelName, $payload) {
    $apiUrl = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$modelName}:generateContent?key=" . $apiKey;
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6); // Timeout singkat per model agar cepat berganti jika gagal
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    return [
        'response' => $response,
        'httpCode' => $httpCode,
        'error' => $curlError
    ];
}

// Urutan model & versi API yang akan dicoba (dari yang paling baru/stabil)
$attempts = [
    ['version' => 'v1beta', 'model' => 'gemini-2.5-flash'],
    ['version' => 'v1',     'model' => 'gemini-2.5-flash'],
    ['version' => 'v1beta', 'model' => 'gemini-1.5-flash'],
    ['version' => 'v1',     'model' => 'gemini-1.5-flash'],
    ['version' => 'v1beta', 'model' => 'gemini-2.0-flash'],
];

$success = false;
$lastError = 'Semua model Gemini yang dicoba gagal merespon.';
$lastHttpCode = 500;
$response = '';

foreach ($attempts as $attempt) {
    $res = callGeminiAPI($apiKey, $attempt['version'], $attempt['model'], $payload);
    
    // Jika ada error jaringan CURL
    if ($res['error']) {
        $lastError = 'Koneksi gagal pada model ' . $attempt['model'] . ': ' . $res['error'];
        $lastHttpCode = 500;
        continue;
    }
    
    // Jika sukses terhubung dan diproses
    if ($res['httpCode'] === 200) {
        $response = $res['response'];
        $lastHttpCode = 200;
        $success = true;
        break; // Keluar dari perulangan karena sudah sukses!
    } else {
        $respDecoded = json_decode($res['response'], true);
        $lastError = isset($respDecoded['error']['message']) 
            ? $respDecoded['error']['message'] 
            : 'Error ' . $res['httpCode'] . ' pada model ' . $attempt['model'];
        $lastHttpCode = $res['httpCode'];
    }
}

if (!$success) {
    http_response_code($lastHttpCode);
    echo json_encode([
        'success' => false,
        'error' => 'API Gemini Error (' . $lastHttpCode . '): ' . $lastError
    ]);
    exit;
}

// Parsing jawaban dari Gemini
$respDecoded = json_decode($response, true);
$aiTextResponse = '';

if (isset($respDecoded['candidates'][0]['content']['parts'][0]['text'])) {
    $aiTextResponse = trim($respDecoded['candidates'][0]['content']['parts'][0]['text']);
}

if (empty($aiTextResponse)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Gagal menerima respon dari AI. Silakan coba lagi.'
    ]);
    exit;
}

// Decode respon JSON dari model AI
$pairData = json_decode($aiTextResponse, true);

if (!$pairData || !isset($pairData['citizen']) || !isset($pairData['undercover'])) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'AI menghasilkan format kata yang salah. Silakan ulangi.',
        'raw' => $aiTextResponse
    ]);
    exit;
}

// Kirim balik ke client
echo json_encode([
    'success' => true,
    'citizen' => trim($pairData['citizen']),
    'undercover' => trim($pairData['undercover']),
    'category' => isset($pairData['category']) ? trim($pairData['category']) : $category
]);
exit;
