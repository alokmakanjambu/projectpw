<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kendaraan Keluar - ParkEase</title>
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
            <li class="active">
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
                <span class="dashboard">Kendaraan Keluar</span>
            </div>
            <div class="profile-details">
                <span class="admin_name"><?php echo $_SESSION['username'] ?? 'Admin'; ?></span>
                <i class='bx bx-user'></i>
            </div>
        </nav>

        <div class="home-content">
            <div class="sales-boxes">
                <!-- Section Cek Kendaraan -->
                <div class="recent-sales box mb-4">
                    <div class="title">Cek Kendaraan</div>
                    <div class="card-body">
                        <form action="proses_transaksi.php" method="POST" class="row g-3">
                            <div class="col-md-4">
                                <label for="plat_nomor" class="form-label">Plat Nomor Kendaraan</label>
                                <input type="text" class="form-control" id="plat_nomor" name="plat_nomor" 
                                       placeholder="Masukkan plat nomor" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-search'></i> Cek Kendaraan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Section Daftar Kendaraan -->
                <div class="recent-sales box">
                    <div class="title">Daftar Kendaraan Aktif</div>
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
                                $kendaraan_aktif = getKendaraanAktif();
                                foreach ($kendaraan_aktif as $kendaraan): 
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
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#prosesKeluarModal"
                                                data-id="<?php echo $kendaraan['_id']; ?>"
                                                data-plat="<?php echo $kendaraan['plat_nomor']; ?>"
                                                data-jenis="<?php echo $kendaraan['jenis_kendaraan']; ?>"
                                                data-waktu-masuk="<?php 
                                                    $waktuMasuk = $kendaraan['waktu_masuk']->toDateTime();
                                                    $waktuMasuk->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                                    echo $waktuMasuk->format('Y-m-d H:i:s'); 
                                                ?>">
                                            <i class='bx bx-log-out-circle'></i> Proses
                                        </button>
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

    <!-- Modal Proses Keluar -->
    <div class="modal fade" id="prosesKeluarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Proses Kendaraan Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="simpan_transaksi.php" method="POST">
                        <input type="hidden" name="id_parkir" id="id_parkir">
                        
                        <div class="mb-3">
                            <label class="form-label">Plat Nomor</label>
                            <input type="text" class="form-control" id="modal_plat_nomor" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Waktu Masuk</label>
                            <input type="text" class="form-control" id="modal_waktu_masuk" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Waktu Keluar</label>
                            <input type="text" class="form-control" id="modal_waktu_keluar" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Durasi</label>
                            <input type="text" class="form-control" id="modal_durasi" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Total Biaya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" name="biaya" id="modal_biaya" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Bayar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="jumlah_bayar" id="jumlah_bayar" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kembalian</label>
                            <div class="alert alert-info">
                                <span id="kembalian">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="mt-3 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-check'></i> Proses Pembayaran
                            </button>
                            <button type="button" class="btn btn-success" onclick="cetakStruk()">
                                <i class='bx bx-printer'></i> Cetak Struk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan template struk yang tersembunyi -->
    <div id="strukTemplate" style="display: none;">
        <div class="struk">
            <h4 class="text-center">ParkEase</h4>
            <p class="text-center">Struk Parkir</p>
            <hr>
            <p>Kode Parkir: <span id="struk_kode"></span></p>
            <p>Plat Nomor: <span id="struk_plat"></span></p>
            <p>Jenis: <span id="struk_jenis"></span></p>
            <p>Waktu Masuk: <span id="struk_masuk"></span></p>
            <p>Waktu Keluar: <span id="struk_keluar"></span></p>
            <p>Durasi: <span id="struk_durasi"></span></p>
            <hr>
            <p>Total Biaya: <span id="struk_biaya"></span></p>
            <p>Bayar: <span id="struk_bayar"></span></p>
            <p>Kembalian: <span id="struk_kembalian"></span></p>
            <hr>
            <p class="text-center">Terima Kasih</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>