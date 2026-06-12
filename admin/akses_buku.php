<?php
session_start();
require '../koneksi.php'; 

// Mengambil data peminjaman yang masih aktif
// Sekarang langsung join ke tabel 'buku' menggunakan 'id_buku' yang ada di tabel 'peminjaman'
$query_akses = "
    SELECT p.id_pinjam, u.username, b.judul, p.tanggal_kembali, p.status 
    FROM peminjaman p
    LEFT JOIN users u ON p.id_user = u.id_user
    LEFT JOIN buku b ON p.id_buku = b.id_buku
    WHERE LOWER(p.status) != 'selesai' AND LOWER(p.status) != 'dikembalikan'
    ORDER BY p.id_pinjam DESC
";
$result_akses = @mysqli_query($conn, $query_akses);

// Jika terjadi error pada kueri, tampilkan pesan error untuk debugging
if (!$result_akses) {
    die("Error pada kueri database: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Buku - LibAdmin Pro</title>
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
            <a href="akses_buku.php" class="flex items-center gap-3 bg-blue-50 text-[#1a56db] px-3 py-2.5 rounded-md text-sm font-semibold">
                <i class="fa-regular fa-handshake w-5 text-center"></i> Akses Buku
            </a>
            <a href="riwayat_akses.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
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
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Akses Buku Aktif</h2>
                <p class="text-gray-500 text-sm">Monitor pemberian lisensi digital dan tenggat waktu peminjaman.</p>
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
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Tenggat Akses</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php 
                        if ($result_akses && mysqli_num_rows($result_akses) > 0) {
                            $hari_ini = strtotime(date('Y-m-d'));

                            while ($row = mysqli_fetch_assoc($result_akses)) {
                                $nama_anggota = !empty($row['username']) ? $row['username'] : "ID Anggota: " . ($row['id_anggota'] ?? 'N/A');
                                $judul_buku = !empty($row['judul']) ? $row['judul'] : "Data tidak lengkap";
                                
                                $tgl_kembali_str = !empty($row['tanggal_kembali']) ? strtotime($row['tanggal_kembali']) : 0;
                                $status = strtolower($row['status'] ?? '');

                                // Cek status apakah sudah kadaluwarsa berdasarkan tanggal hari ini
                                if ($status == 'kadaluwarsa' || ($tgl_kembali_str > 0 && $tgl_kembali_str < $hari_ini)) {
                                    $tenggat_class = "text-red-600 font-bold";
                                    $tenggat_text = date('d M Y', $tgl_kembali_str) . " (Kadaluwarsa)";
                                } else {
                                    $tenggat_class = "text-blue-600 font-medium";
                                    $tenggat_text = $tgl_kembali_str > 0 ? date('d M Y', $tgl_kembali_str) : "-";
                                }
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">TRX-<?php echo str_pad($row['id_pinjam'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?php echo htmlspecialchars($nama_anggota); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($judul_buku); ?></td>
                            <td class="px-6 py-4 <?php echo $tenggat_class; ?>"><?php echo $tenggat_text; ?></td>
                            <td class="px-6 py-4 text-right">
                                <a href="cabut_akses.php?id=<?php echo $row['id_pinjam']; ?>" onclick="return confirm('Yakin ingin mencabut akses ini secara manual?');" class="inline-block bg-red-50 text-red-600 border border-red-100 px-3 py-1.5 rounded text-xs font-bold hover:bg-red-100 transition">
                                    Cabut Akses
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-folder-open text-3xl mb-3 text-gray-300"></i>
                                <p class="font-medium text-[13px]">Saat ini tidak ada akses buku yang aktif.</p>
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