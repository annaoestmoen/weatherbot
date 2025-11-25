<?php
/**
 * Funksjoner for å hente værdata og gi vær- og klær-anbefalinger.
 *
 * @package Weather
 */

/**
 * Hent værdata fra Open-Meteo API for gitt latitude og longitude.
 *
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return array|null Returnerer array med current_weather eller null hvis feil
 */
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

/**
 * Gi anbefaling om klær basert på temperatur, værkode og vind.
 *
 * @param float $temp Temperatur i °C
 * @param int|null $code Open-Meteo værkode (valgfritt)
 * @param float $windSpeed Vindhastighet i m/s
 * @return string Tekst med anbefaling
 */
function getClothingAdvice($temp, $code = null, $windSpeed = 0) {
    $advice = "";

    // Grunnleggende råd basert på temperatur
    if ($temp <= -10) {
        $advice = "It’s extremely cold! Wear thermal layers, a heavy winter coat, insulated gloves, a warm hat, and a scarf. Consider winter boots if walking outside.";
    } elseif ($temp <= 0) {
        $advice = "Freezing! A thick coat, gloves, a hat, and a scarf are essential. Stay warm and limit time outdoors if possible.";
    } elseif ($temp <= 5) {
        $advice = "Very cold. Wear a warm coat, scarf, and gloves. A hat is recommended for extra warmth.";
    } elseif ($temp <= 10) {
        $advice = "Chilly weather. A medium-weight jacket or fleece is good. You might want a scarf if you get cold easily.";
    } elseif ($temp <= 15) {
        $advice = "Mild temperature. A light jacket, sweater, or long-sleeve shirt should keep you comfortable.";
    } elseif ($temp <= 20) {
        $advice = "Pleasant and comfy. Long sleeves or a light hoodie are perfect.";
    } elseif ($temp <= 25) {
        $advice = "Warm day! Short sleeves are great, maybe light pants or shorts. Sunglasses recommended if sunny.";
    } elseif ($temp <= 30) {
        $advice = "Hot! Wear shorts, a t-shirt, stay hydrated, and consider a hat or sunglasses for sun protection.";
    } else {
        $advice = "Extremely hot! Light, breathable clothing, plenty of water, and stay in the shade when possible.";
    }

    // Justering for regn eller snø
    if ($code) {
        if (in_array($code, [51,53,55,56,57,61,63,65,66,67,80,81,82])) {
            $advice .= " Rainy conditions — don't forget an umbrella or waterproof jacket.";
        } elseif (in_array($code, [71,73,75,77,85,86])) {
            $advice .= " Snow is falling — wear waterproof boots and warm outer layers.";
        } elseif (in_array($code, [95,96,99])) {
            $advice .= " Thunderstorms — better to stay indoors if possible and avoid open areas.";
        }
    }

    // Justering for vind
    if ($windSpeed >= 10 && $windSpeed < 20) {
        $advice .= " It's windy — a windbreaker or jacket with a hood is recommended.";
    } elseif ($windSpeed >= 20) {
        $advice .= " Strong winds! Make sure your outer layers are secure and wear a hat that won't blow away.";
    }

    return $advice;
}

/**
 * Konverter Open-Meteo værkode til emoji og kort tekst.
 *
 * @param int $code Værkode
 * @param float $windSpeed Vindhastighet
 * @return array Array med 'emoji' og 'text'
 */
function getWeatherEmoji($code, $windSpeed) {
    $emoji = '';
    $text = '';

    // Map værkode til emoji og tekst
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

    // Legg til vind-info hvis merkbar
    if ($windSpeed >= 10) {
        $text .= " Windy!";
    }

    return ['emoji' => $emoji, 'text' => $text];
}