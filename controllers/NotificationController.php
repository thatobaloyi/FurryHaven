<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../models/Notification.php';
include_once __DIR__ . '/../models/User.php';
include_once __DIR__ . '/../config/databaseconnection.php';

class NotificationController
{
    private $notification;
    private $user;

    public function __construct()
    {
        $this->notification = new Notification();
        $this->user = new User();
    }

    // public function createNotification($crueltyID) {
    //     return $this->notification->createCrueltyNotification($crueltyID);
    // }

    public function getUnread($userID)
    {
        return $this->notification->getUnread($userID);
    }

    public function markAsRead($notificationID)
    {
        return $this->notification->markAsRead($notificationID);
    }

    public function markAllAsRead($userID)
    {
        return $this->notification->markAllAsRead($userID);
    }

    public function sendVetNotification()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include_once __DIR__ . '/../core/functions.php';
            $vetUsername = sanitizeInput($_POST['vet_username']);
            $animalId = sanitizeInput($_POST['animal_id']);
            $animalName = sanitizeInput($_POST['animal_name']);

            $message = "Please complete the medical treatments for animal: " . $animalName . " (ID: " . $animalId . ")";
            $this->notification->setNotificationID($this->notification->generateID());
            $this->notification->setUserID($vetUsername);
            $this->notification->setMessage($message);
            $createAt = date('Y-m-d H:i:s');
            $this->notification->setCreateAt($createAt);
            $this->notification->setIsRead(0);
            $this->notification->setStatus('sent');

            if ($this->notification->create()) {
                $_SESSION['notification'] = [
                    'message' => 'Notification sent to the vet.',
                    'type' => 'success'
                ];
            } else {
                $_SESSION['notification'] = [
                    'message' => 'Failed to send notification.',
                    'type' => 'error'
                ];
            }
            redirectTo('../animaldatabase2.php');
        }
    }
    public function sendStatusUpdateNotification($username, $applicationType)
    {
       
            include_once __DIR__ . '/../core/functions.php';

            $message = "The Status of your $applicationType Application has been Updated!.";
            $this->notification->setNotificationID($this->notification->generateID());
            $this->notification->setUserID($username);
            $this->notification->setMessage($message);
            $createAt = date('Y-m-d H:i:s');
            $this->notification->setCreateAt($createAt);
            $this->notification->setIsRead(0);
            $this->notification->setStatus('sent');

            if ($this->notification->create()) {
                $_SESSION['notification'] = [
                    'message' => 'Notification sent to the user.',
                    'type' => 'success'
                ];
            } else {
                $_SESSION['notification'] = [
                    'message' => 'Failed to send notification.',
                    'type' => 'error'
                ];
            }
            // redirectTo('../applications2.php');
        
    }

    public function createCrueltyNotification($crueltyID)
    {
        try {
            $admins = $this->user->findByRole('Admin');
            // global $conn;
            // $admins = $conn->query("SELECT * FROM users WHERE userRole = 'Admin'");


            if ($admins && $admins->num_rows > 0) {
                $isRead = 0;
                $status = 'Sent';
                $createAt = date('Y-m-d H:i:s');


                while ($admin = $admins->fetch_assoc()) {
                    $notificationID = $this->notification->generateID();
                    $userID = $admin['username'];
                    // var_dump($userID);
                    $message = "A cruelty report $crueltyID has been filed. Take a look!";
                    $this->notification->setUserID($userID);
                    $this->notification->setNotificationID($notificationID);
                    $this->notification->setMessage($message);
                    $this->notification->setCreateAt($createAt);
                    $this->notification->setIsRead($isRead);
                    $this->notification->setStatus($status);
                    if (!$this->notification->create()) {
                        return False;
                        // throw new Exception("Cannot Create a cruelty notification!.");
                    }
                }

                return true;
            } else {
                throw new Exception("No 'Admin' users found to send notifications to.");
            }
        } catch (Exception $e) {
            $_SESSION['notification'] = [
                'message' => $e->getMessage(),
                'type' => 'error'
            ];

            return false;
        }
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $notificationController = new NotificationController();
    switch ($_POST['action']) {
        case 'sendCrueltyNotification':
            $notificationController->createCrueltyNotification($crueltyID);
            break;
        case 'sendVetNotification':
            $notificationController->sendVetNotification();
            break;
        case 'sendStatusNotification':
            $notificationController->sendVetNotification($username, $applicationType);
            break;
        default:
            break;
    }
}
