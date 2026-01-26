<?php 
include_once __DIR__ . '/../config/databaseconnection.php';










class Pledges
{
    private $db;
    private $table = 'pledges';

    // Attributes
    private $id;                // PledgeID
    private $donorId;           // DonorID
    private $campaignId;        // CampaignID
    private $pledgeAmount;      // PledgeAmount
    private $installmentCount;  // InstallmentCount
    private $startDate;         // StartDate
    private $frequency;         // Frequency
    private $isActive;          // IsActive
    private $createdAt;         // CreatedAt
    private $isDeleted;         // isDeleted
    private $status;            // Status

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setDonorId($donorId) { $this->donorId = $donorId; }
    public function setCampaignId($campaignId) { $this->campaignId = $campaignId; }
    public function setPledgeAmount($pledgeAmount) { $this->pledgeAmount = $pledgeAmount; }
    public function setInstallmentCount($installmentCount) { $this->installmentCount = $installmentCount; }
    public function setStartDate($startDate) { $this->startDate = $startDate; }
    public function setFrequency($frequency) { $this->frequency = $frequency; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setIsDeleted($isDeleted) { $this->isDeleted = $isDeleted; }
    public function setStatus($status) { $this->status = $status; }

    // Getters
    public function getId() { return $this->id; }
    public function getDonorId() { return $this->donorId; }
    public function getCampaignId() { return $this->campaignId; }
    public function getPledgeAmount() { return $this->pledgeAmount; }
    public function getInstallmentCount() { return $this->installmentCount; }
    public function getStartDate() { return $this->startDate; }
    public function getFrequency() { return $this->frequency; }
    public function getIsActive() { return $this->isActive; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getIsDeleted() { return $this->isDeleted; }
    public function getStatus() { return $this->status; }



    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // CREATE
    public function create() {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO pledges (PledgeID, DonorID, CampaignID, PledgeAmount, InstallmentCount, StartDate, Frequency, IsActive, Status, CreatedAt, isDeleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssdississi",
            $this->id,
            $this->donorId,
            $this->campaignId,
            $this->pledgeAmount,
            $this->installmentCount,
            $this->startDate,
            $this->frequency,
            $this->isActive,
            $this->status,      // <-- new
            $this->createdAt,
            $this->isDeleted
        );
        return $stmt->execute();
    }

    // READ ALL
    public function findAll()
    {
        $query = "SELECT * FROM $this->table WHERE isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    // READ ONE
    public function findOne($id)
    {
        $query = "SELECT * FROM $this->table WHERE PledgeID = ? AND isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // UPDATE
    public function update($id)
    {
        $query = "UPDATE $this->table SET 
            DonorID = ?, 
            CampaignID = ?, 
            PledgeAmount = ?, 
            InstallmentCount = ?, 
            StartDate = ?, 
            Frequency = ?, 
            IsActive = ?, 
            Status = ?,         -- new
            CreatedAt = ?, 
            isDeleted = ?
            WHERE PledgeID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssdississis",
            $this->donorId,
            $this->campaignId,
            $this->pledgeAmount,
            $this->installmentCount,
            $this->startDate,
            $this->frequency,
            $this->isActive,
            $this->status,      // <-- new
            $this->createdAt,
            $this->isDeleted,
            $id
        );
        return $stmt->execute();
    }

    // SOFT DELETE
    public function softDelete($id)
    {
        $query = "UPDATE $this->table SET isDeleted = 1 WHERE PledgeID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    // HARD DELETE
    public function delete($id)
    {
        $query = "DELETE FROM $this->table WHERE PledgeID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateID(){
        $query = "SELECT MAX(CAST(SUBSTRING(PledgeID, 8) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'PLEDGE-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    public function findByUsername($username)
    {
        $query = "SELECT * FROM $this->table WHERE Username = ? AND isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findAllOrderedByOverdue() {
        global $conn;
        $today = date('Y-m-d');
        $result = $conn->query("SELECT * FROM pledges WHERE isDeleted = 0 
            ORDER BY (Status = 'Pending' AND StartDate < '$today') DESC, StartDate ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

}
