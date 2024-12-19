<?php
session_start();
require_once 'config/database.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plat_nomor = $_POST['plat_nomor'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    
    // Generate kode parkir
    $kode_parkir = date('ymd') . rand(1000, 9999);
    
    // Insert ke database
    $result = $database->kendaraan->insertOne([
        'kode_parkir' => $kode_parkir,
        'plat_nomor' => $plat_nomor,
        'jenis_kendaraan' => $jenis_kendaraan,
        'waktu_masuk' => new MongoDB\BSON\UTCDateTime(),
        'status' => 'active',
        'admin_id' => $_SESSION['admin_id'] 
    ]);
    
    if ($result->getInsertedCount() > 0) {
        $success = "Kendaraan berhasil didaftarkan dengan kode parkir: " . $kode_parkir;
    } else {
        $error = "Gagal mendaftarkan kendaraan!";
    }
}

// Mengambil daftar kendaraan yang baru masuk hari ini
$today_start = new MongoDB\BSON\UTCDateTime(strtotime("today") * 1000);
$kendaraan_masuk = $database->kendaraan->find([
    'waktu_masuk' => ['$gte' => $today_start],
    'status' => 'active',
    'admin_id' => $_SESSION['admin_id']
]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kendaraan Masuk - ParkEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-details">
            <i class='bx bxs-parking'></i>
            <span class="logo_name">ParkEase</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="dashboard.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="link_name">Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="kendaraan_masuk.php">
                    <i class='bx bx-log-in-circle'></i>
                    <span class="link_name">Kendaraan Masuk</span>
                </a>
            </li>
            <li>
                <a href="kendaraan_keluar.php">
                    <i class='bx bx-log-out-circle'></i>
                    <span class="link_name">Kendaraan Keluar</span>
                </a>
            </li>
            <li>
                <a href="laporan.php">
                    <i class='bx bxs-report'></i>
                    <span class="link_name">Laporan</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class='bx bx-log-out'></i>
                    <span class="link_name">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='bx bx-menu sidebarBtn'></i>
                <span class="dashboard">Kendaraan Masuk</span>
            </div>
            <div class="profile-details">
                <span class="admin_name"><?php echo $_SESSION['username']; ?></span>
                <i class='bx bx-user'></i>
            </div>
        </nav>

        <div class="home-content">
            <div class="sales-boxes">
                <div class="recent-sales box">
                    <div class="title"></div>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="recent-sales box mb-4">
                        <div class="title">Input Kendaraan Masuk</div>
                        <div class="card-body">
                            <form action="simpan_kendaraan_masuk.php" method="POST" class="row g-3">
                                <div class="col-md-4">
                                    <label for="plat_nomor" class="form-label">Plat Nomor</label>
                                    <input type="text" class="form-control" id="plat_nomor" name="plat_nomor" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                                    <select class="form-select" id="jenis_kendaraan" name="jenis_kendaraan" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="Motor">Motor</option>
                                        <option value="Mobil">Mobil</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="lokasi_parkir" class="form-label">Lokasi Parkir</label>
                                    <input type="text" class="form-control" id="lokasi_parkir" name="lokasi_parkir" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class='bx bx-save'></i> Simpan Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="title">Daftar Kendaraan Masuk Hari Ini</div>
                    <div class="sales-details">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plat Nomor</th>
                                    <th>Jenis Kendaraan</th>
                                    <th>Waktu Masuk</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Mengambil data kendaraan masuk hari ini
                                $today_start = new MongoDB\BSON\UTCDateTime(strtotime("today") * 1000);
                                $kendaraan_masuk = $database->transaksi->find([
                                    'waktu_masuk' => ['$gte' => $today_start],
                                    'waktu_keluar' => ['$exists' => false],
                                    'status_pembayaran' => 'belum lunas'
                                ]);

                                foreach ($kendaraan_masuk as $kendaraan): 
                                ?>
                                <tr>
                                    <td><?php echo $kendaraan['plat_nomor']; ?></td>
                                    <td><?php echo $kendaraan['jenis_kendaraan']; ?></td>
                                    <td><?php 
                                        $waktuMasuk = $kendaraan['waktu_masuk']->toDateTime();
                                        $waktuMasuk->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                        echo $waktuMasuk->format('H:i:s'); 
                                    ?></td>
                                    <td><?php echo $kendaraan['lokasi_parkir']; ?></td>
                                    <td><span class="badge bg-warning">Parkir</span></td>
                                    <td>
                                        <a href="kendaraan_keluar.php?id=<?php echo $kendaraan['_id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class='bx bx-log-out-circle'></i> Proses Keluar
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>