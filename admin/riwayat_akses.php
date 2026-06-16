<?php
session_start();
require '../koneksi.php'; 

$query_riwayat = "
    SELECT p.id_pinjam, u.username, b.judul, p.tanggal_kembali, p.status 
    FROM peminjaman p
    LEFT JOIN users u ON p.id_user = u.id_user
    LEFT JOIN buku b ON p.id_buku = b.id_buku
    WHERE LOWER(p.status) = 'selesai' OR LOWER(p.status) = 'dikembalikan'
    ORDER BY p.id_pinjam DESC
";
$result_riwayat = @mysqli_query($conn, $query_riwayat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Akses - Ruang Pustaka</title>
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
                <p class="text-xs text-gray-500 mt-1 font-medium">Ruang Pustaka/p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-[#1e3a8a] rounded-xl font-medium transition">
                <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
            </a>
            <a href="data_buku.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-[#1e3a8a] rounded-xl font-medium transition">
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
            <a href="riwayat_akses.php" class="flex items-center gap-3 px-4 py-3 bg-[#1e3a8a] text-white rounded-xl font-semibold shadow-sm">
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
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Riwayat Transaksi</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar histori peminjaman buku yang telah selesai dikembalikan.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Histori Peminjaman</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-[13px] text-gray-400 border-b border-gray-100">
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider w-16 text-center">No</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">ID Peminjaman</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Username</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Buku</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tgl Dikembalikan</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php 
                        if ($result_riwayat && mysqli_num_rows($result_riwayat) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result_riwayat)) {
                                $tgl_selesai = (empty($row['tanggal_kembali']) || $row['tanggal_kembali'] == '0000-00-00') ? '-' : date('d M Y', strtotime($row['tanggal_kembali']));
                        ?>
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 text-center text-gray-500 font-medium"><?= $no++; ?></td>
                            <td class="px-6 py-4 font-medium text-gray-500">PMJ-<?= str_pad($row['id_pinjam'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($row['username']); ?></td>
                            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($row['judul'] ?? '-'); ?></td>
                            <td class="px-6 py-4 text-gray-500"><?= $tgl_selesai; ?></td>
                            <td class="px-6 py-4">
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-md text-[11px] font-bold border border-green-200">Selesai</span>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada riwayat transaksi peminjaman.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>