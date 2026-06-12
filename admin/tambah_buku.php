<?php
session_start();
require '../koneksi.php'; 

// Proses jika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    
    // Ambil nilai kategori sebagai angka (ID)
    $id_kategori = (int)$_POST['id_kategori'];
    $stok = (int)$_POST['stok'];

    // Query INSERT disesuaikan persis dengan nama kolom di database
    $query = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, id_kategori, stok) 
              VALUES ('$judul', '$penulis', '$penerbit', '$tahun_terbit', $id_kategori, $stok)";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Buku baru berhasil ditambahkan!'); window.location.href='data_buku.php';</script>";
        exit();
    } else {
        $error = "Gagal menambahkan buku: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - LibAdmin Pro</title>
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
            <a href="data_buku.php" class="flex items-center gap-3 bg-blue-50 text-[#1a56db] px-3 py-2.5 rounded-md text-sm font-semibold">
                <i class="fa-solid fa-book w-5 text-center"></i> Data Buku
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
        <div class="flex items-center gap-4 mb-8">
            <a href="data_buku.php" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Tambah Buku Baru</h2>
                <p class="text-gray-500 text-sm">Lengkapi formulir di bawah ini untuk menambahkan buku ke dalam katalog.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm max-w-3xl">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Informasi Buku</h3>
            </div>
            
            <?php if(isset($error)) echo "<div class='p-4 bg-red-50 text-red-600 border-b border-red-100 text-sm'>$error</div>"; ?>

            <form action="" method="POST" class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Buku <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" required placeholder="Judul Buku" class="block w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white focus:outline-none focus:border-[#113285] focus:ring-1 focus:ring-[#113285] text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Penulis <span class="text-red-500">*</span></label>
                        <input type="text" name="penulis" required placeholder="Nama Penulis" class="block w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white focus:outline-none focus:border-[#113285] focus:ring-1 focus:ring-[#113285] text-sm">
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Penerbit <span class="text-red-500">*</span></label>
                            <input type="text" name="penerbit" required placeholder="Nama Penerbit" class="block w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white focus:outline-none focus:border-[#113285] focus:ring-1 focus:ring-[#113285] text-sm">
                        </div>
                        <div class="w-1/3">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Terbit <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun_terbit" required placeholder="YYYY" min="1900" max="2099" class="block w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white focus:outline-none focus:border-[#113285] focus:ring-1 focus:ring-[#113285] text-sm">
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                            <select name="id_kategori" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white focus:outline-none focus:border-[#113285] focus:ring-1 focus:ring-[#113285] text-sm">
                                <option value="" disabled selected>Pilih kategori buku...</option>
                                <?php
                                // Memanggil data kategori sesuai kodemu
                                $q = mysqli_query($conn, "SELECT * FROM kategori");
                                while($k = mysqli_fetch_assoc($q)){
                                ?>
                                    <option value="<?= $k['id_kategori'] ?>">
                                        <?= htmlspecialchars($k['nama_kategori']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="w-1/3">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Stock <span class="text-red-500">*</span></label>
                            <input type="number" name="stok" required placeholder="0" min="0" class="block w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white focus:outline-none focus:border-[#113285] focus:ring-1 focus:ring-[#113285] text-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end gap-3">
                    <a href="data_buku.php" class="px-5 py-2.5 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" name="simpan" class="bg-[#113285] text-white px-6 py-2.5 rounded-md text-sm font-semibold shadow hover:bg-blue-900 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Data Buku
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>