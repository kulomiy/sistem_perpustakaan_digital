<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'member') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location.href='beranda.php';</script>";
    exit();
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);
$id_user = $_SESSION['user_id'];

// Mengambil Data User HANYA berdasarkan Username (Untuk Profil)
$query_user = mysqli_query($conn, "SELECT username FROM users WHERE id_user = '$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
$username = $data_user['username'] ?? 'User';

// Membuat Inisial Nama (Contoh: Angela Salma -> AS)
$words = explode(" ", trim($username));
$inisial = "";
if (count($words) >= 2) {
    $inisial = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
} else {
    $inisial = strtoupper(substr($username, 0, 2));
}

// Ambil data buku
$query_detail = "SELECT b.*, k.nama_kategori 
                FROM buku b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                WHERE b.id_buku = '$id_buku'";
$result_detail = mysqli_query($conn, $query_detail);
$buku = mysqli_fetch_assoc($result_detail);

if (!$buku) {
    echo "<script>alert('Data buku tidak ditemukan!'); window.location.href='beranda.php';</script>";
    exit();
}

// 1. Cek secara spesifik: Apakah user sudah meminjam BUKU INI
$cek_status_user = mysqli_query($conn, "
    SELECT status
    FROM peminjaman
    WHERE id_user = '$id_user'
    AND id_buku = '$id_buku'
    AND status IN ('Aktif', 'Antri')
");
$sudah_pinjam_ini = mysqli_num_rows($cek_status_user) > 0;
$status_milik_user = $sudah_pinjam_ini ? mysqli_fetch_assoc($cek_status_user)['status'] : '';

$stok = (int)$buku['stok'];
$modal_tampil = false;

// LOGIKA TAMPILAN TOMBOL & BADGE
if ($sudah_pinjam_ini) {
    if (strtolower($status_milik_user) == 'antri') {
        $status_badge = '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide border border-yellow-200"><i class="fa-solid fa-hourglass-half mr-1"></i> Sedang Antre Buku Ini</span>';
        $btn_pinjam = '<button type="button" disabled class="w-full bg-gray-100 text-gray-500 border border-gray-300 py-3 rounded-xl font-bold cursor-not-allowed flex justify-center items-center gap-2"><i class="fa-solid fa-users"></i> Menunggu Giliran Antrean</button>';
    } else {
        $status_badge = '<span class="bg-blue-100 text-[#1e3a8a] px-3 py-1 rounded-full text-xs font-bold tracking-wide border border-blue-200"><i class="fa-solid fa-bookmark mr-1"></i> Akses Aktif</span>';
        $btn_pinjam = '<button type="button" disabled class="w-full bg-gray-100 text-gray-500 border border-gray-300 py-3 rounded-xl font-bold cursor-not-allowed flex justify-center items-center gap-2"><i class="fa-solid fa-check"></i> Sudah Dipinjam</button>';
    }
} else {
    $modal_tampil = true;
    if ($stok > 0) {
        $status_badge = '<span class="bg-blue-100 text-[#1e3a8a] px-3 py-1 rounded-full text-xs font-bold tracking-wide border border-blue-200"><i class="fa-solid fa-check-circle mr-1"></i> Tersedia (' . $stok . ' Buku)</span>';
        $btn_pinjam = '<label for="modal-toggle" class="w-full bg-[#1e3a8a] text-white py-3 rounded-xl font-bold shadow hover:bg-blue-900 transition flex justify-center items-center gap-2 cursor-pointer"><i class="fa-solid fa-book-open-reader"></i> Pinjam Buku Ini</label>';

        $modal_title = "Konfirmasi Peminjaman";
        $modal_btn_submit = "Konfirmasi Pinjam";
        $durasi_teks = "7 Hari";
        $tgl_kembali_tampil = date('d F Y', strtotime('+7 days'));
        $modal_info_icon = "fa-circle-info text-[#1e3a8a]";
        $modal_info_bg = "bg-blue-50/50 border-blue-100";
        $modal_info_text = "Akses akan terkunci otomatis saat masa pinjam telah selesai.";
    } else {
        $status_badge = '<span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold tracking-wide border border-red-200"><i class="fa-solid fa-circle-xmark mr-1"></i> Copy Kosong</span>';
        $btn_pinjam = '<label for="modal-toggle" class="w-full bg-yellow-500 text-white py-3 rounded-xl font-bold shadow hover:bg-yellow-600 transition flex justify-center items-center gap-2 cursor-pointer"><i class="fa-solid fa-users"></i> Antri Buku Ini</label>';

        $modal_title = "Konfirmasi Antrean";
        $modal_btn_submit = "Masuk Antrean";
        $durasi_teks = "Menunggu Giliran";
        $tgl_kembali_tampil = "Akan Diinformasikan";
        $modal_info_icon = "fa-triangle-exclamation text-yellow-600";
        $modal_info_bg = "bg-yellow-50 border-yellow-100";
        $modal_info_text = "Copy saat ini tidak tersedia. Anda akan dimasukkan ke sistem antrean dan mendapatkan akses setelah buku dikembalikan oleh pengguna lain.";
    }
}

$colors = ['from-cyan-700 to-blue-900', 'from-slate-800 to-black', 'from-teal-600 to-emerald-800', 'from-slate-100 to-slate-300', 'from-sky-700 to-cyan-900'];
$bg_cover = $colors[$buku['id_buku'] % count($colors)];
$text_cover = ($bg_cover == 'from-slate-100 to-slate-300') ? 'text-gray-800' : 'text-white';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($buku['judul']) ?> - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen relative">

    <nav class="border-b border-gray-200 bg-white sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <span class="text-[#1e3a8a] font-extrabold text-xl tracking-tight">Ruang Pustaka</span>
                </div>

                <div class="hidden md:flex   space-x-8">
                    <a href="beranda.php" class="border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Beranda</a>
                    <a href="daftar_buku.php" class="border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Daftar Buku</a>
                    <a href="activity.php" class="border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Riwayat Buku</a>
                </div>

                <div class="flex items-center gap-5 text-gray-500 relative">
                    <button id="profile-btn" class="w-9 h-9 rounded-full bg-gradient-to-br from-[#c900ff] to-[#8000ff] text-white font-medium text-sm flex items-center justify-center shadow-sm cursor-pointer hover:ring-2 hover:ring-purple-300 transition focus:outline-none">
                        <?= $inisial ?>
                    </button>

                    <div id="profile-dropdown" class="hidden absolute right-0 top-12 mt-2 w-[280px] bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden z-50 transition-all">
                        <div class="bg-gradient-to-b from-[#263b96] to-[#1e2f75] p-5 flex items-center gap-4 relative">
                            <div class="relative shrink-0">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#c900ff] to-[#8000ff] text-white font-normal text-xl flex items-center justify-center border-2 border-[#263b96] shadow-sm">
                                    <?= $inisial ?>
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-white text-[15px] truncate uppercase tracking-wide"><?= htmlspecialchars($username) ?></h4>
                                <p class="text-blue-200 text-xs font-medium mt-0.5">Member Aktif</p>
                            </div>
                        </div>

                        <div class="p-2 bg-white flex flex-col">
                            <a href="security.php" class="px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-[#263b96] transition rounded-lg flex items-center gap-3">
                                <i class="fa-solid fa-lock text-gray-400 w-4 text-center"></i> Ganti Password
                            </a>
                            <a href="../login.php" class="px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition rounded-lg flex items-center gap-3">
                                <i class="fa-solid fa-arrow-right-from-bracket text-red-400 w-4 text-center"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4 pb-10">
        <div class="mb-4 flex items-center gap-3">

            <a href="beranda.php"
                class="w-9 h-9 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-[#1e3a8a] transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <nav class="flex text-sm text-gray-500 font-medium">
                <a href="beranda.php" class="hover:text-[#1e3a8a] transition">Beranda</a>

                <span class="mx-2">/</span>

                <a href="beranda.php?kategori=<?= $buku['id_kategori'] ?>"
                    class="hover:text-[#1e3a8a] transition">
                    <?= htmlspecialchars($buku['nama_kategori'] ?? 'Kategori') ?>
                </a>

                <span class="mx-2">/</span>

                <span class="text-gray-900">
                    <?= htmlspecialchars($buku['judul']) ?>
                </span>
            </nav>

        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col md:flex-row">
            <div class="md:w-1/3 p-8 bg-gray-50 flex items-center justify-center border-b md:border-b-0 md:border-r border-gray-200">

                <?php if (!empty($buku['cover']) && file_exists("../uploads/cover/" . $buku['cover'])) : ?>

                    <img src="../uploads/cover/<?= htmlspecialchars($buku['cover']) ?>"
                        alt="<?= htmlspecialchars($buku['judul']) ?>"
                        class="w-full max-w-[250px] aspect-[2/3] object-cover shadow-2xl rounded-lg">

                <?php else : ?>

                    <div class="w-full max-w-[250px] aspect-[2/3] bg-gradient-to-br <?= $bg_cover ?> relative p-6 flex flex-col justify-between overflow-hidden shadow-2xl rounded-sm rounded-r-lg border border-black/10">
                        <div class="absolute top-0 right-0 w-48 h-48 bg-white opacity-5 transform rotate-45 translate-x-16 -translate-y-16"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-black opacity-10 rounded-tr-full"></div>

                        <div class="mt-auto relative z-10 text-center">
                            <h3 class="<?= $text_cover ?> font-bold text-xl leading-snug">
                                <?= htmlspecialchars($buku['judul']) ?>
                            </h3>
                            <p class="<?= $text_cover ?> opacity-70 text-sm mt-3">
                                <?= htmlspecialchars($buku['penulis']) ?>
                            </p>
                        </div>
                    </div>

                <?php endif; ?>

            </div>

            <div class="md:w-2/3 p-8 lg:p-10 flex flex-col">
                <div class="mb-4 flex flex-wrap gap-2 items-center">
                    <?= $status_badge ?>
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold tracking-wide border border-gray-200">
                        <i class="fa-solid fa-tags mr-1"></i> <?= htmlspecialchars($buku['nama_kategori'] ?? 'Tidak ada Kategori') ?>
                    </span>
                </div>

                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3 leading-tight"><?= htmlspecialchars($buku['judul']) ?></h1>
                <p class="text-base text-gray-500 font-medium mb-6">Oleh <span class="text-[#1e3a8a]"><?= htmlspecialchars($buku['penulis']) ?></span></p>

                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 mb-8 grid grid-cols-2 gap-y-4 gap-x-6 flex-1">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Penerbit</p>
                        <p class="text-sm text-gray-900 font-semibold"><?= htmlspecialchars($buku['penerbit']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Tahun Terbit</p>
                        <p class="text-sm text-gray-900 font-semibold"><?= htmlspecialchars($buku['tahun_terbit']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Kategori</p>
                        <p class="text-sm text-gray-900 font-semibold"><?= htmlspecialchars($buku['nama_kategori'] ?? '-') ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Copy Tersedia</p>
                        <p class="text-sm text-gray-900 font-semibold"><?= htmlspecialchars($buku['stok']) ?> Copy</p>
                    </div>
                </div>

                <!-- Sinopsis -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-[#1e3a8a]"></i>
                        Sinopsis
                    </h3>

                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                        <?php if (!empty($buku['sinopsis'])): ?>
                            <p class="text-sm text-gray-600 leading-7 text-justify">
                                <?= nl2br(htmlspecialchars($buku['sinopsis'])) ?>
                            </p>
                        <?php else: ?>
                            <p class="text-sm italic text-gray-400">
                                Sinopsis buku belum tersedia.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mt-auto pt-6 border-t border-gray-100">
                    <div class="flex-1">
                        <?= $btn_pinjam ?>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php if ($modal_tampil): ?>
        <input type="checkbox" id="modal-toggle" class="peer hidden">
        <div class="fixed inset-0 z-50 invisible peer-checked:visible opacity-0 peer-checked:opacity-100 transition-all duration-300 flex items-center justify-center p-4">
            <label for="modal-toggle" class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"></label>

            <div class="bg-white rounded-2xl w-full max-w-md p-6 relative shadow-2xl z-10">
                <label for="modal-toggle" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </label>

                <h3 class="font-bold text-xl text-gray-900 mb-5 border-b border-gray-100 pb-3"><?= $modal_title ?></h3>

                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-20 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-center text-[#1e3a8a] text-2xl flex-shrink-0">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 leading-tight"><?= htmlspecialchars($buku['judul']) ?></h4>
                        <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($buku['penulis']) ?></p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-500 font-medium">Durasi Pinjam</span>
                        <span class="text-sm font-bold text-gray-900"><?= $durasi_teks ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 font-medium">Akses Berakhir</span>
                        <span class="text-sm font-bold text-[#1e3a8a]"><?= $tgl_kembali_tampil ?></span>
                    </div>
                </div>

                <div class="flex items-start gap-3 <?= $modal_info_bg ?> border p-3 rounded-lg mb-6">
                    <i class="fa-solid <?= $modal_info_icon ?> mt-0.5"></i>
                    <p class="text-xs text-gray-700 leading-relaxed font-medium">
                        <?= $modal_info_text ?>
                    </p>
                </div>

                <div class="flex gap-3">
                    <label for="modal-toggle" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition cursor-pointer text-center flex items-center justify-center">
                        Batal
                    </label>
                    <form action="proses_pinjam.php" method="POST" class="flex-1">
                        <input type="hidden" name="id_buku" value="<?= $buku['id_buku'] ?>">
                        <button type="submit" class="w-full bg-[#1e3a8a] text-white py-2.5 rounded-xl font-bold hover:bg-blue-900 transition h-full"><?= $modal_btn_submit ?></button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['sukses']) && ($_GET['sukses'] == '1' || $_GET['sukses'] == '2')): ?>
        <?php
        $is_antri = ($_GET['sukses'] == '2');
        $judul_sukses = $is_antri ? "Antrean Berhasil!" : "Booking Berhasil!";
        $icon_sukses = $is_antri ? "fa-users text-yellow-600" : "fa-check-circle text-[#1e3a8a]";
        $bg_icon_sukses = $is_antri ? "bg-yellow-50 ring-yellow-100" : "bg-blue-50 ring-blue-100";
        $pesan_sukses = $is_antri
            ? "Anda telah ditambahkan ke daftar antrean untuk buku <strong>" . htmlspecialchars($buku['judul']) . "</strong>. Kami akan memberi tahu Anda jika buku sudah tersedia."
            : "Buku <strong>" . htmlspecialchars($buku['judul']) . "</strong> telah berhasil dipinjam. Silakan akses di portal riwayat Anda sebelum masa peminjaman habis.";
        ?>
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl w-full max-w-md p-8 text-center relative shadow-2xl z-10">
                <div class="w-20 h-20 <?= $bg_icon_sukses ?> rounded-full flex items-center justify-center mx-auto mb-5 text-4xl border-[4px] border-white shadow-sm ring-1">
                    <i class="fa-solid <?= $icon_sukses ?>"></i>
                </div>

                <h3 class="font-extrabold text-2xl text-gray-900 mb-3"><?= $judul_sukses ?></h3>
                <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                    <?= $pesan_sukses ?>
                </p>

                <div class="flex flex-col gap-3">
                    <a href="activity.php" class="block w-full bg-[#1e3a8a] text-white py-3 rounded-xl font-bold shadow hover:bg-blue-900 transition text-sm">Lihat Riwayat Pinjam</a>
                    <a href="detail_buku.php?id=<?= $buku['id_buku'] ?>" class="block w-full bg-white border border-gray-300 text-gray-700 py-3 rounded-xl font-bold hover:bg-gray-50 transition text-sm">Tutup</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <footer class="bg-[#f8fafc] border-t border-gray-200 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div>
                <h4 class="text-[#1e3a8a] font-bold text-sm mb-1">Ruang Pustaka</h4>
                <p class="text-[11px] text-gray-500 font-medium">&copy; <?= date('Y'); ?> Sistem Manajemen Ruang Pustaka</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('click', function(event) {
            const profileBtn = document.getElementById('profile-btn');
            const dropdown = document.getElementById('profile-dropdown');

            if (profileBtn && dropdown) {
                if (profileBtn.contains(event.target)) {
                    dropdown.classList.toggle('hidden');
                } else if (!dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });
    </script>
</body>

</html>