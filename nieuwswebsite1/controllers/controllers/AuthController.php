<?php
session_start();
require_once __DIR__ . '/../../config/config/database.php';

class AuthController {
    public function login() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return 'Gebruikersnaam en wachtwoord zijn verplicht.';
        }

        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            return 'Ongeldige gebruikersnaam of wachtwoord.';
        }

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        return true;
    }

    public function isLoggedIn() {
        return !empty($_SESSION['admin_logged_in']);
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /nieuwswebsite1/admin_panel/login.php');
            exit;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /nieuwswebsite1/admin_panel/login.php');
        exit;
    }
}
?>