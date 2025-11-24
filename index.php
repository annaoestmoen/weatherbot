<?php
require_once 'functions/auth.php'; // Henter login-funksjoner
require_once 'functions/validation.php';

$error = '';

// Sjekk om vi har en utloggingsmelding
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

// Behandle login-skjema
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $is_admin = isset($_POST['admin']);

    // -------------------
    // SANITISERING & VALIDERING
    // -------------------
    $email = sanitizeString($email);
    $password = sanitizeString($password);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ugyldig e-postadresse.";
    } elseif (strlen($password) < 6) { // enkel passord-sjekk
        $error = "Passord må være minst 6 tegn.";
    } else {
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
<body>
    <h2 style="text-align:left;">Login</h2>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Vis utloggingsmelding hvis satt -->
    <?php if ($logout_message): ?>
        <p class="success"><?= htmlspecialchars($logout_message) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>E-post</label><br>
        <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>"><br>

        <label>Passord</label><br>
        <input type="password" name="password" required><br>

        <input type="checkbox" name="admin" id="admin">
        <label for="admin">Admin login</label><br><br>

        <button type="submit">Logg inn</button>

        <p>Har du ikke bruker? <a href="register.php">Registrer deg her</a></p>
    </form>
</body>
</html>

