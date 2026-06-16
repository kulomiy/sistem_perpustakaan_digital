<?php
session_start();
// Mundur 1 folder untuk memanggil koneksi
require '../koneksi.php'; 

$query_buku = mysqli_query($conn, "SELECT * FROM buku LIMIT 8");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Buku - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <div class="text-xl font-bold text-blue-800">Ruang Pustaka</div>
        <div class="space-x-4">
            <a href="#" class="text-gray-600 hover:text-blue-800">Home</a>
            <a href="katalog.php" class="text-blue-800 font-medium">Daftar Buku</a>
            <a href="../login.php" class="text-red-600 ml-4">Logout</a> </div>
    </nav>

    <div class="max-w-6xl mx-auto mt-8 bg-blue-100 rounded-lg p-10 text-center">
        <h1 class="text-3xl font-bold text-blue-900 mb-4">Jelajahi Dunia Pengetahuan</h1>
        <p class="text-blue-700 mb-6">Akses ribuan koleksi buku digital, jurnal, dan literatur akademik.</p>
        <div class="max-w-xl mx-auto flex">
            <input type="text" placeholder="Cari Buku..." class="w-full px-4 py-2 rounded-l-md border focus:outline-none">
            <button class="bg-blue-800 text-white px-6 py-2 rounded-r-md">Cari</button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto mt-10 px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Koleksi Terbaru</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php 
            if(mysqli_num_rows($query_buku) > 0) {
                while($buku = mysqli_fetch_assoc($query_buku)) {
            ?>
            <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="h-48 bg-gray-300 w-full flex items-center justify-center text-gray-500">Cover</div>
                <div class="p-4">
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Tersedia</span>
                    <h3 class="font-bold text-gray-800 mt-2"><?php echo htmlspecialchars($buku['judul']); ?></h3>
                    <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($buku['penulis']); ?></p>
                </div>
            </div>
            <?php 
                }
            } else {
                echo "<p class='col-span-4 text-center text-gray-500'>Belum ada data buku.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>