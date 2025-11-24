<?php
require_once __DIR__ . '/config/db.php';
require_once 'functions/validation.php';
session_start();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validering og sanitizing
    $email = validateEmail($email);
    if (!$email) $errors[] = "Ugyldig e-postadresse.";

    if ($password !== $password2) {
        $errors[] = "Passordene matcher ikke.";
    } else {
        $password = validatePassword($password);
        if (!$password) $errors[] = "Passord må være minst 8 tegn.";
    }

    // Fortsett hvis OK
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "E-post er allerede registrert.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            try {
                $stmt->execute([$email, $hash]);
                $success = "Bruker registrert. Du kan nå logge inn.";
            } catch (Exception $e) {
                error_log("Registreringsfeil: " . $e->getMessage());
                $errors[] = "Noe gikk galt. Prøv igjen senere.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="utf-8">
<title>Registrer - Mitt prosjekt</title>
</head>
<body>
    <h2>Registrer ny bruker</h2>

    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color:green;"><?= htmlspecialchars($success) ?></div>
        <p><a href="index.php">Gå til innlogging</a></p>
    <?php else: ?>

    <form method="post" action="">
        <label>E-post</label><br>
        <input type="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"><br><br>

        <label>Passord</label><br>
        <input type="password" name="password" required><br><br>

        <label>Gjenta passord</label><br>
        <input type="password" name="password2" required><br><br>

        <button type="submit">Registrer</button>
    </form>

    <?php endif; ?>

    <p><a href="index.php">Tilbake til innlogging</a></p>
</body>
</html>