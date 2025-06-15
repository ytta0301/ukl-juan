<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Akun</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #999;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .profile-picture img {
            max-width: 200px;
            border-radius: 8px;
        }

        form {
            margin-top: 20px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<h1>Biodata Diri</h1>

<?php
session_start();

// Konfigurasi databas
$host = 'localhost';
$db_user = 'root'; // Sesuaikan jika perlu
$db_pass = '';     // Sesuaikan jika perlu
$db_name = 'data_website_top_up';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$user = null;

// Ambil data user
$sql = "SELECT * FROM user WHERE id = $_SESSION[id]";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<p class='message error'>Data user tidak ditemukan.</p>";
    exit;
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    // Upload gambar
    $target_dir = "uploads/";
    $foto_profil = $user['img']; // default foto lama

    if (!empty($_FILES["foto_profil"]["name"])) {
        $target_file = $target_dir . basename($_FILES["foto_profil"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Cek apakah file benar-benar gambar
        $check = getimagesize($_FILES["foto_profil"]["tmp_name"]);
        if ($check === false) {
            echo "<p class='message error'>File bukan gambar.</p>";
            $uploadOk = 0;
        }

        // Cek ukuran file (max 2MB)
        if ($_FILES["foto_profil"]["size"] > 2000000) {
            echo "<p class='message error'>Ukuran file terlalu besar.</p>";
            $uploadOk = 0;
        }

        // Cek format file
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "<p class='message error'>Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.</p>";
            $uploadOk = 0;
        }

        // Jika lolos validasi, upload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["foto_profil"]["tmp_name"], $target_file)) {
                $foto_profil = basename($_FILES["foto_profil"]["name"]);
            } else {
                echo "<p class='message error'>Gagal mengunggah file.</p>";
            }
        }
    }

    // Update data ke database
    $stmt = $conn->prepare("UPDATE user SET username = ?, email = ?, password = ?, img = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $password, $foto_profil, $id);

    if ($stmt->execute()) {
        echo "<p class='message success'>Data berhasil diperbarui!</p>";
        header("refresh:2"); // Reload setelah 2 detik
    } else {
        echo "<p class='message error'>Gagal memperbarui data: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<div class="container">
    <!-- Kolom biodata -->
    <div>
        <table>
            <tr><th>Nama</th><td><?= htmlspecialchars($user['username']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
            <tr><th>Password</th><td>••••••••</td></tr>
        </table>
        <button onclick="document.getElementById('editForm').style.display='block'" style="margin-top:15px;">Edit Biodata</button>
    </div>

    <!-- Foto Profil -->
    <div class="profile-picture">
        <img src="<?= $user['img'] ? 'uploads/' . htmlspecialchars($user['img']) : 'https://via.placeholder.com/200'  ?>" alt="Foto Profil">
    </div>
</div>

<!-- Form Edit -->
<form id="editForm" style="display:none;" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">

    <label for="name">Nama:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($img['img']) ?>" required>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="password">Password (kosongkan jika tidak ingin mengubah):</label>
    <input type="password" name="password">

    <label for="foto_profil">Foto Profil:</label>
    <input type="file" name="foto_profil">

    <button type="submit">Simpan Perubahan</button>
    <button type="button" onclick="document.getElementById('editForm').style.display='none'">Batal</button>
</form>

</body>
</html>