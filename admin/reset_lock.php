<?php
session_start();
require_once '../config/db.php';
require_once '../functions/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = (int) $_POST['user_id'];

    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET failed_attempts = 0,
                lock_until = NULL
            WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);

        echo "OK"; // send enkel respons til JS
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Feil: " . $e->getMessage();
        exit;
    }
}

http_response_code(400);
echo "Ugyldig forespørsel";
exit;