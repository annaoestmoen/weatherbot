<?php
require_once 'functions/auth.php';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $is_admin = isset($_POST['admin']); // checkbox eller knapp for admin

    $result = login($username, $password, $is_admin);

    if ($result === true) {
        if ($is_admin) {
            header('Location: admin/index.php');
        } else {
            header('Location: user/index.php');
        }
        exit;
    } else {
        $error = $result;
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
        <label>Brukernavn</label><br>
        <input type="text" name="username" required><br>

        <label>Passord</label><br>
        <input type="password" name="password" required><br>

        <input type="checkbox" name="admin" id="admin">
        <label for="admin">Admin login</label><br><br>

        <button type="submit">Logg inn</button>
    </form>
</body>
</html>

