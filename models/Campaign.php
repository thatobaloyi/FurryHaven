<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Campaign
{
    private $db;
    private $table = 'campaign';

    // Attributes
    private $id; // CampaignID
    private $name; // CampaignName
    private $description; // CampaignDescription
    private $startDate; // Campaign_StartDate
    private $endDate; // Campagin_EndDate
    private $raisedAmount;
    private $goalAmount; // TargetAmount
    private $createdBy; // InitiatedBy

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    public function setRaisedAmount($amount){
        $this->raisedAmount=$amount;
    }

    public function getRaisedAmount(){
        return $this->raisedAmount;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }
    public function setGoalAmount($goalAmount)
    {
        $this->goalAmount = $goalAmount;
    }
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
    public function getDescription()
    {
        return $this->description;
    }
    public function getStartDate()
    {
        return $this->startDate;
    }
    public function getEndDate()
    {
        return $this->endDate;
    }
    public function getGoalAmount()
    {
        return $this->goalAmount;
    }
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (CampaignID, CampaignName, CampaignDescription, Campaign_StartDate, Campaign_EndDate, TargetAmount, InitiatedBy) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssss", $this->id, $this->name, $this->description, $this->startDate, $this->endDate, $this->goalAmount, $this->createdBy);
        return $stmt->execute();
    }

    public function findAll()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findOne($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE CampaignID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . " SET CampaignName = ?, CampaignDescription = ?, Campaign_StartDate = ?, Campagin_EndDate = ?, TargetAmount = ?, InitiatedBy = ? WHERE CampaignID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssss", $this->name, $this->description, $this->startDate, $this->endDate, $this->goalAmount, $this->createdBy, $id);
        return $stmt->execute();
    }
    public function updateRaisedAmount($id)
    {
        $query = "UPDATE " . $this->table . " SET amountRaised = ? WHERE CampaignID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $this->raisedAmount, $id);
        return $stmt->execute();
    }

    

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE CampaignID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function softDelete($id)
    {
        $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE CampaignID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }
        return true;
    }


    public function restore(string $id)
    {
        $query = "UPDATE $this->table SET isDeleted = 0 WHERE CampaignID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(CampaignID, 6) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'CAMP-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}