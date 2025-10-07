<?php
// Custom error handler
function weatherbotErrorHandler($errno, $errstr, $errfile, $errline)
{
    $logMessage = "[" . date("Y-m-d H:i:s") . "] Error: $errstr in $errfile on line $errline\n";
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
    
    // Show user-friendly message (optional)
    if ($errno !== E_NOTICE && $errno !== E_USER_NOTICE) {
        echo "<div style='color: red;'>En feil oppstod. Prøv igjen senere.</div>";
    }
    return true; // Prevent PHP default error handler
}

// Set custom error handler
set_error_handler('weatherbotErrorHandler');

// Optional: handle uncaught exceptions
set_exception_handler(function($exception) {
    $logMessage = "[" . date("Y-m-d H:i:s") . "] Uncaught Exception: " . $exception->getMessage() . "\n";
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
    echo "<div style='color: red;'>En uventet feil oppstod. Prøv igjen senere.</div>";
});