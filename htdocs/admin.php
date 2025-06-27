<?php
include("includes/db.php");
include("includes/auth.php");

$imie    = $_SESSION['imie'];

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        // Usuwanie
        $delete_id = intval($_POST['delete_id']);
        $stmt = $conn->prepare("DELETE FROM media WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
    } elseif (isset($_POST['tytul'], $_POST['typ'], $_POST['link'])) {
        // Dodawanie
        $tytul = $_POST['tytul'];
        $typ = $_POST['typ'];
        $link = $_POST['link'];
        $dostepnosc = isset($_POST['dostepnosc']) ? intval($_POST['dostepnosc']) : 1;

        $stmt = $conn->prepare("INSERT INTO media (tytul, typ, link, dostepnosc) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $tytul, $typ, $link, $dostepnosc);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel admina</title>
    <link rel="stylesheet" href="css/style_dash.css"> <!-- Link do pliku CSS -->
</head>
<body>
    <header>
        <h2>Witaj, <?= htmlspecialchars($imie) ?>!</h2>
        <nav>
            <a href="index.php">Strona główna</a>
            <a href="funds.php">Wpłać pieniądze</a>
            <a href="my_rentals.php">Moje wypożyczenia</a>
            <a href="edit_account.php">Edytuj dane konta</a>
            <a href="logout.php">Wyloguj</a>
        </nav>
    </header>

    <main>
        <div class="card">
            <h3>Dodaj książkę lub film</h3>
            <form method="post">
                <label for="tytul">Tytuł:</label>
                <input type="text" name="tytul" required><br>

                <label for="typ">Typ:</label>
                <select name="typ">
                    <option value="ksiazka">Książka</option>
                    <option value="film">Film</option>
                </select><br>

                <label for="link">Link do zasobu (np. iframe, YouTube, itp.):</label>
                <input type="text" name="link"><br>

                <label for="dostepnosc">Dostępność:</label>
                <select name="dostepnosc">
                    <option value="1" selected>Dostępne</option>
                    <option value="0">Wypożyczone</option>
                </select><br>

                <input type="submit" value="Dodaj">
            </form>

            <h3>Lista pozycji</h3>
            <ul>
                <?php
                $res = $conn->query("SELECT * FROM media ORDER BY tytul");
                while ($m = $res->fetch_assoc()) {
                    $id = intval($m['id']);
                    $tytul = htmlspecialchars($m['tytul']);
                    $typ = htmlspecialchars($m['typ']);
                    $dostepnosc = $m['dostepnosc'] ? "Dostępne" : "Wypożyczone";

                    echo "<li><strong>$tytul</strong> ($typ) – $dostepnosc ";
                    echo "<form method='post' style='display:inline;' onsubmit=\"return confirm('Na pewno chcesz usunąć?');\">
                            <input type='hidden' name='delete_id' value='$id'>
                            <input type='submit' value='Usuń'>
                          </form></li>";
                }
                ?>
            </ul>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Wypożyczalnia Online</p>
    </footer>
</body>
</html>





