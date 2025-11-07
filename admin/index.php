<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

// Endret tabellnavn til chat_logs
$stmt = $pdo->query("SELECT * FROM chat_logs WHERE is_error = 1 ORDER BY created_at DESC");
$logs = $stmt->fetchAll();
?>

<h2>Feil-chats</h2>
<table border="1">
<tr><th>ID</th><th>Brukermelding</th><th>Botsvar</th><th>Tid</th></tr>
<?php foreach ($logs as $log): ?>
<tr>
    <td><?= htmlspecialchars($log['id']) ?></td>
    <td><?= htmlspecialchars($log['user_message']) ?></td>
    <td><?= htmlspecialchars($log['bot_response'] ?? '—') ?></td>
    <td><?= htmlspecialchars($log['created_at']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<a href="logout.php">Logg ut</a>

