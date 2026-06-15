<?php
session_start();
require '../koneksi.php'; 

if (!isset($_GET['id'])) {
    header("Location: data_buku.php");
    exit();
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);
$query_buku = mysqli_query($conn, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$data_buku = mysqli_fetch_assoc($query_buku);

if (!$data_buku) {
    echo "<script>alert('Data buku tidak ditemukan!'); window.location.href='data_buku.php';</script>";
    exit();
}

$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");

if (isset($_POST['update'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $id_kategori = (int)$_POST['id_kategori'];
    $stok = (int)$_POST['stok'];
    
    // Cek update cover
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        $namaFile = time() . '_' . $_FILES['cover']['name'];
        move_uploaded_file($_FILES['cover']['tmp_name'], "../uploads/cover/" . $namaFile);
        $query = "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun_terbit='$tahun_terbit', id_kategori='$id_kategori', stok='$stok', cover='$namaFile' WHERE id_buku='$id_buku'";
    } else {
        $query = "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun_terbit='$tahun_terbit', id_kategori='$id_kategori', stok='$stok' WHERE id_buku='$id_buku'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Buku berhasil diupdate!'); window.location.href='data_buku.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate buku!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku - E-Library Portal</title>
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
    </aside>

    <main class="ml-64 flex-1 p-8">
        <div class="mb-8 flex items-center gap-4">
            <a href="data_buku.php" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-[#1e3a8a] transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Data Buku</h2>
                <p class="text-gray-500 text-sm mt-1">Perbarui informasi buku ini.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-4xl">
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Buku <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" value="<?= htmlspecialchars($data_buku['judul']); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Penulis <span class="text-red-500">*</span></label>
                        <input type="text" name="penulis" value="<?= htmlspecialchars($data_buku['penulis']); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Penerbit <span class="text-red-500">*</span></label>
                        <input type="text" name="penerbit" value="<?= htmlspecialchars($data_buku['penerbit']); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Terbit <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_terbit" value="<?= htmlspecialchars($data_buku['tahun_terbit']); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] text-sm bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="id_kategori" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] text-sm bg-white">
                            <?php while ($k = mysqli_fetch_assoc($kategori_query)) { ?>
                                <option value="<?= $k['id_kategori']; ?>" <?= $data_buku['id_kategori'] == $k['id_kategori'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($k['nama_kategori']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="stok" value="<?= htmlspecialchars($data_buku['stok']); ?>" min="0" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-[#1e3a8a] focus:ring-1 focus:ring-[#1e3a8a] text-sm bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Cover Buku (Biarkan kosong jika tidak diubah)</label>
                    <div class="mt-1 flex flex-col items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition relative overflow-hidden">
                        
                        <input id="file-upload" name="cover" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" onchange="previewImage(event)">
                        
                        <?php if(!empty($data_buku['cover'])): ?>
                            <div id="upload-prompt" class="hidden space-y-1 text-center">
                                <i class="fa-solid fa-image text-gray-400 text-3xl mb-2"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <span class="relative bg-white rounded-md font-medium text-[#1e3a8a] hover:underline">Upload file</span>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>

                            <div id="image-preview-container" class="flex flex-col items-center justify-center w-full z-0">
                                <img id="image-preview" src="../uploads/cover/<?= htmlspecialchars($data_buku['cover']) ?>" alt="Preview Cover" class="h-40 w-auto object-contain mb-3 rounded shadow-sm border border-gray-200">
                                <p id="file-name" class="text-sm font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">Cover Saat Ini</p>
                                <p class="text-xs text-gray-400 mt-2">Klik area ini untuk mengganti cover</p>
                            </div>
                        <?php else: ?>
                            <div id="upload-prompt" class="space-y-1 text-center">
                                <i class="fa-solid fa-image text-gray-400 text-3xl mb-2"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <span class="relative bg-white rounded-md font-medium text-[#1e3a8a] hover:underline">Upload file</span>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>

                            <div id="image-preview-container" class="hidden flex flex-col items-center justify-center w-full z-0">
                                <img id="image-preview" src="#" alt="Preview Cover" class="h-40 w-auto object-contain mb-3 rounded shadow-sm border border-gray-200">
                                <p id="file-name" class="text-sm font-bold text-[#1e3a8a] bg-blue-50 px-3 py-1 rounded-full"></p>
                                <p class="text-xs text-gray-400 mt-1">Klik kotak ini lagi jika ingin mengganti gambar</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <a href="data_buku.php" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" name="update" class="bg-[#1e3a8a] text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow hover:bg-blue-900 transition flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Update Buku
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewImage(event) {
            const input = event.target;
            const prompt = document.getElementById('upload-prompt');
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');
            const fileNameDisplay = document.getElementById('file-name');

            // Jika user memilih file baru
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    fileNameDisplay.textContent = input.files[0].name;
                    
                    // Ubah styling badge nama file agar terlihat seperti input baru
                    fileNameDisplay.className = "text-sm font-bold text-[#1e3a8a] bg-blue-50 px-3 py-1 rounded-full";
                    
                    prompt.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                // Jangan lakukan apa-apa jika batal memilih, biarkan gambar sebelumnya tetap tampil
            }
        }
    </script>
</body>
</html>