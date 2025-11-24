<?php
session_start();
require_once '../config/db.php';

// Sjekk at admin er logget inn 
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php?error=not_logged_in');
    exit;
}

// Hent admin-navn fra session
$adminName = $_SESSION['admin_email'];

// Hent feil-logs fra chat_logs 
$stmt = $pdo->query("SELECT * FROM chat_logs WHERE is_error = 1 ORDER BY created_at DESC");
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Vis navnet på innlogget admin -->
<h2>Hei <?= htmlspecialchars($adminName) ?>!</h2>

<h3>Feil-chats</h3>

<!-- Tabell med feil-logger fra databasen -->
<table border="1">
<tr><th>ID</th><th>Brukermelding</th><th>Botsvar</th><th>Tid</th></tr>

<!-- Loop gjennom logs og vis innhold -->
<?php foreach ($logs as $log): ?>
<tr>
    <td><?= htmlspecialchars($log['id']) ?></td>
    <td><?= htmlspecialchars($log['user_message']) ?></td>
    <td><?= htmlspecialchars($log['bot_response'] ?? '—') ?></td>
    <td><?= htmlspecialchars($log['created_at']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<!-- Link for å logge ut -->
<a href="logout.php">Logg ut</a>
</body>
</html>

