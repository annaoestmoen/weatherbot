<?php
session_start();
require_once __DIR__ . '/../config/db.php';

function login($username, $password, $is_admin = false) {
    global $pdo;

    $table = $is_admin ? 'admins' : 'users';

    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || $user['password'] !== $password) {
        return "Feil brukernavn eller passord.";
    }

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

function logout($is_admin = false) {
    if ($is_admin) {
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_username'], $_SESSION['admin_id']);
    } else {
        unset($_SESSION['user_logged_in'], $_SESSION['user_username'], $_SESSION['user_id']);
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
