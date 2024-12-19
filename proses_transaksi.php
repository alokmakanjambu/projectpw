<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plat_nomor = $_POST['plat_nomor'];
    
    try {
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $database = $client->parkease;
        $collection = $database->transaksi;

        // Cari kendaraan berdasarkan plat nomor yang masih parkir
        $kendaraan = $collection->findOne([
            'plat_nomor' => $plat_nomor,
            'status_pembayaran' => 'belum lunas'
        ]);

        if ($kendaraan) {
            // Redirect kembali dengan data kendaraan
            $_SESSION['temp_kendaraan'] = $kendaraan;
            header('Location: kendaraan_keluar.php?status=found');
        } else {
            header('Location: kendaraan_keluar.php?status=not_found');
        }
    } catch (Exception $e) {
        header('Location: kendaraan_keluar.php?status=error');
    }
} 