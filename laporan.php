<?php
session_start();
require_once 'config/database.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Filter tanggal
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Convert dates to MongoDB format
$start = new MongoDB\BSON\UTCDateTime(strtotime($start_date) * 1000);
$end = new MongoDB\BSON\UTCDateTime(strtotime($end_date . ' +1 day') * 1000);

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->parkease;
    $collection = $database->transaksi;

    // Mengambil data laporan
    $laporan = $collection->find([
        'waktu_masuk' => [
            '$gte' => $start,
            '$lt' => $end
        ]
    ])->toArray();

    // Hitung statistik
    $total_kendaraan = $collection->count([
        'waktu_masuk' => ['$gte' => $start, '$lt' => $end]
    ]);

    $total_motor = $collection->count([
        'waktu_masuk' => ['$gte' => $start, '$lt' => $end],
        'jenis_kendaraan' => 'Motor'
    ]);

    $total_mobil = $collection->count([
        'waktu_masuk' => ['$gte' => $start, '$lt' => $end],
        'jenis_kendaraan' => 'Mobil'
    ]);

    // Hitung total pendapatan
    $total_pendapatan = 0;
    foreach ($laporan as $data) {
        if (isset($data->biaya)) {
            $total_pendapatan += $data->biaya;
        }
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - ParkEase</title>
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
            <li class="active">
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
                <span class="dashboard">Laporan Parkir</span>
            </div>
            <div class="profile-details">
                <span class="admin_name"><?php echo $_SESSION['username']; ?></span>
                <i class='bx bx-user'></i>
            </div>
        </nav>

        <div class="home-content">
            <!-- Filter Section -->
            <div class="sales-boxes mb-4">
                <div class="recent-sales box">
                    <div class="title">Filter Laporan</div>
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" 
                                   value="<?php echo $start_date; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="end_date" 
                                   value="<?php echo $end_date; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="statistics-container">
                <div class="sales-boxes mb-4">
                    <div class="recent-sales box">
                        <div class="title">Statistik</div>
                        <div class="overview-boxes p-3">
                            <div class="box">
                                <div class="right-side">
                                    <div class="box-topic">Total Kendaraan</div>
                                    <div class="number">
                                        <?php echo $total_kendaraan; ?>
                                        <i class='bx bx-car cart'></i>
                                    </div>
                                </div>
                            </div>
                            <div class="box">
                                <div class="right-side">
                                    <div class="box-topic">Total Motor</div>
                                    <div class="number">
                                        <?php echo $total_motor; ?>
                                        <i class='bx bx-cycling cart two'></i>
                                    </div>
                                </div>
                            </div>

                            <div class="box">
                                <div class="right-side">
                                    <div class="box-topic">Total Kendaraan</div>
                                    <div class="number">
                                        <?php echo $total_kendaraan; ?>
                                        <i class='bx bx-car cart'></i>
                                    </div>
                                </div>
                            </div>

                            <div class="box">
                                <div class="right-side">
                                    <div class="box-topic">Total Pendapatan</div>
                                    <div class="number">
                                        Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?>
                                        <i class='bx bx-money cart four'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Report -->
            <div class="sales-boxes">
                <div class="recent-sales box">
                    <div class="title">Detail Laporan</div>
                    <div class="sales-details">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Plat Nomor</th>
                                    <th>Jenis</th>
                                    <th>Waktu Masuk</th>
                                    <th>Waktu Keluar</th>
                                    <th>Durasi</th>
                                    <th>Biaya</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laporan as $data): ?>
                                <tr>
                                    <td><?php echo $data->plat_nomor; ?></td>
                                    <td><?php echo $data->jenis_kendaraan; ?></td>
                                    <td><?php 
                                        $waktuMasuk = $data->waktu_masuk->toDateTime();
                                        $waktuMasuk->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                        echo $waktuMasuk->format('d/m/Y H:i:s'); 
                                    ?></td>
                                    <td>
                                        <?php 
                                        echo isset($data->waktu_keluar) 
                                            ? $data->waktu_keluar->toDateTime()->format('d/m/Y H:i:s')
                                            : '-';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $waktu_masuk = $data->waktu_masuk->toDateTime();
                                        $waktu_keluar = isset($data->waktu_keluar) 
                                            ? $data->waktu_keluar->toDateTime()
                                            : new DateTime();
                                        $durasi = $waktu_masuk->diff($waktu_keluar);
                                        echo $durasi->format('%H jam %i menit');
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo isset($data->biaya) 
                                            ? 'Rp ' . number_format($data->biaya, 0, ',', '.')
                                            : '-';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($data->status_pembayaran === 'belum lunas'): ?>
                                            <span class="badge bg-warning">Parkir</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php endif; ?>
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