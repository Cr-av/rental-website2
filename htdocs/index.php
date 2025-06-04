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
    <style>
        body.home-page {
            background-color: #5c4084;
            font-family: Arial, sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .home-box {
            background-color: #7a52a3;
            padding: 40px;
            border-radius: 12px;
            width: 450px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            text-align: center;
        }

        .home-box h1 {
            margin-bottom: 20px;
        }

        .home-box a {
            color: #eee;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        .home-box a:hover {
            text-decoration: underline;
        }

        .home-links {
            margin-top: 20px;
        }
    </style>
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



