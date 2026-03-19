<?php
session_start();
include '../conn.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    echo "<script>
          alert('Anda harus login terlebih dahulu')
          window.location.href = '../auth/login.php'
        </script>";
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

$query = mysqli_query( $conn, "SELECT * FROM pengguna where id = '$id_pengguna'");
$data_user = mysqli_fetch_assoc($query);

$nama_user = $data_user['username'];
$inisial = strtoupper(substr($nama_user, 0, 1));

if ($_SESSION['role'] === 'admin') {
    $query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
}else {
    $query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi where id_pengguna = '$id_pengguna'");
}

$data_total = mysqli_fetch_assoc($query_total);
$total_pengajuan = $data_total['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kreditku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-green-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <div class="flex-1 flex flex-col overflow-y-auto">

            <header class="bg-white p-4 shadow-sm border-b border-green-100 flex justify-between items-center px-8">
                <h1 class="text-xl font-bold text-green-800">Sistem Kredit</h1>

                <a href="profile.php" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-lg hover:bg-green-600 hover:shadow-md transition-all" title="Lihat Profil">
                    <?php echo $inisial; ?>
                </a>
            </header>

            <main class="p-8">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                <p class="text-gray-500 mt-1">Selamat datang kembali, <span class="font-semibold text-green-700"><?php echo ($data_user['username']); ?></span>!</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    <div class="bg-white p-6 rounded-xl border border-green-100 shadow-sm border-l-4 border-l-green-500">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Pengajuan</h3>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_pengajuan; ?></p>
                    </div>
                </div>
            </main>

        </div>

        <aside class="w-64 bg-white border-l border-green-100 flex flex-col shadow-sm relative z-10">

            <div class="p-6 border-b border-green-100 text-center">
                <h2 class="text-2xl font-black text-green-600 tracking-wider">KREDIT<span class="text-green-800">KU</span></h2>
            </div>

            <nav class="flex-1 p-4 space-y-2 mt-2">

                <?php
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
                ?>
                    <a href="dashboard.php" class="block px-4 py-3 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                        Dashboard
                    </a>
                    <a href="transaksi.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Transaksi
                    </a>
                    <a href="pengajuan.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Pengajuan
                    </a>

                <?php
                } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                ?>
                    <a href="dashboard.php" class="block px-4 py-3 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                        Dashboard
                    </a>
                    <a href="../admin/transaksi.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Transaksi
                    </a>
                    <a href="../admin/kendaraan.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Data Kendaraan
                    </a>
                <?php
                }
                ?>

            </nav>

            <div class="p-4 border-t border-green-100">
                <a href="../action/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="block w-full text-center px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 font-semibold rounded-lg transition-colors">
                    Logout
                </a>
            </div>

        </aside>

    </div>

</body>

</html>