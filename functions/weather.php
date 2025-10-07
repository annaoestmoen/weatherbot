<?php

function getWeather($lat, $lon) {
    $apiUrl = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['current_weather'])) {
            return $data['current_weather'];
        }
    }
    return null;
}

function getClothingAdvice($temp) {
    if ($temp <= -10) {
        return "It’s *really* freezing! Layer up with thermal wear, a thick winter coat, gloves, hat, and scarf. Stay warm out there!";
    }
    if ($temp <= 0) {
        return "Below zero! Bundle up with a heavy jacket, gloves, and a beanie. Hot chocolate recommended.";
    }
    if ($temp <= 5) {
        return "Very cold and crisp. A warm coat, scarf, and maybe a hat will do wonders.";
    }
    if ($temp <= 10) {
        return "A bit chilly. A medium jacket or fleece should keep you comfortable. Maybe grab a scarf too.";
    }
    if ($temp <= 15) {
        return "Mild weather. A light jacket or sweater is perfect — no need for heavy layers.";
    }
    if ($temp <= 20) {
        return "Pleasant and comfy! Long sleeves or a light hoodie will do just fine.";
    }
    if ($temp <= 25) {
        return "Warm day ahead! Short sleeves are perfect — maybe sunglasses too.";
    }
    if ($temp <= 30) {
        return "Hot! Stay cool with shorts, t-shirt, and plenty of water.";
    }
    return "It’s *really* hot out! Find shade, wear light clothing, and keep hydrated.";
}

// New function: convert weather code to emoji + fun text
function getWeatherEmoji($code, $windSpeed) {
    $emoji = '';
    $text = '';

    switch($code) {
        case 0:
        case 1:
        case 2:
            $emoji = "☀️";
            $text = "Sunny!";
            break;
        case 3:
            $emoji = "⛅";
            $text = "Partly cloudy!";
            break;
        case 45:
        case 48:
            $emoji = "🌫️";
            $text = "Foggy!";
            break;
        case 51:
        case 53:
        case 55:
        case 56:
        case 57:
            $emoji = "🌦️";
            $text = "Light rain!";
            break;
        case 61:
        case 63:
        case 65:
        case 66:
        case 67:
            $emoji = "🌧️";
            $text = "Raining!";
            break;
        case 71:
        case 73:
        case 75:
        case 77:
            $emoji = "❄️";
            $text = "Snowing!";
            break;
        case 80:
        case 81:
        case 82:
            $emoji = "🌧️";
            $text = "Pouring rain!";
            break;
        case 85:
        case 86:
            $emoji = "❄️🌨️";
            $text = "Heavy snow!";
            break;
        case 95:
        case 96:
        case 99:
            $emoji = "🌩️";
            $text = "Thunderstorm!";
            break;
        default:
            $emoji = "";
            $text = "";
            break;
    }

    // Add wind info if notable
    if ($windSpeed >= 10) {
        $text .= " Windy!";
    }

    return ['emoji' => $emoji, 'text' => $text];
}
