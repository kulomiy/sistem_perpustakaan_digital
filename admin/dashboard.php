<?php
session_start();
require '../koneksi.php'; 

// Mengambil jumlah total buku dan anggota
$total_buku = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM buku"));
$total_anggota = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='member'"));

// Menghitung Akses Aktif & Berakhir secara otomatis dari database
$total_aktif = 0;
$total_berakhir = 0;
$query_pinjam = @mysqli_query($conn, "SELECT status, tanggal_kembali FROM peminjaman");

if($query_pinjam) {
    $hari_ini = strtotime(date('Y-m-d'));
    while($row = mysqli_fetch_assoc($query_pinjam)) {
        $status_pinjam = strtolower($row['status'] ?? 'aktif');
        $tgl_kembali = strtotime($row['tanggal_kembali']);
        
        // Abaikan data jika buku sudah selesai dikembalikan
        if($status_pinjam == 'selesai' || $status_pinjam == 'dikembalikan') {
            continue;
        }
        
        // Cek apakah akses sudah kadaluwarsa (lewat tanggal batas atau statusnya kadaluwarsa)
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
    ORDER BY p.id_pinjam DESC 
    LIMIT 5
";
$result_transaksi = @mysqli_query($conn, $query_transaksi);

// Fallback jika detail_peminjaman tidak ada
if (!$result_transaksi) {
    $result_transaksi = mysqli_query($conn, "SELECT * FROM peminjaman ORDER BY id_pinjam DESC LIMIT 5");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LibAdmin Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="flex min-h-screen text-gray-800">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-20">
        <div class="p-6 flex items-center gap-3">
            <div class="bg-[#1a56db] text-white p-2 rounded-lg flex items-center justify-center w-10 h-10">
                <i class="fa-solid fa-book-open text-lg"></i>
            </div>
            <div>
                <h1 class="text-[#1a56db] text-lg font-bold leading-tight">LibAdmin Pro</h1>
                <p class="text-[11px] text-gray-500 font-medium">Library Management System</p>
            </div>
        </div>

        <nav class="flex-1 px-4 mt-2 space-y-1">
            <a href="dashboard.php" class="flex items-center gap-3 bg-blue-50 text-[#1a56db] px-3 py-2.5 rounded-md text-sm font-semibold">
                <i class="fa-solid fa-table-cells-large w-5 text-center"></i> Dashboard
            </a>
            <a href="data_buku.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-book-open w-5 text-center"></i> Data Buku
            </a>
            <a href="data_anggota.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-user-group w-5 text-center"></i> Data Anggota
            </a>
            <a href="akses_buku.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-regular fa-handshake w-5 text-center"></i> Akses Buku
            </a>
            <a href="riwayat_akses.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center transform rotate-180"></i> Riwayat Akses
            </a>
        </nav>

        <div class="border-t border-gray-200 mt-auto">
            <div class="p-4 flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=Admin+User&background=1f2937&color=fff" alt="Admin" class="w-9 h-9 rounded-md object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">Admin User</p>
                    <p class="text-[11px] text-gray-500 truncate">System Administrator</p>
                </div>
            </div>
            <a href="../login.php" class="flex items-center gap-3 text-red-600 hover:bg-red-50 px-7 py-3 text-sm font-bold transition border-t border-gray-100">
                <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Logout
            </a>
        </div>
    </aside>

    <main class="ml-64 flex-1 p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-3xl font-bold text-[#1e40af] mb-1">Tinjauan Sistem</h2>
                <p class="text-gray-500 text-sm">Ringkasan aktivitas perpustakaan hari ini.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900">Admin Utama</p>
                    <p class="text-xs text-gray-500">admin@libpro.edu</p>
                </div>
                <div class="w-10 h-10 bg-[#0f766e] text-white rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6 relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 w-16 h-16 bg-blue-50/50 rounded-bl-3xl"></div>
                <div class="w-10 h-10 bg-[#1a56db] text-white rounded flex items-center justify-center mb-4 relative z-10">
                    <i class="fa-regular fa-file-lines text-lg"></i>
                </div>
                <p class="text-gray-500 text-[13px] font-medium mb-1">Total Koleksi Buku</p>
                <h3 class="text-3xl font-bold text-gray-900"><?php echo number_format($total_buku); ?></h3>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 w-16 h-16 bg-[#0f766e]/5 rounded-bl-3xl"></div>
                <div class="w-10 h-10 bg-[#0f766e] text-white rounded flex items-center justify-center mb-4 relative z-10">
                    <i class="fa-solid fa-user-group text-sm"></i>
                </div>
                <p class="text-gray-500 text-[13px] font-medium mb-1">Total Anggota Aktif</p>
                <h3 class="text-3xl font-bold text-gray-900"><?php echo number_format($total_anggota); ?></h3>
            </div>
            
            <div class="bg-white rounded-xl border border-gray-200 p-6 relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 w-16 h-16 bg-gray-50 rounded-bl-3xl"></div>
                <div class="w-10 h-10 bg-gray-100 text-gray-500 rounded flex items-center justify-center mb-4 relative z-10">
                    <i class="fa-solid fa-right-left text-sm"></i>
                </div>
                <p class="text-gray-500 text-[13px] font-medium mb-1">Akses Aktif</p>
                <h3 class="text-3xl font-bold text-gray-900"><?php echo number_format($total_aktif); ?></h3>
            </div>
            
            <div class="bg-white rounded-xl border border-red-200 p-6 relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 w-16 h-16 bg-red-50/50 rounded-bl-3xl"></div>
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded flex items-center justify-center mb-4 relative z-10">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <p class="text-gray-500 text-[13px] font-medium mb-1">Akses Berakhir</p>
                <h3 class="text-3xl font-bold text-red-600"><?php echo number_format($total_berakhir); ?></h3>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-900 text-lg">Transaksi Terbaru</h3>
        <a href="akses_buku.php" class="text-[#1a56db] text-sm font-semibold hover:underline">Lihat Semua</a>
    </div>

    <div class="overflow-x-auto w-full bg-white rounded-xl shadow-sm border border-gray-200">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Anggota</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Judul Buku</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl Mulai</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Tenggat</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-100">
            <?php 
            $result_trx = mysqli_query($conn, $query_transaksi);
            if($result_trx && mysqli_num_rows($result_trx) > 0) {
                while($trx = mysqli_fetch_assoc($result_trx)) {
                    $status = strtolower($trx['status'] ?? '');
                    // Badge diperbesar sedikit agar lebih proporsional
                    $badge = ($status == 'aktif') ? '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[11px] font-bold">AKTIF</span>' : '<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[11px] font-bold">SELESAI</span>';
                    
                    $tgl_pinjam = !empty($trx['tanggal_pinjam']) ? date('d M Y', strtotime($trx['tanggal_pinjam'])) : "-";
                    $tgl_kembali = !empty($trx['tanggal_kembali']) ? date('d M Y', strtotime($trx['tanggal_kembali'])) : "-";
            ?>
            <tr class="hover:bg-blue-50/50 transition duration-150">
                <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">TRX-<?php echo str_pad($trx['id_pinjam'], 4, '0', STR_PAD_LEFT); ?></td>
                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap"><?php echo htmlspecialchars($trx['username']); ?></td>
                <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($trx['judul'] ?? 'Data tidak lengkap'); ?></td>
                <td class="px-6 py-4 text-gray-600 whitespace-nowrap"><?php echo $tgl_pinjam; ?></td>
                <td class="px-6 py-4 text-gray-600 whitespace-nowrap"><?php echo $tgl_kembali; ?></td>
                <td class="px-6 py-4"><?php echo $badge; ?></td>
            </tr>
            <?php } } else { ?>
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data transaksi.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
    </main>
</body>
</html>