<?php
session_start();
require_once __DIR__ . '/../config/db.php'; // Kobler til databasen

function login($username, $password, $is_admin = false) {
    global $pdo;

    $table = $is_admin ? 'admins' : 'users'; // Velg tabell basert på brukerrolle

    // Hent bruker fra databasen
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        return "Feil brukernavn eller passord.";
    }

    // Sjekk om brukeren er låst
    if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {
        $remaining = (strtotime($user['lock_until']) - time()) / 60;
        return "Kontoen er midlertidig låst. Prøv igjen om " . ceil($remaining) . " minutter.";
    }

    // Sjekk passord
    if ($user['password'] !== $password) {
        // Øk failed_attempts
        $failed = $user['failed_attempts'] + 1;

        if ($failed >= 3) {
            // Lås brukeren i 1 time
            $lock_until = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt = $pdo->prepare("UPDATE {$table} SET failed_attempts = ?, lock_until = ? WHERE id = ?");
            $stmt->execute([$failed, $lock_until, $user['id']]);
            return "For mange feil innloggingsforsøk. Kontoen er låst i 1 time.";
        } else {
            // Oppdater kun failed_attempts
            $stmt = $pdo->prepare("UPDATE {$table} SET failed_attempts = ? WHERE id = ?");
            $stmt->execute([$failed, $user['id']]);
            return "Feil brukernavn eller passord. Du har " . (3 - $failed) . " forsøk igjen.";
        }
    }

    // Nullstill failed_attempts og lock_until ved riktig passord
    $stmt = $pdo->prepare("UPDATE {$table} SET failed_attempts = 0, lock_until = NULL WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Sett session
    if ($is_admin) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_id'] = $user['id'];
    } else {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
    }

    return true;
}


// Logg ut funksjon
function logout($is_admin = false) {
    if ($is_admin) {
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_username'], $_SESSION['admin_id']);
    } else {
        unset($_SESSION['user_logged_in'], $_SESSION['user_username'], $_SESSION['user_id']);
    }
}

// Sjekk om admin er logget inn
function requireAdmin() {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: ../index.php?error=login');
        exit;
    }
}

// Sjekk om bruker er logget inn
function requireUser() {
    if (empty($_SESSION['user_logged_in'])) {
        header('Location: ../index.php?error=login');
        exit;
    }
}
