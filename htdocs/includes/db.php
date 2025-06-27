<!-- polaczenie z bazą danych -->
<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'wypozyczalnia';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}
?>

