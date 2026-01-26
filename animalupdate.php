<?php

require_once("config.php");

$Animal_Name = $_REQUEST['name'];
$Animal_Type = $_REQUEST['type'];
$Animal_Breed = $_REQUEST['breed'];
$Animal_Gender = $_REQUEST['gender'];
$Animal_AgeGroup = $_REQUEST['age'];
$Animal_HealthStatus = $_REQUEST['status'];
$Animal_Name = $_REQUEST['name'];
$Animal_Vacc_Status = $_REQUEST['vaccination'];
$Animal_RescueDate = $_REQUEST['rescuedate'];
$Animal_Rescuelocatioon = $_REQUEST['name'];


$conn = new mysqli(USERNAME, SERVER, PASSWORD, DATABASE);

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);

} else {
    "Successful";
}

$sql = "UPDATE animal SET  "



?>