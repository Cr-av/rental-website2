<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['media_id'], $_POST['dni'])) {
    $user_id  = $_SESSION['user_id'];
    $media_id = (int)$_POST['media_id'];
    $dni      = (int)$_POST['dni'];

    if ($dni <= 0) {
        die("Nieprawidłowy okres wypożyczenia.");
    }

    $data_wyp = date('Y-m-d H:i:s');
    $data_zwr = date('Y-m-d H:i:s', strtotime("+$dni days"));

    $stawka_dzienna = 2.00;
    $oplata = $dni * $stawka_dzienna;

    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($userBalance);
    $stmt->fetch();
    $stmt->close();

    if ($userBalance >= $oplata) {
        $updateBalanceQuery = "UPDATE users SET balance = balance - ? WHERE id = ?";
        $updateBalanceStmt = $conn->prepare($updateBalanceQuery);
        $updateBalanceStmt->bind_param("di", $oplata, $user_id);
        $updateBalanceStmt->execute();
        $updateBalanceStmt->close();

        $stmt = $conn->prepare("
            INSERT INTO wypozyczenia (user_id, media_id, data_wypozyczenia, data_zwrotu, status, oplata)
            VALUES (?, ?, ?, ?, 'wypozyczone', ?)
        ");

        if (!$stmt) {
            die("Błąd zapytania: " . $conn->error);
        }

        //issd: i - int, s - string, d - decimal
        $stmt->bind_param("iissd", $user_id, $media_id, $data_wyp, $data_zwr, $oplata);

        if ($stmt->execute()) {
            $conn->query("UPDATE media SET dostepnosc = 0 WHERE id = $media_id");

            $_SESSION['balance'] -= $oplata;

            header("Location: dashboard.php");
            exit;
        } else {
            die("Nie udało się wypożyczyć pozycji. Błąd: " . $stmt->error);
        }
    } else {
        echo "Nie masz wystarczających środków na koncie, aby wypożyczyć tę pozycję.";
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>






