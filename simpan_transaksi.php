<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_parkir = $_POST['id_parkir'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $biaya = $_POST['biaya'];
    
    // Update data transaksi
    try {
        // Pastikan id_parkir tidak kosong
        if (empty($id_parkir)) {
            throw new Exception("ID Parkir tidak valid");
        }

        // Konversi string ID menjadi ObjectId
        $objectId = new MongoDB\BSON\ObjectId($id_parkir);
        
        updateTransaksiKeluar(
            $objectId, 
            date('Y-m-d H:i:s'), 
            (int)$biaya
        );
        
        header('Location: kendaraan_keluar.php?status=success');
    } catch (Exception $e) {
        header('Location: kendaraan_keluar.php?status=error&message=' . urlencode($e->getMessage()));
    }
} 