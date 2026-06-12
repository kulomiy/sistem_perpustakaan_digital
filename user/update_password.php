<?php
session_start();
require '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['user_id'];
    $pass_lama = $_POST['current_password'];
    $pass_baru = $_POST['new_password'];
    $konfirmasi_pass = $_POST['confirm_password'];

    // 1. Ambil data user
    $query = mysqli_query($conn, "SELECT password FROM users WHERE id_user = '$id_user'");
    $data = mysqli_fetch_assoc($query);

    // 2. Debugging: Cek apakah data ditemukan
    if (!$data) {
        die("User tidak ditemukan di database.");
    }

    // 3. Verifikasi: Apakah password di database sama dengan pass_lama?
    // Jika password di database teks biasa, gunakan perbandingan ==
    if ($pass_lama === $data['password']) { 
        
        if ($pass_baru === $konfirmasi_pass) {
            // UPDATE langsung
            $update = mysqli_query($conn, "UPDATE users SET password = '$pass_baru' WHERE id_user = '$id_user'");
            
            if ($update) {
                echo "<script>alert('Password berhasil diperbarui!'); window.location.href='security.php';</script>";
            } else {
                echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Password baru tidak cocok!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Password lama salah! Anda memasukkan: $pass_lama, di DB: " . $data['password'] . "'); window.history.back();</script>";
    }
}
?>