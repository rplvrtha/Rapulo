<?php
namespace Rapulo\Features\Auth;
use Rapulo\Core\Component;
use Rapulo\Core\ORM;

class AuthController {
    public function showLogin() {
        $component = new LoginComponent();
        echo $component->render();
    }

    public function login() {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';

        $user = ORM::table('users')
            ->where('username', '=', $username)
            ->get();

        if (!empty($user) && password_verify($password, $user[0]->password)) {
            $_SESSION['user'] = $user[0];
            header('Location: /dashboard');
            exit;
        } else {
            echo 'Invalid credentials';
        }
    }

    public function dashboard() {
        echo 'Welcome to Dashboard, ' . htmlspecialchars($_SESSION['user']->username);
    }
}