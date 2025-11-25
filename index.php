<?php
/**
 * Login-side for brukere og administratorer
 *
 * Håndterer innlogging med validering og videresending til korrekt dashboard.
 *
 * @package Weatherbot
 */

require_once 'functions/auth.php';
require_once 'functions/validation.php';

$error = '';

// Håndter utloggingsmeldinger
$logout_message = '';
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'logged_out_user':
            $logout_message = 'Du er nå logget ut som bruker.';
            break;
        case 'logged_out_admin':
            $logout_message = 'Du er nå logget ut som administrator.';
            break;
    }
}

// Behandle login-skjema ved POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $is_admin = isset($_POST['admin']);

    // Sanitering
    $email = sanitizeString($email);
    $password = sanitizeString($password);

    // Enkel validering
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ugyldig e-postadresse.";
    } elseif (strlen($password) < 6) { 
        $error = "Passord må være minst 6 tegn.";
    } else {
        // Forsøk login
        $result = login($email, $password, $is_admin);

        if ($result === true) {
            // Videresend til riktig dashboard
            header('Location: ' . ($is_admin ? 'admin/index.php' : 'user/index.php'));
            exit;
        } else {
            $error = $result; // Sett feilmelding fra login-funksjonen
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Weatherbot - Login</title>
    <link rel="stylesheet" href="/weatherbot/assets/css/style.css">
</head>
    <body>
        <div class="center-text">
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- Vis utloggingsmelding hvis satt -->
        <?php if ($logout_message): ?>
            <p class="success"><?= htmlspecialchars($logout_message) ?></p>
        <?php endif; ?>

        <!-- Login-skjema -->
        <form method="post">
            <label>E-post</label><br>
            <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>"><br>

            <label>Passord</label><br>
            <input type="password" name="password" required><br>

            <!-- Admin checkbox -->
            <input type="checkbox" name="admin" id="admin">
            <label for="admin">Admin login</label><br><br>

            <button type="submit">Logg inn</button>

            <!-- Link til registrering -->
            <p>Har du ikke bruker? <a href="register.php">Registrer deg her</a></p>
        </form>
    </body>
</html>