<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Feil brukernavn eller passord.";
    }
}
?>
<form method="POST">
    <input name="username" placeholder="Brukernavn" required>
    <input name="password" type="password" placeholder="Passord" required>
    <button type="submit">Logg inn</button>
    <?php if (!empty($error)) echo "<p>$error</p>"; ?>
</form>

