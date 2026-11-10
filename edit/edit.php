<?php
$id        = $_POST['id'];
$nama      = $_POST['nama'];
$jenis     = $_POST['jenis'];
$alamat    = $_POST['alamat'];
$kec       = $_POST['kec'];
$latitude  = $_POST['latitude'];
$longitude = $_POST['longitude'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rumahsakit_mlg";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "UPDATE rumahsakit SET 
        nama='$nama', 
        jenis='$jenis', 
        alamat='$alamat', 
        kec='$kec', 
        latitude=$latitude, 
        longitude=$longitude 
        WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../index.php");
    exit();
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>