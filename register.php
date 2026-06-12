<?php
session_start();
require 'koneksi.php';

if(isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    
    if($password == $confirm) {
        // Cek apakah username sudah ada
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
    <title>Register - Nexus Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6">
    <div class="bg-white flex flex-col md:flex-row w-full max-w-5xl rounded-xl shadow-lg overflow-hidden h-[600px]">
        
        <!-- Left Banner -->
        <div class="hidden md:flex w-5/12 bg-[#003882] p-10 flex-col justify-between relative">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative z-10 flex items-center gap-2 text-white font-bold text-lg mb-8">
                <i class="fa-solid fa-book"></i> Nexus Library
            </div>
            <div class="relative z-10 mt-auto">
                <h2 class="text-3xl font-bold text-white mb-4">Discover a World of Knowledge</h2>
                <p class="text-blue-100 text-sm leading-relaxed">Create an account to access thousands of e-books and save your favorites securely in your personal digital portal.</p>
            </div>
        </div>

        <!-- Right Form -->
        <div class="w-full md:w-7/12 p-10 overflow-y-auto">
            <h2 class="text-4xl font-bold text-[#003882] mb-2">Register</h2>
            <p class="text-gray-500 text-sm mb-8">Join the Nexus Library E-Book Portal.</p>
            
            <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4'>$error</p>"; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fa-regular fa-user"></i></div>
                        <input type="text" name="username" placeholder="Masukkan Username" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md text-sm focus:ring-[#003882] focus:border-[#003882]" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fa-solid fa-lock"></i></div>
                        <input type="password" name="password" placeholder="••••••••" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md text-sm focus:ring-[#003882] focus:border-[#003882]" required>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <input type="password" name="confirm_password" placeholder="••••••••" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md text-sm focus:ring-[#003882] focus:border-[#003882]" required>
                    </div>
                </div>
                <div class="flex items-center mb-6">
                    <input type="checkbox" required class="w-4 h-4 rounded border-gray-300 text-[#003882]">
                    <label class="ml-2 text-sm text-gray-600">I agree to the <a href="#" class="text-[#003882] hover:underline">Terms and Conditions</a> and Privacy Policy.</label>
                </div>
                <button type="submit" name="register" class="w-full bg-[#003882] text-white py-3 rounded-md text-sm font-bold shadow-md hover:bg-blue-900 transition mb-6">Register</button>
            </form>
            <p class="text-center text-sm text-gray-600">Already have an account? <a href="login.php" class="text-[#003882] font-bold hover:underline">Login</a></p>
        </div>
    </div>
</body>
</html>