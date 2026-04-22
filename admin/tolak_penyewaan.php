<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/koneksi.php";

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID tidak valid.");
}

$stmt = $conn->prepare("UPDATE penyewaan SET status = 'ditolak' WHERE id = ? AND status = 'pending'");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: penyewaan.php");
exit;
?>