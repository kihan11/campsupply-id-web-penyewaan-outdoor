<?php
require_once "auth.php";
require_once __DIR__ . "/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_alat  = trim($_POST['nama_alat'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? '');
    $harga_sewa = (int)($_POST['harga_sewa'] ?? 0);
    $stok       = (int)($_POST['stok'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama_alat === '' || $kategori === '' || $harga_sewa < 0 || $stok < 0) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO stok_alat (nama_alat, kategori, harga_sewa, stok, keterangan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiis", $nama_alat, $kategori, $harga_sewa, $stok, $keterangan);

        if ($stmt->execute()) {
            header("Location: stok.php");
            exit;
        } else {
            $error = "Gagal menambahkan alat. Bisa jadi nama alat sudah ada.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Alat</title>
    <style>
        body{
            font-family:Arial;
            background:#f5f7fa;
            margin:0;
            padding:0;
        }
        .box{
            width:500px;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:10px;
            box-shadow:0 4px 14px rgba(0,0,0,0.08);
        }
        h2{
            margin-top:0;
        }
        label{
            font-weight:bold;
            display:block;
            margin-top:12px;
        }
        input, textarea{
            width:100%;
            padding:10px;
            margin:8px 0;
            box-sizing:border-box;
            border:1px solid #d1d5db;
            border-radius:6px;
        }
        button, .btn-kembali{
            padding:10px 15px;
            background:#2563eb;
            color:#fff;
            border:none;
            border-radius:6px;
            text-decoration:none;
            display:inline-block;
            cursor:pointer;
            margin-top:10px;
        }
        .btn-kembali{
            background:#6b7280;
        }
        .error{
            color:#b91c1c;
            background:#fee2e2;
            padding:10px;
            border-radius:6px;
            margin-bottom:15px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Tambah Alat</h2>

        <?php if(isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nama Alat</label>
            <input type="text" name="nama_alat" required>

            <label>Kategori</label>
            <input type="text" name="kategori" placeholder="Contoh: Tenda, Peralatan Masak, Alat Perairan" required>

            <label>Harga Sewa / Hari</label>
            <input type="number" name="harga_sewa" min="0" required>

            <label>Stok</label>
            <input type="number" name="stok" min="0" required>

            <label>Keterangan</label>
            <textarea name="keterangan" rows="4"></textarea>

            <button type="submit">Simpan</button>
            <a href="stok.php" class="btn-kembali">Kembali</a>
        </form>
    </div>
</body>
</html>