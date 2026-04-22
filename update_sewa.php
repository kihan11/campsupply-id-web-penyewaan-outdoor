<?php
include 'koneksi.php';

$id              = $_POST['id'];
$nama            = $_POST['nama'];
$alamat          = $_POST['alamat'];
$telepon         = $_POST['telepon'];
$alat            = $_POST['alat'];
$jumlah          = $_POST['jumlah'];
$tanggal_sewa    = $_POST['tanggal_sewa'];
$tanggal_kembali = $_POST['tanggal_kembali'];
$metode          = $_POST['metode'];
$status          = $_POST['status'];

$query = "UPDATE penyewaan SET
          nama_pelanggan='$nama',
          alamat='$alamat',
          telepon='$telepon',
          alat='$alat',
          jumlah='$jumlah',
          tanggal_sewa='$tanggal_sewa',
          tanggal_kembali='$tanggal_kembali',
          metode_pembayaran='$metode',
          status='$status'
          WHERE id='$id'";

if (mysqli_query($conn, $query)) {
    header("Location: index.php");
    exit;
} else {
    echo "Gagal update data: " . mysqli_error($conn);
}
?>