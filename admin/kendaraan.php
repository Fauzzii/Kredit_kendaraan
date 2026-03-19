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

if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='../main/dashboard.php';</script>";
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];
$query_user = mysqli_query($conn, "SELECT * FROM pengguna where id = '$id_pengguna'");
$data_user = mysqli_fetch_assoc($query_user);

$nama_user = $data_user['username'];
$inisial = strtoupper(substr($nama_user, 0, 1));

if (isset($_POST['tambah_kendaraan'])) {
    $merk = $_POST['merk'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $model = $_POST['model'];

    $simpan = mysqli_query($conn, "INSERT INTO kendaraan (merk, jenis, harga, model) VALUES ('$merk', '$jenis', '$harga', '$model')");
    if ($simpan) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='kendaraan.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data.');</script>";
    }
}

if (isset($_POST['edit_kendaraan'])) {
    $id_kendaraan = $_POST['id'];
    $merk = $_POST['merk'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $model = $_POST['model'];

    $update = mysqli_query($conn, "UPDATE kendaraan SET merk='$merk', jenis='$jenis', harga='$harga', model='$model' WHERE id='$id_kendaraan'");
    if ($update) {
        echo "<script>alert('Data berhasil diubah!'); window.location.href='kendaraan.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data.');</script>";
    }
}

if (isset($_POST['hapus_kendaraan'])) {
    $id_kendaraan = $_POST['id'];

    $delete = mysqli_query($conn, "DELETE FROM kendaraan WHERE id='$id_kendaraan'");

    if ($delete) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='kendaraan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.');</script>";
    }
}

$query_kendaraan = mysqli_query($conn, "SELECT * FROM kendaraan ORDER BY id DESC");
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
                    <h2 class="text-2xl font-bold text-gray-800">Kelola Data Kendaraan</h2>
                    <button type="button" onclick="openModal('modalTambah')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                        + Tambah Data
                    </button>
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
                                        <td class="py-3 px-4"><?php echo ($row['merk']); ?></td>
                                        <td class="py-3 px-4 capitalize"><?php echo ($row['jenis']); ?></td>
                                        <td class="py-3 px-4 capitalize"><?php echo ($row['model']); ?></td>
                                        <td class="py-3 px-4"><?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <button type="button" onclick="openModal('modalEdit<?php echo $row['id']; ?>')" class="border border-green-500 text-green-600 hover:bg-green-500 hover:text-white px-3 py-1 rounded text-sm transition-colors mr-1">
                                                Edit
                                            </button>
                                            <button type="button" onclick="openModal('modalHapus<?php echo $row['id']; ?>')" class="border border-red-500 text-red-600 hover:bg-red-500 hover:text-white px-3 py-1 rounded text-sm transition-colors">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>

                                    <div id="modalEdit<?php echo $row['id']; ?>" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
                                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
                                            <div class="flex justify-between items-center p-4 border-b">
                                                <h3 class="text-lg font-bold text-gray-800">Edit Data Kendaraan</h3>
                                                <button type="button" onclick="closeModal('modalEdit<?php echo $row['id']; ?>')" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
                                            </div>
                                            <div class="p-4">
                                                <form method="POST">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                                                        <input type="text" name="merk" value="<?php echo ($row['merk']); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</label>
                                                        <select name="jenis" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                                                            <option value="motor" <?php if ($row['jenis'] == 'motor') echo 'selected'; ?>>Motor</option>
                                                            <option value="mobil" <?php if ($row['jenis'] == 'mobil') echo 'selected'; ?>>Mobil</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                                                        <input type="text" name="model" value="<?php echo ($row['model']); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                                                    </div>
                                                    <div class="mb-6">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                                        <input type="number" name="harga" value="<?php echo $row['harga']; ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                                                    </div>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" onclick="closeModal('modalEdit<?php echo $row['id']; ?>')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                                                        <button type="submit" name="edit_kendaraan" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="modalHapus<?php echo $row['id']; ?>" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
                                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
                                            <div class="flex justify-between items-center p-4 border-b">
                                                <h3 class="text-lg font-bold text-gray-800">Hapus Data Kendaraan</h3>
                                                <button type="button" onclick="closeModal('modalHapus<?php echo $row['id']; ?>')" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
                                            </div>
                                            <div class="p-4">
                                                <form method="POST" class="text-center">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                                    <p class="mb-6 text-gray-600">Apakah Anda yakin ingin menghapus <br><span class="font-bold text-gray-800"><?php echo ($row['merk']); ?></span>?</p>

                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" onclick="closeModal('modalHapus<?php echo $row['id']; ?>')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                                                        <button type="submit" name="hapus_kendaraan" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Ya, Hapus</button>
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
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic">
                                        Data kendaraan masih kosong. Silakan tambah data.
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
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                    <a href="../main/dashboard.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Dashboard
                    </a>
                    <a href="../admin/transaksi.php" class="block px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-green-700 font-medium rounded-lg transition-colors">
                        Transaksi
                    </a>
                    <a href="../admin/kendaraan.php" class="block px-4 py-3 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                        Data Kendaraan
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

    <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-bold text-gray-800">Tambah Kendaraan Baru</h3>
                <button type="button" onclick="closeModal('modalTambah')" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
            </div>
            <form method="POST" class="p-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                    <input type="text" name="merk" placeholder="Masukan merk" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</label>
                    <select name="jenis" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="" disabled selected>-- Pilih Jenis --</option>
                        <option value="motor">Motor</option>
                        <option value="mobil">Mobil</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <input type="text" name="model" placeholder="Masukan model" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" placeholder="Masukan Harga" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" name="tambah_kendaraan" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Simpan Data</button>
                </div>
            </form>
        </div>
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