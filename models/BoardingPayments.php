<?php
include_once __DIR__ . '/../config/databaseconnection.php';


class BoardingPayments
{
    private $db;
    private $table = 'boarding_payment';

    // Attributes
    private $boardPaymentID;
    private $bookingID;
    private $dailyRate;
    private $daysStayed;
    private $paymentMethod;
    private $paymentStatus;
    private $isDeleted;


    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    public function setBoardPaymentID($boardPaymentID)
    {
        $this->boardPaymentID = $boardPaymentID;
    }

    public function setBookingID($bookingID)
    {
        $this->bookingID = $bookingID;
    }

    public function setDailyRate($dailyRate)
    {
        $this->dailyRate = $dailyRate;
    }

    public function setDaysStayed($daysStayed)
    {
        $this->daysStayed = $daysStayed;
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }   

    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }



    public function getboardPaymentID()
    {
        return $this->boardPaymentID;
    }


    public function getBookingID()
    {
        return $this->bookingID;
    }

    public function getDailyRate()
    {
        return $this->dailyRate;
    }

    public function getDaysStayed()
    {
        return $this->daysStayed;
    }


    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }


    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }



    public function createBoardingPayment()
    {
        $query = "INSERT INTO " . $this->table . " (boardPaymentID, bookingID, dailyRate, daysStayed, paymentMethod, paymentStatus, isDeleted) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        // Correct types: s(varchar), s(varchar), d(int/double), i(int), s(enum), s(enum), i(tinyint)
        $stmt->bind_param("ssdissi", $this->boardPaymentID, $this->bookingID, $this->dailyRate, $this->daysStayed, $this->paymentMethod, $this->paymentStatus, $this->isDeleted);
        return $stmt->execute();
    }


    public function updateBoardingPayment()
    {
        $query = "UPDATE " . $this->table . " SET bookingID = ?, dailyRate = ?, daysStayed = ?, paymentMethod = ?, paymentStatus = ?, isDeleted = ? WHERE boardPaymentID = ?";
        $stmt = $this->db->prepare($query);
        // Correct types: s, d, i, s, s, i, s
        $stmt->bind_param("sdissis", $this->bookingID, $this->dailyRate, $this->daysStayed, $this->paymentMethod, $this->paymentStatus, $this->isDeleted, $this->boardPaymentID);
        return $stmt->execute();
    }


    public function partialUpdateBoardingPayment($fields)
    {
        $setClause = [];
        $types = '';
        $values = [];

        foreach ($fields as $field) {
            if (property_exists($this, $field)) {
                $setClause[] = "$field = ?";
                $types .= is_int($this->$field) ? 'i' : (is_double($this->$field) ? 'd' : 's');
                $values[] = $this->$field;
            }
        }

        if (empty($setClause)) {
            return false; // No valid fields to update
        }

        $values[] = $this->boardPaymentID;
        $types .= 's';

        $query = "UPDATE " . $this->table . " SET " . implode(', ', $setClause) . " WHERE boardPaymentID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function softDeleteBoardingPayment($id)
    {
        $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE boardPaymentID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function hardDeleteBoardingPayment($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE boardPaymentID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }


    public function generateID() {
        $query = "SELECT MAX(CAST(SUBSTRING(boardPaymentID, 7) AS UNSIGNED)) as max_id FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'Pay-Bd' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

   
}

































?>