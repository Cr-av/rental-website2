<!-- Panel z formularzem loowania -->
<?php
include("includes/db.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $haslo = $_POST['haslo'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $rez = $stmt->get_result();

    if ($user = $rez->fetch_assoc()) {
        if (password_verify($haslo, $user['haslo_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['imie'] = $user['imie'];
            $_SESSION['rola'] = $user['rola'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Błędne hasło.";
        }
    } else {
        $error = "Nie znaleziono użytkownika.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Logowanie</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" type="image/x-icon" href="css/favicon.ico?v=2">
  <!-- ikony -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
</head>
<body class="login-page">
  <div class="login-box">
    <h2>Logowanie</h2>
    <form method="post">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="email" name="email" placeholder="User Name" required>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="haslo" placeholder="Password" required>
      </div>
      <label class="remember-me">
        <input type="checkbox"> Zapamiętaj mnie
      </label>
      <button type="submit" class="btn-login">Zaloguj się</button>
    </form>
    <p class="help-links">
      <a href="index.php">← Powrót do strony głównej</a>
    </p>
  </div>
</body>
</html>


