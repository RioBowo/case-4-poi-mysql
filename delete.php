<?php
// Include file koneksi database
include 'koneksi.php';

// Tangkap data yang dikirimkan melalui form
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Query untuk menghapus data POI dari tabel poi berdasarkan koordinat
$sql = "DELETE FROM poi WHERE Latitude='$latitude' AND Longitude='$longitude'";

if ($conn->query($sql) === TRUE) {
    echo "Data POI berhasil dihapus";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Tutup koneksi database
$conn->close();
?>
