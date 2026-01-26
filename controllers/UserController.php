<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../models/User.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../core/functions.php';

class UserController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {

        try {
            $path = __DIR__ . '/../create_user.php';

            if (file_exists($path)) {
                require_once $path;
            }
        } catch (Exception $e) {
            echo "Error! " . $e->getMessage();
        }
    }

    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->setUsername(sanitizeInput($_POST['username']));
            $this->user->setPassword(sanitizeInput($_POST['password']));
            $this->user->setFirstName(sanitizeInput($_POST['first_name']));
            $this->user->setLastName(sanitizeInput($_POST['last_name']));
            $this->user->setPreferredName(isset($_POST["preferred_name"]) && !empty($_POST["preferred_name"]) ? sanitizeInput($_POST['preferred_name']) : null);
            $this->user->setEmail(sanitizeInput($_POST['email']));
            $this->user->setPhone(isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : null);
            $this->user->setUserRole(isset($_POST['user_role']) ? sanitizeInput($_POST['user_role']) : null);
            $this->user->setAddedBy(isset($_SESSION['username']) ? sanitizeInput($_SESSION['username']) : null);

            $userExists = $this->user->findByUsername($this->user->getUsername());

            if ($userExists) {
                $_SESSION['notification'] = [
                    'message' => "The username already exists, please use a different username",
                    'type' => 'error'
                ];

                if ($_SESSION['user_role'] == "Admin") {
                    redirectTo('../staffprofile.php');
                } else {
                    redirectTo('../login.php');
                }
            } else {
                $result = $this->user->addUser();
                if ($result === true) {
                    if ($_SESSION['user_role'] == "Admin") {
                        $_SESSION['notification'] = [
                            'message' => "User Successfully Created",
                            'type' => 'success'
                        ];
                        redirectTo('../staffprofile.php');
                    } else {
                        $_SESSION['notification'] = [
                            'message' => "Registration Successful, Proceed to Login",
                            'type' => 'success'
                        ];
                        redirectTo('../login.php');
                    }
                } else {
                    if ($_SESSION['user_role'] == "Admin") {
                        $_SESSION['notification'] = [
                            'message' => "Failed To Create User: " . $result,
                            'type' => 'error'
                        ];
                        redirectTo('../staffprofile.php');
                    } else {
                        $_SESSION['notification'] = [
                            'message' => "Failed To Register Account: " . $result,
                            'type' => 'error'
                        ];
                        redirectTo('../login.php');
                    }
                }
            }
        }
    }

    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $user = $this->user->findByUsername($_POST['username']);
            if ($user) {
                $password = $_POST['password'];
                $password2 = $_POST['password2'];
                if ($password === $password2) {
                    if ($this->user->resetPassword($password, $user['username'])) {
                        $_SESSION['notification'] = [
                            'message' => "Password Reset Successful, Proceed To Login",
                            'type' => 'success'
                        ];

                        if (isLoggedIn()) {
                            redirectTo('../dashboard2.php');
                        } else {
                            redirectTo('../login.php');
                        }
                    } else {
                        $_SESSION['notification'] = [
                            'message' => "Failed To Reset Password, Try Again",
                            'type' => 'error'
                        ];
                        //redirectTo('/user/forgot-password');
                    }
                } else {
                    $_SESSION['notification'] = [
                        'message' => "Passwords don't match!",
                        'type' => 'error'
                    ];
                    if (isLoggedIn()) {
                        redirectTo('../dashboard2.php');
                    } else {
                        redirectTo('../login.php');
                    }
                    //redirectTo('/user/forgot-password');
                }
            } else {
                $_SESSION['notification'] = [
                    'message' => "Username not found!",
                    'type' => 'error'
                ];
                redirectTo('./login.php');
            }
        }
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = sanitizeInput($_POST['username']);
            $firstName = sanitizeInput($_POST['FirstName']);
            $lastName = sanitizeInput($_POST['LastName']);
            $preferredName = isset($_POST["preferredName"]) && !empty($_POST["preferredName"]) ? sanitizeInput($_POST['preferredName']) : null;
            $email = sanitizeInput($_POST['email']);
            $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : null;

            if (isset($_FILES['profilePicture'])) {
                // redirectTo("/test");
                // Check for upload errors for the current file
                if ($_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['profilePicture']['tmp_name'];
                    $file_name = $_FILES['profilePicture']['name'];
                    $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    // Generate a unique name to prevent conflicts
                    $profilePath = $username . '_' . uniqid() . '.' . $imageFileType;
                    $targetDir = __DIR__ . "/../uploads/profiles/";
                    $targetFile = $targetDir . $profilePath;

                    if (move_uploaded_file($tmp_name, $targetFile)) {
                        // $this->user->setEvidence($uniqueFileName);
                    } else {
                        echo "Error: Failed to move uploaded file. Check your `uploads/profiles` folder permissions.";
                    }
                }
            }

            $result = $this->user->updateProfile($username, $firstName, $lastName, $preferredName, $email, $phone, $profilePath);

            if ($result) {
                $_SESSION['notification'] = [
                    'message' => "Profile updated successfully.",
                    'type' => 'success'
                ];
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to update profile.",
                    'type' => 'error'
                ];
            }
            redirectTo('../staffprofile.php');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userController = new UserController();
    switch ($_POST['action']) {
        case 'create':
            $userController->createUser();
            break;
        case 'resetPassword':
            $userController->resetPassword();
            break;
        case 'updateProfile':
            $userController->updateProfile();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}
