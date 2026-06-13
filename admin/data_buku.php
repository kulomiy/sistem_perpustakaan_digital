<?php
session_start();
require '../koneksi.php';

// Menangkap nilai pencarian dan filter dari URL (jika ada)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$filter_kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';

// Menyusun Query Dasar
$query_string = "SELECT buku.*, kategori.nama_kategori 
                 FROM buku 
                 LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
                 WHERE 1=1";

// Jika ada input pencarian (Mencari di Judul ATAU Penulis)
if (!empty($search)) {
    // Menggunakan LIKE biasa yang lebih stabil untuk mencari teks (termasuk potongan huruf) di database
    $query_string .= " AND (
                        buku.judul LIKE '%$search%' OR 
                        buku.penulis LIKE '%$search%'
                      )";
}

// Jika ada filter kategori yang dipilih
if (!empty($filter_kategori)) {
    $query_string .= " AND buku.id_kategori = '$filter_kategori'";
}

// Urutkan dari yang terbaru
$query_string .= " ORDER BY buku.id_buku DESC";

$result = mysqli_query($conn, $query_string);

// Mengambil data kategori untuk dropdown filter
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - LibAdmin Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
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
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Data Buku</h2>
                <p class="text-gray-500 text-sm">Manage library book inventory, add new titles, and track stock.</p>
            </div>
            <a href="tambah_buku.php" class="bg-[#113285] text-white px-5 py-2.5 rounded-md text-sm font-semibold shadow hover:bg-blue-900 transition flex items-center gap-2 w-fit">
                <i class="fa-solid fa-plus"></i> Add New Book
            </a>
        </div>

        <form method="GET" action="" class="bg-gray-50/50 p-4 rounded-t-xl border border-gray-200 border-b-0 flex flex-wrap justify-between items-center gap-4">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari berdasarkan judul atau penulis..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-md bg-white focus:ring-1 focus:ring-[#113285] focus:border-[#113285] text-sm transition shadow-sm">
            </div>

            <div class="flex items-center gap-3">
                <select name="kategori" class="border border-gray-200 text-gray-700 py-2.5 pl-4 pr-8 rounded-md text-sm bg-white focus:outline-none focus:ring-1 focus:ring-[#113285] appearance-none font-medium shadow-sm cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <?php
                    while ($kat = mysqli_fetch_assoc($kategori_query)) {
                        $selected = ($filter_kategori == $kat['id_kategori']) ? 'selected' : '';
                        echo "<option value='" . $kat['id_kategori'] . "' $selected>" . htmlspecialchars($kat['nama_kategori']) . "</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="bg-[#113285] text-white px-5 py-2.5 rounded-md text-sm font-medium hover:bg-blue-900 flex items-center gap-2 transition shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i> Terapkan
                </button>

                <?php if (!empty($search) || !empty($filter_kategori)): ?>
                    <a href="data_buku.php" class="border border-gray-300 text-gray-600 bg-white px-4 py-2.5 rounded-md text-sm font-medium hover:bg-gray-50 transition shadow-sm" title="Hapus Filter">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="bg-white border border-gray-200 rounded-b-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[11px] text-gray-500 bg-white border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Title & Details</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Author</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Status / Stock</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {

                                $stok_buku = (int)$row['stok'];
                                if ($stok_buku > 0) {
                                    $badgeColor = "bg-green-100 text-green-700";
                                    $statusText = "Tersedia";
                                } else {
                                    $badgeColor = "bg-red-100 text-red-600";
                                    $statusText = "Habis";
                                }

                                $kategori_teks = !empty($row['nama_kategori']) ? $row['nama_kategori'] : "Tanpa Kategori";
                        ?>
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-5 flex items-start gap-4">

                                        <div class="w-12 h-16 flex-shrink-0 overflow-hidden rounded border border-gray-200">
                                            <?php if (!empty($row['cover'])): ?>
                                                <img src="../uploads/cover/<?php echo htmlspecialchars($row['cover']); ?>"
                                                    alt="Cover Buku"
                                                    class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                                    <i class="fa-solid fa-book"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <p class="font-bold text-gray-900 text-[13px]">
                                                <?php echo htmlspecialchars($row['judul']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Penerbit: <?php echo htmlspecialchars($row['penerbit']); ?> &bull;
                                                Tahun: <?php echo htmlspecialchars($row['tahun_terbit']); ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700 text-[13px]"><?php echo htmlspecialchars($row['penulis']); ?></td>
                                    <td class="px-6 py-5">
                                        <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded text-[11px] font-semibold tracking-wide">
                                            <?php echo htmlspecialchars($kategori_teks); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-block <?php echo $badgeColor; ?> px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide mb-1.5"><?php echo $statusText; ?></span>
                                        <p class="text-xs text-gray-500"><?php echo $stok_buku; ?> Total</p>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <a href="edit_buku.php?id=<?php echo $row['id_buku']; ?>" class="text-gray-400 hover:text-[#113285] transition mr-3" title="Edit Data">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="hapus_buku.php?id=<?php echo $row['id_buku']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');" class="text-gray-400 hover:text-red-600 transition" title="Hapus Data">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-magnifying-glass text-3xl mb-3 text-gray-300"></i>
                                    <p class="font-medium text-[13px]">Buku yang Anda cari tidak ditemukan.</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white flex justify-between items-center text-[13px] text-gray-600 border-t border-gray-100">
                <?php $total = $result ? mysqli_num_rows($result) : 0; ?>
                <p>Menampilkan <?php echo $total > 0 ? '1' : '0'; ?> - <?php echo $total; ?> dari <strong><?php echo $total; ?></strong> buku</p>
            </div>
        </div>
    </main>
</body>

</html>