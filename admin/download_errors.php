<?php
session_start();
require_once __DIR__ . '/../functions/auth.php';
requireAdmin(); // sjekk at admin er logget inn
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Hent alle feil chats
    $stmt = $pdo->prepare("SELECT * FROM chat_logs WHERE is_error = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $logs = $stmt->fetchAll();

    if (!$logs) {
        die('Ingen feil chats å laste ned.');
    }

    // Opprett PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Feil Chat-Logs',0,1,'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial','',12);

    foreach ($logs as $log) {
        $pdf->MultiCell(0, 6, "Tid: " . $log['created_at']);
        $pdf->MultiCell(0, 6, "Bruker: " . $log['user_message']);
        $pdf->MultiCell(0, 6, "Bot: " . $log['bot_response']);
        $pdf->Ln(5);
    }

    // Send PDF til nettleser
    $pdf->Output('D', 'feil_chats.pdf');

} catch (PDOException $e) {
    die("Feil ved henting av chats: " . $e->getMessage());
}