<?php
session_start();

session_unset();
session_destroy();

session_start();
$_SESSION['notif'] = [
    'type' => 'success',
    'message' => 'Berhasil logout dari halaman admin.'
];

header("Location: login.php");
exit;
?>