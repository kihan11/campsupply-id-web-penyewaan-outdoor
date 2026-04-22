<?php
include 'koneksi.php';

$nama            = $_POST['nama'];
$alamat          = $_POST['alamat'];
$telepon         = $_POST['telepon'];
$alat            = $_POST['alat'];
$jumlah          = $_POST['jumlah'];
$tanggal_sewa    = $_POST['tanggal_sewa'];
$tanggal_kembali = $_POST['tanggal_kembali'];
$metode          = $_POST['metode'];

$query = "INSERT INTO penyewaan 
          (nama_pelanggan, alamat, telepon, alat, jumlah, tanggal_sewa, tanggal_kembali, metode_pembayaran, status)
          VALUES 
          ('$nama', '$alamat', '$telepon', '$alat', '$jumlah', '$tanggal_sewa', '$tanggal_kembali', '$metode', 'Dipinjam')";

if (mysqli_query($conn, $query)) {
    header("Location: index.php");
    exit;
} else {
    echo "Gagal menyimpan data: " . mysqli_error($conn);
}
?>