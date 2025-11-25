<?php
/**
 * Databasekonfigurasjon for Weatherbot.
 * Oppretter en PDO-tilkobling.
 *
 * @package Config
 */

$host = 'localhost';
$db   = 'weatherbot'; 
$user = 'root';    
$pass = '';            
$charset = 'utf8mb4';

// Data Source Name (DSN) for PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO-alternativer
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Kast exceptions ved feil
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Standard hentemetode: assosiativ array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Deaktiver emulering av prepared statements
];

try {
    // Opprett PDO-tilkobling
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Logg eventuelle tilkoblingsfeil
    error_log("DB connection failed: " . $e->getMessage());
}