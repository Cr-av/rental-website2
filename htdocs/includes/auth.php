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

function login(string $email, string $password): bool {
    global $conn;
    $emailEsc = $conn->real_escape_string($email);
    $res      = $conn->query("SELECT * FROM users WHERE email = '$emailEsc' LIMIT 1");
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['imie']    = $user['imie'];
            return true;
        }
    }
    return false;
}

function logout(): void {
    session_unset();
    session_destroy();
}



