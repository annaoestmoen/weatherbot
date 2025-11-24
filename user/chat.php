<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/weather.php';
require_once __DIR__ . '/../functions/chatfunctions.php';
require_once __DIR__ . '/../functions/validation.php';

// Sjekk login
if (empty($_SESSION['user_logged_in']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['reply' => 'Du må være logget inn for å chatte.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Hent favorittby
$stmt = $pdo->prepare("SELECT favorite_city FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();
$favoriteCity = $userData['favorite_city'] ?? null;

// Bruk favorittby hvis melding er tom
$userMessage = trim($_POST['message'] ?? '');
if ($userMessage === '' && $favoriteCity) {
    $userMessage = $favoriteCity;
}

// Validering
if (!validateChatMessage($userMessage)) {
    echo json_encode(['reply' => "Ugyldig melding. Vennligst skriv vanlig tekst uten HTML eller spesialtegn."]);
    exit;
}

$userMessage = sanitizeString($userMessage);

// --- Hent vær ---
$cityName = $userMessage;
$geoApiUrl = 'https://geocoding-api.open-meteo.com/v1/search?name=' . urlencode($cityName) . '&count=1';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $geoApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$geoResponse = curl_exec($ch);
curl_close($ch);

if (!$geoResponse) {
    echo json_encode(['reply' => "Klarte ikke hente bydata."]);
    exit;
}

$geoData = json_decode($geoResponse, true);
if (!isset($geoData['results'][0])) {
    echo json_encode(['reply' => "Could not find city '{$cityName}'"]);
    exit;
}

$lat = $geoData['results'][0]['latitude'];
$lon = $geoData['results'][0]['longitude'];
$resolvedCity = $geoData['results'][0]['name'];

$weatherData = getWeather($lat, $lon);

if (!$weatherData) {
    echo json_encode(['reply' => "Couldn't fetch weather data for {$resolvedCity}"]);
    exit;
}

$temp = $weatherData['temperature'];
$weatherCode = $weatherData['weathercode'];
$windSpeed = $weatherData['windspeed'];

$advice = getClothingAdvice($temp, $weatherCode, $windSpeed);
$weatherInfo = getWeatherEmoji($weatherCode, $windSpeed);

$emoji = $weatherInfo['emoji'] ?? '🌤️';
$desc  = $weatherInfo['text'] ?? '';

$reply  = "<div style='font-size: 2rem; text-align: center;'>{$emoji} {$temp}°C</div>";
$reply .= "<div style='font-size: 1rem; text-align: center; margin-top: 0.5em;'>";
$reply .= "<strong>{$resolvedCity}</strong><br>";
if (!empty($desc)) $reply .= ucfirst($desc) . "<br>";
$reply .= "{$advice}</div>";

$is_error = (strpos(strtolower($reply), "couldn't") !== false) ? 1 : 0;
logChat($userMessage, $reply, $is_error);

echo json_encode(['reply' => $reply]);