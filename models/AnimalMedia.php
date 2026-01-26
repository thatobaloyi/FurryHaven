<?php
include_once __DIR__ . '/../config/databaseconnection.php';
class AnimalMedia
{
    private $conn;
    private $table = 'animalmedia';

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function create($id, $animalID, $mediaType, $filePath, $caption, $uploadedBy)
    {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (anmediaID, animalID, mediaType, filePath, caption, uploadedBy) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id, $animalID, $mediaType, $filePath, $caption, $uploadedBy);
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Database Error: " . $this->conn->error;
            return false;
        }
    }


    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(anmediaID, 7) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'MEDIA-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    public function deleteImageByFilename($animalID, $filename)
    {

        $sql = "DELETE FROM animalmedia WHERE animalID = ? AND filePath = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            // Handle error
            return false;
        }

        $stmt->bind_param("ss", $animalID, $filename);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }


    // In your models/AnimalMedia.php

    public function getImagesByAnimalId($animalId)
    {
        $stmt = $this->conn->prepare("SELECT filePath FROM animalmedia WHERE animalID = ?");
        $stmt->bind_param("s", $animalId);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['filePath'];
        }
        return $images;
    }
}
