<?php
    require_once("config.php");

    $conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

    if ($conn->connect_error){
        die("Connection Falied " . $conn->connect_error);
    }
?>