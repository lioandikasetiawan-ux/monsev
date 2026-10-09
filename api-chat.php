<?php
error_reporting(0);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=UTF-8');

// Memuat Composer Autoload untuk membaca library Dotenv
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();
    }
}

$rawInput  = file_get_contents('php://input');
$inputData = json_decode($rawInput, true);
$messages  = $inputData['messages'] ?? [];

if (empty($messages) || !is_array($messages)) {
    echo json_encode(['status' => 'error', 'reply' => 'Pesan tidak boleh kosong.']);
    exit;
}

// Mengambil API Key dari .env atau environment variable
$apiKey = $_ENV['GROQ_API_KEY'] ?? getenv('GROQ_API_KEY') ?: '';

if (empty($apiKey)) {
    echo json_encode(['status' => 'error', 'reply' => 'API Key belum dikonfigurasi di file .env']);
    exit;
}

$url = 'https://api.groq.com/openai/v1/chat/completions';

// Model teks & model vision (sesuai daftar model yang tersedia di akun)
$textModel   = 'openai/gpt-oss-120b';
$visionModel = 'qwen/qwen3.8-27b';

// Batas ukuran gambar base64 (4MB)
$maxImageBytes = 4 * 1024 * 1024;

// Hemat token (penting untuk akun gratis)
$maxHistoryMessages = 8;     // jumlah pesan terakhir yang dikirim
$visionMaxTokens    = 700;   // batas panjang jawaban saat analisa gambar

$systemPrompt = [
    "role" => "system",
    "content" => "Nama Anda adalah UJANG E AY, seorang AI Assistant profesional yang cerdas, ramah, akurat, dan menguasai konteks dunia kerja di Indonesia termasuk instansi pemerintah seperti BBWS (Balai Besar Wilayah Sungai) di bawah Kementerian PUPR. Anda mengingat riwayat percakapan sebelumnya dan siap membantu pengguna dengan sigap. Anda juga dapat menganalisa gambar yang dilampirkan pengguna (misalnya foto lapangan, screenshot dashboard, peta, grafik, atau dokumen) dan menjelaskannya secara rinci dalam Bahasa Indonesia. Anda hanya dapat membaca/menganalisa gambar, bukan membuat gambar.

ATURAN PANGGILAN KHUSUS:
- Pengguna Anda adalah pimpinan Anda, yang harus selalu dipanggil 'Bos Ganteng'.
- Setiap kali menyapa atau membalas salam, sebut 'Bos Ganteng'. Contoh: jika pengguna bilang 'selamat pagi', balas 'Selamat pagi juga, Bos Ganteng! Ada yang bisa Ujang bantu hari ini?'.
- Dalam jawaban biasa, sisipkan panggilan 'Bos Ganteng' secara natural di awal atau akhir jawaban, tanpa berlebihan di setiap kalimat.
- Tetap berikan jawaban teknis yang akurat dan profesional.
"
];

// Hanya ambil N pesan terakhir, tapi ingat indeks pesan terakhir untuk gambar
$messages  = array_values($messages);
$messages  = array_slice($messages, -$maxHistoryMessages);
$lastIndex = count($messages) - 1;

// Sanitasi pesan: hanya role user/assistant, gambar hanya valid di pesan TERAKHIR
$cleanMessages = [];
$hasImage = false;

foreach ($messages as $i => $m) {
    if (!is_array($m) || !isset($m['role'], $m['content']) || !in_array($m['role'], ['user', 'assistant'], true)) {
        continue;
    }

    $text  = (string)$m['content'];
    $image = $m['image'] ?? null;

    $validImage = false;
    if ($i === $lastIndex && $m['role'] === 'user' && is_string($image)
        && preg_match('#^data:image/(jpeg|png|webp|gif);base64,[A-Za-z0-9+/=]+$#', $image)
        && strlen($image) <= $maxImageBytes) {
        $validImage = true;
    }

    if ($validImage) {
        $hasImage = true;
        $cleanMessages[] = [
            'role'    => 'user',
            'content' => [
                ['type' => 'text', 'text' => $text],
                ['type' => 'image_url', 'image_url' => ['url' => $image]]
            ]
        ];
    } else {
        $cleanMessages[] = ['role' => $m['role'], 'content' => $text];
    }
}

if (empty($cleanMessages)) {
    echo json_encode(['status' => 'error', 'reply' => 'Pesan tidak valid.']);
    exit;
}

$fullMessages = array_merge([$systemPrompt], $cleanMessages);

$data = [
    "model"       => $hasImage ? $visionModel : $textModel,
    "messages"    => $fullMessages,
    "temperature" => 0.5
];
if ($hasImage) {
    $data["max_tokens"] = $visionMaxTokens;
}

// Fungsi kirim request ke Groq
function callGroq($url, $apiKey, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    return [$response, $httpCode, $curlError];
}

list($response, $httpCode, $curlError) = callGroq($url, $apiKey, $data);

// Jika kena rate limit (429), tunggu sebentar lalu coba sekali lagi
if (empty($curlError) && $httpCode === 429) {
    sleep(7);
    list($response, $httpCode, $curlError) = callGroq($url, $apiKey, $data);
}

if (!empty($curlError)) {
    echo json_encode(['status' => 'error', 'reply' => 'Gagal koneksi cURL: ' . $curlError]);
    exit;
}

// Pengecekan 429 HARUS sebelum pengecekan !== 200
if ($httpCode === 429) {
    echo json_encode([
        'status' => 'error',
        'reply'  => 'Batas pemakaian model gratis sedang tercapai, Bos Ganteng. Tunggu sekitar 1 menit lalu coba lagi.'
    ]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode(['status' => 'error', 'reply' => 'Groq API Error HTTP Code: ' . $httpCode . ' | Respons: ' . $response]);
    exit;
}

$responseData = json_decode($response, true);
$aiReply = $responseData['choices'][0]['message']['content'] ?? 'Maaf, format respons dari API tidak valid.';

// Buang blok <think>...</think> jika model menyertakan proses berpikirnya
$aiReply = preg_replace('#<think>.*?</think>#s', '', $aiReply);

echo json_encode([
    'status' => 'success',
    'reply'  => trim($aiReply)
]);