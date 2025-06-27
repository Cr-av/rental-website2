<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$imie    = $_SESSION['imie'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST["name"]);
    $new_surname = trim($_POST["surname"]);
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    $current_password = $_POST["current_password"];

    $stmt = $conn->prepare("SELECT haslo_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (!password_verify($current_password, $hashed_password)) {
        $message = "Błędne aktualne hasło!";
    } else {
        if ($new_password !== $confirm_password) {
            $message = "Nowe hasła nie są takie same!";
        } else {
            $update_query = "UPDATE users SET imie = ?, nazwisko = ?, haslo_hash = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssi", $new_name, $new_surname, $new_hashed_password, $user_id);
            if ($stmt->execute()) {
                $message = "Dane konta zostały zaktualizowane.";
            } else {
                $message = "Wystąpił błąd podczas aktualizacji.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj dane konta</title>
    <link rel="stylesheet" href="css/style_dash2.css">
</head>
<body>

    <header>
        <h2>Witaj, <?= htmlspecialchars($imie) ?>!</h2>
        <nav>
            <a href="index.php">Strona główna</a>
            <a href="funds.php">Wpłać pieniądze</a>
            <a href="my_rentals.php">Moje wypożyczenia</a>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
                <a href="admin.php">Panel administratora</a>
            <?php endif; ?>
            <a href="logout.php">Wyloguj</a>
        </nav>
    </header>


<div class="card2">
    <h2 style="text-align: center; margin-bottom: 1.5rem;">Zmiana danych logowania</h2>
    <form method="POST" class="edit-form" style="display: flex; flex-direction: column; gap: 15px;">
        <input type="text" name="name" placeholder="Nowe imię" required style="padding: 10px; border-radius: 8px; border: none;">
        <input type="text" name="surname" placeholder="Nowe nazwisko" required style="padding: 10px; border-radius: 8px; border: none;">
        <input type="password" name="new_password" placeholder="Nowe hasło" required style="padding: 10px; border-radius: 8px; border: none;">
        <input type="password" name="confirm_password" placeholder="Powtórz nowe hasło" required style="padding: 10px; border-radius: 8px; border: none;">
        <input type="password" name="current_password" placeholder="Obecne hasło" required style="padding: 10px; border-radius: 8px; border: none;">

        <button type="submit" class="btn">Zapisz zmiany</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="error-message" style="margin-top: 20px; text-align: center;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
</div>















