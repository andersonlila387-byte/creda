<?php
$url = 'https://cdn.dribbble.com/userupload/44483904/file/6206085c99775e50f8a633058272bb93.png';
$destination = __DIR__ . '/dribbble_ref.png';

$ch = curl_init($url);
$fp = fopen($destination, 'wb');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

$success = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);
fclose($fp);

if ($success && $httpCode == 200 && filesize($destination) > 0) {
    echo "DOWNLOAD_SUCCESS:" . filesize($destination);
} else {
    echo "DOWNLOAD_FAILED: HTTP $httpCode - Error: $error";
}
