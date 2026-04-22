<?php
require_once "auth.php";
require_once __DIR__ . "/koneksi.php";

$result = $conn->query("SELECT * FROM stok_alat ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Stok</title>
    <style>
        body{font-family:Arial;background:#f5f7fa;margin:0}
        .nav{background:#1f2937;padding:15px}
        .nav a{color:#fff;text-decoration:none;margin-right:15px}
        .container{padding:20px}
        table{width:100%;border-collapse:collapse;background:#fff}
        th,td{padding:12px;border:1px solid #ddd}
        .btn{
            background:#2563eb;
            color:#fff;
            text-decoration:none;
            padding:8px 12px;
            border-radius:6px;
            display:inline-block;
            margin-right:5px;
        }
        .btn-green{
            background:#16a34a;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="stok.php">Kelola Stok</a>
        <a href="penyewaan.php">Data Penyewaan</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Kelola Stok Alat</h2>
        <p>
            <a class="btn btn-green" href="tambah_alat.php">+ Tambah Alat</a>
        </p>

        <table>
            <tr>
                <th>ID</th>
                <th>Nama Alat</th>
                <th>Stok</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>

            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['nama_alat']); ?></td>
                <td><?= $row['stok']; ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                <td>
                    <a class="btn" href="edit_stok.php?id=<?= $row['id']; ?>">Edit Stok</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>