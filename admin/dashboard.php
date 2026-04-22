<?php
require_once "auth.php";
require_once __DIR__ . "/koneksi.php";

$totalPenyewaan = $conn->query("SELECT COUNT(*) as total FROM penyewaan")->fetch_assoc()['total'];
$totalPending   = $conn->query("SELECT COUNT(*) as total FROM penyewaan WHERE status='pending'")->fetch_assoc()['total'];
$totalDisetujui = $conn->query("SELECT COUNT(*) as total FROM penyewaan WHERE status='disetujui'")->fetch_assoc()['total'];
$totalAlat      = $conn->query("SELECT COUNT(*) as total FROM stok_alat")->fetch_assoc()['total'];

$notif = $_SESSION['notif'] ?? null;
unset($_SESSION['notif']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
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
            font-size:16px;
        }

        .nav a:hover{
            color:#93c5fd;
        }

        .container{
            padding:20px;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:15px;
            margin-top:20px;
        }

        .card{
            background:#fff;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 8px rgba(0,0,0,.08);
        }

        .card h3{
            margin:0 0 10px;
            font-size:32px;
        }

        .card p{
            margin:0;
            color:#374151;
        }

        /* Notif */
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

        @media (max-width: 900px){
            .cards{
                grid-template-columns:repeat(2, 1fr);
            }
        }

        @media (max-width: 600px){
            .cards{
                grid-template-columns:1fr;
            }

            .custom-notif{
                left:20px;
                right:20px;
                min-width:auto;
            }
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
        <h2>Dashboard Admin</h2>
        <p>Halo, <strong><?= htmlspecialchars($_SESSION['admin_nama']); ?></strong></p>

        <div class="cards">
            <div class="card">
                <h3><?= $totalPenyewaan; ?></h3>
                <p>Total Penyewaan</p>
            </div>
            <div class="card">
                <h3><?= $totalPending; ?></h3>
                <p>Pesanan Pending</p>
            </div>
            <div class="card">
                <h3><?= $totalDisetujui; ?></h3>
                <p>Pesanan Disetujui</p>
            </div>
            <div class="card">
                <h3><?= $totalAlat; ?></h3>
                <p>Jumlah Jenis Alat</p>
            </div>
        </div>
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