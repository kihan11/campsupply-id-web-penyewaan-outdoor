<?php
$conn = mysqli_connect("localhost", "root", "", "sewa_camping");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>