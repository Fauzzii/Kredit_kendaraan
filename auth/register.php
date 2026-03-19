<?php

include '../conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $cek_email = mysqli_query($conn, "SELECT * FROM pengguna where email = '$email'");

    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email sudah terdaftar, gunakan email lain!');</script>";
    } else {
        $simpan = mysqli_query($conn, "INSERT INTO pengguna (username, email, password, role) VALUES ('$username', '$email', '$password', 'user')");

        if ($simpan) {
            echo "<script>
                    alert('Daftar akun berhasil! Silakan login.');
                  </script>";
        } else {
            echo "<script>alert('Gagal mendaftar, silakan coba lagi.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kreditku\</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-green-50 flex items-center justify-center min-h-screen font-sans antialiased">

    <div class="bg-white p-8 rounded-2xl shadow-sm w-full max-w-sm border border-green-100">

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-green-800 mb-2">Selamat Datang</h2>
            <p class="text-sm text-gray-500">Silakan masuk untuk melanjutkan</p>
        </div>

        <form method="POST">
            <div class="mb-5">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" placeholder="Masukkan username Anda" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
            <div class="mb-5">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" placeholder="Masukkan email Anda" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>

            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" placeholder="Masukkan password Anda" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>

            <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-lg transition-colors duration-200">
                Daftar Akun
            </button>
        </form>

        <div class="flex justify-end mt-6 text-sm">
            <a href="login.php" class="text-green-600 hover:text-green-700 hover:underline">Sudah punya akun?</a>
        </div>

    </div>

</body>

</html>