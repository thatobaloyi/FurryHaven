<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/functions.php'; // For redirectTo, isLoggedIn, sanitizeInput

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

class AuthController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $username = sanitizeInput($_POST['username'] ?? '');
            $password = sanitizeInput($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $_SESSION['error_message'] = 'Please enter both username and password.';
                redirectTo('../login.php');
            }

            $foundUser = $this->user->findByUsername($username); // get the user

            if ($foundUser && password_verify($password, $foundUser['password'])) {
                session_regenerate_id(true);
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['first_name'] = $foundUser['FirstName'];
                $_SESSION['preferredName'] = $foundUser['preferredName']; // <-- Make sure this matches your DB column
                $_SESSION['user_role'] = $foundUser['userRole'];

                switch ($_SESSION['user_role']) {
                    case 'Admin':
                        $_SESSION['admin_username'] = $foundUser['username'];
                        break;
                    case 'Vet':
                        $_SESSION['vet_username'] = $foundUser['username'];
                        break;
                    case 'Volunteer':
                        $_SESSION['volunteer_username'] = $foundUser['username'];
                        break;
                    case 'Adopter':
                        $_SESSION['adopter_username'] = $foundUser['username'];
                        break;
                    case 'Guest':
                        $_SESSION['guest_username'] = $foundUser['username'];
                        redirectTo('../homepage.php');
                        break;
                }


                $_SESSION['start_time'] = time();

                $_SESSION['notification'] = [
                    'message' => "Welcome, $username !",
                    'type' => 'success' // You can use this for styling
                ];

                redirectTo('../dashboard2.php');
            } else {
                $_SESSION['notification'] = [
                    'message' => "Incorrect Username or Password",
                    'type' => 'error' // You can use this for styling
                ];
                redirectTo('../login.php');
            }
        }
    }

    public function logout()
    {
        session_destroy();
        $_SESSION['notification'] = [
            'message' => "Successfully Logged Out",
            'type' => 'success' // You can use this for styling
        ];
        redirectTo('../login.php');
    }
}

if (isset($_REQUEST['action'])) {
    $authController = new AuthController();
    switch ($_REQUEST['action']) {
        case 'login':
            $authController->login();
            break;
        case 'logout':
            $authController->logout();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}