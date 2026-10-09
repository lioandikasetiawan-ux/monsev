<?php
header('Content-Type: application/json; charset=UTF-8');

// Memuat Composer Autoload untuk membaca file .env
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();
    }
}

// Ambil token dari environment
$api_token = $_ENV['SERVER_MONITOR_TOKEN'] ?? getenv('SERVER_MONITOR_TOKEN') ?: '';

// Ambil IP server dari environment (dengan fallback IP default jika .env belum diset)
$server36_ip = $_ENV['SERVER_36_IP'] ?? getenv('SERVER_36_IP') ?: '103.144.231.36';
$server38_ip = $_ENV['SERVER_38_IP'] ?? getenv('SERVER_38_IP') ?: '103.144.231.38';
$server46_ip = $_ENV['SERVER_46_IP'] ?? getenv('SERVER_46_IP') ?: '103.144.231.46';

function fetchServerData($url, $token) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "X-API-Token: " . $token . "\r\n",
            'timeout' => 3
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response === FALSE) {
        return [
            'status' => 'Offline',
            'cpu_load' => '-',
            'cpu_load_5m' => '-',
            'ram_total' => '-',
            'ram_used' => '-',
            'ram_pct' => '0%',
            'swap_total' => '-',
            'swap_used' => '-',
            'swap_pct' => '0%',
            'disk_total' => '-',
            'disk_used' => '-',
            'disk_pct' => '0%',
            'web_server' => 'Offline',
            'geoserver' => 'Offline'
        ];
    }
    
    $data = json_decode($response, true);
    if (!is_array($data)) {
        return [
            'status' => 'Error Format',
            'cpu_load' => '-',
            'cpu_load_5m' => '-',
            'ram_total' => '-',
            'ram_used' => '-',
            'ram_pct' => '0%',
            'swap_total' => '-',
            'swap_used' => '-',
            'swap_pct' => '0%',
            'disk_total' => '-',
            'disk_used' => '-',
            'disk_pct' => '0%',
            'web_server' => 'Offline',
            'geoserver' => 'Offline'
        ];
    }
    
    return $data;
}

$servers = [
    [
        'name' => 'Server 36 (Pusat)',
        'ip' => $server36_ip,
        'metrics' => fetchServerData("http://{$server36_ip}/server-stats.php", $api_token)
    ],
    [
        'name' => 'Server 38',
        'ip' => $server38_ip,
        'metrics' => fetchServerData("http://{$server38_ip}/server-stats.php", $api_token)
    ],
    [
        'name' => 'Server 46',
        'ip' => $server46_ip,
        'metrics' => fetchServerData("http://{$server46_ip}/server-stats.php", $api_token)
    ]
];

echo json_encode([
    'success' => true,
    'servers' => $servers
], JSON_PRETTY_PRINT);
?>