<?php
require_once __DIR__ . "/koneksi.php";

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$nama     = "Administrator";

$stmt = $conn->prepare("INSERT INTO admin (username, password, nama_lengkap) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $password, $nama);

if ($stmt->execute()) {
    echo "Admin berhasil dibuat.<br>";
    echo "Username: admin<br>";
    echo "Password: admin123";
} else {
    echo "Gagal membuat admin: " . $stmt->error;
}
?>