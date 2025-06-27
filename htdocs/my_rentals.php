<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$imie    = $_SESSION['imie'];

$query = "
    SELECT m.tytul, w.data_wypozyczenia, w.data_zwrotu, w.oplata, w.status, w.id AS wypo_id
    FROM wypozyczenia w
    JOIN media m ON w.media_id = m.id
    WHERE w.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$view_error = '';
$view_title = '';
$view_embed = '';

if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $view_id = (int)$_GET['view'];

    $stmt = $conn->prepare("
        SELECT m.tytul, m.link
        FROM wypozyczenia w
        JOIN media m ON w.media_id = m.id
        WHERE w.id = ? AND w.user_id = ? AND w.status = 'wypozyczone'
    ");
    $stmt->bind_param('ii', $view_id, $user_id);
    $stmt->execute();
    $res_view = $stmt->get_result();

    if ($res_view && $res_view->num_rows === 1) {
        $row = $res_view->fetch_assoc();
        $view_title = htmlspecialchars($row['tytul']);
        $link = htmlspecialchars($row['link']);
        $extension = strtolower(pathinfo($link, PATHINFO_EXTENSION));

        if ($extension === 'mp4') {
            $view_embed = "
                <video controls autoplay style='width: 100%; max-width: 100%; height: auto; display: block; margin: 0 auto;'>
                    <source src=\"$link\" type=\"video/mp4\">
                    Twoja przeglądarka nie wspiera odtwarzania wideo.
                </video>
            ";
        } elseif ($extension === 'pdf') {
            $view_embed = "
                <iframe src=\"$link\" width=\"100%\" height=\"800px\" style='border: none;'></iframe>
            ";
        } else {
            $view_embed = "Nieobsługiwany format pliku: .$extension";
        }
    } else {
        $view_error = 'Brak dostępu do tego wypożyczenia.';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje wypożyczenia</title>
    <link rel="stylesheet" href="css/style_dash.css">
    <script src="js/main.js"></script>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .rental-list li {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 1rem;
            background: #59347cdc;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: 0.2s ease;
        }

        .rental-list li:hover {
            background-color: #6d429b;
            transform: scale(1.01);
        }

        .rental-top {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .rental-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem; 
            font-size: 0.9rem;
        }

        .status-zwrócone {
            color: #2abe2f;
            font-weight: bold;
        }

        .status-wypozyczone {
            color: #ffcc00;
            font-weight: bold;
        }

        .rental-buttons {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .viewer {
            padding: 1rem;
            background-color: #653b8d;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .error {
            color: red;
        }

        
    </style>
</head>
<body>
<header>
    <h2>Witaj, <?= htmlspecialchars($imie) ?>!</h2>
    <nav>
        <a href="index.php">Strona główna</a>
        <a href="funds.php">Wpłać pieniądze</a>
        <a href="edit_account.php">Edytuj dane konta</a>
        <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <a href="admin.php">Panel administratora</a>
        <?php endif; ?>
        <a href="logout.php">Wyloguj</a>
    </nav>
</header>

<main>
    <?php if ($view_embed || $view_error): ?>
        <section class="card viewer">
            <h3>Podgląd: <?= $view_title ?: '' ?></h3>
            <?php if ($view_error): ?>
                <p class="error"><?= $view_error ?></p>
            <?php else: ?>
                <?= $view_embed ?>
            <?php endif; ?>
            <p><a href="my_rentals.php" class="btn">Wróć do swoich wypożyczeń</a></p>
        </section>
    <?php endif; ?>

    <section class="card">
        <h3><i class="fas fa-book-reader"></i> Twoje wypożyczenia</h3>
        <?php if ($res && $res->num_rows > 0): ?>
            <ul class="rental-list">
                <?php while ($row = $res->fetch_assoc()): 
                    $tyt = htmlspecialchars($row['tytul']);
                    $dat = htmlspecialchars($row['data_wypozyczenia']);
                    $zwr = htmlspecialchars($row['data_zwrotu']);
                    $opl = htmlspecialchars($row['oplata']);
                    $sts = htmlspecialchars($row['status']);
                    $wid = (int)$row['wypo_id'];
                    $statusClass = $sts === 'zwrócone' ? 'status-zwrócone' : 'status-wypozyczone';
                ?>
                <li>
                    <div class="rental-top"><i class="fas fa-book"></i> <?= $tyt ?></div>
                    <div class="rental-info">
                        <span><i class="fas fa-clock"></i> <?= $dat ?> → <?= $zwr ?></span>
                        <span><i class="fas fa-wallet"></i> <?= $opl ?> zł</span>
                        <span class="<?= $statusClass ?>"><i class="fas fa-info-circle"></i> <?= $sts ?></span>
                    </div>

                    <?php if ($sts === 'wypozyczone'): ?>
                        <div class="rental-buttons">
                            <a class="btn" href="zwroc.php?id=<?= $wid ?>"><i class="fas fa-undo"></i> Zwróć</a>
                            <a class="btn" href="?view=<?= $wid ?>"><i class="fas fa-eye"></i> Zobacz</a>
                        </div>
                    <?php endif; ?>
                </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Nie masz żadnych wypożyczeń.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p style="text-align: center;">&copy; 2025 Wypożyczalnia Online</p>
</footer>
</body>
</html>


