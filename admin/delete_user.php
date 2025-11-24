<?php
session_start();
require_once '../config/db.php';
require_once '../functions/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);

    header("Location: users_overview.php");
    exit;
}

header("HTTP/1.1 400 Bad Request");
echo "Ugyldig forespørsel.";
