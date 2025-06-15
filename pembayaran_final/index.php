<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    die("Anda belum login. Silakan <a href='Login.php'>login di sini</a>.");
}

$user_id = $_SESSION['id'];
$foto = "";
$nama = "";

// Ambil data pengguna
$query = "SELECT id, Nama, foto, created_at FROM accc WHERE id = $user_id";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Data pengguna tidak ditemukan.");
}

$data = mysqli_fetch_assoc($result);
$foto = $data['foto'];
$nama = $data['Nama'];

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ganti foto
    if (isset($_POST['foto']) && !empty(trim($_POST['foto']))) {
        $foto_baru = mysqli_real_escape_string($koneksi, $_POST['foto']);
        $sql_update_foto = "UPDATE accc SET foto='$foto_baru' WHERE id=$user_id";
        mysqli_query($koneksi, $sql_update_foto);
        $foto = $foto_baru;
    }

    // Ganti nama
    if (isset($_POST['nama']) && !empty(trim($_POST['nama']))) {
        $nama_baru = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $sql_update_nama = "UPDATE accc SET Nama='$nama_baru' WHERE id=$user_id";
        mysqli_query($koneksi, $sql_update_nama);
        $data['Nama'] = $nama_baru; // Update nama di variabel
    }

    // Reload halaman setelah update
    header("Location: Akun.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="Style-akun.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="interactive, colourfull, watery, text _ FoToKu.png" width="75px" height="75px">
            </div>
            <div class="menu">
                <a href="Landing.php">Home</a>
                <a href="Credits.php">Credits</a>
                <a href="Contact.php">Contact</a>
            </div>
            <div class="register">
                <a href="landing.php">Kembali</a>
            </div>
        </nav>
    </header>

    <div class="wrapper">
        <div class="info">
            <h3>Tentang Saya</h3>

            <?php
            $tanggal_join = date("j F Y", strtotime($data['created_at'])); ?>
            <div class="deskripsi">
                Bergabung sejak: <strong><?= $tanggal_join ?></strong>
            </div>

            <div class="edit">
                <h4>Edit Profil</h4>

                <!-- Form Ganti Foto -->
                <label for="foto" class="form-label">Ganti Foto:</label><br>
                <form method="post">
                    <input type="text" name="foto" placeholder="https://example.com/foto.jpg" 
                           value="<?= htmlspecialchars($foto) ?>" autofocus onfocus="this.select()"><br>
                    <button type="submit">Simpan Foto</button>
                </form>

                <br>

                <!-- Form Ganti Nama -->
                <label for="nama" class="form-label">Ganti Nama:</label><br>
                <form method="post">
                    <input type="text" name="nama" placeholder="<?= htmlspecialchars($data['Nama']) ?>"
                           value="<?= htmlspecialchars($data['Nama']) ?>"><br>
                    <button type="submit">Simpan Nama</button>
                </form>
            </div>
        </div>

        <div class="profil">
            <img class="foto" src="<?= htmlspecialchars($foto) ?>">
            <div class="nama"><strong><?= htmlspecialchars($data['Nama']) ?></strong></div>
        </div>
    </div>
</body>
</html>