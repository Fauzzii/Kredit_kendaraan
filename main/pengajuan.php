<?php
session_start();
include '../conn.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    echo "<script>
          alert('Anda harus login terlebih dahulu')
          window.location.href='../auth/login.php'
        </script>";
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];
$query_user = mysqli_query($conn, "SELECT * FROM pengguna where id = '$id_pengguna'");
$data_user = mysqli_fetch_assoc($query_user);

$nama_user = $data_user['username'];
$inisial = strtoupper(substr($nama_user, 0, 1));

$query_pengajuan = mysqli_query($conn, "
    SELECT t.id AS id_transaksi, t.dp, t.tenor, t.cicilan_perbulan, t.status_pengajuan,
           k.merk, k.model, k.harga
    FROM transaksi t
    JOIN kendaraan k ON t.id_kendaraan = k.id
    WHERE t.id_pengguna = '$id_pengguna'
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengajuan - Kreditku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-green-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <div class="flex-1 flex flex-col overflow-y-auto">

            <header class="bg-white p-4 shadow-sm border-b border-green-100 flex justify-between items-center px-8">
                <h1 class="text-xl font-bold text-green-800">Sistem Kredit</h1>
                <a href="../main/profile.php" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-lg hover:bg-green-600 transition-all" title="Lihat Profil">
                    <?php echo $inisial; ?>
                </a>
            </header>

            <main class="p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat Pengajuan Kredit Saya</h2>
                    <p class="text-gray-500 text-sm mt-1">Pantau status persetujuan dari kendaraan yang Anda ajukan.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-green-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-green-100 text-green-800">
                            <tr>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">No</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Kendaraan</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Harga OTR</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Tenor</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Cicilan/Bulan</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200 text-center">Status Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            
                            <?php if (mysqli_num_rows($query_pengajuan) > 0) : ?>
                                <?php
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($query_pengajuan)) :
                                    
                                    $bg_status = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                    
                                    if ($row['status_pengajuan'] == 'disetujui') {
                                        $bg_status = 'bg-green-100 text-green-700 border-green-200';
                                    } elseif ($row['status_pengajuan'] == 'ditolak') {
                                        $bg_status = 'bg-red-100 text-red-700 border-red-200';
                                    }
                                ?>
                                    <tr class="hover:bg-green-50 border-b border-gray-100 last:border-0 align-middle">
                                        <td class="py-4 px-4"><?php echo $no++; ?></td>
                                        
                                        <td class="py-4 px-4">
                                            <span class="font-bold text-gray-800 block"><?php echo ($row['merk'] . ' ' . $row['model']); ?></span>
                                            <?php if(isset($row['tanggal_pengajuan'])) : ?>
                                                <span class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tanggal_pengajuan'])); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="py-4 px-4">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td class="py-4 px-4"><?php echo ($row['tenor']); ?> Bulan</td>
                                        <td class="py-4 px-4 font-semibold text-green-600">Rp <?php echo number_format($row['cicilan_perbulan'], 0, ',', '.'); ?></td>
                                        
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border uppercase tracking-wider <?php echo $bg_status; ?>">
                                                <?php echo ($row['status_pengajuan']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-lg font-medium text-gray-500">Belum ada riwayat pengajuan.</p>
                                            <p class="text-sm mt-1">Silakan lakukan pengajuan kendaraan di menu Transaksi.</p>
                                            <a href="transaksi.php" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-semibold">Ajukan Sekarang</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </main>

        </div>

        <aside class="w-64 bg-white border-l border-green-100 flex flex-col shadow-sm">
            <div class="p-6 border-b border-green-100 text-center">
                <h2 class="text-2xl font-black text-green-600 tracking-wider">KREDIT<span class="text-green-800">KU</span></h2>
            </div>
            <nav class="flex-1 p-4 space-y-2 mt-2">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') { ?>
                    <a href="../main/dashboard.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Dashboard
                    </a>
                    <a href="../main/transaksi.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Transaksi
                    </a>
                    <a href="../main/pengajuan.php" class="block px-4 py-3 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                        Pengajuan
                    </a>
                <?php } ?>
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