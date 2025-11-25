<?php
/**
 * Logger ut admin-bruker.
 *
 * @package AdminPanel
 */

session_start();
require_once '../functions/auth.php';

// Logg ut admin (true = admin)
logout(true);

// Redirect til innloggingsside med melding
header('Location: ../index.php?message=logged_out_admin');
exit;