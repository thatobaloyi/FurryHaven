<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class VetStaff extends User{
    private $db;
    private $table = 'vetstaff';

    // Attributes
    private $vetID;
    private $username;
    private $specialisation;
    private $hireDate;
    private $appointedBy;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setVetID($vetID)
    {
        $this->vetID = $vetID;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function setSpecialisation($specialisation)
    {
        $this->specialisation = $specialisation;
    }
    public function setHireDate($hireDate)
    {
        $this->hireDate = $hireDate;
    }
    public function setAppointedBy($appointedBy)
    {
        $this->appointedBy = $appointedBy;
    }

    // Getters
    public function getVetID()
    {
        return $this->vetID;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getSpecialisation()
    {
        return $this->specialisation;
    }
    public function getHireDate()
    {
        return $this->hireDate;
    }
    public function getAppointedBy()
    {
        return $this->appointedBy;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (vetID, username, specialisation, hireDate, appointedBy) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssss", $this->vetID, $this->username, $this->specialisation, $this->hireDate, $this->appointedBy);
        return $stmt->execute();
    }

    public function findAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findOne($username)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET username = ?, specialisation = ?, hireDate = ?, appointedBy = ? WHERE vetID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssss", $this->username, $this->specialisation, $this->hireDate, $this->appointedBy, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE vetID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }


    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(vetID, 5) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'VET-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}
