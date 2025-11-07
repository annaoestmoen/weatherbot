<?php
session_start();
require_once '../functions/auth.php';

// Sjekk om det er admin eller vanlig bruker som logger ut
// $is_admin = true hvis admin-logout, false ellers
$is_admin = isset($_GET['admin']) && $_GET['admin'] == 1;

logout($is_admin);

// Etter logout, send brukeren til login/ index.php med beskjed
header('Location: ../index.php?message=logged_out');
exit;
