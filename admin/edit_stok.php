<?php
require_once "auth.php";
require_once __DIR__ . "/koneksi.php";

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID alat tidak valid.");
}

$stmt = $conn->prepare("SELECT * FROM stok_alat WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data alat tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_alat  = trim($_POST['nama_alat'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? '');
    $harga_sewa = (int)($_POST['harga_sewa'] ?? 0);
    $stok       = (int)($_POST['stok'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama_alat === '' || $kategori === '' || $harga_sewa < 0 || $stok < 0) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        $update = $conn->prepare("UPDATE stok_alat SET nama_alat = ?, kategori = ?, harga_sewa = ?, stok = ?, keterangan = ? WHERE id = ?");
        $update->bind_param("ssiisi", $nama_alat, $kategori, $harga_sewa, $stok, $keterangan, $id);

        if ($update->execute()) {
            header("Location: stok.php");
            exit;
        } else {
            $error = "Gagal menyimpan perubahan.";
        }
    }

    $stmt = $conn->prepare("SELECT * FROM stok_alat WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Stok Alat</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f3f4f6;
            margin:0;
            padding:0;
        }
        .container{
            width:550px;
            margin:40px auto;
            background:#fff;
            padding:30px;
            border-radius:12px;
            box-shadow:0 4px 18px rgba(0,0,0,0.08);
        }
        h2{
            margin-top:0;
            margin-bottom:20px;
        }
        label{
            display:block;
            margin-top:12px;
            font-weight:bold;
        }
        input, textarea{
            width:100%;
            padding:10px;
            margin-top:6px;
            box-sizing:border-box;
            border:1px solid #d1d5db;
            border-radius:6px;
        }
        textarea{
            resize:vertical;
        }
        .btn-group{
            margin-top:20px;
            display:flex;
            gap:10px;
        }
        button, .btn-kembali{
            padding:10px 16px;
            border:none;
            border-radius:6px;
            text-decoration:none;
            cursor:pointer;
            display:inline-block;
        }
        button{
            background:#2563eb;
            color:#fff;
        }
        .btn-kembali{
            background:#6b7280;
            color:#fff;
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
    <div class="container">
        <h2>Edit Stok Alat</h2>

        <?php if(isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Nama Alat</label>
            <input type="text" name="nama_alat" value="<?= htmlspecialchars($data['nama_alat']); ?>" required>

            <label>Kategori</label>
            <input type="text" name="kategori" value="<?= htmlspecialchars($data['kategori'] ?? ''); ?>" required>

            <label>Harga Sewa / Hari</label>
            <input type="number" name="harga_sewa" min="0" value="<?= htmlspecialchars($data['harga_sewa'] ?? 0); ?>" required>

            <label>Stok</label>
            <input type="number" name="stok" min="0" value="<?= htmlspecialchars($data['stok']); ?>" required>

            <label>Keterangan</label>
            <textarea name="keterangan" rows="4"><?= htmlspecialchars($data['keterangan'] ?? ''); ?></textarea>

            <div class="btn-group">
                <button type="submit">Simpan Perubahan</button>
                <a href="stok.php" class="btn-kembali">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>