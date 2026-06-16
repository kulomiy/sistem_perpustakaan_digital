<?php
session_start();
require '../koneksi.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'member') { 
    header("Location: ../login.php"); 
    exit(); 
}

$id_user = $_SESSION['user_id'];
$query_user = mysqli_query($conn, "SELECT username FROM users WHERE id_user = '$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
$username = $data_user['username'] ?? 'User';

$words = explode(" ", trim($username));
$inisial = (count($words) >= 2) ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($username, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Security - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body class="flex min-h-screen text-gray-800">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-20">
        <div class="p-6 flex items-center gap-3">
            <div class="bg-[#003882] text-white p-2 rounded-lg flex items-center justify-center w-10 h-10 font-bold">NL</div>
            <div>
                <h1 class="text-[#003882] text-lg font-bold leading-tight">Ruang Pustaka</h1>
                <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wide">Member Portal</p>
            </div>
        </div>
        <nav class="flex-1 px-4 mt-4 space-y-1">
            <a href="beranda.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 px-3 py-3 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-house w-5 text-center"></i> Beranda
            </a>
            <a href="activity.php" class="flex items-center gap-3 text-gray-600 hover:bg-gray-50 px-3 py-3 rounded-md text-sm font-medium transition">
                <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> Activity History
            </a>
            <a href="security.php" class="flex items-center gap-3 text-[#003882] bg-blue-50 font-semibold px-3 py-3 rounded-md text-sm relative">
                <div class="absolute left-0 top-0 h-full w-1 bg-[#003882] rounded-r-md"></div>
                <i class="fa-solid fa-shield-halved w-5 text-center"></i> Security
            </a>
        </nav>
        <div class="border-t border-gray-200 mt-auto p-4">
            <a href="../login.php" class="flex items-center gap-3 text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium transition"><i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Logout</a>
        </div>
    </aside>

    <main class="ml-64 flex-1">
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center relative z-40">
            <div></div> <div class="flex items-center gap-5 text-gray-500 relative">
                <button id="profile-btn" class="w-9 h-9 rounded-full bg-gradient-to-br from-[#c900ff] to-[#8000ff] text-white font-medium text-sm flex items-center justify-center shadow-sm cursor-pointer hover:ring-2 hover:ring-purple-300 transition focus:outline-none">
                    <?= $inisial ?>
                </button>
                <div id="profile-dropdown" class="hidden absolute right-0 top-12 mt-2 w-[280px] bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden z-50">
                    <div class="bg-gradient-to-b from-[#263b96] to-[#1e2f75] p-5 text-white flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#c900ff] to-[#8000ff] flex items-center justify-center font-bold text-xl"><?= $inisial ?></div>
                        <div>
                            <h4 class="font-bold text-[15px] uppercase tracking-wide"><?= htmlspecialchars($username) ?></h4>
                            <p class="text-blue-200 text-xs mt-0.5">Member Aktif</p>
                        </div>
                    </div>
                    <div class="p-2">
                        <a href="security.php" class="px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-[#263b96] transition rounded-lg flex items-center gap-3">
                            <i class="fa-solid fa-lock text-gray-400 w-4 text-center"></i> Change Password
                        </a>
                        <a href="../login.php" class="px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition rounded-lg flex items-center gap-3">
                            <i class="fa-solid fa-arrow-right-from-bracket text-red-400 w-4 text-center"></i> Log Out
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-8 max-w-4xl mx-auto">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Security</h2>
                <p class="text-gray-500 text-sm">Manage your account credentials securely.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                <div class="flex items-start gap-4 mb-8 border-b border-gray-100 pb-6">
                    <div class="bg-blue-50 text-[#003882] p-3 rounded-lg"><i class="fa-solid fa-lock text-lg"></i></div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Change Password</h3>
                        <p class="text-sm text-gray-500">Ensure your account is using a secure password.</p>
                    </div>
                </div>
                
               <!-- Ubah bagian <form> di security.php menjadi seperti ini: -->
<form action="update_password.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Current Password</label>
        <input type="password" name="current_password" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882]/20 focus:border-[#003882] text-sm">
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">New Password</label>
        <input type="password" name="new_password" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882]/20 focus:border-[#003882] text-sm">
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Confirm New Password</label>
        <input type="password" name="confirm_password" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882]/20 focus:border-[#003882] text-sm">
    </div>
    <div class="md:col-span-2 pt-4 text-right">
        <button type="submit" class="bg-[#003882] text-white px-8 py-3 rounded-lg text-sm font-bold hover:bg-blue-900 transition shadow-md">Update Password</button>
    </div>
</form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('click', function(event) {
            const profileBtn = document.getElementById('profile-btn');
            const dropdown = document.getElementById('profile-dropdown');
            if (profileBtn && dropdown) {
                if (profileBtn.contains(event.target)) {
                    dropdown.classList.toggle('hidden');
                } else if (!dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>