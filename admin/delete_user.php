<?php
/**
 * Sletter en bruker basert på ID.
 * Krever at brukeren er administrator.
 *
 * @package UserManagement
 */

session_start();

require_once '../config/db.php';
require_once '../functions/auth.php';

requireAdmin();  // Sjekk at brukeren er admin før noe utføres

// Håndter POST-forespørsel for sletting av bruker
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id']; // Sikrer integer for SQL

    // Slett brukeren fra databasen
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);

    header("Location: users_overview.php"); // Redirect til oversikt
    exit;
}

// Feilmelding ved ugyldig forespørsel
header("HTTP/1.1 400 Bad Request");
echo "Ugyldig forespørsel.";