<?php
session_start();
require '../koneksi.php';

// Cek sesi user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'member') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mengambil Data User untuk Profil
$query_user = mysqli_query($conn, "SELECT username FROM users WHERE id_user = '$user_id'");
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

// Mengambil riwayat peminjaman
// Ubah kueri $query_activity menjadi:
$query_activity = "
    SELECT p.id_pinjam, p.tanggal_pinjam, p.tanggal_kembali, p.status, b.id_buku, b.judul, b.penulis, b.cover
    FROM peminjaman p
    LEFT JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_user = '$user_id'
    ORDER BY p.id_pinjam DESC
";
$result_activity = @mysqli_query($conn, $query_activity);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Activity History - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">

    <nav class="border-b border-gray-200 bg-white sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <span class="text-[#1e3a8a] font-extrabold text-xl tracking-tight">Ruang Pustaka</span>
                </div>

                <div class="hidden md:flex space-x-8">
                    <a href="beranda.php" class="border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Beranda</a>
                    <a href="daftar_buku.php" class="border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Daftar Buku</a>
                    <a href="activity.php" class="border-b-2 border-[#1e3a8a] text-[#1e3a8a] inline-flex items-center px-1 pt-1 text-sm font-bold">Riwayat Buku</a>
                </div>

                <div class="flex items-center gap-5 text-gray-500 relative">
                    <button id="profile-btn" class="w-9 h-9 rounded-full bg-gradient-to-br from-[#c900ff] to-[#8000ff] text-white font-medium text-sm flex items-center justify-center shadow-sm cursor-pointer hover:ring-2 hover:ring-purple-300 transition focus:outline-none">
                        <?= $inisial ?>
                    </button>

                    <div id="profile-dropdown" class="hidden absolute right-0 top-12 mt-2 w-[280px] bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden z-50 transition-all">
                        <div class="bg-gradient-to-b from-[#263b96] to-[#1e2f75] p-5 flex items-center gap-4 relative">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#c900ff] to-[#8000ff] text-white font-normal text-xl flex items-center justify-center border-2 border-[#263b96] shadow-sm">
                                <?= $inisial ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-white text-[15px] truncate uppercase tracking-wide"><?= htmlspecialchars($username) ?></h4>
                                <p class="text-blue-200 text-xs font-medium mt-0.5">Member Aktif</p>
                            </div>
                        </div>
                        <div class="p-2 bg-white flex flex-col">
                            <a href="security.php" class="px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-[#263b96] transition rounded-lg flex items-center gap-3">
                                <i class="fa-solid fa-lock text-gray-400 w-4 text-center"></i> Change Password
                            </a>
                            <a href="../login.php" class="px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition rounded-lg flex items-center gap-3">
                                <i class="fa-solid fa-arrow-right-from-bracket text-red-400 w-4 text-center"></i> Log Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-5xl mx-auto w-full px-4 pt-4 pb-12">
        <div class="flex justify-between items-end mb-4 border-b border-gray-100 pb-3">
            <div>
                <h2 class="text-3xl font-extrabold text-[#1e3a8a] mb-2">Riwayat Pinjam</h2>
                <p class="text-gray-500 text-sm">Monitor riwayat peminjaman buku digital Anda.</p>
            </div>
        </div>

        <div class="space-y-4">
            <?php
            if ($result_activity && mysqli_num_rows($result_activity) > 0) {
                while ($row = mysqli_fetch_assoc($result_activity)) {
                    $judul = htmlspecialchars($row['judul'] ?? "Buku Tanpa Judul");
                    $status = strtolower($row['status'] ?? 'aktif');

                    if ($status == 'antri') {
                        $badge = '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-bold border border-yellow-200">ANTRE</span>';
                    } elseif ($status == 'aktif') {
                        $badge = '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold border border-blue-200">ACTIVE</span>';
                    } else {
                        $badge = '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold border border-green-200">FINISHED</span>';
                    }
            ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-center gap-6 shadow-sm hover:shadow-md transition">

                        <!-- Cover Buku -->
                        <div class="w-16 h-20 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                            <?php if (!empty($row['cover'])): ?>
                                <img src="../uploads/cover/<?= htmlspecialchars($row['cover']) ?>"
                                    alt="<?= htmlspecialchars($judul) ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-book"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Informasi Buku -->
                        <div class="flex-1">
                            <div class="mb-1"><?= $badge ?></div>

                            <h3 class="text-lg font-bold text-gray-900">
                                <?= $judul ?>
                            </h3>

                            <p class="text-sm text-gray-500">
                                by <?= htmlspecialchars($row['penulis'] ?? '-') ?>
                            </p>

                            <p class="text-xs text-gray-400 mt-2">
                                Dipinjam:
                                <?php
                                if (
                                    empty($row['tanggal_pinjam']) ||
                                    $row['tanggal_pinjam'] == '0000-00-00'
                                ) {
                                    echo '-';
                                } else {
                                    echo date('d M Y', strtotime($row['tanggal_pinjam']));
                                }
                                ?>
                            </p>
                        </div>

                        <!-- Tombol Receipt -->
                        <div class="flex-shrink-0">
                            <button
                                onclick="document.getElementById('receipt-<?= $row['id_pinjam'] ?>').classList.remove('hidden')"
                                class="bg-[#1e3a8a] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-900 transition">
                                <i class="fa-solid fa-receipt mr-1"></i>
                                Bukti Pinjam
                            </button>
                        </div>
                    </div>

                    <!-- MODAL RECEIPT -->
                    <div id="receipt-<?= $row['id_pinjam'] ?>"
                        class="hidden fixed inset-0 z-[9999] flex items-center justify-center">

                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                            onclick="document.getElementById('receipt-<?= $row['id_pinjam'] ?>').classList.add('hidden')">
                        </div>

                        <div class="relative z-10 w-full max-w-sm flex flex-col">

                            <button onclick="document.getElementById('receipt-<?= $row['id_pinjam'] ?>').classList.add('hidden')"
                                class="absolute top-3 right-3 text-white/70 hover:text-white transition cursor-pointer z-20">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>

                            <div class="bg-[#1e3a8a] rounded-t-2xl w-full pt-6 pb-4 px-5 text-center">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-[#1e3a8a] text-3xl mx-auto mb-3 shadow-sm">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <h3 class="font-bold text-xl text-white">Bukti Peminjaman</h3>
                                <p class="text-blue-200 text-sm mt-1">Ruang Pustaka</p>
                            </div>

                            <div class="bg-white rounded-b-2xl w-full p-4">
                                <div class="text-center mb-4">
                                    <h4 class="font-bold text-gray-900 text-base leading-tight"><?= htmlspecialchars($row['judul']) ?></h4>
                                    <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($row['penulis']) ?></p>
                                </div>

                                <div class="border-t-2 border-dashed border-gray-200 my-3"></div>

                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">ID Peminjaman</span>
                                        <span class="text-sm font-bold text-gray-900">PMJ-<?= str_pad($row['id_pinjam'], 4, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Username</span>
                                        <span class="text-sm font-bold text-gray-900"><?= htmlspecialchars($username) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Tanggal Pinjam</span>
                                        <span class="text-sm font-bold text-gray-900">
                                            <?php echo (empty($row['tanggal_pinjam']) || $row['tanggal_pinjam'] == '-' || $row['tanggal_pinjam'] == '0000-00-00') ? '-' : date('d M Y', strtotime($row['tanggal_pinjam'])); ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Batas Pinjam</span>
                                        <span class="text-sm font-bold text-[#1e3a8a]">
                                            <?php echo (empty($row['tanggal_kembali']) || $row['tanggal_kembali'] == '-' || $row['tanggal_kembali'] == '0000-00-00') ? '-' : date('d M Y', strtotime($row['tanggal_kembali'])); ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Status</span>
                                        <?php if (strtolower($row['status']) == 'aktif'): ?>
                                            <span class="bg-blue-100 text-[#1e3a8a] px-2.5 py-0.5 rounded border border-blue-200 text-xs font-bold">Aktif</span>
                                        <?php elseif (strtolower($row['status']) == 'antri'): ?>
                                            <span class="bg-yellow-100 text-yellow-700 px-2.5 py-0.5 rounded border border-yellow-200 text-xs font-bold">Antre</span>
                                        <?php else: ?>
                                            <span class="bg-green-100 text-green-700 px-2.5 py-0.5 rounded border border-green-200 text-xs font-bold">Dikembalikan</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="border-t-2 border-dashed border-gray-200 my-3"></div>

                                <div class="text-center mb-4">
                                    <div class="text-[11px] text-gray-400 mt-2 font-medium">Dibuat pada <?= date('d M Y H:i') ?></div>
                                </div>

                                <button onclick="document.getElementById('receipt-<?= $row['id_pinjam'] ?>').classList.add('hidden')"
                                    class="w-full bg-[#1e3a8a] text-white py-2.5 rounded-xl font-bold shadow hover:bg-blue-900 transition text-center flex items-center justify-center text-sm">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                <?php }
            } else { ?>
                <div class="text-center py-20 text-gray-500">Tidak ada riwayat peminjaman.</div>
            <?php } ?>
        </div>
    </main>

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