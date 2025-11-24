<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Sjekk at bruker er logget inn
if (empty($_SESSION['user_logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: ../index.php?error=not_logged_in');
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Hent eksisterende favorittby
$stmt = $pdo->prepare("SELECT favorite_city FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();
$currentFavorite = $userData['favorite_city'] ?? '';

// Behandle skjema
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCity = trim($_POST['favorite_city'] ?? '');
    
    if ($newCity === '') {
        $error = "Favorittbyen kan ikke være tom.";
    } elseif (strlen($newCity) > 100) {
        $error = "Favorittbyen er for lang (maks 100 tegn).";
    } else {
        // Rens input
        $newCity = htmlspecialchars($newCity, ENT_QUOTES, 'UTF-8');

        // Oppdater i databasen
        $stmt = $pdo->prepare("UPDATE users SET favorite_city = ? WHERE id = ?");
        if ($stmt->execute([$newCity, $userId])) {
            $success = "Favorittbyen ble oppdatert!";
            $currentFavorite = $newCity;
        } else {
            $error = "Noe gikk galt, prøv igjen.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rediger favorittby</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="profile-container">
        <h2>Rediger favorittby</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="favorite_city">Favorittby:</label><br>
            <input type="text" name="favorite_city" id="favorite_city" value="<?= htmlspecialchars($currentFavorite) ?>" required><br><br>
            <button type="submit">Lagre</button>
        </form>

        <br>
        <a href="index.php">← Tilbake til chatten</a>
    </div>
</body>
</html>