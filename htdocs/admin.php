<?php
include("includes/db.php");
include("includes/auth.php");

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tytul = $_POST['tytul'];
    $autor = $_POST['autor'];
    $typ = $_POST['typ'];
    $gatunek = $_POST['gatunek'];
    $rok = intval($_POST['rok']);

    $stmt = $conn->prepare("INSERT INTO media (tytul, autor, typ, gatunek, rok) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $tytul, $autor, $typ, $gatunek, $rok);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html>
<head><title>Panel admina</title></head>
<body>
<h2>Panel administratora</h2>
<p><a href="index.php">Strona główna</a> | <a href="logout.php">Wyloguj</a></p>

<h3>Dodaj książkę lub film</h3>
<form method="post">
    Tytuł: <input type="text" name="tytul" required><br>
    Autor: <input type="text" name="autor"><br>
    Typ: 
    <select name="typ">
        <option value="ksiazka">Książka</option>
        <option value="film">Film</option>
    </select><br>
    Gatunek: <input type="text" name="gatunek"><br>
    Rok: <input type="number" name="rok"><br>
    <input type="submit" value="Dodaj">
</form>

<h3>Lista pozycji</h3>
<ul>
<?php
$res = $conn->query("SELECT * FROM media ORDER BY tytul");
while ($m = $res->fetch_assoc()) {
    echo "<li>{$m['tytul']} ({$m['typ']}) – ";
    echo $m['dostepnosc'] ? "Dostępne" : "Wypożyczone";
    echo "</li>";
}
?>
</ul>
</body>
</html>


