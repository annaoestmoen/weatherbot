<?php
// functions/auth.php
session_start();
require_once __DIR__ . '/../config/db.php';

/**
 * Forsøk å logge inn bruker eller admin.
 * @param string $email
 * @param string $password
 * @param bool $is_admin
 * @return true|string True ved suksess, feilmelding ellers.
 */
function login($email, $password, $is_admin = false) {
    global $pdo;

    $table = $is_admin ? 'admins' : 'users';

    // Hent bruker basert på email
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return "Feil e-post eller passord.";
    }

    // Sjekk lås
    if (!empty($user['lock_until']) && strtotime($user['lock_until']) > time()) {
        $remaining = ceil((strtotime($user['lock_until']) - time()) / 60);
        return "Kontoen er midlertidig låst. Prøv igjen om {$remaining} minutter.";
    }

    // Sjekk passord med password_verify
    if (!password_verify($password, $user['password'])) {
        // Øk failed_attempts
        $failed = (int)$user['failed_attempts'] + 1;

        if ($failed >= 3) {
            $lock_until = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt = $pdo->prepare("UPDATE {$table} SET failed_attempts = ?, lock_until = ? WHERE id = ?");
            $stmt->execute([$failed, $lock_until, $user['id']]);
            return "For mange feil innloggingsforsøk. Kontoen er låst i 1 time.";
        } else {
            $stmt = $pdo->prepare("UPDATE {$table} SET failed_attempts = ? WHERE id = ?");
            $stmt->execute([$failed, $user['id']]);
            return "Feil e-post eller passord. Du har " . (3 - $failed) . " forsøk igjen.";
        }
    }

    // Riktig passord: nullstill failed_attempts og lock_until
    $stmt = $pdo->prepare("UPDATE {$table} SET failed_attempts = 0, lock_until = NULL WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Sett sessioner
    if ($is_admin) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['admin_id'] = $user['id'];
    } else {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];
    }

    return true;
}

function logout($is_admin = false) {
    if ($is_admin) {
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_email'], $_SESSION['admin_id']);
    } else {
        unset($_SESSION['user_logged_in'], $_SESSION['user_email'], $_SESSION['user_id']);
    }
}

function requireAdmin() {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: ../index.php?error=login');
        exit;
    }
}

function requireUser() {
    if (empty($_SESSION['user_logged_in'])) {
        header('Location: ../index.php?error=login');
        exit;
    }
}