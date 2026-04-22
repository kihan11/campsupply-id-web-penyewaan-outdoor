<?php
session_start();
require_once __DIR__ . "/koneksi.php";

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['error'] = "Username dan password wajib diisi.";
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama_lengkap'];

        $_SESSION['notif'] = [
            'type' => 'success',
            'message' => 'Login berhasil. Selamat datang di halaman admin.'
        ];

        header("Location: dashboard.php");
        exit;
    }
}

$_SESSION['error'] = "Username atau password salah.";
header("Location: login.php");
exit;
?>