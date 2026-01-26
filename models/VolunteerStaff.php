<?php

include_once __DIR__ . '/../config/databaseconnection.php';
require_once 'User.php';

class VolunteerStaff extends User
{
    private $db;
    private $table = 'volunteer';

    // Attributes
    private $VolunteerID;
    private $Address;
    private $EmergencyContact;
    private $AppointedBy;
    private $Available;
    private $username;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setAddress($Address) { $this->Address = $Address; }
    public function setEmergencyContact($EmergencyContact) { $this->EmergencyContact = $EmergencyContact; }
    public function setAppointedBy($AppointedBy) { $this->AppointedBy = $AppointedBy; }
    public function setAvailable($Available) { $this->Available = $Available; }
    public function setUsername($username) { $this->username = $username; }
    public function setVolunteerID($id) { $this->VolunteerID = $id; }

    // Getters
    public function getVolunteerID() { return $this->VolunteerID; }
    public function getAddress() { return $this->Address; }
    public function getEmergencyContact() { return $this->EmergencyContact; }
    public function getAppointedBy() { return $this->AppointedBy; }
    public function getAvailable() { return $this->Available; }
    public function getUsername() { return $this->username; }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO $this->table (VolunteerID, Address, EmergencyContact, AppointedBy, Available, username) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssis", $this->VolunteerID, $this->Address, $this->EmergencyContact, $this->AppointedBy, $this->Available, $this->username);
        return $stmt->execute();
    }

    public function findAll()
    {
        $query = "SELECT v.VolunteerID, u.FirstName, u.LastName, v.Address, v.EmergencyContact, u.email, u.phone, v.AppointedBy, v.Available 
                  FROM $this->table v
                  LEFT JOIN users u ON v.username = u.username";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findOne($id)
    {
        $query = "SELECT v.VolunteerID, u.FirstName, u.LastName, v.username, v.Address, v.EmergencyContact, u.email, u.phone, v.AppointedBy, v.Available 
                  FROM $this->table v
                  LEFT JOIN users u ON v.username = u.username
                  WHERE v.VolunteerID = ?";
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
        $query = "UPDATE " . $this->table . " SET Address = ?, EmergencyContact = ?, AppointedBy = ?, Available = ? WHERE VolunteerID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssis", $this->Address, $this->EmergencyContact, $this->AppointedBy, $this->Available, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE VolunteerID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(VolunteerID, 5) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'VOL-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}