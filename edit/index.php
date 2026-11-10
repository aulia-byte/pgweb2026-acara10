<!DOCTYPE html>
<html>
<head>
    <title>Form Edit Data Rumah Sakit</title>
</head>
<body>
    <h2>Form Edit Data Rumah Sakit</h2>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rumahsakit_mlg";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "SELECT * FROM rumahsakit WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
?>
    <form action="edit.php" onsubmit="return validateForm(event)" method="post">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label>Nama Rumah Sakit:</label><br>
        <input type="text" id="nama" name="nama" value="<?php echo $row['nama']; ?>"><br><br>

        <label>Jenis Rumah Sakit:</label><br>
        <input type="text" id="jenis" name="jenis" value="<?php echo $row['jenis']; ?>"><br><br>

        <label>Alamat:</label><br>
        <input type="text" id="alamat" name="alamat" value="<?php echo $row['alamat']; ?>"><br><br>

        <label>Kecamatan:</label><br>
        <input type="text" id="kec" name="kec" value="<?php echo $row['kec']; ?>"><br><br>

        <label>Latitude:</label><br>
        <input type="text" id="latitude" name="latitude" value="<?php echo $row['latitude']; ?>"><br><br>

        <label>Longitude:</label><br>
        <input type="text" id="longitude" name="longitude" value="<?php echo $row['longitude']; ?>"><br><br>

        <input type="submit" value="Update Data">
    </form>
<?php
    }
} else {
    echo "Data tidak ditemukan.";
}
$conn->close();
?>

    <p id="informasi" style="color: red; font-weight: bold;"></p>

    <script>
    function validateForm(event) {
        let nama = document.getElementById("nama").value;
        let jenis = document.getElementById("jenis").value;
        let alamat = document.getElementById("alamat").value;
        let kec = document.getElementById("kec").value;
        let latitude = document.getElementById("latitude").value;
        let longitude = document.getElementById("longitude").value;
        let text = "";

        if (nama.trim() === "" || jenis.trim() === "" || alamat.trim() === "" || kec.trim() === "") {
            text = "Semua bidang teks wajib diisi!";
            event.preventDefault();
            document.getElementById("informasi").innerHTML = text;
            return false;
        }

        if (isNaN(latitude) || latitude.trim() === "") {
            text = "Latitude harus berupa angka/desimal!";
            event.preventDefault();
            document.getElementById("informasi").innerHTML = text;
            return false;
        }

        if (isNaN(longitude) || longitude.trim() === "") {
            text = "Longitude harus berupa angka/desimal!";
            event.preventDefault();
            document.getElementById("informasi").innerHTML = text;
            return false;
        }

        return true;
    }
    </script>
</body>
</html>