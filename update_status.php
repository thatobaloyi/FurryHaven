<?php
require('config/databaseconnection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['crueltyID'])) {
    $crueltyID = $conn->real_escape_string($_POST['crueltyID']);
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : null;

    // get current admin (adjust depending on how you store it in session)
    $currentAdmin = $_SESSION['username'] ?? 'Unknown Admin';

    if ($status) {
        // normal manual status change
        $sql = "UPDATE crueltyreport 
                SET status = '$status', assignedTo = '$currentAdmin'
                WHERE crueltyID = '$crueltyID'";
    } else {
        // auto-assign when report is first opened (from modal click)
        $sql = "UPDATE crueltyreport 
                SET status = 'Pending', assignedTo = '$currentAdmin'
                WHERE crueltyID = '$crueltyID' AND status = 'Open'";
    }

    if ($conn->query($sql)) {
        // If this was a manual status change via dropdown, redirect back
        if ($status) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            echo "Auto-updated";
        }
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
$conn->close();
?>

