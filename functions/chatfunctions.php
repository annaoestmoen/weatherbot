<?php
function logChat($user_message, $bot_response, $is_error = 0) {
    // Forsøk å laste DB-tilkobling
    $dbFile = __DIR__ . '/../config/db.php';
    if (!file_exists($dbFile)) {
        error_log("logChat: fant ikke DB-konfig: $dbFile");
        return false;
    }
    require $dbFile; // forventer at dette setter $pdo

    if (!isset($pdo) || !$pdo instanceof PDO) {
        error_log("logChat: \$ erpdo ikke satt eller ikke en PDO-instans");
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
        // Logg til PHP error_log så du ser feilen i XAMPP logs
        error_log("logChat - PDOException: " . $e->getMessage());
        return false;
    }
}
