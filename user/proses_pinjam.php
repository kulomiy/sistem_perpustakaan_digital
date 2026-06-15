<?php
session_start();
require '../koneksi.php';

if(isset($_POST['id_buku']) && isset($_SESSION['user_id'])) {
    
    $id_buku = mysqli_real_escape_string($conn, $_POST['id_buku']);
    $id_user = $_SESSION['user_id'];
    
    // 1. Cek apakah user sudah punya pinjaman/antrean untuk buku ini
    // Menggunakan tabel 'peminjaman' langsung (karena id_buku sudah ada di sini)
    $cek_riwayat = mysqli_query($conn, "
        SELECT id_pinjam
        FROM peminjaman
        WHERE id_user = '$id_user'
        AND id_buku = '$id_buku'
        AND status IN ('Aktif', 'Antri')
    ");

    if(mysqli_num_rows($cek_riwayat) > 0) {
        echo "
        <script>
            alert('Anda masih memiliki pinjaman atau antrean untuk buku ini.');
            window.location.href='detail_buku.php?id=$id_buku';
        </script>";
        exit();
    }
    
    // 2. Cek ketersediaan stok buku
    $cek_buku = mysqli_query($conn, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
    $data_buku = mysqli_fetch_assoc($cek_buku);
    
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime('+7 days'));
    
    // JIKA STOK TERSEDIA -> PINJAM NORMAL
    if($data_buku && $data_buku['stok'] > 0) {
        
        $query_pinjam = "INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_kembali, status) 
                        VALUES ('$id_user', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'Aktif')";

        if(mysqli_query($conn, $query_pinjam)) {
            mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
            header("Location: detail_buku.php?id=$id_buku&sukses=1");
            exit();
        } else {
            echo "<script>alert('Gagal memproses peminjaman: " . mysqli_error($conn) . "'); window.history.back();</script>";
        }
    } 
    // JIKA STOK HABIS (0) -> MASUK ANTREAN
    else {
        $query_antri = "INSERT INTO peminjaman (id_user, id_buku, tanggal_antri, status) 
                        VALUES ('$id_user', '$id_buku', CURDATE(), 'Antri')";
        
        if(mysqli_query($conn, $query_antri)) {
            header("Location: detail_buku.php?id=$id_buku&sukses=2");
            exit();
        } else {
            echo "<script>alert('Gagal memasukkan ke antrean: " . mysqli_error($conn) . "'); window.history.back();</script>";
        }
    }
} else {
    header("Location: beranda.php");
    exit();
}
?>