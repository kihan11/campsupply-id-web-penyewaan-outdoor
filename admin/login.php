<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$notif = null;

if (isset($_SESSION['notif'])) {
    $notif = $_SESSION['notif'];
    unset($_SESSION['notif']);
}

if (isset($_SESSION['error'])) {
    $notif = [
        'type' => 'error',
        'message' => $_SESSION['error']
    ];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <style>
        body{
            font-family:Arial,sans-serif;
            background:#f4f6f9;
            margin:0;
            padding:0;
        }
        .box{
            width:350px;
            margin:80px auto;
            background:#fff;
            padding:25px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,.1);
            position:relative;
        }
        h2{
            margin-top:0;
            margin-bottom:20px;
        }
        input{
            width:100%;
            padding:10px;
            margin:8px 0;
            box-sizing:border-box;
            border:1px solid #d1d5db;
            border-radius:6px;
        }
        button{
            width:100%;
            padding:10px;
            background:#2563eb;
            color:#fff;
            border:none;
            border-radius:6px;
            cursor:pointer;
            margin-top:10px;
        }
        .custom-notif{
            width:350px;
            margin:30px auto 0;
            padding:14px 16px;
            border-radius:10px;
            box-shadow:0 6px 18px rgba(0,0,0,.08);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
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
            line-height:1.4;
            flex:1;
        }
        .notif-close{
            background:transparent;
            border:none;
            font-size:20px;
            cursor:pointer;
            color:inherit;
            width:auto;
            padding:0;
            margin:0;
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

    <div class="box">
        <h2>Login Admin</h2>

        <form action="proses_login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
    </div>

    <script>
        setTimeout(function () {
            var notif = document.getElementById('customNotif');
            if (notif) {
                notif.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>