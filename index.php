<?php
// index.php — тест исходящего запроса к Яндекс.Метрике
echo "<h2>Тест запроса к Яндекс.Метрике</h2>";

$ch = curl_init('https://api-metrika.yandex.ru/management/v1/counters');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP-код: " . $http_code . "<br>";
echo "Ответ: <pre>" . htmlspecialchars(substr($response, 0, 1000)) . "</pre>";
