<?php
session_start();

$_SESSION['notif'] = [
    'type' => 'error',
    'message' => 'Pesanan tidak bisa dihapus oleh user. Silakan menghubungi admin.'
];

header("Location: index.php#data");
exit;
?>