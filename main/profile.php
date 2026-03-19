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

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $no_hp = $_POST['no_hp'];
    $nik = $_POST['nik'];
    $alamat = $_POST['alamat'];
    $pekerjaan = $_POST['pekerjaan'];
    $penghasilan = $_POST['penghasilan'];

    $update = mysqli_query($conn, "UPDATE pengguna SET 
        no_hp = '$no_hp', 
        nik = '$nik', 
        alamat = '$alamat', 
        pekerjaan = '$pekerjaan', 
        penghasilan = '$penghasilan' 
        WHERE id = '$id_pengguna'");

    if ($update) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil.');</script>";
    }
}

$query = mysqli_query($conn, "SELECT * FROM pengguna where id = '$id_pengguna'" );
$data_user = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-green-50 min-h-screen p-6 font-sans antialiased flex justify-center items-center">

    <div class="bg-white p-8 rounded-2xl shadow-sm w-full max-w-2xl border border-green-100">

        <div class="flex justify-between items-center mb-8 border-b border-green-100 pb-4">
            <div>
                <h2 class="text-2xl font-bold text-green-800">Profil Saya</h2>
                <p class="text-sm text-gray-500">Lengkapi data diri Anda untuk pengajuan kredit</p>
            </div>
            <a href="dashboard.php" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                Kembali
            </a>
        </div>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" value="<?php echo $data_user['username']; ?>" disabled
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 cursor-not-allowed">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                <input type="email" value="<?php echo($data_user['email']); ?>" disabled
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 cursor-not-allowed">
            </div>

            <div>
                <label for="no_hp" class="block mb-2 text-sm font-medium text-gray-700">Nomor HP / WhatsApp</label>
                <input type="text" id="no_hp" name="no_hp" value="<?php echo($data_user['no_hp']); ?>" placeholder="Masukan nomor telepon"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label for="nik" class="block mb-2 text-sm font-medium text-gray-700">NIK (Nomor Induk Kependudukan)</label>
                <input type="text" id="nik" name="nik" value="<?php echo($data_user['nik']); ?>" placeholder="Masukan NIK sesuai KTP" maxlength="16"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label for="pekerjaan" class="block mb-2 text-sm font-medium text-gray-700">Pekerjaan</label>
                <input type="text" id="pekerjaan" name="pekerjaan" value="<?php echo ($data_user['pekerjaan']); ?>" placeholder="Masukan Pekerjaan"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label for="penghasilan" class="block mb-2 text-sm font-medium text-gray-700">Penghasilan Per Bulan (Rp)</label>
                <input type="number" id="penghasilan" name="penghasilan" value="<?php echo ($data_user['penghasilan']); ?>" placeholder="Masukan Gaji"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2">
                <label for="alamat" class="block mb-2 text-sm font-medium text-gray-700">Alamat Lengkap (Sesuai KTP)</label>
                <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"><?php echo ($data_user['alamat']); ?></textarea>
            </div>

            <div class="md:col-span-2 mt-4">
                <button type="submit"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition-colors duration-200">
                    Simpan Profil
                </button>
            </div>

        </form>

    </div>

</body>

</html>