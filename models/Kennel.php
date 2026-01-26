<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Kennel
{
    private $db;
    private $table = 'kennel';

    // Attributes
    private $id; // Kennel_ID
    private $name; // Kennel_Name
    private $address; // Kennel_Address
    private $capacity; // Kennel_Capacity
    private $occupancy; // Kennel_Occupancy
    private $contactDetails; // Kennel_ContactDetails
    private $type; // Kennel_Type
    private $fullCapacity; // FullCapacity

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setId($id){
        $this->id = $id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setAddress($address)
    {
        $this->address = $address;
    }
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }
    public function setOccupancy($occupancy)
    {
        $this->occupancy = $occupancy;
    }
    public function setContactDetails($contactDetails)
    {
        $this->contactDetails = $contactDetails;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
    public function setFullCapacity($fullCapacity)
    {
        $this->fullCapacity = $fullCapacity;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function getCapacity()
    {
        return $this->capacity;
    }
    public function getOccupancy()
    {
        return $this->occupancy;
    }
    public function getContactDetails()
    {
        return $this->contactDetails;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getFullCapacity()
    {
        return $this->fullCapacity;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (Kennel_ID, Kennel_Name, Kennel_Address, Kennel_Capacity, Kennel_Occupancy, Kennel_ContactDetails, Kennel_Type, FullCapacity) VALUES (?,?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssiisss", $this->id, $this->name, $this->address, $this->capacity, $this->occupancy, $this->contactDetails, $this->type, $this->fullCapacity);
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
        $query = "SELECT * FROM " . $this->table . " WHERE Kennel_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET Kennel_Name = ?, Kennel_Address = ?, Kennel_Capacity = ?, Kennel_Occupancy = ?, Kennel_ContactDetails = ?, Kennel_Type = ?, FullCapacity = ? WHERE Kennel_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssiissss", $this->name, $this->address, $this->capacity, $this->occupancy, $this->contactDetails, $this->type, $this->fullCapacity, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE Kennel_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(Kennel_ID, 6) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'KENN-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    public function incrementOccupancy($id)
    {
        $query = "UPDATE " . $this->table . " SET Kennel_Occupancy = Kennel_Occupancy + 1 WHERE Kennel_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function decrementOccupancy($id)
    {
        $query = "UPDATE " . $this->table . " SET Kennel_Occupancy = Kennel_Occupancy - 1 WHERE Kennel_ID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}
