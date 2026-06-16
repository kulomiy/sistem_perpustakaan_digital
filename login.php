<?php
session_start();
require 'koneksi.php'; 

if (isset($_POST['Masuk'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        if ($password == $data['password']) {
            $_SESSION['user_id'] = $data['id_user']; 
            $_SESSION['role'] = $data['role'];
            
            if($data['role'] == 'admin') {
                header("Location: admin/dashboard.php"); 
            } else {
                header("Location: user/beranda.php"); // Diarahkan ke Activity History
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak terdaftar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 flex h-screen">

    <!-- Left Panel: Branding -->
    <div class="hidden md:flex md:w-5/12 bg-[#003882] relative flex-col justify-between p-12 overflow-hidden border-r border-gray-200">
        <!-- Overlay pattern placeholder -->
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-bold text-white mb-2">Ruang Pustaka</h1>
            <p class="text-blue-200 text-sm">Temukan, Baca, dan Kelola Pengetahuan dalam Satu Tempat</p>
        </div>
        <div class="relative z-10 mt-auto bg-[#002b66]/50 border border-blue-400/20 p-5 rounded-lg backdrop-blur-sm">
            <p class="text-blue-100 text-sm italic">"Ruang digital yang memudahkanmu mengakses koleksi, membaca, dan menikmati pengalaman literasi yang nyaman."</p>
        </div>
    </div>

    <!-- Right Panel: Login Form -->
    <div class="w-full md:w-7/12 flex items-center justify-center bg-white p-8">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-bold text-[#003882] mb-2">Selamat Datang Kembali</h2>
            <p class="text-gray-500 text-sm mb-8">Masuk untuk melanjutkan perjalanan literasimu bersama Ruang Pustaka.</p>
            
            <?php if(isset($error)) echo "<p class='bg-red-50 text-red-600 p-3 rounded-md text-sm mb-4 border border-red-100'><i class='fa-solid fa-circle-exclamation mr-2'></i>$error</p>"; ?>
            
            <form method="POST" action="">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-regular fa-envelope"></i></div>
                        <input type="text" name="username" placeholder="Masukkan username..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882] focus:border-transparent text-sm transition" required>
                    </div>
                </div>
               <div class="mb-6">
    <div class="flex justify-between items-center mb-2">
        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Password</label>
    </div>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-lock text-sm"></i></div>
        <input type="password" name="password" id="passwordField" placeholder="••••••••" class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882] focus:border-transparent text-sm transition" required>
        <div id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer hover:text-gray-600">
            <i class="fa-regular fa-eye" id="eyeIcon"></i>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#passwordField');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        // Cek tipe input: jika password ubah ke text, jika text ubah ke password
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        // Ganti ikon mata (buka/tutup)
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>
                <button type="submit" name="Masuk" class="w-full bg-[#003882] text-white py-3 rounded-md text-sm font-bold shadow-md hover:bg-blue-900 transition mb-6">Masuk</button>
            </form>
            
            <p class="text-center text-sm text-gray-600">Belum punya akun? <a href="register.php" class="text-[#003882] font-bold hover:underline">Daftar</a></p>
        </div>
    </div>
</body>
</html>