<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');

if(isset($_GET['q'])){
    $q = $conn->real_escape_string($_GET['q']);

    // Search in the Animal table by Animal_Name (case-insensitive)
    $sql = "SELECT Animal_ID, Animal_Name, Animal_Type, Animal_Breed 
            FROM animal 
            WHERE Animal_Name LIKE '%$q%' AND isDeleted = 0
            ORDER BY Animal_Name ASC 
            LIMIT 10";

    $result = $conn->query($sql);

    if(!$result){
        echo "Database query failed: " . $conn->error;
        exit;
    }

    if($result->num_rows > 0){
        echo "<ul style='list-style:none; padding-left:0; margin:0;'>";
        while($row = $result->fetch_assoc()){
            echo "<li style='padding:5px 10px; border-bottom:1px solid #ddd;'>"
                 .htmlspecialchars($row['Animal_Name']) 
                 . " (ID: ".htmlspecialchars($row['Animal_ID']).", Type: ".htmlspecialchars($row['Animal_Type']).")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='padding:5px 10px;'>No matching animals found.</p>";
    }
}
?>


