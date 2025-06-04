<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$imie    = $_SESSION['imie'];

// --- Obsługa podglądu wypożyczonego media ---
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
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $row        = $res->fetch_assoc();
        $view_title = htmlspecialchars($row['tytul']);
        $link       = htmlspecialchars($row['link']);

        // jednolite osadzenie wszystkiego jako iframe
        $view_embed = "<iframe src=\"$link\" width=\"100%\" height=\"800px\" frameborder=\"0\"></iframe>";
    } else {
        $view_error = 'Brak dostępu do tego wypożyczenia.';
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel użytkownika</title>
    <link rel="stylesheet" href="css/style_dash.css">
</head>
<body>
    <header>
        <h2>Witaj, <?= htmlspecialchars($imie) ?>!</h2>
        <nav>
            <a href="index.php">Strona główna</a>
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
                <p><a href="dashboard.php" class="btn">Wróć do panelu</a></p>
            </section>
        <?php endif; ?>

        <section class="card">
            <h3>Dostępne książki i filmy</h3>
            <ul>
            <?php
            $result = $conn->query("SELECT * FROM media WHERE dostepnosc = 1");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tytul = htmlspecialchars($row['tytul']);
                    $typ   = htmlspecialchars($row['typ']);
                    $id    = (int)$row['id'];
                    echo "<li>
                            $tytul ($typ)
                            <a class='btn' href='wypozycz.php?id=$id'>Wypożycz</a>
                          </li>";
                }
            } else {
                echo "<li>Brak dostępnych pozycji.</li>";
            }
            ?>
            </ul>
        </section>

        <section class="card">
            <h3>Twoje wypożyczenia</h3>
            <?php
            $stmt = $conn->prepare("
                SELECT m.tytul, w.data_wypozyczenia, w.status, w.id AS wypo_id
                FROM wypozyczenia w
                JOIN media m ON w.media_id = m.id
                WHERE w.user_id = ?
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            ?>

            <?php if ($res && $res->num_rows > 0): ?>
                <ul>
                <?php while ($row = $res->fetch_assoc()): 
                    $tyt = htmlspecialchars($row['tytul']);
                    $dat = htmlspecialchars($row['data_wypozyczenia']);
                    $sts = htmlspecialchars($row['status']);
                    $wid = (int)$row['wypo_id'];
                ?>
                    <li>
                        <?= "$tyt | Wypożyczono: $dat | Status: $sts" ?>
                        <?php if ($sts === 'wypozyczone'): ?>
                            <a class="btn" href="zwroc.php?id=<?= $wid ?>">Zwróć</a>
                            <a class="btn" href="dashboard.php?view=<?= $wid ?>">Zobacz</a>
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
        <p>&copy; 2025 Biblioteka Online</p>
    </footer>
</body>
</html>









