<?php
// Tangkap data POST dari form HTML
$nama   = $_POST['nama'];
$jenis  = $_POST['jenis'];
$alamat = $_POST['alamat'];
$kec    = $_POST['kec'];
$lat    = $_POST['latitude'];
$long   = $_POST['longitude'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rumahsakit_mlg";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Kolom id terisi otomatis (Auto Increment)
$sql = "INSERT INTO rumahsakit (nama, jenis, alamat, kec, latitude, `longitude`) 
        VALUES ('$nama', '$jenis', '$alamat', '$kec', $lat, $long)";

if ($conn->query($sql) === TRUE) {
    // Kembali otomatis ke tabel utama setelah simpan berhasil
    header("Location: ../index.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>