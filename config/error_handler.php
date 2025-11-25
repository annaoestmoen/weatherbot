<?php
/**
 * Custom error- og exception-handler for Weatherbot.
 * Logger feil til fil og viser brukervennlig melding.
 *
 * @package Core
 */

/**
 * Håndterer PHP-feil.
 *
 * @param int $errno Feilnummer
 * @param string $errstr Feilbeskrivelse
 * @param string $errfile Fil der feilen oppstod
 * @param int $errline Linjenummer for feilen
 * @return bool True for å forhindre standard PHP-feilhåndtering
 */
function weatherbotErrorHandler($errno, $errstr, $errfile, $errline)
{
    $logMessage = "[" . date("Y-m-d H:i:s") . "] Error: $errstr in $errfile on line $errline\n";
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
    
    // Vis enkel brukermelding (ikke for notices)
    if ($errno !== E_NOTICE && $errno !== E_USER_NOTICE) {
        echo "<div style='color: red;'>En feil oppstod. Prøv igjen senere.</div>";
    }
    return true;
}

// Sett custom error handler
set_error_handler('weatherbotErrorHandler');

// Håndter ubehandlede exceptions
set_exception_handler(function($exception) {
    $logMessage = "[" . date("Y-m-d H:i:s") . "] Uncaught Exception: " . $exception->getMessage() . "\n";
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
    echo "<div style='color: red;'>En uventet feil oppstod. Prøv igjen senere.</div>";
});