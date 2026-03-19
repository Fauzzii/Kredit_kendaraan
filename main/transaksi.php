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

$query_kendaraan = mysqli_query($conn, "SELECT * FROM kendaraan ORDER BY id DESC");

if (isset($_POST['ajukan_kredit'])) {
    $id_kendaraan = $_POST['id_kendaraan'];
    $tenor = $_POST['tenor'];

    $dp = $_POST['dp_real'];
    $bunga_persen = 5;
    $total_bunga = $_POST['bunga_real'];
    $cicilan_per_bulan = $_POST['cicilan_real'];
    $total_pembayaran = $_POST['total_real'];

    $simpan = mysqli_query($conn, "INSERT INTO transaksi 
        (id_pengguna, id_kendaraan, dp, tenor, bunga, cicilan_perbulan, total_pembayaran, total_bunga, status_pengajuan) 
        VALUES 
        ('$id_pengguna', '$id_kendaraan', '$dp', '$tenor', '$bunga_persen', '$cicilan_per_bulan', '$total_pembayaran', '$total_bunga', 'menunggu')");

    if ($simpan) {
        echo "<script>alert('Pengajuan berhasil! Silakan tunggu konfirmasi admin.'); window.location.href='transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal mengajukan kredit.');</script>";
    }
}

$query_pending = mysqli_query($conn, "SELECT id_kendaraan FROM transaksi WHERE id_pengguna = '$id_pengguna' AND status_pengajuan = 'menunggu'");
$kendaraan_pending = [];
while ($pending = mysqli_fetch_assoc($query_pending)) {
    $kendaraan_pending[] = $pending['id_kendaraan'];
}
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
                    <h2 class="text-2xl font-bold text-gray-800">Ajukan Kredit Kendaraan</h2>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-green-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-green-100 text-green-800">
                            <tr>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">No</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Merk</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Jenis</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Model</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200">Harga (Rp)</th>
                                <th class="py-3 px-4 font-semibold text-sm border-b border-green-200 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            <?php
                            if (mysqli_num_rows($query_kendaraan) > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($query_kendaraan)) {
                            ?>
                                    <tr class="hover:bg-green-50 border-b border-gray-100 last:border-0">
                                        <td class="py-3 px-4"><?php echo $no++; ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['merk']); ?></td>
                                        <td class="py-3 px-4 capitalize"><?php echo htmlspecialchars($row['jenis']); ?></td>
                                        <td class="py-3 px-4 capitalize"><?php echo htmlspecialchars($row['model']); ?></td>
                                        <td class="py-3 px-4"><?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <?php
                                            if (in_array($row['id'], $kendaraan_pending)) {
                                            ?>
                                                <button type="button" disabled class="bg-gray-300 text-gray-500 cursor-not-allowed px-3 py-1 rounded text-sm font-medium mr-1" title="Pengajuan sedang diproses">
                                                    Menunggu
                                                </button>
                                            <?php
                                            } else {
                                            ?>
                                                <button type="button" onclick="openModal('modalTransaksi<?php echo $row['id']; ?>')" class="border border-green-500 text-green-600 hover:bg-green-500 hover:text-white px-3 py-1 rounded text-sm transition-colors mr-1">
                                                    Ajukan
                                                </button>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <div id="modalTransaksi<?php echo $row['id']; ?>" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
                                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
                                            <div class="flex justify-between items-center p-4 border-b">
                                                <h3 class="text-lg font-bold text-gray-800">Ajukan Permintaan Kredit</h3>
                                                <button type="button" onclick="closeModal('modalTransaksi<?php echo $row['id']; ?>')" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
                                            </div>
                                            <div class="p-4">
                                                <form method="POST">
                                                    <input type="hidden" name="id_kendaraan" value="<?php echo $row['id']; ?>">

                                                    <div class="bg-green-50 p-3 rounded-lg border border-green-100 mb-4 text-sm">
                                                        <span class="font-bold text-green-800"><?php echo htmlspecialchars($row['merk']) . ' ' . htmlspecialchars($row['model']); ?></span><br>
                                                        Harga: <span class="font-bold">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tenor Bulan</label>
                                                        <select name="tenor" id="tenor_<?php echo $row['id']; ?>" onchange="hitungKredit(<?php echo $row['id']; ?>, <?php echo $row['harga']; ?>)" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none font-semibold text-green-700">
                                                            <option value="" disabled selected>-- Pilih Tenor --</option>
                                                            <option value="12">12 Bulan (1 Tahun)</option>
                                                            <option value="24">24 Bulan (2 Tahun)</option>
                                                            <option value="36">36 Bulan (3 Tahun)</option>
                                                            <option value="48">48 Bulan (4 Tahun)</option>
                                                            <option value="60">60 Bulan (5 Tahun)</option>
                                                        </select>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-500 mb-1">DP (50%)</label>
                                                            <input type="text" id="dp_tampil_<?php echo $row['id']; ?>" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm">
                                                            <input type="hidden" name="dp_real" id="dp_real_<?php echo $row['id']; ?>">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-500 mb-1">Total Bunga (5% / thn)</label>
                                                            <input type="text" id="bunga_tampil_<?php echo $row['id']; ?>" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm">
                                                            <input type="hidden" name="bunga_real" id="bunga_real_<?php echo $row['id']; ?>">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-green-700 mb-1">Cicilan / Bulan</label>
                                                            <input type="text" id="cicilan_tampil_<?php echo $row['id']; ?>" readonly class="w-full px-3 py-2 bg-green-100 border border-green-300 rounded-lg text-green-800 font-bold text-sm">
                                                            <input type="hidden" name="cicilan_real" id="cicilan_real_<?php echo $row['id']; ?>">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-500 mb-1">Total Pembayaran</label>
                                                            <input type="text" id="total_tampil_<?php echo $row['id']; ?>" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm">
                                                            <input type="hidden" name="total_real" id="total_real_<?php echo $row['id']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" onclick="closeModal('modalTransaksi<?php echo $row['id']; ?>')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                                                        <button type="submit" name="ajukan_kredit" class="px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600">Proses Pengajuan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 italic">
                                        Data kendaraan belum tersedia.
                                    </td>
                                </tr>
                            <?php } ?>
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
                    <a href="../main/transaksi.php" class="block px-4 py-3 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                        Transaksi
                    </a>
                    <a href="../main/pengajuan.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
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

    <script>
        function openModal(modalID) {
            document.getElementById(modalID).classList.remove('hidden');
        }

        function closeModal(modalID) {
            document.getElementById(modalID).classList.add('hidden');
            const form = document.querySelector(`#${modalID} form`);
            if (form) form.reset();
        }

        function hitungKredit(id_modal, harga_kendaraan) {
            let tenorBulan = parseInt(document.getElementById('tenor_' + id_modal).value);

            if (isNaN(tenorBulan)) return;

            let tahun = tenorBulan / 12;

            let dp = harga_kendaraan * 0.50; //dp 50% dari harga asli
            let sisaHutang = harga_kendaraan - dp;
            let totalBunga = (harga_kendaraan * 0.05) * tahun; //bunga 5% per tahun dari harga asli
            let totalHutang = sisaHutang + totalBunga;
            let cicilanPerBulan = totalHutang / tenorBulan;
            let totalPembayaranAkhir = dp + totalHutang;

            document.getElementById('dp_tampil_' + id_modal).value = formatRupiah(dp);
            document.getElementById('bunga_tampil_' + id_modal).value = formatRupiah(totalBunga);
            document.getElementById('cicilan_tampil_' + id_modal).value = formatRupiah(cicilanPerBulan);
            document.getElementById('total_tampil_' + id_modal).value = formatRupiah(totalPembayaranAkhir);

            document.getElementById('dp_real_' + id_modal).value = Math.round(dp);
            document.getElementById('bunga_real_' + id_modal).value = Math.round(totalBunga);
            document.getElementById('cicilan_real_' + id_modal).value = Math.round(cicilanPerBulan);
            document.getElementById('total_real_' + id_modal).value = Math.round(totalPembayaranAkhir);
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }
    </script>
</body>

</html>