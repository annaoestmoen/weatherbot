<?php
session_start();
require_once '../config/db.php';
require_once '../functions/auth.php';
requireAdmin(); // sjekk at admin er logget inn

// Hent alle brukere
$stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8">
<title>Admin - Brukeroversikt</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Hei <?= htmlspecialchars($_SESSION['admin_email']) ?>!</h2>
<h3>Brukere</h3>

<table border="1">
<tr>
    <th>ID</th>
    <th>Email</th>
    <th>Favorittby</th>
    <th>Feilforsøk</th>
    <th>Låsetid</th>
    <th>Slett bruker</th>
    <th>Åpne bruker</th>
</tr>

<?php foreach ($users as $user): ?>
<tr>
    <td><?= htmlspecialchars($user['id']) ?></td>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><?= htmlspecialchars($user['favorite_city'] ?? '—') ?></td>
    <td><?= htmlspecialchars($user['failed_attempts']) ?></td>
    <td><?= htmlspecialchars($user['lock_until'] ?? '—') ?></td>

    <!-- Slett knapp i egen celle -->
    <td>
        <form action="delete_user.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <button type="submit" onclick="return confirm('Er du sikker på at du vil slette denne brukeren?')">Slett</button>
        </form>
    </td>

    <!-- Nullstill lås i egen celle -->
    <td>
    <button class="reset-lock-btn" data-user-id="<?= $user['id'] ?>">Nullstill lås</button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<br>
<a href="index.php">Tilbake til admin dashboard</a> | 
<a href="logout.php">Logg ut</a>

</body>
</html>

<script>
document.querySelectorAll('.reset-lock-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const userId = btn.dataset.userId;

        if (!confirm('Er du sikker på at du vil nullstille låsen for denne brukeren?')) return;

        try {
            const formData = new FormData();
            formData.append('user_id', userId);

            const response = await fetch('reset_lock.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.text(); // vi sender bare tekst/ok fra PHP

            // Oppdater tabellen visuelt
            const row = btn.closest('tr');
            row.querySelector('td:nth-child(5)').textContent = '—';      // Lock Until
            row.querySelector('td:nth-child(4)').textContent = '0';      // failed_attempts

            alert('Låsen er nullstilt!');
        } catch (err) {
            console.error(err);
            alert('Noe gikk galt.');
        }
    });
});
</script>