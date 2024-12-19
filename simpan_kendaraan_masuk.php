<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plat_nomor = strtoupper($_POST['plat_nomor']);
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $lokasi_parkir = $_POST['lokasi_parkir'];

    try {
        simpanTransaksi($plat_nomor, $jenis_kendaraan, $lokasi_parkir);
        header('Location: kendaraan_masuk.php?status=success');
    } catch (Exception $e) {
        header('Location: kendaraan_masuk.php?status=error&message=' . urlencode($e->getMessage()));
    }
} 