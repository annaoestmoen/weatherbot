<?php
require_once __DIR__ . '/../config/db.php'; // sørg for PDO

function logChat($user_message, $bot_response, $is_error = 0) {
    global $pdo; // bruk PDO fra db.php

    if (!isset($pdo) || !$pdo instanceof PDO) {
        error_log("logChat: \$pdo ikke satt eller ikke PDO");
        return false;
    }

    try {
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
        error_log("logChat - PDOException: " . $e->getMessage());
        return false;
    }
}

