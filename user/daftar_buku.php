<?php
session_start();
require '../koneksi.php';

// Cek sesi user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'member') {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION['user_id'];

// Mengambil Data User untuk Profil (Hanya username)
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

// Menangkap parameter kategori dan pencarian dari URL
$kategori_aktif = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

// Mengambil SEMUA data kategori untuk tombol filter
$query_kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Menyusun Query pengambilan buku
$sql_buku = "SELECT * FROM buku WHERE 1=1";
if (!empty($kategori_aktif)) {
    $sql_buku .= " AND id_kategori = '$kategori_aktif'";
}
if (!empty($search)) {
    $sql_buku .= " AND (judul LIKE '%$search%' OR penulis LIKE '%$search%' OR penerbit LIKE '%$search%')";
}
$sql_buku .= " ORDER BY judul ASC";
$query_buku = mysqli_query($conn, $sql_buku);

$search_param = !empty($search) ? '&search=' . urlencode($search) : '';
$search_only_param = !empty($search) ? '?search=' . urlencode($search) : '';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - E-Library Portal</title>
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
                    <span class="text-[#1e3a8a] font-extrabold text-xl tracking-tight">E-Library Portal</span>
                </div>

                <div class="hidden md:flex space-x-8">
                    <a href="beranda.php" class="border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Home</a>
                    <a href="daftar_buku.php" class="border-b-2 border-[#1e3a8a] text-[#1e3a8a] inline-flex items-center px-1 pt-1 text-sm font-bold transition">Daftar Buku</a>
                    <a href="activity.php" class="border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 inline-flex items-center px-1 pt-1 text-sm font-medium transition">Riwayat Buku</a>
                </div>

                <!-- Profile Menu -->
                <div class="flex items-center gap-5 text-gray-500 relative">

                    <!-- Search Bar Baru di Navigasi -->
                    <form action="daftar_buku.php" method="GET" class="hidden md:flex items-center bg-gray-100 rounded-full px-4 py-1.5 border border-gray-200 focus-within:ring-2 focus-within:ring-blue-200">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Telusuri..." class="bg-transparent border-none focus:outline-none text-sm w-32 md:w-48 px-1">
                        <button type="submit" class="text-gray-400 hover:text-[#1e3a8a]">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </button>
                    </form>

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

    <main class="flex-1">

        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-3xl font-extrabold text-[#0f172a] mb-3">Daftar Koleksi Buku</h1>
                <p class="text-[#1e3a8a] text-sm md:text-base max-w-2xl mx-auto font-medium">Jelajahi seluruh koleksi literatur kami. Gunakan filter kategori untuk menemukan buku yang paling sesuai dengan minat Anda.</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">

                <div class="flex flex-wrap gap-2">
                    <?php
                    $semua_class = empty($kategori_aktif)
                        ? 'bg-blue-100 text-[#1e3a8a] border-blue-200 font-bold'
                        : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 font-semibold';
                    ?>
                    <a href="daftar_buku.php<?= $search_only_param ?>" class="<?= $semua_class ?> border px-4 py-2 rounded-full text-xs transition shadow-sm hover:shadow">Semua Kategori</a>

                    <?php
                    if ($query_kategori && mysqli_num_rows($query_kategori) > 0) {
                        while ($kat = mysqli_fetch_assoc($query_kategori)) {
                            $btn_class = ($kategori_aktif == $kat['id_kategori'])
                                ? 'bg-blue-100 text-[#1e3a8a] border-blue-200 font-bold'
                                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-900 font-semibold';
                    ?>
                            <a href="daftar_buku.php?kategori=<?= $kat['id_kategori'] ?><?= $search_param ?>" class="<?= $btn_class ?> border px-4 py-2 rounded-full text-xs transition shadow-sm hover:shadow">
                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>

                <div class="text-sm font-semibold text-gray-500 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                    Menampilkan <span class="text-[#1e3a8a]"><?= mysqli_num_rows($query_buku) ?></span> Buku
                </div>
            </div>

            <hr class="border-gray-200 mb-8">

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 mb-16">
                <?php
                if ($query_buku && mysqli_num_rows($query_buku) > 0) {
                    while ($buku = mysqli_fetch_assoc($query_buku)) {
                        $stok = (int)$buku['stok'];

                        if ($stok > 0) {
                            $badge = '<span class="bg-white/90 backdrop-blur-sm text-[#1e3a8a] px-2.5 py-1 rounded text-[10px] font-bold shadow-sm border border-white/50">Tersedia</span>';
                        } else {
                            $badge = '<span class="bg-red-50/90 backdrop-blur-sm text-red-600 px-2.5 py-1 rounded text-[10px] font-bold shadow-sm border border-red-100/50">Dipinjam</span>';
                        }
                ?>
                        <a href="detail_buku.php?id=<?= $buku['id_buku'] ?>" class="block bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow group flex flex-col h-full cursor-pointer">
                            <div class="h-64 relative overflow-hidden bg-gray-100">

                                <?php if (!empty($buku['cover'])): ?>
                                    <img src="../uploads/cover/<?= htmlspecialchars($buku['cover']) ?>"
                                        alt="<?= htmlspecialchars($buku['judul']) ?>"
                                        class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                        <i class="fa-solid fa-book text-5xl"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="absolute top-3 right-3 z-10">
                                    <?= $badge ?>
                                </div>

                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-bold text-gray-900 text-[13px] mb-1 leading-tight line-clamp-2 group-hover:text-[#1e3a8a] transition-colors"><?= htmlspecialchars($buku['judul']) ?></h3>
                                <p class="text-gray-500 text-xs mt-auto line-clamp-1"><?= htmlspecialchars($buku['penulis']) ?></p>
                            </div>
                        </a>
                    <?php
                    }
                } else {
                    ?>
                    <div class="col-span-full py-16 text-center text-gray-500 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                        <div class="w-16 h-16 bg-white text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-sm">
                            <i class="fa-solid fa-book-open-reader"></i>
                        </div>
                        <p class="text-gray-900 font-bold mb-1 text-lg">Buku Tidak Ditemukan</p>
                        <p class="text-sm font-medium">Buku pada kategori ini belum tersedia atau kata kunci pencarian tidak cocok.</p>
                        <a href="daftar_buku.php" class="inline-block mt-5 bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-gray-50 transition">Kembali ke Semua Kategori</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <footer class="bg-[#f8fafc] border-t border-gray-200 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h4 class="text-[#1e3a8a] font-bold text-sm mb-1">E-Library Portal</h4>
                <p class="text-[11px] text-gray-500 font-medium">&copy; <?= date('Y'); ?> Digital Library Management System. Knowledge-Centric & Accessible.</p>
            </div>
            <div class="flex gap-6 text-[11px] font-semibold text-gray-500">
                <a href="#" class="hover:text-[#1e3a8a] transition">Privacy Policy</a>
                <a href="#" class="hover:text-[#1e3a8a] transition">Terms of Service</a>
                <a href="#" class="hover:text-[#1e3a8a] transition">Help Center</a>
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