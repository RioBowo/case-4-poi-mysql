<?php
// Include file koneksi database
include 'koneksi.php';

// Query untuk membaca semua data POI dari tabel poi
$sql = "SELECT * FROM poi";
$result = $conn->query($sql);

// Array untuk menyimpan data POI
$poiData = array();

// Jika terdapat data, simpan ke dalam array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $poiData[] = $row;
    }
}

// Encode array ke format JSON dan kirimkan sebagai respons
echo json_encode($poiData);

// Tutup koneksi database
$conn->close();
?>
