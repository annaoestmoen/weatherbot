<?php
session_start();
require_once '../functions/auth.php';

// Logg ut admin
logout(true); // true = admin

// Send tilbake til admin-innloggingssiden eller index med beskjed
header('Location: ../index.php?message=logged_out_admin');
exit;

