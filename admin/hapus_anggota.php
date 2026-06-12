<?php
require '../koneksi.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");
echo "<script>alert('Data berhasil dihapus'); window.location.href='data_anggota.php';</script>";
?>