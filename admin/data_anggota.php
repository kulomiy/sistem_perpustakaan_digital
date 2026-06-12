<?php
session_start();
require '../koneksi.php'; 

// Ambil data anggota saja
$query = "SELECT * FROM users WHERE role='member'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - LibAdmin Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
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
            <a href="dashboard.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-table-cells-large w-5 text-center"></i> Dashboard
            </a>
            <a href="data_buku.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-book-open w-5 text-center"></i> Data Buku
            </a>
            <a href="data_anggota.php" class="flex items-center gap-3 bg-blue-50 text-[#1a56db] px-3 py-2.5 rounded-md text-sm font-semibold">
                <i class="fa-solid fa-user-group w-5 text-center"></i> Data Anggota
            </a>
            <a href="akses_buku.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-regular fa-handshake w-5 text-center"></i> Akses Buku
            </a>
            <a href="riwayat_akses.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center transform rotate-180"></i> Riwayat Akses
            </a>
        </nav>

        <div class="border-t border-gray-200 mt-auto p-4">
            <a href="../login.php" class="flex items-center gap-3 text-red-600 hover:bg-red-50 px-3 py-2 rounded-md text-sm font-bold transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Logout
            </a>
        </div>
    </aside>

    <main class="ml-64 flex-1 p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Data Anggota</h2>
                <p class="text-gray-500 text-sm">Kelola akun pengguna dan pantau status keanggotaan.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="text-[11px] text-gray-500 bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider">ID User</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <?php if(mysqli_num_rows($result) > 0) { 
                        while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">USR-<?php echo str_pad($row['id_user'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                <?php echo substr($row['username'], 0, 2); ?>
                            </div>
                            <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($row['username']); ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 capitalize"><?php echo htmlspecialchars($row['role']); ?></td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-[11px] font-bold">Aktif</span>
                        </td>
                        <td class="px-6 py-4 text-right">
    <a href="hapus_anggota.php?id=<?php echo $row['id_user']; ?>" 
       onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');" 
       class="text-gray-400 hover:text-red-600 transition p-1 ml-2" title="Hapus Anggota">
        <i class="fa-solid fa-trash"></i>
    </a>
</td>
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data anggota.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>