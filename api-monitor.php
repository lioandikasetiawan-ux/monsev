<?php
header('Content-Type: application/json');

$api_token = 'RAHASIA_SUPER_AMAN_BANGET_123';

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
        'ip' => '103.144.231.36',
        'metrics' => fetchServerData('http://103.144.231.36/server-stats.php', $api_token)
    ],
    [
        'name' => 'Server 38',
        'ip' => '103.144.231.38',
        'metrics' => fetchServerData('http://103.144.231.38/server-stats.php', $api_token)
    ],
    [
        'name' => 'Server 46',
        'ip' => '103.144.231.46',
        'metrics' => fetchServerData('http://103.144.231.46/server-stats.php', $api_token)
    ]
];

echo json_encode([
    'success' => true,
    'servers' => $servers
], JSON_PRETTY_PRINT);
?>