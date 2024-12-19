<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$kendaraan_id = new MongoDB\BSON\ObjectId($_GET['id']);

// Ambil data kendaraan
$kendaraan = $database->kendaraan->findOne([
    '_id' => $kendaraan_id,
    'admin_id' => $_SESSION['admin_id']
]);

if ($kendaraan) {
    // Hitung durasi
    $waktu_masuk = $kendaraan->waktu_masuk->toDateTime();
    $waktu_keluar = new DateTime();
    
    // Hitung durasi dalam jam
    $durasi = $waktu_masuk->diff($waktu_keluar);
    $jam = $durasi->h + ($durasi->days * 24);
    
    // Tentukan tarif (contoh sederhana)
    $tarif_per_jam = 5000; // Misalnya Rp 5000 per jam
    $total_biaya = $jam * $tarif_per_jam;

    // Update status kendaraan
    $database->kendaraan->updateOne(
        ['_id' => $kendaraan_id],
        ['$set' => [
            'status' => 'inactive',
            'waktu_keluar' => new MongoDB\BSON\UTCDateTime(),
            'total_biaya' => $total_biaya
        ]]
    );

    // Redirect dengan pesan sukses
    $_SESSION['pesan'] = "Kendaraan berhasil keluar. Total biaya: Rp " . number_format($total_biaya);
    header('Location: dashboard.php');
    exit();
} else {
    $_SESSION['error'] = "Kendaraan tidak ditemukan";
    header('Location: dashboard.php');
    exit();
}
?>