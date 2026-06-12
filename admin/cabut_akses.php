<?php
session_start();
require '../koneksi.php';

// Pastikan yang mengakses adalah admin (opsional, sesuaikan dengan sistem sesimu)
// if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php"); exit();
// }

// Cek apakah ada parameter ID transaksi (id_pinjam) yang dikirim dari tombol
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Ambil id_buku dari transaksi ini untuk memproses pengembalian stok
    // Ganti langkah 1 menjadi:
$query_get_buku = mysqli_query($conn, "SELECT id_buku FROM peminjaman WHERE id_pinjam = '$id_pinjam'");
    
    if ($row_buku = mysqli_fetch_assoc($query_get_buku)) {
        $id_buku = $row_buku['id_buku'];

        // 2. Ubah status peminjaman menjadi 'Dikembalikan'
        $query_update = "UPDATE peminjaman SET status = 'Dikembalikan' WHERE id_pinjam = '$id_pinjam'";
        
        if (mysqli_query($conn, $query_update)) {
            
            // 3. Kembalikan stok fisik/digital buku (+1) ke perpustakaan
            mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");
            
            // 4. (SMART SYSTEM) Cek apakah ada user yang sedang 'Antri' untuk buku ini
            // Ganti bagian pengecekan antrean (langkah 4) di cabut_akses.php menjadi:
$cek_antre = mysqli_query($conn, "
    SELECT id_pinjam 
    FROM peminjaman 
    WHERE id_buku = '$id_buku' AND status = 'Antri'
    ORDER BY tanggal_pinjam ASC 
    LIMIT 1
");

            // Jika ada yang mengantre, otomatis berikan akses ke pengantre pertama
            if (mysqli_num_rows($cek_antre) > 0) {
                $antrean = mysqli_fetch_assoc($cek_antre);
                $id_pinjam_antre = $antrean['id_pinjam'];
                
                // Buat tanggal pinjam baru (Mulai hari ini sampai 7 hari ke depan)
                $tgl_pinjam_baru = date('Y-m-d');
                $tgl_kembali_baru = date('Y-m-d', strtotime('+7 days'));
                
                // Aktifkan status antrean tersebut menjadi Aktif
                mysqli_query($conn, "UPDATE peminjaman SET status = 'Aktif', tanggal_pinjam = '$tgl_pinjam_baru', tanggal_kembali = '$tgl_kembali_baru' WHERE id_pinjam = '$id_pinjam_antre'");
                
                // Kurangi stok kembali (-1) karena buku langsung dialihkan ke user yang antre
                mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
            }

            // Tampilkan pesan sukses dan kembalikan ke halaman Akses Buku
            echo "<script>alert('Akses berhasil dicabut! Buku telah dikembalikan ke sistem.'); window.location.href='akses_buku.php';</script>";
        } else {
            echo "<script>alert('Gagal mencabut akses buku. Terjadi kesalahan pada database.'); window.location.href='akses_buku.php';</script>";
        }
    } else {
        echo "<script>alert('Data detail peminjaman tidak ditemukan.'); window.location.href='akses_buku.php';</script>";
    }
} else {
    // Jika tidak ada parameter ID, lemparkan kembali ke halaman Akses Buku
    header("Location: akses_buku.php");
}
?>