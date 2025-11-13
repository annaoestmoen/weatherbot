<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/weather.php';
require_once __DIR__ . '/../functions/chatfunctions.php';

// Sjekk om bruker har sendt melding
if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
    echo json_encode(['reply' => 'Please type something!']);
    exit;
}

$userMessage = trim($_POST['message']);
$reply = "";

// Hvis bruker sender lat/lon fra "Bruk min lokasjon"
if (isset($_POST['lat']) && isset($_POST['lon'])) {
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $weatherData = getWeather($lat, $lon);

    if ($weatherData) {
        $temp = $weatherData['temperature'];
        $weatherCode = $weatherData['weathercode'];
        $windSpeed = $weatherData['windspeed'];

        $advice = getClothingAdvice($temp, $weatherCode, $windSpeed);
        $weatherInfo = getWeatherEmoji($weatherCode, $windSpeed);

        $reply = "
        <div class='weather-card'>
          <div class='weather-emoji'>{$weatherInfo['emoji']}</div>
          <div class='temp'>{$temp}°C</div>
          <div class='desc'>{$weatherInfo['text']}</div>
          <div class='advice'>{$advice}</div>
        </div>
        ";
    } else {
        $reply = "Couldn't fetch weather data for your location. 🛰️";
    }

} else {
    // Rydd opp i meldingen og hent bynavn
    $messageClean = strtolower($userMessage);
    $patterns = [
        '/what about\s+/i',
        '/how(\'s| is) the weather in\s+/i',
        '/do i need.* in\s+/i',
        '/weather in\s+/i',
        '/what should i wear today in\s+/i',
        '/what should i wear in\s+/i',
        '/hvordan er været i\s+/i',
        '/hva er temperaturen i\s+/i',
        '/trenger jeg.* i\s+/i',
        '/vær(et)? i\s+/i'
    ];

    foreach ($patterns as $pattern) {
        $messageClean = preg_replace($pattern, '', $messageClean);
    }

    $cityName = trim($messageClean);
    if (empty($cityName)) $cityName = $config['default_city'];
    $cityName = preg_replace('/[?.!,]/', '', $cityName);
    $cityName = trim($cityName);

    // Hent bykoordinater
    $geoApiUrl = 'https://geocoding-api.open-meteo.com/v1/search?name=' . urlencode($cityName) . '&count=1';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $geoApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $geoResponse = curl_exec($ch);
    curl_close($ch);

    if ($geoResponse) {
        $geoData = json_decode($geoResponse, true);
        if (isset($geoData['results'][0])) {
            $lat = $geoData['results'][0]['latitude'];
            $lon = $geoData['results'][0]['longitude'];
            $resolvedCity = $geoData['results'][0]['name'];

            $weatherData = getWeather($lat, $lon);

            if ($weatherData) {
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
            } else {
                $reply = "Couldn't fetch weather data right now for {$resolvedCity}. Please try again later. 🛰️";
            }

        } else {
            $reply = "Oops! I couldn't find the city '{$cityName}'. Are you sure it exists? 🤔";
        }

    } else {
        $reply = "Uh oh... Something went wrong while checking the weather. 🌩️";
    }
}

// **Logg chat etter at $reply er ferdig**
$is_error = (strpos(strtolower($reply), "couldn't") !== false || strpos(strtolower($reply), "uh oh") !== false) ? 1 : 0;
logChat($userMessage, $reply, $is_error);

// Send JSON-respons
echo json_encode(['reply' => $reply]);