<?php
session_start();
require_once __DIR__ . "/admin/koneksi.php";

$notifLocal = null;

/*
|--------------------------------------------------------------------------
| Ambil data alat dari tabel stok_alat
|--------------------------------------------------------------------------
*/
$daftarAlat = [];
$qAlat = $conn->query("SELECT * FROM stok_alat ORDER BY nama_alat ASC");

if ($qAlat) {
    while ($item = $qAlat->fetch_assoc()) {
        $daftarAlat[] = $item;
    }
}

/*
|--------------------------------------------------------------------------
| Bikin map stok untuk validasi saat user submit form
|--------------------------------------------------------------------------
*/
$stokMap = [];
foreach ($daftarAlat as $item) {
    $stokMap[$item['nama_alat']] = (int)$item['stok'];
}

/*
|--------------------------------------------------------------------------
| Simpan data penyewaan user
|--------------------------------------------------------------------------
*/
if (isset($_POST['simpan'])) {
    $nama_pelanggan    = trim($_POST['nama_pelanggan'] ?? '');
    $alamat            = trim($_POST['alamat'] ?? '');
    $telepon           = trim($_POST['telepon'] ?? '');
    $alat              = trim($_POST['alat'] ?? '');
    $jumlah            = (int)($_POST['jumlah'] ?? 0);
    $tanggal_sewa      = $_POST['tanggal_sewa'] ?? '';
    $tanggal_kembali   = $_POST['tanggal_kembali'] ?? '';
    $metode_pembayaran = trim($_POST['metode_pembayaran'] ?? '');
    $status            = "pending";

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
        if (!isset($stokMap[$alat])) {
            $notifLocal = [
                'type' => 'error',
                'message' => 'Alat yang dipilih tidak tersedia di katalog.'
            ];
        } elseif ($stokMap[$alat] <= 0) {
            $notifLocal = [
                'type' => 'error',
                'message' => 'Maaf, stok alat yang dipilih sedang habis.'
            ];
        } elseif ($jumlah > $stokMap[$alat]) {
            $notifLocal = [
                'type' => 'error',
                'message' => 'Jumlah yang diminta melebihi stok yang tersedia.'
            ];
        } else {
            $stmt = $conn->prepare("INSERT INTO penyewaan (nama_pelanggan, alamat, telepon, alat, jumlah, tanggal_sewa, tanggal_kembali, metode_pembayaran, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssssissss",
                $nama_pelanggan,
                $alamat,
                $telepon,
                $alat,
                $jumlah,
                $tanggal_sewa,
                $tanggal_kembali,
                $metode_pembayaran,
                $status
            );

            if ($stmt->execute()) {
                $_SESSION['notif'] = [
                    'type' => 'success',
                    'message' => 'Pesanan berhasil dibuat dan sedang menunggu persetujuan admin.'
                ];
                header("Location: index.php#data");
                exit;
            } else {
                $notifLocal = [
                    'type' => 'error',
                    'message' => 'Gagal menyimpan data: ' . $stmt->error
                ];
            }
        }
    } else {
        $notifLocal = [
            'type' => 'error',
            'message' => 'Semua field wajib diisi.'
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Flash notif
|--------------------------------------------------------------------------
*/
$flashNotif = $_SESSION['notif'] ?? null;
unset($_SESSION['notif']);

/*
|--------------------------------------------------------------------------
| Data penyewaan
|--------------------------------------------------------------------------
*/
$data = $conn->query("SELECT * FROM penyewaan ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CampSupply ID | Sewa Alat Camping & Outdoor</title>
  <link rel="stylesheet" href="sewa.css">

  <style>
    .custom-notif{
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 320px;
      max-width: 420px;
      padding: 16px 18px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      font-family: Arial, sans-serif;
      animation: slideIn 0.3s ease;
    }

    .custom-notif.success{
      background: #e8f7ec;
      border-left: 6px solid #1f8f4d;
      color: #14532d;
    }

    .custom-notif.error{
      background: #fdecec;
      border-left: 6px solid #dc2626;
      color: #7f1d1d;
    }

    .notif-text{
      font-size: 14px;
      line-height: 1.5;
      flex: 1;
    }

    .notif-close{
      background: transparent;
      border: none;
      font-size: 22px;
      cursor: pointer;
      color: inherit;
      line-height: 1;
    }

    @keyframes slideIn{
      from{
        opacity: 0;
        transform: translateY(-10px);
      }
      to{
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

  <?php $notif = $flashNotif ?: $notifLocal; ?>
  <?php if (!empty($notif)) : ?>
    <div class="custom-notif <?php echo $notif['type']; ?>" id="customNotif">
      <div class="notif-text">
        <?php echo htmlspecialchars($notif['message']); ?>
      </div>
      <button type="button" class="notif-close" onclick="document.getElementById('customNotif').style.display='none'">×</button>
    </div>
  <?php endif; ?>

  <header>
    <div class="overlay">
      <h1>CampSupply ID</h1>
      <p>
        Solusi praktis untuk penyewaan alat camping dan outdoor yang lengkap, terpercaya,
        dan siap menemani setiap perjalanan petualangan Anda.
      </p>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="#beranda">Beranda</a></li>
      <li><a href="#katalog">Katalog Alat</a></li>
      <li><a href="#sewa">Form Sewa</a></li>
      <li><a href="#data">Data Penyewaan</a></li>
    </ul>
  </nav>

  <main>
    <section id="beranda">
      <h2>Selamat Datang di CampSupply ID</h2>
      <p>
        CampSupply ID hadir untuk membantu Anda mendapatkan perlengkapan camping dan outdoor
        dengan lebih mudah, cepat, dan nyaman. Mulai dari tenda, carrier, sleeping bag,
        hingga perlengkapan pendukung lainnya, semua dapat dilihat, dipilih, dan dipesan
        secara praktis melalui sistem yang terorganisir. Kami berkomitmen memberikan layanan
        penyewaan yang modern, aman, dan terpercaya agar setiap petualangan Anda terasa
        lebih siap, lebih seru, dan lebih berkesan.
      </p>
    </section>

    <section id="katalog">
      <h2>Katalog Perlengkapan Outdoor</h2>
      <table border="1" cellpadding="10" cellspacing="0">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Alat</th>
            <th>Kategori</th>
            <th>Harga Sewa / Hari</th>
            <th>Stok</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($daftarAlat)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($daftarAlat as $item) : ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($item['nama_alat']); ?></td>
                <td><?= htmlspecialchars($item['kategori'] ?? '-'); ?></td>
                <td>
                  <?php
                  if (isset($item['harga_sewa']) && $item['harga_sewa'] !== null && $item['harga_sewa'] !== '') {
                      echo 'Rp' . number_format($item['harga_sewa'], 0, ',', '.');
                  } else {
                      echo '-';
                  }
                  ?>
                </td>
                <td><?= (int)$item['stok']; ?></td>
                <td><?= ((int)$item['stok'] > 0) ? 'Tersedia' : 'Kosong'; ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="6" style="text-align:center;">Belum ada alat yang tersedia.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section id="sewa">
      <h2>Form Penyewaan</h2>
      <form action="" method="post">
        <fieldset>
          <legend>Data Pelanggan</legend>

          <p>
            <label>Nama Lengkap:</label><br>
            <input type="text" name="nama_pelanggan" required>
          </p>

          <p>
            <label>Alamat:</label><br>
            <textarea name="alamat" rows="4" cols="30" required></textarea>
          </p>

          <p>
            <label>No. Telepon:</label><br>
            <input type="text" name="telepon" required>
          </p>
        </fieldset>

        <fieldset>
          <legend>Data Penyewaan</legend>

          <p>
            <label>Pilih Alat:</label><br>
            <select name="alat" required>
              <option value="">-- Pilih Alat --</option>
              <?php if (!empty($daftarAlat)) : ?>
                <?php foreach ($daftarAlat as $item) : ?>
                  <?php if ((int)$item['stok'] > 0) : ?>
                    <option value="<?= htmlspecialchars($item['nama_alat']); ?>">
                      <?= htmlspecialchars($item['nama_alat']); ?> (Stok: <?= (int)$item['stok']; ?>)
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </p>

          <p>
            <label>Jumlah:</label><br>
            <input type="number" name="jumlah" min="1" required>
          </p>

          <p>
            <label>Tanggal Sewa:</label><br>
            <input type="date" name="tanggal_sewa" required>
          </p>

          <p>
            <label>Tanggal Kembali:</label><br>
            <input type="date" name="tanggal_kembali" required>
          </p>

          <p>
            <label>Metode Pembayaran:</label><br>
            <select name="metode_pembayaran" required>
              <option value="">-- Pilih Metode --</option>
              <option value="Transfer Bank">Transfer Bank</option>
              <option value="Cash">Cash</option>
              <option value="E-Wallet">E-Wallet</option>
            </select>
          </p>
        </fieldset>

        <button type="submit" name="simpan">Simpan Data Sewa</button>
        <button type="reset">Reset</button>
      </form>
    </section>

    <section id="data">
      <h2>Data Penyewaan</h2>
      <table border="1" cellpadding="10" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Pelanggan</th>
            <th>Alat</th>
            <th>Jumlah</th>
            <th>Tanggal Sewa</th>
            <th>Tanggal Kembali</th>
            <th>Metode</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data && $data->num_rows > 0) : ?>
            <?php while ($row = $data->fetch_assoc()) : ?>
              <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                <td><?= htmlspecialchars($row['alat']); ?></td>
                <td><?= $row['jumlah']; ?></td>
                <td><?= $row['tanggal_sewa']; ?></td>
                <td><?= $row['tanggal_kembali']; ?></td>
                <td><?= htmlspecialchars($row['metode_pembayaran']); ?></td>
                <td>
                  <?php
                  if ($row['status'] === 'pending') {
                      echo 'Menunggu Persetujuan';
                  } elseif ($row['status'] === 'disetujui') {
                      echo 'Disetujui Admin';
                  } elseif ($row['status'] === 'ditolak') {
                      echo 'Ditolak Admin';
                  } else {
                      echo htmlspecialchars($row['status']);
                  }
                  ?>
                </td>
                <td>
                  <a href="edit_sewa.php?id=<?= $row['id']; ?>">Edit</a> |
                  <a href="hapus_sewa.php?id=<?= $row['id']; ?>" class="btn-hapus">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else : ?>
            <tr>
              <td colspan="9" style="text-align:center;">Belum ada data penyewaan.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 CampSupply ID - Adventure Starts Here.</p>
  </footer>

  <script src="script.js"></script>

  <script>
    setTimeout(function () {
      var notif = document.getElementById('customNotif');
      if (notif) {
        notif.style.display = 'none';
      }
    }, 3000);
  </script>

  <div id="modalHapus" class="modal">
    <div class="modal-content">
      <h3>Konfirmasi Hapus</h3>
      <p>Yakin ingin menghapus data ini?</p>
      <div class="modal-actions">
        <button type="button" id="btnBatal">Batal</button>
        <a href="#" id="btnKonfirmasiHapus">Ya, Hapus</a>
      </div>
    </div>
  </div>

</body>
</html>