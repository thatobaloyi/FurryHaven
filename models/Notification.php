<?php
include_once __DIR__ . '/../config/databaseconnection.php';

class Notification
{
    private $db;
    private $table = "notificationlogs";


    private $notificationID;
    private $userID;
    private $message;
    private $isRead;
    private $createAt;
    private $status;
   




    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }


    public function create()
    {
        $stmt = $this->db->prepare("
                    INSERT INTO $this->table (notificationID ,userID, message, isRead, createAt, status) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
        $stmt->bind_param('sssiss', $this->notificationID, $this->userID, $this->message, $this->isRead, $this->createAt, $this->status);
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }
        return true;
    }

    public function getUnread($userID)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM $this->table  
            WHERE userID = ? AND isRead = 0 
            ORDER BY createAt DESC
        ");
        $stmt->bind_param('s', $userID);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function markAsRead($notificationID)
    {
        $stmt = $this->db->prepare("UPDATE $this->table SET isRead = 1 WHERE notificationID = ?");
        $stmt->bind_param('s', $notificationID);
        return $stmt->execute();
    }

    public function markAllAsRead($userID)
    {
        $stmt = $this->db->prepare("UPDATE $this->table SET isRead = 1 WHERE userID = ?");
        $stmt->bind_param('s', $userID);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(notificationID, 6) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'NOTS-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    // Getters
    public function getNotificationID()
    {
        return $this->notificationID;
    }
    public function getUserID()
    {
        return $this->userID;
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function getIsRead()
    {
        return $this->isRead;
    }
    public function getCreateAt()
    {
        return $this->createAt;
    }
    public function getStatus()
    {
        return $this->status;
    }
    

    // Setters
    public function setNotificationID($notificationID)
    {
        $this->notificationID = $notificationID;
    }
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }
    public function setMessage($message)
    {
        $this->message = $message;
    }
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

}
