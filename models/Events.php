<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Events {

    private $db;


    private $table = "events";
    private $event_id;
    private $title;
    private $details;
    private $event_date;
    private $isDeleted = 0;
    private $addedBy;



    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Getter and Setter for event_id
    public function getEventId() {
        return $this->event_id;
    }
    public function setEventId($event_id) {
        $this->event_id = $event_id;
    }

    // Getter and Setter for title
    public function getTitle() {
        return $this->title;
    }
    public function setTitle($title) {
        $this->title = $title;
    }

    // Getter and Setter for details
    public function getDetails() {
        return $this->details;
    }
    public function setDetails($details) {
        $this->details = $details;
    }

    // Getter and Setter for event_date
    public function getEventDate() {
        return $this->event_date;
    }
    public function setEventDate($event_date) {
        $this->event_date = $event_date;
    }

    // Getter and Setter for isDeleted
    public function getIsDeleted() {
        return $this->isDeleted;
    }
    public function setIsDeleted($isDeleted) {
        $this->isDeleted = $isDeleted;
    }

    // Getter and Setter for addedBy
    public function getAddedBy() {
        return $this->addedBy;
    }
    public function setAddedBy($addedBy) {
        $this->addedBy = $addedBy;
    }



    public function generateEventId() {
        $query = "SELECT MAX(CAST(SUBSTRING(event_id, 6) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'EVT-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }


    public function create(){
        $query = "INSERT INTO $this->table (event_id, title, details, event_date, isDeleted, addedBy) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssssis', $this->event_id, $this->title, $this->details, $this->event_date, $this->isDeleted, $this->addedBy);
        return $stmt->execute();
    }


    public function update($event_id){
        $query = "UPDATE $this->table SET title = ?, details = ?, event_date = ?, isDeleted = ? WHERE event_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssis', $this->title, $this->details, $this->event_date, $this->isDeleted, $event_id);
        return $stmt->execute();
    }

    public function softdelete($event_id){
        $query = "UPDATE $this->table SET isDeleted = 1 WHERE event_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $event_id);
        return $stmt->execute();
    }

    public function hardDelete($event_id){
        $query = "DELETE FROM $this->table WHERE event_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $event_id);
        return $stmt->execute();
    }

    public function getAll(){
        $query = "select * from $this->table where isDeleted = 0 order by event_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    

}























?>