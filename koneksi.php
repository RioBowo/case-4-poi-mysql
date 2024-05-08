<?php
$namahost = "localhost";
$username = "root";
$password = "";
$database = "digital_maps";
$conn = new mysqli($namahost, $username, $password, $database);
if($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}
?>