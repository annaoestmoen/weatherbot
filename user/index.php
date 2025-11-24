<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/weather.php';
require_once __DIR__ . '/../functions/chatfunctions.php';

if (empty($_SESSION['user_logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: ../index.php?error=not_logged_in');
    exit;
}

$userId = $_SESSION['user_id'];

// Hent brukerens favorittby
$stmt = $pdo->prepare("SELECT favorite_city, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();
$favoriteCity = $userData['favorite_city'] ?? '';
$email = $userData['email'] ?? 'Ukjent bruker';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Weatherbot</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div id="chatbox" data-favorite-city="<?= htmlspecialchars($favoriteCity) ?>">
    <h2>Weatherbot</h2>

    <h3>Hei <?= htmlspecialchars($email) ?>!</h3>

    <!-- Profilboks -->
    <div class="profile-box">
    <a href="profile.php" class="button">Min profil</a>
    
        <?php if ($favoriteCity): ?>
        <p>Favorittbyen din: <strong><?= htmlspecialchars($favoriteCity) ?></strong></p>
        <?php else: ?>
        <p>Du har ikke satt en favorittby ennå. <a href="profile.php">Legg til her</a>.</p>
        <?php endif; ?>
        </div>

    <!-- Meldingsvindu -->
    <div id="messages"></div>

    <!-- Tekstfelt for brukerinput -->
    <input type="text" id="inputBox" placeholder="Ask about the weather in any city!" />

    <!-- Send-knapp -->
    <button id="sendBtn">Send</button>

    <!-- Logg ut-knapp -->
    <a href="logout.php" class="button">Logg ut</a>
</div>

<script src="../assets/js/chat.js"></script>
</body>
</html>
