<?php
require_once "auth.php";
require_once __DIR__ . "/koneksi.php";

/*
|--------------------------------------------------------------------------
| Proses hapus pesanan
|--------------------------------------------------------------------------
*/
if (isset($_GET['hapus'])) {
    $id = (int)($_GET['hapus'] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM penyewaan WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: penyewaan.php?notif=hapus_sukses");
            exit;
        } else {
            header("Location: penyewaan.php?notif=hapus_gagal");
            exit;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Notif
|--------------------------------------------------------------------------
*/
$notif = null;

if (isset($_GET['notif'])) {
    if ($_GET['notif'] === 'hapus_sukses') {
        $notif = [
            'type' => 'success',
            'message' => 'Pesanan berhasil dihapus.'
        ];
    } elseif ($_GET['notif'] === 'hapus_gagal') {
        $notif = [
            'type' => 'error',
            'message' => 'Pesanan gagal dihapus.'
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Ambil data penyewaan
|--------------------------------------------------------------------------
*/
$result = $conn->query("SELECT * FROM penyewaan ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penyewaan</title>
    <style>
        body{
            font-family:Arial, sans-serif;
            background:#f5f7fa;
            margin:0;
        }

        .nav{
            background:#1f2937;
            padding:15px 20px;
        }

        .nav a{
            color:#fff;
            text-decoration:none;
            margin-right:15px;
        }

        .nav a:hover{
            color:#93c5fd;
        }

        .container{
            padding:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:#fff;
            box-shadow:0 0 8px rgba(0,0,0,.08);
        }

        th, td{
            padding:12px;
            border:1px solid #ddd;
            vertical-align:top;
            text-align:left;
        }

        th{
            background:#f9fafb;
        }

        .btn{
            color:#fff;
            text-decoration:none;
            padding:7px 10px;
            border-radius:6px;
            display:inline-block;
            margin-bottom:5px;
            font-size:13px;
        }

        .acc{background:#16a34a}
        .tolak{background:#dc2626}
        .hapus{background:#6b7280}

        .badge{
            display:inline-block;
            padding:6px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:bold;
        }

        .pending{
            background:#fef3c7;
            color:#92400e;
        }

        .setuju{
            background:#dcfce7;
            color:#166534;
        }

        .ditolak{
            background:#fee2e2;
            color:#991b1b;
        }

        .custom-notif{
            position:fixed;
            top:20px;
            right:20px;
            z-index:9999;
            min-width:320px;
            max-width:420px;
            padding:16px 18px;
            border-radius:12px;
            box-shadow:0 10px 25px rgba(0,0,0,0.15);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            animation:slideIn .3s ease;
        }

        .custom-notif.success{
            background:#e8f7ec;
            border-left:6px solid #1f8f4d;
            color:#14532d;
        }

        .custom-notif.error{
            background:#fdecec;
            border-left:6px solid #dc2626;
            color:#7f1d1d;
        }

        .notif-text{
            font-size:14px;
            line-height:1.5;
            flex:1;
        }

        .notif-close{
            background:transparent;
            border:none;
            font-size:22px;
            cursor:pointer;
            color:inherit;
            line-height:1;
        }

        @keyframes slideIn{
            from{
                opacity:0;
                transform:translateY(-10px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        /* Modal Logout */
        .modal-logout{
            display:none;
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.45);
            z-index:9998;
            justify-content:center;
            align-items:center;
        }

        .modal-logout-content{
            background:#fff;
            width:380px;
            max-width:90%;
            padding:24px;
            border-radius:12px;
            box-shadow:0 10px 30px rgba(0,0,0,0.2);
            text-align:center;
        }

        .modal-logout-content h3{
            margin-top:0;
            margin-bottom:10px;
        }

        .modal-logout-content p{
            margin-bottom:20px;
            color:#374151;
        }

        .modal-logout-actions{
            display:flex;
            justify-content:center;
            gap:10px;
        }

        .modal-logout-actions button,
        .modal-logout-actions a{
            padding:10px 16px;
            border:none;
            border-radius:8px;
            text-decoration:none;
            cursor:pointer;
            font-size:14px;
        }

        #batalLogout{
            background:#9ca3af;
            color:#fff;
        }

        #yaLogout{
            background:#dc2626;
            color:#fff;
        }
    </style>
</head>
<body>

    <?php if ($notif): ?>
        <div class="custom-notif <?= htmlspecialchars($notif['type']); ?>" id="customNotif">
            <div class="notif-text">
                <?= htmlspecialchars($notif['message']); ?>
            </div>
            <button type="button" class="notif-close" onclick="document.getElementById('customNotif').style.display='none'">×</button>
        </div>
    <?php endif; ?>

    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="stok.php">Kelola Stok</a>
        <a href="penyewaan.php">Data Penyewaan</a>
        <a href="#" id="btnLogout">Logout</a>
    </div>

    <div class="container">
        <h2>Data Penyewaan</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Alat</th>
                <th>Jumlah</th>
                <th>Tgl Sewa</th>
                <th>Tgl Kembali</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                    <td><?= htmlspecialchars($row['alamat']); ?></td>
                    <td><?= htmlspecialchars($row['telepon']); ?></td>
                    <td><?= htmlspecialchars($row['alat']); ?></td>
                    <td><?= $row['jumlah']; ?></td>
                    <td><?= $row['tanggal_sewa']; ?></td>
                    <td><?= $row['tanggal_kembali']; ?></td>
                    <td><?= htmlspecialchars($row['metode_pembayaran']); ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <span class="badge pending">Pending</span>
                        <?php elseif ($row['status'] === 'disetujui'): ?>
                            <span class="badge setuju">Disetujui</span>
                        <?php elseif ($row['status'] === 'ditolak'): ?>
                            <span class="badge ditolak">Ditolak</span>
                        <?php else: ?>
                            <?= htmlspecialchars($row['status']); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <a class="btn acc" href="acc_penyewaan.php?id=<?= $row['id']; ?>" onclick="return confirm('ACC pesanan ini?')">ACC</a>
                            <a class="btn tolak" href="tolak_penyewaan.php?id=<?= $row['id']; ?>" onclick="return confirm('Tolak pesanan ini?')">Tolak</a>
                        <?php endif; ?>

                        <a class="btn hapus" href="penyewaan.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus pesanan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" style="text-align:center;">Belum ada data penyewaan.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <div id="modalLogout" class="modal-logout">
        <div class="modal-logout-content">
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin logout?</p>
            <div class="modal-logout-actions">
                <button type="button" id="batalLogout">Batal</button>
                <a href="logout.php" id="yaLogout">Ya, Logout</a>
            </div>
        </div>
    </div>

    <script>
        const btnLogout = document.getElementById('btnLogout');
        const modalLogout = document.getElementById('modalLogout');
        const batalLogout = document.getElementById('batalLogout');
        const notif = document.getElementById('customNotif');

        if (btnLogout) {
            btnLogout.addEventListener('click', function(e){
                e.preventDefault();
                modalLogout.style.display = 'flex';
            });
        }

        if (batalLogout) {
            batalLogout.addEventListener('click', function(){
                modalLogout.style.display = 'none';
            });
        }

        window.addEventListener('click', function(e){
            if (e.target === modalLogout) {
                modalLogout.style.display = 'none';
            }
        });

        setTimeout(function () {
            if (notif) {
                notif.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>