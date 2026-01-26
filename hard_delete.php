<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');  

$aid = $conn->real_escape_string($_GET['id']); 

$undeleteQuery = "DELETE FROM animal WHERE Animal_ID = '$aid'";
$result = $conn->query($undeleteQuery);

if ($result) {
    // Success: show alert and redirect
    echo "<script>
        alert('Animal has been successfully deleted!');
        window.location.href='deleted_records.php';
    </script>";
} else {
    // Error
    echo "Error: " . $conn->error;
}
?>
