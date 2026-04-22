<?php
session_start();
require_once __DIR__ . "/admin/koneksi.php";

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    die("ID tidak valid.");
}

$error = '';

if (isset($_POST['update'])) {
    $nama_pelanggan    = trim($_POST['nama_pelanggan'] ?? '');
    $alamat            = trim($_POST['alamat'] ?? '');
    $telepon           = trim($_POST['telepon'] ?? '');
    $alat              = trim($_POST['alat'] ?? '');
    $jumlah            = (int)($_POST['jumlah'] ?? 0);
    $tanggal_sewa      = $_POST['tanggal_sewa'] ?? '';
    $tanggal_kembali   = $_POST['tanggal_kembali'] ?? '';
    $metode_pembayaran = trim($_POST['metode_pembayaran'] ?? '');

    if (
        $nama_pelanggan !== '' &&
        $alamat !== '' &&
        $telepon !== '' &&
        $alat !== '' &&
        $jumlah > 0 &&
        $tanggal_sewa !== '' &&
        $tanggal_kembali !== '' &&
        $metode_pembayaran !== ''
    ) {
        $stmt = $conn->prepare("UPDATE penyewaan SET nama_pelanggan=?, alamat=?, telepon=?, alat=?, jumlah=?, tanggal_sewa=?, tanggal_kembali=?, metode_pembayaran=? WHERE id=?");
        $stmt->bind_param(
            "ssssisssi",
            $nama_pelanggan,
            $alamat,
            $telepon,
            $alat,
            $jumlah,
            $tanggal_sewa,
            $tanggal_kembali,
            $metode_pembayaran,
            $id
        );

        if ($stmt->execute()) {
            $_SESSION['notif'] = [
                'type' => 'success',
                'message' => 'Pesanan berhasil diperbarui.'
            ];
            header("Location: index.php#data");
            exit;
        } else {
            $error = "Gagal update data: " . $stmt->error;
        }
    } else {
        $error = "Semua field wajib diisi.";
    }
}

$stmt = $conn->prepare("SELECT * FROM penyewaan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Data penyewaan tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Data Penyewaan</title>
  <link rel="stylesheet" href="sewa.css">
  <style>
    .edit-wrapper{
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .error-box{
      background: #fdecec;
      color: #7f1d1d;
      padding: 12px 16px;
      border-left: 5px solid #dc2626;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .status-info{
      background: #f3f4f6;
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      color: #374151;
    }
    .btn-group{
      display: flex;
      gap: 10px;
      margin-top: 20px;
      flex-wrap: wrap;
    }
    .btn-link{
      display: inline-block;
      padding: 10px 16px;
      text-decoration: none;
      border-radius: 8px;
      background: #6b7280;
      color: #fff;
    }
  </style>
</head>
<body>
  <main>
    <section class="edit-wrapper">
      <h2>Edit Data Penyewaan</h2>

      <?php if ($error !== '') : ?>
        <div class="error-box">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <div class="status-info">
        <strong>Status saat ini:</strong>
        <?php
        if ($row['status'] === 'pending') {
            echo 'Menunggu Persetujuan Admin';
        } elseif ($row['status'] === 'disetujui') {
            echo 'Disetujui Admin';
        } elseif ($row['status'] === 'ditolak') {
            echo 'Ditolak Admin';
        } else {
            echo htmlspecialchars($row['status']);
        }
        ?>
      </div>

      <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <p>
          <label>Nama Lengkap:</label><br>
          <input type="text" name="nama_pelanggan" value="<?php echo htmlspecialchars($row['nama_pelanggan']); ?>" required>
        </p>

        <p>
          <label>Alamat:</label><br>
          <textarea name="alamat" required><?php echo htmlspecialchars($row['alamat']); ?></textarea>
        </p>

        <p>
          <label>No. Telepon:</label><br>
          <input type="text" name="telepon" value="<?php echo htmlspecialchars($row['telepon']); ?>" required>
        </p>

        <p>
          <label>Pilih Alat:</label><br>
          <select name="alat" required>
            <option value="Tenda 4 Orang" <?php if($row['alat'] == "Tenda 4 Orang") echo "selected"; ?>>Tenda 4 Orang</option>
            <option value="Sleeping Bag" <?php if($row['alat'] == "Sleeping Bag") echo "selected"; ?>>Sleeping Bag</option>
            <option value="Carrier 60L" <?php if($row['alat'] == "Carrier 60L") echo "selected"; ?>>Carrier 60L</option>
            <option value="Kompor Portable" <?php if($row['alat'] == "Kompor Portable") echo "selected"; ?>>Kompor Portable</option>
          </select>
        </p>

        <p>
          <label>Jumlah:</label><br>
          <input type="number" name="jumlah" min="1" value="<?php echo $row['jumlah']; ?>" required>
        </p>

        <p>
          <label>Tanggal Sewa:</label><br>
          <input type="date" name="tanggal_sewa" value="<?php echo $row['tanggal_sewa']; ?>" required>
        </p>

        <p>
          <label>Tanggal Kembali:</label><br>
          <input type="date" name="tanggal_kembali" value="<?php echo $row['tanggal_kembali']; ?>" required>
        </p>

        <p>
          <label>Metode Pembayaran:</label><br>
          <select name="metode_pembayaran" required>
            <option value="Transfer Bank" <?php if($row['metode_pembayaran'] == "Transfer Bank") echo "selected"; ?>>Transfer Bank</option>
            <option value="Cash" <?php if($row['metode_pembayaran'] == "Cash") echo "selected"; ?>>Cash</option>
            <option value="E-Wallet" <?php if($row['metode_pembayaran'] == "E-Wallet") echo "selected"; ?>>E-Wallet</option>
          </select>
        </p>

        <div class="btn-group">
          <button type="submit" name="update">Update Data</button>
          <a href="index.php#data" class="btn-link">Kembali</a>
        </div>
      </form>
    </section>
  </main>
</body>
</html>