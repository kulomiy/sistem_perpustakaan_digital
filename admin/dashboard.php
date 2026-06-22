<?php
session_start();
require '../koneksi.php'; 

// Mengambil jumlah total buku dan anggota
// Mengambil jumlah total buku menggunakan fungsi agregat COUNT
$q_buku = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku");
$total_buku = mysqli_fetch_assoc($q_buku)['total'];

// Mengambil jumlah total anggota menggunakan fungsi agregat COUNT
$q_anggota = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='member'");
$total_anggota = mysqli_fetch_assoc($q_anggota)['total'];

// Menghitung Akses Aktif & Berakhir
$total_aktif = 0;
$total_berakhir = 0;
$query_pinjam = @mysqli_query($conn, "SELECT status, tanggal_kembali FROM peminjaman");

if($query_pinjam) {
    $hari_ini = strtotime(date('Y-m-d'));
    while($row = mysqli_fetch_assoc($query_pinjam)) {
        $status_pinjam = strtolower($row['status'] ?? 'aktif');
        $tgl_kembali = strtotime($row['tanggal_kembali']);
        
        if($status_pinjam == 'selesai' || $status_pinjam == 'dikembalikan') {
            continue;
        }
        
        if($status_pinjam == 'kadaluwarsa' || ($tgl_kembali && $tgl_kembali < $hari_ini)) {
            $total_berakhir++;
        } else {
            $total_aktif++;
        }
    }
}

// Mengambil data transaksi terbaru
$query_transaksi = "
    SELECT p.id_pinjam, u.username, b.judul, p.tanggal_pinjam, p.tanggal_kembali, p.status 
    FROM peminjaman p
    LEFT JOIN users u ON p.id_user = u.id_user
    LEFT JOIN buku b ON p.id_buku = b.id_buku
    ORDER BY p.id_pinjam DESC LIMIT 5
";
$result_transaksi = @mysqli_query($conn, $query_transaksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ruang Pustaka</title>
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
                <h1 class="text-[#1e3a8a] font-bold text-lg leading-none">Kontrol Panel</h1>
                <p class="text-xs text-gray-500 mt-1 font-medium">Ruang Pustaka</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-[#1e3a8a] text-white rounded-xl font-semibold shadow-sm">
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
                <i class="fa-solid fa-key w-5"></i> Akses Pinjam
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
        <header class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
            <p class="text-gray-500 text-sm mt-1">Ringkasan aktivitas dan data perpustakaan.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full opacity-50"></div>
                <div class="w-14 h-14 bg-blue-100 text-[#1e3a8a] rounded-xl flex items-center justify-center text-2xl relative z-10">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-500 mb-1">Total Buku</p>
                    <h3 class="text-2xl font-bold text-gray-900"><?= $total_buku; ?></h3>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-green-50 rounded-full opacity-50"></div>
                <div class="w-14 h-14 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-2xl relative z-10">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-500 mb-1">Total Anggota</p>
                    <h3 class="text-2xl font-bold text-gray-900"><?= $total_anggota; ?></h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-orange-50 rounded-full opacity-50"></div>
                <div class="w-14 h-14 bg-orange-100 text-orange-500 rounded-xl flex items-center justify-center text-2xl relative z-10">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-500 mb-1">Akses Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-900"><?= $total_aktif; ?></h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-50 rounded-full opacity-50"></div>
                <div class="w-14 h-14 bg-red-100 text-red-600 rounded-xl flex items-center justify-center text-2xl relative z-10">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-500 mb-1">Akses Berakhir</p>
                    <h3 class="text-2xl font-bold text-gray-900"><?= $total_berakhir; ?></h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Transaksi Terbaru</h3>
                <a href="riwayat_akses.php" class="text-sm font-semibold text-[#1e3a8a] hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-[13px] text-gray-400 border-b border-gray-100">
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Username</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Buku</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tgl Pinjam</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Batas Pinjam</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php 
                        if($result_transaksi && mysqli_num_rows($result_transaksi) > 0) {
                            while($trx = mysqli_fetch_assoc($result_transaksi)) {
                                $status = strtolower($trx['status']);
                                if($status == 'aktif') {
                                    $badge = '<span class="bg-blue-100 text-[#1e3a8a] px-3 py-1 rounded-md text-[11px] font-bold border border-blue-200">Aktif</span>';
                                } else if($status == 'antri') {
                                    $badge = '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-md text-[11px] font-bold border border-yellow-200">Antre</span>';
                                } else {
                                    $badge = '<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-md text-[11px] font-bold border border-gray-200">Selesai</span>';
                                }
                        ?>
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 font-medium text-gray-500">PMJ-<?= str_pad($trx['id_pinjam'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($trx['username']); ?></td>
                            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($trx['judul'] ?? '-'); ?></td>
                            <td class="px-6 py-4 font-medium text-[#1e3a8a]"><?= (!empty($trx['tanggal_pinjam']) && $trx['tanggal_pinjam'] != '0000-00-00') ? date('d M Y', strtotime($trx['tanggal_pinjam'])) : '-'; ?></td>
                            <td class="px-6 py-4 font-medium text-[#1e3a8a]"><?= (!empty($trx['tanggal_kembali']) && $trx['tanggal_kembali'] != '0000-00-00') ? date('d M Y', strtotime($trx['tanggal_kembali'])) : '-'; ?></td>
                            <td class="px-6 py-4"><?= $badge; ?></td>
                        </tr>
                        <?php } } else { ?>
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada transaksi.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>