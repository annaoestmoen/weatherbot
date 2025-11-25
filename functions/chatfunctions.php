<?php
/**
 * Logger chatmeldinger mellom bruker og bot.
 *
 * @param string $user_message Meldingen fra brukeren
 * @param string $bot_response Svar fra boten
 * @param int|bool $is_error Sett til 1 hvis dette er en feil-chat, ellers 0
 * @return bool True hvis logg er lagret, false ved feil
 *
 * @package Chat
 */

require_once __DIR__ . '/../config/db.php'; // sørg for PDO-tilkobling

function logChat($user_message, $bot_response, $is_error = 0) {
    global $pdo; // bruk PDO fra db.php

    // Sjekk at PDO er initialisert
    if (!isset($pdo) || !$pdo instanceof PDO) {
        error_log("logChat: \$pdo ikke satt eller ikke PDO");
        return false;
    }

    try {
        // Sett inn chat i databasen
        $stmt = $pdo->prepare("
            INSERT INTO chat_logs (user_message, bot_response, is_error, created_at)
            VALUES (:user_message, :bot_response, :is_error, NOW())
        ");
        $stmt->execute([
            ':user_message' => $user_message,
            ':bot_response' => $bot_response,
            ':is_error' => (int)$is_error
        ]);
        return true;
    } catch (PDOException $e) {
        // Logg feil
        error_log("logChat - PDOException: " . $e->getMessage());
        return false;
    }
}