<!-- Zastępuje domyślny index.html, tutaj działa        -->
<!-- jako "strona główna" wyświetlana przed przejściem  -->
<!-- do faktycznego dashboardu'u                        -->
<?php
include("includes/db.php");
include("includes/auth.php");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wypożyczalnia</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="css/favicon.ico?v=2">
</head>
<body class="home-page">
    <div class="home-box">
        <h1>📚 Wypożyczalnia</h1>

        <?php if (isLoggedIn()): ?>
            <p>Witaj, <strong><?= htmlspecialchars($_SESSION['imie']) ?></strong>!</p>
            <div class="home-links">
                <a href="dashboard.php">Przejdź do panelu</a> |
                <a href="logout.php">Wyloguj się</a>
            </div>
        <?php else: ?>
            <p>Miło Cię widzieć!</p>
            <div class="home-links">
                <a href="login.php">Zaloguj się</a> |
                <a href="register.php">Zarejestruj się</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>



