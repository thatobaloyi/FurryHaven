<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Donation
{
    private $db;
    private $table = 'donations';

    // Attributes
    private $id;                // DonationID
    private $donorId;           // DonorID
    private $campaignId;        // CampaignID
    private $donationType;      // DonationType
    private $donationAmount;    // DonationAmount
    private $donationFlag;      // DonationFlag
    private $donationDate;      // DonationDate
    private $paymentMethod;     // PaymentMethod
    private $receiptIssued;     // ReceiptIssued (varchar)
    private $installmentId;     // installmentID
    private $isDeleted;         // isDeleted
    private $isRecurring;        // IsRecurring
    private $frequency;          // Frequency
    private $nextBillingDate;    // NextBillingDate
    private $isActive;         // isActive

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setDonorId($donorId) { $this->donorId = $donorId; }
    public function setCampaignId($campaignId) { $this->campaignId = $campaignId; }
    public function setDonationType($donationType) { $this->donationType = $donationType; }
    public function setDonationAmount($donationAmount) { $this->donationAmount = $donationAmount; }
    public function setDonationFlag($donationFlag) { $this->donationFlag = $donationFlag; }
    public function setDonationDate($donationDate) { $this->donationDate = $donationDate; }
    public function setPaymentMethod($paymentMethod) { $this->paymentMethod = $paymentMethod; }
    public function setReceiptIssued($receiptIssued) { $this->receiptIssued = $receiptIssued; }
    public function setInstallmentId($installmentId) { $this->installmentId = $installmentId; }
    public function setIsDeleted($isDeleted) { $this->isDeleted = $isDeleted; }
    public function setIsRecurring($isRecurring) { $this->isRecurring = $isRecurring; }
    public function setFrequency($frequency) { $this->frequency = $frequency; }
    public function setNextBillingDate($nextBillingDate) { $this->nextBillingDate = $nextBillingDate; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    // Getters
    public function getId() { return $this->id; }
    public function getDonorId() { return $this->donorId; }
    public function getCampaignId() { return $this->campaignId; }
    public function getDonationType() { return $this->donationType; }
    public function getDonationAmount() { return $this->donationAmount; }
    public function getDonationFlag() { return $this->donationFlag; }
    public function getDonationDate() { return $this->donationDate; }
    public function getPaymentMethod() { return $this->paymentMethod; }
    public function getReceiptIssued() { return $this->receiptIssued; }
    public function getInstallmentId() { return $this->installmentId; }
    public function getIsDeleted() { return $this->isDeleted; }
    public function getIsRecurring() { return $this->isRecurring; }
    public function getFrequency() { return $this->frequency; }
    public function getNextBillingDate() { return $this->nextBillingDate; }
    public function getIsActive() { return $this->isActive; }

    // CREATE
    public function create()
    {
        $query = "INSERT INTO $this->table 
            (DonationID, DonorID, CampaignID, DonationType, DonationAmount, DonationFlag, DonationDate, PaymentMethod, ReceiptIssued, installmentID, isDeleted, IsRecurring, Frequency, NextBillingDate, isActive)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssssdissssiissi",
            $this->id,
            $this->donorId,
            $this->campaignId,
            $this->donationType,
            $this->donationAmount,
            $this->donationFlag,
            $this->donationDate,
            $this->paymentMethod,
            $this->receiptIssued,
            $this->installmentId,
            $this->isDeleted,
            $this->isRecurring,
            $this->frequency,
            $this->nextBillingDate,
            $this->isActive
        );
        if (!$stmt->execute()) {
            throw new Exception($this->db->error);
        }
        return true;
    }

    // READ ALL
    public function findAll()
    {
        $query = "SELECT * FROM $this->table WHERE isDeleted = '0'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Leaderboard (top donations)
    public function leaderboard($limit = 10)
    {
        $query = "SELECT DonorID, SUM(DonationAmount) AS TotalDonated, MAX(DonationDate) AS LastDonation
                  FROM donations
                  WHERE isDeleted = 0
                  GROUP BY DonorID
                  ORDER BY TotalDonated DESC";
        if ($limit && is_int($limit) && $limit > 0) {
            $query .= " LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $limit);
        } else {
            $stmt = $this->db->prepare($query);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    // READ ONE
    public function findOne($id)
    {
        $query = "SELECT * FROM $this->table WHERE DonationID = ? AND isDeleted = '0'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Find by Donor Username
    public function findByUsername($username)
    {
        $query = "SELECT * FROM $this->table WHERE DonorID = ? AND isDeleted = '0'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    // UPDATE
    public function update($id)
    {
        $query = "UPDATE $this->table SET 
            DonorID = ?, 
            CampaignID = ?, 
            DonationType = ?, 
            DonationAmount = ?, 
            DonationFlag = ?, 
            DonationDate = ?, 
            PaymentMethod = ?, 
            ReceiptIssued = ?, 
            installmentID = ?, 
            isDeleted = ?,
            IsRecurring = ?,
            Frequency = ?,
            NextBillingDate = ?,
            isActive = ?
            WHERE DonationID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "sssdisssssissis",
            $this->donorId,
            $this->campaignId,
            $this->donationType,
            $this->donationAmount,
            $this->donationFlag,
            $this->donationDate,
            $this->paymentMethod,
            $this->receiptIssued,
            $this->installmentId,
            $this->isDeleted,
            $this->isRecurring,
            $this->frequency,
            $this->nextBillingDate,
            $this->isActive, // <-- new
            $id
        );
        return $stmt->execute();
    }

    // SOFT DELETE
    public function softDelete($id)
    {
        $query = "UPDATE $this->table SET isDeleted = '1' WHERE DonationID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    // HARD DELETE
    public function delete($id)
    {
        $query = "DELETE FROM $this->table WHERE DonationID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    // Generate new DonationID
    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(DonationID, 6) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'DONA-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    public function findAllRecurringDue()
    {
        $today = date('Y-m-d H:i:s');
        $query = "SELECT * FROM $this->table WHERE IsRecurring = 1 AND NextBillingDate <= ? AND isDeleted = '0'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function updateNextBillingDate($donationId, $nextBillingDate)
    {
        $query = "UPDATE $this->table SET NextBillingDate = ? WHERE DonationID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $nextBillingDate, $donationId);
        return $stmt->execute();
    }

    public function findAllOrderedByDate() {
        global $conn;
        $result = $conn->query("SELECT * FROM donations WHERE isDeleted = 0 ORDER BY DonationDate DESC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
