<?php
// Include file koneksi database
include 'koneksi.php';

// Tangkap data yang dikirimkan melalui form
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Query untuk melakukan update data POI berdasarkan koordinat
$sql = "UPDATE poi SET Latitude='$latitude', Longitude='$longitude' WHERE Latitude='$latitude' AND Longitude='$longitude'";

if ($conn->query($sql) === TRUE) {
    echo "Data POI berhasil diupdate";
} else {
    echo "Error updating record: " . $conn->error;
}

// Tutup koneksi database
$conn->close();
?>
