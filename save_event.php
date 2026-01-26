<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$serverName = "is3-dev.ict.ru.ac.za";
$user = "EzTeck";
$password = "Ezt3ck!25";
$database = "ezteck";  

 $conn = new mysqli($serverName, $user ,$password, $database);

include_once __DIR__ . '/./config/databaseconnection.php';

global $conn;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $details = $_POST['details'];
    $date = $_POST['event_date'];

    $stmt = $conn->prepare("INSERT INTO events (title, event_date, details) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $date, $details);
    $stmt->execute();

    header("Location: /");
}
?>