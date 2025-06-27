<!-- logowanie/autoryzacja -->
 
<?php
session_start();

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function setBalanceInSession($balance): void {
    $_SESSION['balance'] = $balance;
}

function login(string $email, string $password): bool {
    global $conn;
    $emailEsc = $conn->real_escape_string($email);
    $res      = $conn->query("SELECT * FROM users WHERE email = '$emailEsc' LIMIT 1");

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // Debugowanie: sprawdzenie, czy saldo jest dostępne w danych użytkownika
        var_dump($user);  // Sprawdź, czy 'balance' jest dostępne w danych

        if (password_verify($password, $user['password'])) {
            // Zapisywanie danych do sesji
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['imie'] = $user['imie'];
            setBalanceInSession($user['balance']);

            // Debugowanie: sprawdzenie zawartości sesji
            var_dump($_SESSION);  // Wyświetl całą zawartość $_SESSION

            return true;
        }
    }
    return false;
}





function logout(): void {
    session_unset();
    session_destroy();
}

function isAdmin() {
    return isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin';
}










