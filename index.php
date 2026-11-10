<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web GIS Sebaran Rumah Sakit Kota Malang</title>

    <!-- Leaflet CSS Library -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        .btn-tambah {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: #ffa7ed;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        /* Wadah Tabel dengan Scrollbar */
        .table-container {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        /* Wadah Peta Leaflet */
        #map {
            height: 480px;
            width: 100%;
            border: 2px solid #ccc;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <h2>Web GIS Sebaran Rumah Sakit Kota Malang</h2>
    
    <a href="input/index.html" class="btn-tambah">+ Input Data Rumah Sakit Baru</a>

    <?php
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "rumahsakit_mlg";

    // 1. Koneksi ke database
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM rumahsakit";
    $result = $conn->query($sql);
    ?>

    <h3>Tabel Data Rumah Sakit</h3>

    <!-- WADAH TABEL DENGAN PENOMORAN BERURUTAN -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th> <!-- Diubah dari ID menjadi No -->
                    <th>Nama Rumah Sakit</th>
                    <th>Jenis</th>
                    <th>Alamat</th>
                    <th>Kecamatan</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th colspan="2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = 1; // Inisialisasi nomor urut dari angka 1
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $no++ . "</td> <!-- Menampilkan 1, 2, 3, dst -->
                            <td>" . $row["nama"] . "</td>
                            <td>" . $row["jenis"] . "</td>
                            <td>" . $row["alamat"] . "</td>
                            <td>" . $row["kec"] . "</td>
                            <td>" . $row["latitude"] . "</td>
                            <td>" . $row["longitude"] . "</td>
                            <td><a href='delete.php?id=" . $row["id"] . "'>Hapus</a></td> <!-- Tetap pakai id asli database -->
                            <td><a href='edit/index.php?id=" . $row["id"] . "'>Edit</a></td> <!-- Tetap pakai id asli database -->
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Belum ada data rumah sakit.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- WADAH PETA LEAFLETJS -->
    <h3>Peta Sebaran Rumah Sakit</h3>
    <div id="map"></div>

    <!-- Leaflet JavaScript Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        /* Inisialisasi Peta - Center Kota Malang */
        var map = L.map('map').setView([-7.9826, 112.6308], 13);

        /* Basemap 1: OpenStreetMap */
        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        /* Basemap 2: Esri World Imagery */
        var esriWorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data &copy; Google'
        });

        var googleStreet = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data &copy; Google'});

        var pinkIcon = L.icon({
            iconUrl: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 36" width="24" height="36"><path fill="%23FF69B4" stroke="%23C71585" stroke-width="1.5" d="M12 0C5.373 0 0 5.373 0 12c0 9 12 24 12 24s12-15 12-24c0-6.627-5.373-12-12-12z"/><circle cx="12" cy="12" r="4.5" fill="%23FFFFFF"/></svg>',
            iconSize: [25, 37],
            iconAnchor: [12, 37],
            popupAnchor: [1, -32]
        });    

        /* Layer Group Marker */
        var hospitalLayer = L.layerGroup().addTo(map);

        /* Render Marker dari Database MySQL */
        <?php
        if ($result->num_rows > 0) {
            $result->data_seek(0); // Reset pointer query ke baris pertama
            while($row = $result->fetch_assoc()) {
                $id    = $row['id'];
                $nama  = addslashes($row['nama']);
                $jenis = addslashes($row['jenis']);
                $alamat= addslashes($row['alamat']);
                $lat   = $row['latitude'];
                $long  = $row['longitude'];

                echo "L.marker([$lat, $long])
                    .bindPopup('<b>$nama</b><br>$jenis<br>$alamat<br><br><a href=\"edit/index.php?id=$id\">Edit</a> | <a href=\"delete.php?id=$id\">Hapus</a>')
                    .addTo(hospitalLayer);\n";
            }
        }
        ?>

        /* Control Layer */
        var baseMaps = {
            "OpenStreetMap": osm,
            "Esri World Imagery": esriWorldImagery,
            "Google Maps Street": googleStreet,
            "Google Maps Satellite": googleSat
        };

        var overlayMaps = {
            "Sebaran Rumah Sakit": hospitalLayer
        };

        L.control.layers(baseMaps, overlayMaps, {collapsed: false}).addTo(map);

        /* Scale Bar */
        L.control.scale({
            maxWidth: 150, 
            position: 'bottomright'
        }).addTo(map);
    </script>

    <?php $conn->close(); ?>
</body>
</html>