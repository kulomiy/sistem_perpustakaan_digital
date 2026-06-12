<?php
session_start();
require '../koneksi.php'; 

// Mengambil data riwayat peminjaman (status sudah selesai / dikembalikan)
// Kita langsung JOIN ke tabel 'buku' menggunakan kolom 'id_buku' yang ada di tabel 'peminjaman'
$query_riwayat = "
    SELECT p.id_pinjam, u.username, b.judul, p.tanggal_kembali, p.status 
    FROM peminjaman p
    LEFT JOIN users u ON p.id_user = u.id_user
    LEFT JOIN buku b ON p.id_buku = b.id_buku
    WHERE LOWER(p.status) = 'selesai' OR LOWER(p.status) = 'dikembalikan'
    ORDER BY p.id_pinjam DESC
";
$result_riwayat = @mysqli_query($conn, $query_riwayat);

// Jika terjadi error pada kueri, tampilkan pesan error
if (!$result_riwayat) {
    die("Error pada kueri database: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Akses - LibAdmin Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body class="flex min-h-screen text-gray-800">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-20">
        <div class="p-6 flex items-center gap-3">
            <div class="bg-[#1a56db] text-white p-2 rounded-lg flex items-center justify-center w-10 h-10"><i class="fa-solid fa-book-open text-lg"></i></div>
            <div>
                <h1 class="text-[#1a56db] text-lg font-bold leading-tight">LibAdmin Pro</h1>
                <p class="text-[11px] text-gray-500 font-medium">Library Management System</p>
            </div>
        </div>

        <nav class="flex-1 px-4 mt-2 space-y-1">
            <a href="dashboard.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
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
            <a href="riwayat_akses.php" class="flex items-center gap-3 bg-blue-50 text-[#1a56db] px-3 py-2.5 rounded-md text-sm font-semibold">
                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center transform rotate-180"></i> Riwayat Akses
            </a>
        </nav>
        
        <div class="border-t border-gray-200 mt-auto p-4">
            <div class="flex items-center gap-3 mb-4 px-2">
                <img src="https://ui-avatars.com/api/?name=Admin+User&background=1f2937&color=fff" alt="Admin" class="w-9 h-9 rounded-md object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">Admin User</p>
                    <p class="text-[11px] text-gray-500 truncate">System Administrator</p>
                </div>
            </div>
            <a href="../login.php" class="flex items-center gap-3 text-red-600 hover:bg-red-50 px-3 py-2 rounded-md text-sm font-bold transition"><i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Logout</a>
        </div>
    </aside>

    <main class="ml-64 flex-1 p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Riwayat Akses</h2>
                <p class="text-gray-500 text-sm">Log transaksi masa lalu dan akses yang telah dikembalikan.</p>
            </div>
            </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[11px] text-gray-500 bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">ID Transaksi</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Nama Anggota</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Judul Buku</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Tanggal Selesai</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php 
                        if ($result_riwayat && mysqli_num_rows($result_riwayat) > 0) {
                            while ($row = mysqli_fetch_assoc($result_riwayat)) {
                                $nama_anggota = !empty($row['username']) ? $row['username'] : "ID Anggota: " . ($row['id_anggota'] ?? 'N/A');
                                $judul_buku = !empty($row['judul']) ? $row['judul'] : "Data tidak lengkap";
                                $tgl_selesai = !empty($row['tanggal_kembali']) ? date('d M Y', strtotime($row['tanggal_kembali'])) : "-";
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-500">TRX-<?php echo str_pad($row['id_pinjam'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?php echo htmlspecialchars($nama_anggota); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($judul_buku); ?></td>
                            <td class="px-6 py-4 text-gray-500"><?php echo $tgl_selesai; ?></td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-[11px] font-bold border border-gray-200">
                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-folder-open text-3xl mb-3 text-gray-300"></i>
                                <p class="font-medium text-[13px]">Belum ada riwayat transaksi peminjaman.</p>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>