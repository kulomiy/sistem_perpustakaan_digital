<?php
session_start();
require 'koneksi.php';

if(isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    
    if($password == $confirm) {
        $cek = mysqli_query($conn, "SELECT username FROM users WHERE username='$username'");
        if(mysqli_num_rows($cek) == 0) {
            $insert = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'member')");
            if($insert) {
                echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
            } else {
                $error = "Registrasi gagal, coba lagi.";
            }
        } else {
            $error = "Username sudah terdaftar!";
        }
    } else {
        $error = "Konfirmasi password tidak cocok!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 flex h-screen">

    <div class="hidden md:flex md:w-5/12 bg-[#003882] relative flex-col justify-between p-12 overflow-hidden border-r border-gray-200">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-bold text-white mb-2">Ruang Pustaka</h1>
            <p class="text-blue-200 text-sm">Jelajahi Dunia Literasi Tanpa Batas</p>
        </div>
        <div class="relative z-10 mt-auto bg-[#002b66]/50 border border-blue-400/20 p-5 rounded-lg backdrop-blur-sm">
            <p class="text-blue-100 text-sm italic">"Masuk dan temukan ribuan koleksi digital untuk menemani perjalanan membaca dan belajarmu bersama Ruang Pustaka."</p>
        </div>
    </div>

    <div class="w-full md:w-7/12 flex items-center justify-center bg-white p-8 overflow-y-auto">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-bold text-[#003882] mb-2">Daftar</h2>
            <p class="text-gray-500 text-sm mb-8">Masuk dan jelajahi dunia literasi bersama Ruang Pustaka.</p>
            
            <?php if(isset($error)) echo "<p class='bg-red-50 text-red-600 p-3 rounded-md text-sm mb-4 border border-red-100'><i class='fa-solid fa-circle-exclamation mr-2'></i>$error</p>"; ?>

            <form method="POST" action="">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-regular fa-user"></i></div>
                        <input type="text" name="username" placeholder="Masukkan username..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882] focus:border-transparent text-sm transition" required>
                    </div>
                </div>
                
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-lock text-sm"></i></div>
                        <input type="password" name="password" placeholder="••••••••" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882] focus:border-transparent text-sm transition" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-rotate-left text-sm"></i></div>
                        <input type="password" name="confirm_password" placeholder="••••••••" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003882] focus:border-transparent text-sm transition" required>
                    </div>
                </div>

                <button type="submit" name="register" class="w-full bg-[#003882] text-white py-3 rounded-md text-sm font-bold shadow-md hover:bg-blue-900 transition mb-6">Daftar</button>
            </form>
            
            <p class="text-center text-sm text-gray-600">Sudah punya akun? <a href="login.php" class="text-[#003882] font-bold hover:underline">Masuk</a></p>
        </div>
    </div>
</body>
</html>