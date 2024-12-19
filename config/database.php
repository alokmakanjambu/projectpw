<?php
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->parkease;
} catch (Exception $e) {
    die("Error koneksi database: " . $e->getMessage());
}

function simpanTransaksi($platNomor, $jenisKendaraan, $lokasiParkir) {
    try {
        global $database;
        $collection = $database->transaksi;

        $waktuMasuk = new DateTime();
        
        $transaksi = [
            'plat_nomor' => $platNomor,
            'jenis_kendaraan' => $jenisKendaraan,
            'waktu_masuk' => new MongoDB\BSON\UTCDateTime($waktuMasuk->getTimestamp() * 1000),
            'lokasi_parkir' => $lokasiParkir,
            'status_pembayaran' => 'belum lunas'
        ];

        $result = $collection->insertOne($transaksi);
        return $result->getInsertedId();
    } catch (Exception $e) {
        throw new Exception("Error menyimpan transaksi: " . $e->getMessage());
    }
}

function updateTransaksiKeluar($idTransaksi, $waktuKeluar, $biaya) {
    try {
        global $database;
        $collection = $database->transaksi;

        if (!($idTransaksi instanceof MongoDB\BSON\ObjectId)) {
            $idTransaksi = new MongoDB\BSON\ObjectId($idTransaksi);
        }

        $updateResult = $collection->updateOne(
            ['_id' => $idTransaksi],
            ['$set' => [
                'waktu_keluar' => new MongoDB\BSON\UTCDateTime(strtotime($waktuKeluar) * 1000),
                'biaya' => (int)$biaya,
                'status_pembayaran' => 'lunas'
            ]]
        );

        if ($updateResult->getModifiedCount() === 0) {
            throw new Exception("Tidak ada data yang diupdate");
        }

        return true;
    } catch (Exception $e) {
        throw new Exception("Error memperbarui transaksi: " . $e->getMessage());
    }
}

function getKendaraanAktif() {
    try {
        global $database;
        $collection = $database->transaksi;

        $result = $collection->find([
            'waktu_keluar' => ['$exists' => false],
            'status_pembayaran' => 'belum lunas'
        ]);

        return iterator_to_array($result);
    } catch (Exception $e) {
        throw new Exception("Error mengambil data kendaraan aktif: " . $e->getMessage());
    }
}

function getTransaksiById($id) {
    try {
        global $database;
        $collection = $database->transaksi;

        $objectId = new MongoDB\BSON\ObjectId($id);
        return $collection->findOne(['_id' => $objectId]);
    } catch (Exception $e) {
        throw new Exception("Error mengambil data transaksi: " . $e->getMessage());
    }
}
?>