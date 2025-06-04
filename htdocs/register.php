<?php
include("includes/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (imie, nazwisko, email, haslo_hash, rola) VALUES (?, ?, ?, ?, 'user')");
    $stmt->bind_param("ssss", $imie, $nazwisko, $email, $haslo);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Błąd rejestracji.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h2>Rejestracja</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="imie" placeholder="Imię" required>
            </div>
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="nazwisko" placeholder="Nazwisko" required>
            </div>
            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="haslo" placeholder="Hasło" required>
            </div>
            <input type="submit" class="btn-login" value="Zarejestruj się">
        </form>

        <div class="help-links">
            Masz już konto?
            <br><a href="login.php">← Zaloguj się</a>
        </div>
    </div>
</body>
</html>



