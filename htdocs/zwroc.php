<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

// Pobieramy ID wypożyczenia z GET
$wyp_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Jeśli brak lub niepoprawne ID — wracamy na dashboard bez komunikatów
if ($wyp_id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Sprawdzamy, czy istnieje aktywne wypożyczenie o tym ID
$res = $conn->query("
    SELECT media_id 
    FROM wypozyczenia 
    WHERE id = {$wyp_id} 
      AND status = 'wypozyczone'
    LIMIT 1
");

if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    $media_id = (int)$row['media_id'];

    // Rozpoczynamy transakcję, aktualizujemy status i dostępność
    $conn->begin_transaction();
    $conn->query("
        UPDATE wypozyczenia
        SET status = 'zwrocone',
            data_zwrotu = NOW()
        WHERE id = {$wyp_id}
    ");
    $conn->query("
        UPDATE media
        SET dostepnosc = 1
        WHERE id = {$media_id}
    ");
    $conn->commit();
}

// Po wszystkim wracamy na dashboard
header('Location: dashboard.php');
exit();


