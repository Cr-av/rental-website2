<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$imie    = $_SESSION['imie'];

// Pobranie salda użytkownika z bazy
$query = "SELECT balance FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($userBalance);
$stmt->fetch();
$stmt->close();

// Obsługa formularza wypożyczenia
$errorMessage = ''; // Zmienna do przechowywania komunikatów błędu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['media_id'], $_POST['dni'])) {
    $media_id = (int)$_POST['media_id'];
    $dni = (int)$_POST['dni'];

    if ($dni <= 0) {
        $errorMessage = "<div class='error-message'>Nieprawidłowy okres wypożyczenia.</div>"; // Komunikat o błędzie
    } else {
        // Oblicz opłatę
        $stawka_dzienna = 2.00;
        $oplata = $dni * $stawka_dzienna;

        // Sprawdź, czy użytkownik ma wystarczające środki
        if ($userBalance >= $oplata) {
            // Obliczenie dat wypożyczenia
            $data_wyp = date('Y-m-d H:i:s');
            $data_zwr = date('Y-m-d H:i:s', strtotime("+$dni days"));

            // Wykonaj zapytanie do bazy, aby dodać wypożyczenie
            $stmt = $conn->prepare("
                INSERT INTO wypozyczenia (user_id, media_id, data_wypozyczenia, data_zwrotu, status, oplata)
                VALUES (?, ?, ?, ?, 'wypozyczone', ?)
            ");
            $stmt->bind_param("iissd", $user_id, $media_id, $data_wyp, $data_zwr, $oplata);
            
            if ($stmt->execute()) {
                // Zmiana dostępności media w bazie
                $conn->query("UPDATE media SET dostepnosc = 0 WHERE id = $media_id");

                // Zmniejszenie salda użytkownika
                $updateBalanceQuery = "UPDATE users SET balance = balance - ? WHERE id = ?";
                $updateBalanceStmt = $conn->prepare($updateBalanceQuery);
                $updateBalanceStmt->bind_param("di", $oplata, $user_id);
                $updateBalanceStmt->execute();
                $updateBalanceStmt->close();

                // Zaktualizowanie salda w sesji
                $_SESSION['balance'] -= $oplata;

                header("Location: dashboard.php");
                exit;
            } else {
                $errorMessage = "<div class='error-message'>Nie udało się wypożyczyć pozycji. Błąd: " . $stmt->error . "</div>"; // Błąd zapytania
            }
        } else {
            // Jeśli saldo jest niewystarczające
            $errorMessage = "<div class='error-message'>Nie masz wystarczających środków na koncie, aby wypożyczyć tę pozycję.</div>"; // Komunikat o braku środków
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel użytkownika</title>
    <link rel="stylesheet" href="css/style_dash.css">
    <script src="js/main.js"></script>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico?v=2">
</head>
<body>
    <header>
        <h2>Witaj, <?= htmlspecialchars($imie) ?>!</h2>
        <nav>
            <a href="index.php">Strona główna</a>
            <a href="funds.php">Wpłać pieniądze</a>
            <a href="my_rentals.php">Moje wypożyczenia</a>
            <a href="edit_account.php">Edytuj dane konta</a>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
                <a href="admin.php">Panel administratora</a>
            <?php endif; ?>
            <a href="logout.php">Wyloguj</a>
        </nav>
        <p>Twoje saldo: <?= number_format($userBalance, 2) ?> PLN</p>
    </header>

    <main>
        <!-- Wyświetlanie komunikatu o błędzie -->
        <?php if ($errorMessage): ?>
            <?= $errorMessage ?>
        <?php endif; ?>

        <!-- Wyświetlanie książek i filmów -->
        <section class="card">
            <h3>Dostępne książki i filmy</h3>
            <ul>
                <?php
                $result = $conn->query("SELECT * FROM media WHERE dostepnosc = 1");

                $opisy = [
                    'Matrix' => 'Kultowy film sci-fi o symulowanej rzeczywistości. (FILM)',
                    'Lalka' => 'Powieść Bolesława Prusa o miłości i społeczeństwie. (KSIĄŻKA)',
                    'Incepcja' => 'Film o snach wewnątrz snów. (FILM)',
                    'Chłopi' => 'Epopeja o życiu wsi polskiej. (KSIĄŻKA)',
                    '1984' => 'Wizja państwa totalitarnego autorstwa George’a Orwella. (KSIĄŻKA)',
                    'W pustyni i w puszczy' => 'Przygody dzieci w Afryce. (KSIĄŻKA)',
                    'Forrest Gump' => 'Historia prostego człowieka, który staje się świadkiem przełomowych wydarzeń w historii USA. (FILM)',
                    'Pan Tadeusz' => 'Epopeja narodowa Adama Mickiewicza opowiadająca o szlacheckiej Polsce i sporach rodowych. (KSIĄŻKA)',
                ];

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $tytul = htmlspecialchars($row['tytul']);
                        $typ   = htmlspecialchars($row['typ']);
                        $id    = (int)$row['id'];

                        $baseTitle = $tytul;
                        $imgPath = "img/" . $baseTitle . ".jpg";
                        $opis = $opisy[$baseTitle] ?? 'Brak opisu.';

                        echo "
                        <li style='display: flex; align-items: center; gap: 20px; margin-bottom: 15px;'>
                            <img src='$imgPath' alt='Okładka $baseTitle' style='width: 80px; height: auto; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);'>
                            <div style='flex-grow: 1;'>
                                <strong>$tytul</strong><br>
                                <small>$opis</small>
                            </div>
                            <form action='dashboard.php' method='post' style='display:inline;'>
                                <input type='hidden' name='media_id' value='$id'>
                                <select name='dni' class='select-dni' style='margin-right: 10px;'>
                                    <option value='1'>1 dzień</option>
                                    <option value='3'>3 dni</option>
                                    <option value='7'>7 dni</option>
                                    <option value='14'>14 dni</option>
                                </select>
                                <button type='submit' class='btn'>Wypożycz</button>
                            </form>
                        </li>";
                    }
                } else {
                    echo "<li>Brak dostępnych pozycji.</li>";
                }
                ?>
            </ul>
        </section>
    </main>

    <footer>
        <p style="text-align: center;">&copy; 2025 Wypożyczalnia Online</p>
    </footer>
</body>
</html>













