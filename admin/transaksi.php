<?php
session_start();
include '../conn.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    echo "<script>alert('Anda harus login terlebih dahulu'); window.location.href='../auth/login.php';</script>";
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Anda bukan admin.'); window.location.href='../main/dashboard.php';</script>";
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];
$query_user = mysqli_query($conn, "SELECT * FROM pengguna where id = '$id_pengguna'");
$data_user = mysqli_fetch_assoc($query_user);

$nama_user = $data_user['username'];
$inisial = strtoupper(substr($nama_user, 0, 1));

if (isset($_POST['terima_pengajuan'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $update = mysqli_query($conn, "UPDATE transaksi SET status_pengajuan = 'disetujui' WHERE id = '$id_transaksi'");
    
    if ($update) {
        echo "<script>alert('Pengajuan berhasil DISETUJUI!'); window.location.href='transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses data.');</script>";
    }
}

if (isset($_POST['tolak_pengajuan'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $update = mysqli_query($conn, "UPDATE transaksi SET status_pengajuan = 'ditolak' WHERE id = '$id_transaksi'");
    
    if ($update) {
        echo "<script>alert('Pengajuan berhasil DITOLAK!'); window.location.href='transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses data.');</script>";
    }
}

$query_transaksi = mysqli_query($conn, "
    SELECT t.id AS id_transaksi, t.dp, t.tenor, t.cicilan_perbulan, t.status_pengajuan,
           p.username, p.email, p.nik, p.no_hp, p.alamat, p.pekerjaan, p.penghasilan,
           k.merk, k.model, k.harga
    FROM transaksi t
    JOIN pengguna p ON t.id_pengguna = p.id
    JOIN kendaraan k ON t.id_kendaraan = k.id
    ORDER BY t.id DESC
");
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
                <a href="../main/profile.php" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-lg hover:bg-green-600 transition-all" title="Lihat Profil">
                    <?php echo $inisial; ?>
                </a>
            </header>

            <main class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Kelola Pengajuan Kredit Masuk</h2>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-green-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-green-100 text-green-800">
                            <tr>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">No</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Nama Pemohon</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Kendaraan</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Tenor</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Cicilan/Bulan</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200 text-center">Status</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            
                            <?php if (mysqli_num_rows($query_transaksi) > 0) : ?>
                                <?php
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($query_transaksi)) :
                                    $bg_status = 'bg-yellow-100 text-yellow-700';
                                    if ($row['status_pengajuan'] == 'disetujui') $bg_status = 'bg-green-100 text-green-700';
                                    if ($row['status_pengajuan'] == 'ditolak') $bg_status = 'bg-red-100 text-red-700';
                                ?>
                                    <tr class="hover:bg-green-50 border-b border-gray-100 last:border-0 align-middle">
                                        <td class="py-3 px-4"><?php echo $no++; ?></td>
                                        <td class="py-3 px-4 font-medium text-gray-800"><?php echo ($row['username']); ?></td>
                                        <td class="py-3 px-4"><?php echo ($row['merk'] . ' ' . $row['model']); ?></td>
                                        <td class="py-3 px-4"><?php echo ($row['tenor']); ?> Bln</td>
                                        <td class="py-3 px-4">Rp <?php echo number_format($row['cicilan_perbulan'], 0, ',', '.'); ?></td>
                                        
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize <?php echo $bg_status; ?>">
                                                <?php echo ($row['status_pengajuan']); ?>
                                            </span>
                                        </td>
                                        
                                        <td class="py-3 px-4 text-center flex justify-center space-x-1">
                                            <button type="button" onclick="openModal('modalProfil<?php echo $row['id_transaksi']; ?>')" class="border border-blue-500 text-blue-600 hover:bg-blue-500 hover:text-white px-3 py-1 rounded text-xs font-medium transition-colors" title="Lihat Profil Pemohon">
                                                Profil
                                            </button>

                                            <?php if ($row['status_pengajuan'] == 'menunggu') : ?>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="id_transaksi" value="<?php echo $row['id_transaksi']; ?>">
                                                    <button type="submit" name="terima_pengajuan" onclick="return confirm('Yakin ingin MENYETUJUI pengajuan ini?');" class="bg-green-500 text-white hover:bg-green-600 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                        Terima
                                                    </button>
                                                </form>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="id_transaksi" value="<?php echo $row['id_transaksi']; ?>">
                                                    <button type="submit" name="tolak_pengajuan" onclick="return confirm('Yakin ingin MENOLAK pengajuan ini?');" class="bg-red-500 text-white hover:bg-red-600 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                        Tolak
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <div id="modalProfil<?php echo $row['id_transaksi']; ?>" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
                                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
                                            <div class="flex justify-between items-center p-4 border-b bg-green-50">
                                                <h3 class="text-lg font-bold text-green-800">Profil Pemohon</h3>
                                                <button type="button" onclick="closeModal('modalProfil<?php echo $row['id_transaksi']; ?>')" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
                                            </div>
                                            <div class="p-6 space-y-3 text-sm text-gray-700">
                                                <div><span class="font-semibold block text-xs text-gray-500">Nama Lengkap</span> <?php echo ($row['username']); ?></div>
                                                <div><span class="font-semibold block text-xs text-gray-500">Email</span> <?php echo ($row['email']); ?></div>
                                                <div><span class="font-semibold block text-xs text-gray-500">NIK (KTP)</span> <?php echo !empty($row['nik']) ? ($row['nik']) : '<i class="text-red-400">Belum diisi</i>'; ?></div>
                                                <div><span class="font-semibold block text-xs text-gray-500">Nomor HP / WA</span> <?php echo !empty($row['no_hp']) ? ($row['no_hp']) : '<i class="text-red-400">Belum diisi</i>'; ?></div>
                                                <div><span class="font-semibold block text-xs text-gray-500">Pekerjaan</span> <?php echo !empty($row['pekerjaan']) ? ($row['pekerjaan']) : '<i class="text-red-400">Belum diisi</i>'; ?></div>
                                                <div><span class="font-semibold block text-xs text-gray-500">Penghasilan / Bulan</span> <?php echo !empty($row['penghasilan']) ? 'Rp ' . number_format($row['penghasilan'], 0, ',', '.') : '<i class="text-red-400">Belum diisi</i>'; ?></div>
                                                <div><span class="font-semibold block text-xs text-gray-500">Alamat Lengkap</span> <?php echo !empty($row['alamat']) ? ($row['alamat']) : '<i class="text-red-400">Belum diisi</i>'; ?></div>
                                            </div>
                                            <div class="p-4 border-t text-right bg-gray-50">
                                                <button type="button" onclick="closeModal('modalProfil<?php echo $row['id_transaksi']; ?>')" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400 italic">
                                        Belum ada pengajuan kredit yang masuk.
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
                <a href="../main/dashboard.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                    Dashboard
                </a>
                <a href="transaksi.php" class="block px-4 py-3 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                    Transaksi
                </a>
                <a href="kendaraan.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                    Data Kendaraan
                </a>
            </nav>
            <div class="p-4 border-t border-green-100">
                <a href="../action/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="block w-full text-center px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 font-semibold rounded-lg transition-colors">
                    Logout
                </a>
            </div>
        </aside>

    </div>

    <script>
        function openModal(modalID) {
            document.getElementById(modalID).classList.remove('hidden');
        }

        function closeModal(modalID) {
            document.getElementById(modalID).classList.add('hidden');
        }
    </script>
</body>

</html>