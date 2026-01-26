<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');  

$aid = $conn->real_escape_string($_GET['id']); 

$cid = $conn->real_escape_string($_GET['id']); 

$undeleteQueryAnimal = "UPDATE animal SET isDeleted = 0 WHERE Animal_ID = '$aid'";
$result1 = $conn->query($undeleteQueryAnimal);

$undeleteQueryCruelty = "UPDATE crueltyreport SET isDeleted = 0 WHERE crueltyID = '$cid'";
$result2 = $conn->query($undeleteQueryAnimal);

if ($result1) {
    // Success: show alert and redirect
    echo "<script>
        alert('Animal has been successfully reinstated!');
        window.location.href='deleted_records.php';
    </script>";
} else {
    // Error
    echo "Error: " . $conn->error;
}


if ($result2) {
    // Success: show alert and redirect
    echo "<script>
        alert('Animal has been successfully reinstated!');
        window.location.href='deleted_records.php';
    </script>";
} else {
    // Error
    echo "Error: " . $conn->error;
}
?>
