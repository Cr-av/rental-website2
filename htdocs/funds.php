<?php
require_once('includes/db.php'); 

session_start();
$imie    = $_SESSION['imie'];

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id']; 

$query = "SELECT balance FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId); // Parametr użytkownika jako liczba całkowita
$stmt->execute();
$stmt->bind_result($userBalance);
$stmt->fetch();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amount'])) {
    $amount = (float) $_POST['amount']; // Kwota wpłaty
    if ($amount > 0) {
        $updateQuery = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("di", $amount, $userId); // Parametry: kwota jako liczba zmiennoprzecinkowa, id użytkownika
        $updateStmt->execute();
        $updateStmt->close();

        $_SESSION['balance'] += $amount;
        
        header("Location: dashboard.php");
        exit;
    } else {
        $errorMessage = "Kwota musi być większa niż 0!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj środki</title>
    <link rel="stylesheet" href="css/style_dash2.css"> <!-- Link do stylów CSS -->
</head>
<body>
    <header>
        <h2>Witaj, <?= htmlspecialchars($imie) ?>!</h2>
        <nav>
            <a href="index.php">Strona główna</a>
            <a href="my_rentals.php">Moje wypożyczenia</a>
            <a href="edit_account.php">Edytuj dane konta</a>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
                <a href="admin.php">Panel administratora</a>
            <?php endif; ?>
            <a href="logout.php">Wyloguj</a>
        </nav>
    </header>

    <main>
        <div class="card">
    <h2>Dodawanie środków do konta</h2>
    <h3>Twoje saldo: <?= number_format($userBalance, 2) ?> PLN</h3>

    <?php if (isset($errorMessage)) { ?>
        <p class="error-message"><?= $errorMessage ?></p>
    <?php } ?>

    <form action="funds.php" method="POST">
        <label for="amount">Kwota do wpłaty (PLN):</label>
        <input type="number" id="amount" name="amount" step="0.01" required>
        <button type="submit">Wyślij</button>
    </form>
</div>

    </main>

    <footer>
        <p>&copy; 2025 Wypożyczalnia Online</p>
    </footer>
</body>
</html>













