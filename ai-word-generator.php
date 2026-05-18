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

$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

// Jalankan request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Koneksi ke server Gemini gagal: ' . $curlError
    ]);
    exit;
}

if ($httpCode !== 200) {
    $respDecoded = json_decode($response, true);
    $errorMessage = isset($respDecoded['error']['message']) ? $respDecoded['error']['message'] : 'Gagal terhubung ke API Gemini.';
    http_response_code($httpCode);
    echo json_encode([
        'success' => false,
        'error' => 'API Gemini Error (' . $httpCode . '): ' . $errorMessage
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
