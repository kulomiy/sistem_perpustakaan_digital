<?php
session_start();

// Pengecekan sesi: jika sudah login, langsung arahkan sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] == 'member') {
        header("Location: user/katalog.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-900 flex items-center justify-center h-screen text-white">
    
    <div class="text-center px-6">
        <div class="mb-6">
            <span class="bg-white text-blue-900 p-4 rounded-full inline-block font-bold text-2xl">
                EL
            </span>
        </div>
        
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Ruang Pustaka</h1>
        <p class="text-lg md:text-xl text-blue-200 mb-8 max-w-lg mx-auto">
            "A sanctuary for the mind, bridging the gap between high-utility administration and an inviting digital portal."
        </p>
        
        <div class="space-x-4">
            <a href="login.php" class="inline-block bg-white text-blue-900 px-8 py-3 rounded-md font-bold hover:bg-gray-100 transition shadow-lg">
                Login
            </a>
            <a href="#" class="inline-block border-2 border-white text-white px-8 py-3 rounded-md font-bold hover:bg-white hover:text-blue-900 transition shadow-lg">
                Register
            </a>
        </div>
    </div>

</body>
</html>