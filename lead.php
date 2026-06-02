<?php
declare(strict_types=1);

$defaultConfig = [
    'SITE_NAME' => 'Кузовной цех',
    'PRIMARY_PHONE' => '+7 (901) 762-88-47',
    'TG_BOT_TOKEN' => '',
    'TG_CHAT_ID' => '',
];

$userConfig = [];
if (is_file(__DIR__ . '/config.php')) {
    $loadedConfig = require __DIR__ . '/config.php';
    if (is_array($loadedConfig)) {
        $userConfig = $loadedConfig;
    }
}

$config = array_replace($defaultConfig, $userConfig);

function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    return str_contains($accept, 'application/json') || strtolower($xhr) === 'xmlhttprequest';
}

function clean_text(?string $value, int $limit = 500): string
{
    $value = trim((string) $value);
    $value = strip_tags($value);
    $value = preg_replace('/\s+/u', ' ', $value) ?? '';

    if (mb_strlen($value, 'UTF-8') > $limit) {
        $value = mb_substr($value, 0, $limit, 'UTF-8');
    }

    return $value;
}

function normalize_phone(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (strlen($digits) === 11 && $digits[0] === '8') {
        $digits = '7' . substr($digits, 1);
    }

    return $digits;
}

function respond(bool $ok, string $message, int $status = 200): never
{
    if (wants_json()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $target = $ok ? '/?sent=1#lead' : '/?error=1#lead';
    header('Location: ' . $target, true, 303);
    exit;
}

function send_telegram(string $token, string $chatId, string $message): bool
{
    if ($token === '' || $chatId === '') {
        return false;
    }

    $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
    $payload = http_build_query([
        'chat_id' => $chatId,
        'text' => $message,
        'disable_web_page_preview' => 'true',
    ]);

    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $result = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        if ($result === false || $status < 200 || $status >= 300) {
            return false;
        }

        $decoded = json_decode((string) $result, true);
        return is_array($decoded) && ($decoded['ok'] ?? false) === true;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 10,
        ],
    ]);

    $result = @file_get_contents($url, false, $context);
    if ($result === false) {
        return false;
    }

    $decoded = json_decode($result, true);
    return is_array($decoded) && ($decoded['ok'] ?? false) === true;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Метод не поддерживается.', 405);
}

if (clean_text($_POST['website'] ?? '', 100) !== '') {
    respond(true, 'Заявка отправлена.');
}

$name = clean_text($_POST['name'] ?? '', 80);
$phoneRaw = clean_text($_POST['phone'] ?? '', 40);
$phoneDigits = normalize_phone($phoneRaw);
$service = clean_text($_POST['service'] ?? 'Не указано', 120);
$car = clean_text($_POST['car'] ?? '', 120);
$comment = clean_text($_POST['comment'] ?? '', 800);
$privacy = isset($_POST['privacy']) && $_POST['privacy'] === '1';

if (!$privacy) {
    respond(false, 'Подтвердите согласие на обработку данных.', 422);
}

if (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
    respond(false, 'Укажите корректный телефон для связи.', 422);
}

$phoneForMessage = '+' . $phoneDigits;
$messageLines = [
    'Новая заявка с сайта: ' . clean_text((string) $config['SITE_NAME'], 80),
    '',
    'Имя: ' . ($name !== '' ? $name : 'не указано'),
    'Телефон: ' . $phoneForMessage,
    'Услуга: ' . ($service !== '' ? $service : 'не указано'),
    'Автомобиль: ' . ($car !== '' ? $car : 'не указан'),
    'Комментарий: ' . ($comment !== '' ? $comment : 'нет'),
    '',
    'Источник: лендинг',
];

$ok = send_telegram(
    clean_text((string) $config['TG_BOT_TOKEN'], 200),
    clean_text((string) $config['TG_CHAT_ID'], 80),
    implode("\n", $messageLines)
);

if (!$ok) {
    respond(false, 'Заявка не отправилась. Позвоните по телефону ' . clean_text((string) $config['PRIMARY_PHONE'], 40) . '.', 500);
}

respond(true, 'Заявка отправлена. Скоро с вами свяжутся.');
