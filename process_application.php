<?php
require('config/databaseconnection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin username (from session)
$admin = $_SESSION['username'] ?? 'Admin';

// Make sure all required parameters exist
if(isset($_GET['type'], $_GET['id'], $_GET['action'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];
    $action = $_GET['action'];

    if($type === 'animal') {
        // Fetch the application
        $stmt = $conn->prepare("SELECT * FROM animalapplication WHERE animalappID=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if(!$row = $result->fetch_assoc()) {
            die("Application not found.");
        }

        $animalID = $row['animalID'];
        $adopterID = $row['username'];
        $screeningNotes = $row['screeningNotes'] ?? '';

        if($action === 'approve') {
            // Check if animal is already adopted/fostered
            $checkStmt = $conn->prepare("SELECT * FROM adoption WHERE AnimalID=? AND AdoptionStatus='Accepted'");
            $checkStmt->bind_param("s", $animalID);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if($checkResult->num_rows > 0) {
                die("This animal has already been adopted/fostered.");
            }

            // Insert into adoption
            $adoptionID = 'ADOPT-' . str_pad(rand(1,99999), 5, '0', STR_PAD_LEFT);
            $adoptionStatus = 'Accepted';

            $stmt2 = $conn->prepare("INSERT INTO adoption 
                (AdoptionID, AnimalID, AdopterID, AdoptionStatus, ScreeningNotes, ApprovedBy) 
                VALUES (?,?,?,?,?,?)");
            $stmt2->bind_param("ssssss", $adoptionID, $animalID, $adopterID, $adoptionStatus, $screeningNotes, $admin);
            $stmt2->execute();

            // Update animalapplication status
            $status = 'Accepted';
            $stmt3 = $conn->prepare("UPDATE animalapplication SET applicationStatus=? WHERE animalappID=?");
            $stmt3->bind_param("ss", $status, $id);
            $stmt3->execute();

            // Redirect to approved adopters page
            header("Location: approvedadopters2.php");
            exit();

        } elseif($action === 'disapprove') {
            // Mark application as rejected/deleted
            $status = 'Rejected';
            $stmt4 = $conn->prepare("UPDATE animalapplication SET applicationStatus=?, isDeleted=1 WHERE animalappID=?");
            $stmt4->bind_param("ss", $status, $id);
            $stmt4->execute();

            // Redirect to deleted records page
            header("Location: deleted_records.php");
            exit();

        } else {
            die("Invalid action.");
        }

    } else {
        die("Invalid application type.");
    }

} else {
    die("Invalid request.");
}
?>
