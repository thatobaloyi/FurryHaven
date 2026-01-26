<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class MedicalProcedure
{
    private $db;

    private $table = "medicalprocedure";
    private $medicalID;
    private $animalID;
    private $vetID;
    private $procedureType;
    private $procedureOutcome;
    private $procedureDate;
    private $details;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setMedicalID($medicalID)
    {
        $this->medicalID = $medicalID;
    }

    public function setAnimalID($animalID)
    {
        $this->animalID = $animalID;
    }

    public function setVetID($vetID)
    {
        $this->vetID = $vetID;
    }

    public function setProcedureType($procedureType)
    {
        $this->procedureType = $procedureType;
    }

    public function setProcedureOutcome($procedureOutcome)
    {
        $this->procedureOutcome = $procedureOutcome;
    }

    public function setProcedureDate($procedureDate)
    {
        $this->procedureDate = $procedureDate;
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }

    // Getters
    public function getMedicalID()
    {
        return $this->medicalID;
    }

    public function getAnimalID()
    {
        return $this->animalID;
    }

    public function getVetID()
    {
        return $this->vetID;
    }

    public function getProcedureType()
    {
        return $this->procedureType;
    }

    public function getProcedureOutcome()
    {
        return $this->procedureOutcome;
    }

    public function getProcedureDate()
    {
        return $this->procedureDate;
    }

    public function getDetails()
    {
        return $this->details;
    }

    // CRUD methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (medicalID, animalID, vetID, procedureType, procedureOutcome, procedureDate, details) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssss", $this->medicalID, $this->animalID, $this->vetID, $this->procedureType, $this->procedureOutcome, $this->procedureDate, $this->details);
        return $stmt->execute();
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOne($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE medicalID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findByAnimalId($animalID)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE animalID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $animalID);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function update($id)
    {
        $query = "UPDATE $this->table SET procedureOutcome = ?,  details = ? WHERE medicalID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss",  $this->procedureOutcome, $this->details, $id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        return true;
    }


    public function delete($id)
    {
        $query = "UPDATE " . $this->table . " SET isDeleted = 1 WHERE medicalID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s",   $id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        return true;
    }

    // public function delete($id)
    // {
    //     $query = "DELETE FROM " . $this->table . " WHERE medicalID = ?";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->bind_param("s", $id);
    //     return $stmt->execute();
    // }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(medicalID, 4) AS UNSIGNED)) as max_id FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'MP-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }


    public function getMedicalProceduresByVetAndDate($vetID, $date)
    {
        $query = "SELECT mp.*, u.FirstName, u.LastName 
                  FROM " . $this->table . " mp
                  INNER JOIN users u ON u.username = mp.vetID
                  WHERE mp.vetID = ? AND DATE(mp.procedureDate) = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $vetID, $date);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAllMedicalProceduresByRange($start, $end)
    {
        $query = "SELECT mp.*, u.FirstName, u.LastName 
                  FROM " . $this->table . " mp
                  INNER JOIN users u ON u.username = mp.vetID
                  WHERE DATE(mp.procedureDate) BETWEEN ? AND ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $start, $end);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getMedicalProceduresByVetAndRange($vetID, $start, $end)
    {
        $query = "SELECT mp.*, u.FirstName, u.LastName 
                  FROM " . $this->table . " mp
                  INNER JOIN users u ON u.username = mp.vetID
                  WHERE mp.vetID = ? AND DATE(mp.procedureDate) BETWEEN ? AND ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $vetID, $start, $end);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getNumberofProceduresForVetForDate($vetID, $date)
    {
        $query = "SELECT DATE(procedureDate) as day, COUNT(*) as num_procedures
              FROM medicalprocedure
              WHERE vetID = ? AND DATE(procedureDate) BETWEEN ? AND ? AND isDeleted = 0
              GROUP BY day";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $vetID, $date, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['num_procedures'] ?? 0;
    }

    public function getNumberOfProceduresForVetForRange($vetID, $start, $end)
    {
        $query = "SELECT DATE(procedureDate) as day, COUNT(*) as num_procedures
              FROM medicalprocedure
              WHERE vetID = ? AND DATE(procedureDate) BETWEEN ? AND ? AND isDeleted = 0
              GROUP BY day";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $vetID, $start, $end);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getProceduresForVetForDay($vetID, $date)
    {
        $query = "SELECT mp.*, a.Animal_Name as animalName
              FROM medicalprocedure mp
              JOIN animal a ON a.Animal_ID = mp.animalID
              WHERE mp.vetID = ? AND DATE(mp.procedureDate) = ? AND mp.isDeleted = 0
              ORDER BY mp.procedureDate ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $vetID, $date);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function hasIncompleteProcedures($animalID)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE animalID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $animalID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (strtolower($row['procedureOutcome']) !== 'completed') {
                return true;
            }
        }
        return false;
    }
}
