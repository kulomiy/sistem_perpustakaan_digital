<?php
session_start();

// Jika belum login atau bukan member, kembalikan ke form login utama
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'member') {
    header("Location: ../login.php");
    exit();
}

// Jika sesi valid, langsung arahkan ke halaman Activity History
header("Location: activity.php");
exit();
?>