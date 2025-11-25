<?php
/**
 * Logout-side for vanlig bruker.
 *
 * Logger ut brukeren og sender tilbake til innloggingssiden.
 *
 * @package Weatherbot
 */

session_start();
require_once '../functions/auth.php';

// Logg ut vanlig bruker
logout(false); 

// Send tilbake til innloggingssiden med beskjed
header('Location: ../index.php?message=logged_out_user');
exit;