<?php
include_once __DIR__ . '/../config/databaseconnection.php';

class PledgeInstallments
{
    private $db;
    private $table = 'pledge_installments';

    // Attributes
    private $installmentId;   // InstallmentID
    private $pledgeId;        // PledgeID
    private $dueDate;         // DueDate
    private $amountDue;       // AmountDue
    private $status;          // Status
    private $isDeleted;       // isDeleted

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setInstallmentId($installmentId) { $this->installmentId = $installmentId; }
    public function setPledgeId($pledgeId) { $this->pledgeId = $pledgeId; }
    public function setDueDate($dueDate) { $this->dueDate = $dueDate; }
    public function setAmountDue($amountDue) { $this->amountDue = $amountDue; }
    public function setStatus($status) { $this->status = $status; }
    public function setIsDeleted($isDeleted) { $this->isDeleted = $isDeleted; }

    // Getters
    public function getInstallmentId() { return $this->installmentId; }
    public function getPledgeId() { return $this->pledgeId; }
    public function getDueDate() { return $this->dueDate; }
    public function getAmountDue() { return $this->amountDue; }
    public function getStatus() { return $this->status; }
    public function getIsDeleted() { return $this->isDeleted; }

    // CREATE
    public function create()
    {
        $query = "INSERT INTO $this->table 
            (InstallmentID, PledgeID, DueDate, AmountDue, Status, isDeleted)
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param(
            "sssdsi",
            $this->installmentId,
            $this->pledgeId,
            $this->dueDate,
            $this->amountDue,
            $this->status,
            $this->isDeleted
        );
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        return true;
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
    public function findOne($installmentId)
    {
        $query = "SELECT * FROM $this->table WHERE InstallmentID = ? AND isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $installmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // READ by PledgeID
    public function findByPledgeId($pledgeId)
    {
        $query = "SELECT * FROM $this->table WHERE PledgeID = ? AND isDeleted = 0 ORDER BY DueDate ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $pledgeId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // UPDATE
    public function update($installmentId)
    {
        $query = "UPDATE $this->table SET 
            PledgeID = ?, 
            DueDate = ?, 
            AmountDue = ?, 
            Status = ?, 
            isDeleted = ?
            WHERE InstallmentID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssdsis",
            $this->pledgeId,
            $this->dueDate,
            $this->amountDue,
            $this->status,
            $this->isDeleted,
            $installmentId
        );
        return $stmt->execute();
    }

    // SOFT DELETE
    public function softDelete($installmentId)
    {
        $query = "UPDATE $this->table SET isDeleted = 1 WHERE InstallmentID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $installmentId);
        return $stmt->execute();
    }

    // HARD DELETE
    public function delete($installmentId)
    {
        $query = "DELETE FROM $this->table WHERE InstallmentID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $installmentId);
        return $stmt->execute();
    }

    public function generateID(){
        $query = "SELECT MAX(CAST(SUBSTRING(InstallmentID, 13) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'INSTALLMENT-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    public function getNextIdNumber() {
        $query = "SELECT MAX(CAST(SUBSTRING(InstallmentID, 13) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return ($result['max_id']) ? $result['max_id'] + 1 : 1;
    }

    public function findAllOrderedByOverdue() {
        global $conn;
        $today = date('Y-m-d');
        $result = $conn->query("SELECT * FROM pledge_installments 
            WHERE isDeleted = 0 
            ORDER BY (Status = 'Pending' AND DueDate < '$today') DESC, DueDate ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

?>