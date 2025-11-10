<?php
session_start();
// Sjekk at bruker er logget inn
if (empty($_SESSION['user_logged_in'])) {
    header('Location: ../index.php?error=not_logged_in');
    exit;
}

// Hent brukernavn fra session
$username = $_SESSION['user_username'] ?? 'Ukjent bruker';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weatherbot</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div id="chatbox">
        <h2>Weatherbot</h2>

        <h3>Hei <?= htmlspecialchars($username) ?>!</h3>

        <!-- Meldingsvindu -->
        <div id="messages"></div>

        <!-- Tekstfelt for brukerinput -->
        <input type="text" id="inputBox" placeholder="Ask about the weather in any city!" />

        <!-- Send-knapp -->
        <button id="sendBtn">Send</button>

        <!-- Logg ut-knapp -->
        <a href="logout.php">Logg ut</a>
    </div>

    <!-- Link til JavaScript -->
    <script src="../assets/js/chat.js"></script>
</body>
</html>



