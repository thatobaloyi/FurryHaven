<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class AdminStaff extends User{
    private $db;
    private $table = 'adminstaff';

    // Attributes
    private $adminID;
    private $username;
    private $department;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setAdminID($adminID)
    {
        $this->adminID = $adminID;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    // Getters
    public function getAdminID()
    {
        return $this->adminID;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getDepartment()
    {
        return $this->department;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO $this->table (AdminID, Username, Department) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $this->adminID, $this->username, $this->department);
        return $stmt->execute();
    }

    public function findAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findOne($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE AdminID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET Username = ?, Department = ? WHERE AdminID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $this->username, $this->department, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE AdminID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(AdminID, 7) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'ADMIN-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}
