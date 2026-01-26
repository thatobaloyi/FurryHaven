<?php
require('config/databaseconnection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin username (make sure this is set when the admin logs in)
$admin = $_SESSION['username'] ?? 'Admin';

if(isset($_GET['type'], $_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];

    if($type === 'animal') {

        // 1️⃣ Fetch the application data
        $stmt = $conn->prepare("SELECT * FROM animalapplication WHERE animalappID=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()) {
            $animalID = $row['animalID'];
            $adopterID = $row['username'];
            $screeningNotes = $row['screeningNotes'] ?? '';

            // 2️⃣ Check if this animal is already adopted/fostered
            $checkStmt = $conn->prepare("SELECT * FROM adoption WHERE AnimalID=? AND AdoptionStatus='Accepted'");
            $checkStmt->bind_param("s", $animalID);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if($checkResult->num_rows > 0) {
                die("This animal has already been adopted/fostered.");
            }

            // 3️⃣ Generate a unique AdoptionID
            $adoptionID = 'ADOPT-' . str_pad(rand(1,99999), 5, '0', STR_PAD_LEFT);

            // 4️⃣ Insert into adoption table
            $stmt2 = $conn->prepare("INSERT INTO adoption 
                (AdoptionID, AnimalID, AdopterID, AdoptionStatus, ScreeningNotes, ApprovedBy) 
                VALUES (?,?,?,?,?,?)");
            $adoptionStatus = 'Accepted';
            $stmt2->bind_param("ssssss", $adoptionID, $animalID, $adopterID, $adoptionStatus, $screeningNotes, $admin);
            $stmt2->execute();

            // 5️⃣ Update animalapplication status
            $status = 'Accepted';
            $stmt3 = $conn->prepare("UPDATE animalapplication SET applicationStatus=? WHERE animalappID=?");
            $stmt3->bind_param("ss", $status, $id);
            $stmt3->execute();

            // 6️⃣ Redirect to approved adopters page
            header("Location: approvedadopters2.php");
            exit();

        } else {
            die("Application not found.");
        }

    } else {
        die("Invalid application type.");
    }
} else {
    die("Invalid request.");
}
?>

