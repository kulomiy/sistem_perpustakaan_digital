<?php
session_start();
require '../koneksi.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$filter_kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';

$query_string = "SELECT buku.*, kategori.nama_kategori 
                 FROM buku 
                 LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
                 WHERE 1=1";

if (!empty($search)) {
    $query_string .= " AND (buku.judul LIKE '%$search%' OR buku.penulis LIKE '%$search%')";
}
if (!empty($filter_kategori)) {
    $query_string .= " AND buku.id_kategori = '$filter_kategori'";
}

$query_string .= " ORDER BY buku.id_buku DESC";
$result = mysqli_query($conn, $query_string);
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Buku - E-Library Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body class="flex min-h-screen text-gray-800">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-20 shadow-sm">
        <div class="p-6 flex items-center gap-3">
            <div class="bg-[#1e3a8a] text-white p-2.5 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-book-open text-xl"></i>
            </div>
            <div>
                <h1 class="text-[#1e3a8a] font-bold text-lg leading-none">Admin Panel</h1>
                <p class="text-xs text-gray-500 mt-1 font-medium">E-Library Portal</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-[#1e3a8a] rounded-xl font-medium transition">
                <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
            </a>
            <a href="data_buku.php" class="flex items-center gap-3 px-4 py-3 bg-[#1e3a8a] text-white rounded-xl font-semibold shadow-sm">
                <i class="fa-solid fa-book w-5"></i> Data Buku
            </a>
            <a href="data_anggota.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-[#1e3a8a] rounded-xl font-medium transition">
                <i class="fa-solid fa-users w-5"></i> Data Anggota
            </a>
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Transaksi</p>
            </div>
            <a href="akses_buku.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-[#1e3a8a] rounded-xl font-medium transition">
                <i class="fa-solid fa-key w-5"></i> Akses Aktif
            </a>
            <a href="riwayat_akses.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-[#1e3a8a] rounded-xl font-medium transition">
                <i class="fa-solid fa-clock-rotate-left w-5"></i> Riwayat
            </a>
        </nav>
        <div class="p-4 border-t border-gray-100">
            <a href="../login.php" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl font-bold transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="ml-64 flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manajemen Buku</h2>
                <p class="text-gray-500 text-sm mt-1">Kelola data koleksi buku perpustakaan.</p>
            </div>
            <a href="tambah_buku.php" class="bg-[#1e3a8a] text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-blue-900 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Buku
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[250px] relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Cari judul atau penulis..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] text-sm bg-white">
                    </div>
                    <select name="kategori" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] text-sm bg-white min-w-[180px]">
                        <option value="">Semua Kategori</option>
                        <?php while ($kat = mysqli_fetch_assoc($kategori_query)) { ?>
                            <option value="<?= $kat['id_kategori']; ?>" <?= $filter_kategori == $kat['id_kategori'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="bg-[#1e3a8a] text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-900 transition">Filter</button>
                    <?php if (!empty($search) || !empty($filter_kategori)) { ?>
                        <a href="data_buku.php" class="bg-white text-gray-600 border border-gray-300 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-50 transition">Reset</a>
                    <?php } ?>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-[13px] text-gray-400 border-b border-gray-100">
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider w-16 text-center">No</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Detail Buku</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium"><?= $no++; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-16 bg-gray-100 rounded-md overflow-hidden flex-shrink-0 border border-gray-200">
                                                <?php if (!empty($row['cover'])) { ?>
                                                    <img src="../uploads/cover/<?= htmlspecialchars($row['cover']); ?>" class="w-full h-full object-cover">
                                                <?php } else { ?>
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-book"></i></div>
                                                <?php } ?>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 leading-tight"><?= htmlspecialchars($row['judul']); ?></h4>
                                                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($row['penulis']); ?> &bull; <?= htmlspecialchars($row['tahun_terbit']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 font-medium"><?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-md text-xs font-bold border border-gray-200"><?= $row['stok']; ?> Tersedia</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="edit_buku.php?id=<?= $row['id_buku']; ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center hover:bg-[#1e3a8a] hover:text-white transition" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="hapus_buku.php?id=<?= $row['id_buku']; ?>" onclick="return confirm('Hapus buku ini?');" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php } } else { ?>
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Buku tidak ditemukan.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>