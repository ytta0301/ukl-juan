<?php
session_start();

// Cek apakah file koneksi.php tersedia
$koneksi_path = 'koneksi.php';
if (!file_exists($koneksi_path)) {
    die("File koneksi.php tidak ditemukan.");
}

include $koneksi_path;

// Validasi koneksi database
if (!$conn) {
    die("Koneksi ke database gagal.");
}

$sukses = "";
$error = "";
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$username = $email = $password = $foto = "";

// Hapus data
if ($action == 'delete' && $id > 0) {
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: adminvsuser.php?status=sukses_hapus");
        exit;
    } else {
        header("Location: adminvsuser.php?status=gagal_hapus");
        exit;
    }
}

// Ambil data untuk edit
if ($action == 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $username = htmlspecialchars($row['username']);
        $email = htmlspecialchars($row['email']);
        $password = $row['password'];
        $foto = $row['img'];
    } else {
        $error = "Data tidak ditemukan.";
        $action = '';
    }
}

// Simpan atau Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Folder upload
    $target_dir = "uploads/";

    // Buat folder uploads jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Upload Foto
    if (isset($_FILES["foto"]) && $_FILES["foto"]["name"] != "") {
        $nama_file = basename($_FILES["foto"]["name"]);
        $new_name = uniqid() . "-" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $nama_file);
        $target_file = $target_dir . $new_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi jenis file
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($imageFileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = $target_file;
            } else {
                $error = "Gagal mengunggah file.";
            }
        } else {
            $error = "Hanya file JPG, JPEG, PNG yang diperbolehkan.";
        }
    } else {
        // Jika update dan tidak upload foto baru, gunakan foto lama
        if ($action == 'edit' && !empty($_POST['old_foto'])) {
            $foto = $_POST['old_foto'];
        } elseif ($action != 'edit') {
            $error = "Foto wajib diunggah saat menambah pengguna baru.";
        }
    }

    if (!$error && $username && $email && $foto) {
        if ($action == 'edit' && $id > 0) {
            if ($new_password) {
                $stmt = $conn->prepare("UPDATE user SET username=?, email=?, password=?, img=? WHERE id=?");
                $stmt->bind_param("ssssi", $username, $email, $new_password, $foto, $id);
            } else {
                $stmt = $conn->prepare("UPDATE user SET username=?, email=?, img=? WHERE id=?");
                $stmt->bind_param("sssi", $username, $email, $foto, $id);
            }

            if ($stmt->execute()) {
                $sukses = "Data berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui data.";
            }
        } else {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user(username, email, password, img) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $new_password, $foto);

            if ($stmt->execute()) {
                $sukses = "Data berhasil disimpan.";
            } else {
                $error = "Gagal menyimpan data.";
            }
        }

        header("Location: adminvsuser.php?status=" . ($sukses ? "sukses" : "gagal"));
        exit;
    } else {
        if (empty($username) || empty($email)) {
            $error = "Harap lengkapi semua field.";
        } elseif (empty($foto)) {
            $error = "Harap unggah foto profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CRUD user</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; padding-top: 40px; }
    .container { max-width: 700px; }
    .profile-pic {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4"><?= $action == 'edit' ? 'Edit' : 'Tambah' ?> Pengguna</h2>

  <?php 
  if (isset($_GET['status'])) {
      if ($_GET['status'] == 'sukses') {
          echo '<div class="alert alert-success">Data berhasil disimpan/diperbarui.</div>';
      } elseif ($_GET['status'] == 'gagal') {
          echo '<div class="alert alert-danger">Terjadi kesalahan saat menyimpan data.</div>';
      } elseif ($_GET['status'] == 'sukses_hapus') {
          echo '<div class="alert alert-success">Data berhasil dihapus.</div>';
      } elseif ($_GET['status'] == 'gagal_hapus') {
          echo '<div class="alert alert-danger">Gagal menghapus data.</div>';
      }
  }
  
  if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php elseif ($sukses): ?>
    <div class="alert alert-success"><?= $sukses ?></div>
  <?php endif; ?>

  <!-- Form Input -->
  <form action="" method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" name="username" value="<?= $username ?>" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" name="email" value="<?= $email ?>" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">
        Password <?= $action == 'edit' ? '(Kosongkan jika tidak ingin ganti)' : '' ?>
      </label>
      <input type="password" class="form-control" name="password">
    </div>
    <div class="mb-3">
      <label for="foto" class="form-label">Foto Profil</label>
      <input type="file" class="form-control" name="foto">
      <?php if ($action == 'edit' && !empty($foto)): ?>
        <input type="hidden" name="old_foto" value="<?= $foto ?>">
        <p><strong>Foto saat ini:</strong></p>
        <img src="<?= $foto ?>" alt="Profil" class="profile-pic mb-2">
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">
      <?= $action == 'edit' ? 'Update' : 'Simpan' ?>
    </button>
    <?php if ($action == 'edit'): ?>
      <a href="adminvsuser.php" class="btn btn-secondary">Batal</a>
    <?php endif; ?>
  </form>

  <hr>

  <!-- Daftar User -->
  <h4>Daftar Pengguna</h4>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Password (Hash)</th>
        <th>Foto</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM user ORDER BY id DESC");
      while ($row = $result->fetch_assoc()):
      ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><code><?= substr(htmlspecialchars($row['password']), 0, 20) ?>...</code></td>
          <td>
            <?php if (!empty($row['img'])): ?>
              <img src="<?= $row['img'] ?>" alt="Foto" class="profile-pic">
            <?php else: ?>
              <span>-</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="adminvsuser.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="adminvsuser.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>