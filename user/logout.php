<?php
session_start();
require_once '../functions/auth.php';

// Logg ut vanlig bruker
logout(false); // false = vanlig bruker

// Send tilbake til innloggingssiden med beskjed
header('Location: ../index.php?message=logged_out_user');
exit;


