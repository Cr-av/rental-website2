<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$user_id  = (int)$_SESSION['user_id'];
$media_id = (int)$_GET['id'];
if ($media_id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Sprawdź dostępność
$res = $conn->query("SELECT dostepnosc FROM media WHERE id = $media_id");
$row = $res ? $res->fetch_assoc() : null;
if (!$row || $row['dostepnosc'] == 0) {
    die("Pozycja niedostępna.");
}

// Wypożyczanie w transakcji
$conn->begin_transaction();

$stmt = $conn->prepare("INSERT INTO wypozyczenia (user_id, media_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $media_id);
if (!$stmt->execute()) {
    $conn->rollback();
    die("Błąd podczas wypożyczania: " . $stmt->error);
}

if (!$conn->query("UPDATE media SET dostepnosc = 0 WHERE id = $media_id")) {
    $conn->rollback();
    die("Błąd podczas aktualizacji dostępności.");
}

$conn->commit();

header("Location: dashboard.php");
exit();



