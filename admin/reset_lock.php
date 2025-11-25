<?php
/**
 * Tilbakestiller låste brukere (failed_attempts og lock_until).
 * Krever at admin er logget inn.
 *
 * @package UserManagement
 */

session_start();
require_once '../config/db.php';
require_once '../functions/auth.php';
requireAdmin();

// Håndter POST-forespørsel for å tilbakestille en bruker
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = (int) $_POST['user_id'];

    try {
        // Nullstill feilforsøk og lås
        $stmt = $pdo->prepare("
            UPDATE users
            SET failed_attempts = 0,
                lock_until = NULL
            WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);

        echo "OK"; // Enkel respons til JavaScript
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Feil: " . $e->getMessage();
        exit;
    }
}

// Feilmelding for ugyldig forespørsel
http_response_code(400);
echo "Ugyldig forespørsel";
exit;