<?php
session_start();
require '../koneksi.php';

// Pastikan ada parameter ID yang dikirim
if (isset($_GET['id'])) {
    $id_buku = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Jalankan query hapus
    $query = "DELETE FROM buku WHERE id_buku = '$id_buku'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data buku berhasil dihapus!'); window.location.href='data_buku.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data buku!'); window.location.href='data_buku.php';</script>";
    }
} else {
    // Jika tidak ada ID, kembalikan ke halaman data buku
    header("Location: data_buku.php");
    exit();
}
?>