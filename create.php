<?php
// Include file koneksi database
include 'koneksi.php';

// Tangkap data yang dikirimkan melalui form
$lokasi = $_POST['lokasi'];
$deskripsi = $_POST['deskripsi'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Query untuk menambahkan data POI ke dalam tabel poi
$sql = "INSERT INTO poi (Lokasi, Deskripsi, Latitude, Longitude) VALUES ('$lokasi', '$deskripsi', '$latitude', '$longitude')";
if ($conn->query($sql) === TRUE) {
    echo "Data POI berhasil ditambahkan";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Tutup koneksi database
$conn->close();
?>
