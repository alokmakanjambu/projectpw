<?php
session_start();
require_once 'config/database.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Statistik untuk user yang login
$total_aktif = $database->kendaraan->count([
    'status' => 'active',
    'admin_id' => $_SESSION['admin_id']
]);

$total_hari_ini = $database->kendaraan->count([
    'waktu_masuk' => [
        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime("today") * 1000)
    ],
    'admin_id' => $_SESSION['admin_id']
]);

// Mengambil daftar kendaraan aktif
$kendaraan_aktif = $database->kendaraan->find([
    'status' => 'active',
    'admin_id' => $_SESSION['admin_id']
]);

// Statistik dashboard
$total_kendaraan = $database->kendaraan->count([
    'admin_id' => $_SESSION['admin_id']
]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ParkEase</title>
    <!-- CSS -->
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
            <li class="active">
                <a href="dashboard.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="link_name">Dashboard</span>
                </a>
            </li>
            <li>
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
                <span class="dashboard">Dashboard</span>
            </div>
            <div class="profile-details">
                <span class="admin_name"><?php echo $_SESSION['username']; ?></span>
                <i class='bx bx-user'></i>
            </div>
        </nav>

        <div class="home-content">
            <div class="overview-boxes">
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Total Kendaraan Aktif</div>
                        <div class="number">
                            <?php 
                            $kendaraan_aktif = getKendaraanAktif();
                            echo count($kendaraan_aktif); 
                            ?>
                        </div>
                    </div>
                    <i class='bx bxs-car-garage cart'></i>
                </div>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Total Hari Ini</div>
                        <div class="number"><?php echo $total_hari_ini; ?></div>
                        <div class="indicator">
                            <i class='bx bx-time'></i>
                            <span class="text">Kendaraan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sales-boxes">
                <div class="recent-sales box">
                    <div class="title">Kendaraan Aktif</div>
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
                                <?php foreach ($kendaraan_aktif as $kendaraan): ?>
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

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>