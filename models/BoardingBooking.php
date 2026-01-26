<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class BoardingBooking
{
    private $db;
    private $table = 'boarding_bookings';

    // Attributes
    private $boardBookID;
    private $boardingAnimalID;
    private $booking_start_date;
    private $booking_end_date;
    private $cageNumber;
    private $status;
    private $isDeleted; 

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setBoardBookID($boardBookID)
    {
        $this->boardBookID = $boardBookID;
    }

    public function setBoardingAnimalID($boardingAnimalID)
    {
        $this->boardingAnimalID = $boardingAnimalID;
    }

    public function setBookingStartDate($booking_start_date)
    {
        $this->booking_start_date = $booking_start_date;
    }

    public function setBookingEndDate($booking_end_date)
    {
        $this->booking_end_date = $booking_end_date;
    }

    public function setCageNumber($cageNumber)
    {
        $this->cageNumber = $cageNumber;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    // Getters
    public function getBoardBookID()
    {
        return $this->boardBookID;
    }

    public function getBoardingAnimalID()
    {
        return $this->boardingAnimalID;
    }

    public function getBookingStartDate()
    {
        return $this->booking_start_date;
    }

    public function getBookingEndDate()
    {
        return $this->booking_end_date;
    }

    public function getCageNumber()
    {
        return $this->cageNumber;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    public function getDb()
    {
        return $this->db;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (boardBookID, boardingAnimalID, booking_start_date, booking_end_date, cageNumber, status, isDeleted) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssi", $this->boardBookID, $this->boardingAnimalID, $this->booking_start_date, $this->booking_end_date, $this->cageNumber, $this->status, $this->isDeleted);
        return $stmt->execute();
         
    }


    public function update($id)
    {
       $query = "UPDATE " . $this->table . " SET boardingAnimalID = ?, booking_start_date = ?, booking_end_date = ?, cageNumber = ?, status = ?, isDeleted = ? WHERE boardBookID = ?";
       $stmt = $this->db->prepare($query);
       $stmt->bind_param("sssisii", $this->boardingAnimalID, $this->booking_start_date, $this->booking_end_date, $this->cageNumber, $this->status, $this->isDeleted, $id);
       return $stmt->execute();
    }

    public function partialUpdate($id, $fields)
    {
        $setClause = [];
        $types = '';
        $values = [];

        foreach ($fields as $field) {
            if (property_exists($this, $field)) {
                $setClause[] = "$field = ?";
                $types .= $this->getParamType($this->$field);
                $values[] = $this->$field;
            }
        }

        if (empty($setClause)) {
            return false; // No valid fields to update
        }

        $setClauseStr = implode(", ", $setClause);
        $query = "UPDATE " . $this->table . " SET $setClauseStr WHERE boardBookID = ?";
        $stmt = $this->db->prepare($query);

        // Bind parameters dynamically
        $types .= 's'; // for boardBookID
        $values[] = $id;
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
    }


    public function softDelete($id)
    {
        $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE boardBookID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function hardDelete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE boardBookID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function doesBookingExist($animalID, $startDate, $endDate)
    {
        $query = "SELECT boardBookID FROM " . $this->table . " 
                  WHERE boardingAnimalID = ? 
                    AND isDeleted = 0
                    AND booking_start_date <= ?
                    AND booking_end_date >= ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $animalID, $endDate, $startDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function generateID()
    {
        // The prefix 'booking-' is 8 characters long, so we start extracting the number from position 9.
        $query = "SELECT MAX(CAST(SUBSTRING(boardBookID, 9) AS UNSIGNED)) as max_id FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'booking-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }


    public function getAvailableCages($startDate, $endDate)
    {
        // A cage is unavailable if a booking exists that overlaps with the requested date range.
        // Overlap occurs if an existing booking's start date is before/on the new end date,
        // AND the existing booking's end date is after/on the new start date.
        $query = "SELECT c.CageID 
                  FROM cage c 
                  INNER JOIN kennel k ON c.Kennel_ID = k.Kennel_ID 
                  WHERE k.Kennel_Type = 'Boarded Animals' 
                  AND c.CageID NOT IN (
                    SELECT cageNumber FROM " . $this->table . " 
                    WHERE booking_start_date <= ? 
                      AND booking_end_date >= ? 
                      AND isDeleted = 0
                )";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $endDate, $startDate);
        $stmt->execute(); 
        $result = $stmt->get_result();
        $availableCages = [];
        while ($row = $result->fetch_assoc()) {
            $availableCages[] = $row['CageID'];
        }
        return $availableCages;
    }

    public function assignCage($startDate, $endDate)
    {
        $availableCages = $this->getAvailableCages($startDate, $endDate);
        if (!empty($availableCages)) {
            return $availableCages[0]; // Assign the first available cage
        }
        return null; // No cages available
    }

    public function checkIn($id, $anid, $cage)
    {
        $this->db->begin_transaction();
        try {
            $query1 = "UPDATE " . $this->table . " SET status = 'Checked In' WHERE boardBookID = ?";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bind_param("s", $id);
            $stmt1->execute();

            $query2 = "UPDATE cage SET Boarded_Animal_ID = ?, Occupancy_Status = 1 WHERE CageID = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("ss", $anid, $cage);
            $stmt2->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function checkOut($id, $anid, $cage)
    {
        $this->db->begin_transaction();
        try {
            $query1 = "UPDATE " . $this->table . " SET status = 'Checked Out' WHERE boardBookID = ?";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bind_param("s", $id);
            $stmt1->execute();

            $query2 = "UPDATE cage SET  Boarded_Animal_ID = NULL, Occupancy_Status = 0 WHERE CageID = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("s", $cage);
            $stmt2->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) { // It's good practice to log the error: $e->getMessage()
            $this->db->rollback();
            return false;
        }
    }


    public function showAllBookings(){
        $query = "SELECT * FROM " . $this->table . " WHERE isDeleted = 0 order by booking_start_date desc";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getUpcomingBookings() {
        $today = date('Y-m-d');
        $query = "SELECT b.* , a.name AS animal_name, u.FirstName AS owner_first, u.LastName AS owner_last FROM " . $this->table . " b
                  JOIN boarding_animals a ON b.boardingAnimalID = a.boardAnimalID
                  JOIN users u ON a.ownerID = u.username
                  WHERE booking_start_date > ? AND b.isDeleted = 0 
                  ORDER BY booking_start_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function showActiveBookings(){
        $query = "SELECT b.*, 
                     a.name AS animal_name, 
                     u.FirstName AS owner_first, 
                     u.LastName AS owner_last
              FROM " . $this->table . " b
              JOIN boarding_animals a ON b.boardingAnimalID = a.boardAnimalID
              JOIN users u ON a.ownerID = u.username
              WHERE b.status = 'Checked In' AND b.isDeleted = 0
              ORDER BY b.booking_start_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getUserActiveBookings($username) {
        $query = "SELECT b.*, 
                     a.name AS animal_name, 
                     u.FirstName AS owner_first, 
                     u.LastName AS owner_last
              FROM " . $this->table . " b
              JOIN boarding_animals a ON b.boardingAnimalID = a.boardAnimalID
              JOIN users u ON a.ownerID = u.username
              WHERE b.status = 'Checked In' AND b.isDeleted = 0 AND a.ownerID = ?
              ORDER BY b.booking_start_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getUserUpcomingBookings($username) {
        $today = date('Y-m-d');
        $query = "SELECT b.*, 
                     a.name AS animal_name, 
                     u.FirstName AS owner_first, 
                     u.LastName AS owner_last
              FROM " . $this->table . " b
              JOIN boarding_animals a ON b.boardingAnimalID = a.boardAnimalID
              JOIN users u ON a.ownerID = u.username
              WHERE b.booking_start_date > ? AND b.isDeleted = 0 AND a.ownerID = ?
              ORDER BY b.booking_start_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $today, $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    // new: return bookings with status = 'Checked Out'
    public function getCompletedBookings() {
        $query = "SELECT b.*, 
                         a.name AS animal_name, 
                         u.FirstName AS owner_first, 
                         u.LastName AS owner_last
                  FROM " . $this->table . " b
                  JOIN boarding_animals a ON b.boardingAnimalID = a.boardAnimalID
                  JOIN users u ON a.ownerID = u.username
                  WHERE b.status = 'Checked Out' AND b.isDeleted = 0
                  ORDER BY b.booking_end_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getUserCompletedBookings($username) {
        $query = "SELECT b.*, 
                         a.name AS animal_name, 
                         u.FirstName AS owner_first, 
                         u.LastName AS owner_last
                  FROM " . $this->table . " b
                  JOIN boarding_animals a ON b.boardingAnimalID = a.boardAnimalID
                  JOIN users u ON a.ownerID = u.username
                  WHERE b.status = 'Checked Out' AND b.isDeleted = 0 AND a.ownerID = ?
                  ORDER BY b.booking_end_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

}
