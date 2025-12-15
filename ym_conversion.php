<?php
// ym_conversion.php — с принудительным логированием

// --- Настройки ---
$SECRET_TOKEN = 'k9Fz2mPq7vXb4nRt8sLw3yHj6AeC5uNd';
$COUNTER_ID   = '105785371';
$OAUTH_TOKEN = 'y0__xD9l7nJCBj-qTwg3LSM1hXm5Ij7vu67shCDusmFebyuVtWQFQ';

// --- Функция логирования ---
function log_msg($msg) {
    $log_file = __DIR__ . '/ym_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . ' | ' . $msg . "\n", FILE_APPEND | LOCK_EX);
}

// --- Логируем входящий запрос ---
log_msg('REQUEST: ' . $_SERVER['REQUEST_URI']);

// --- Проверка токена ---
if (!isset($_GET['token']) || $_GET['token'] !== $SECRET_TOKEN) {
    log_msg('ERROR: Invalid token');
    http_response_code(403);
    exit('Forbidden');
}

// --- Проверка параметров ---
$ymclid = $_GET['ymclid'] ?? '';
$goal   = $_GET['goal']   ?? '';

if (!$ymclid || !in_array($goal, ['lead', 'conversion'])) {
    log_msg('ERROR: Bad params — ymclid=' . $ymclid . ', goal=' . $goal);
    http_response_code(400);
    exit('Bad params');
}

// --- Сопоставление целей ---
$goal_id = $goal === 'lead' ? 493949488 : 493949480;

// --- Подготовка данных ---
$data = json_encode([
    'goals' => [
        ['id' => $goal_id, 'click_id' => $ymclid, 'price' => 0, 'currency' => 'RUB']
    ]
], JSON_UNESCAPED_UNICODE);

if ($data === false) {
    log_msg('ERROR: JSON encode failed');
    http_response_code(500);
    exit('JSON error');
}

// --- Исправленный URL (БЕЗ пробелов!) ---
$url = "https://api-metrika.yandex.ru/management/v1/counter/{$COUNTER_ID}/conversion";
log_msg("Sending to: {$url}");

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: OAuth ' . $OAUTH_TOKEN,
        'Content-Type: application/json'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

log_msg("API response: HTTP {$http_code} | {$response}");

if ($http_code === 200) {
    echo "OK";
} else {
    http_response_code(500);
    exit('API error');
}
