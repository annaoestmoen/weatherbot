<?php
require_once __DIR__ . '/config/db.php';
session_start();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password2 = isset($_POST['password2']) ? $_POST['password2'] : '';

    // Validering e-post
    if ($email === '') {
        $errors[] = "E-post kan ikke være tom.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ugyldig e-postadresse.";
    }

    // Validering passord
    if ($password === '' || $password2 === '') {
        $errors[] = "Begge passordfelt må fylles ut.";
    } elseif ($password !== $password2) {
        $errors[] = "Passordene matcher ikke.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Passord må være minst 8 tegn (anbefalt).";
    }

    // Fortsett hvis OK
    if (empty($errors)) {
        // Sjekk om epost finnes
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = "E-post er allerede registrert.";
        } else {
            // Hash passord
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