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

$name = $img = "";

// Hapus data
if ($action == 'delete' && $id > 0) {
    $stmt = $conn->prepare("DELETE FROM game WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: adminvsgame.php?status=sukses_hapus");
        exit;
    } else {
        header("Location: adminvsgame.php?status=gagal_hapus");
        exit;
    }
}

// Ambil data untuk edit
if ($action == 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM game WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $img = $row['img'];
    } else {
        $error = "Data tidak ditemukan.";
        $action = '';
    }
}

// Simpan atau Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);

    // Folder upload
    $target_dir = "uploads/";

    // Buat folder uploads jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Upload Foto
    if (isset($_FILES["img"]) && $_FILES["img"]["name"] != "") {
        $nama_file = basename($_FILES["img"]["name"]);
        $new_name = uniqid() . "-" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $nama_file);
        $target_file = $target_dir . $new_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi jenis file
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($imageFileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                echo "Gambar berhasil diunggah ke: " . $target_file . "<br>";
                $img = $target_file; // Simpan path ke database
            } else {
                $error = "Gagal mengunggah file.";
            }
        } else {
            $error = "Hanya file JPG, JPEG, PNG yang diperbolehkan.";
        }
    } else {
        // Jika update dan tidak upload foto baru, gunakan foto lama
        if ($action == 'edit' && !empty($_POST['old_img'])) {
            $img = $_POST['old_img'];
        } elseif ($action != 'edit') {
            $error = "Foto wajib diunggah saat menambah data baru.";
        }
    }

    if (!$error && $name && $img) {
        if ($action == 'edit' && $id > 0) {
            $stmt = $conn->prepare("UPDATE game SET name=?, img=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $img, $id);

            if ($stmt->execute()) {
                $sukses = "Data berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui data.";
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO game(name, img) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $img);

            if ($stmt->execute()) {
                $sukses = "Data berhasil disimpan.";
            } else {
                $error = "Gagal menyimpan data.";
            }
        }

        header("Location: adminvsgame.php?status=" . ($sukses ? "sukses" : "gagal"));
        exit;
    } else {
        if (empty($name)) {
            $error = "Harap lengkapi semua field.";
        } elseif (empty($img)) {
            $error = "Harap unggah foto.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CRUD Game</title>
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
  <h2 class="mb-4"><?= $action == 'edit' ? 'Edit' : 'Tambah' ?> Game</h2>

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
      <label for="name" class="form-label">Nama Game</label>
      <input type="text" class="form-control" name="name" value="<?= $name ?>" required>
    </div>
    <div class="mb-3">
      <label for="img" class="form-label">Gambar</label>
      <input type="file" class="form-control" name="img">
      <?php if ($action == 'edit' && !empty($img)): ?>
        <input type="hidden" name="old_img" value="<?= $img ?>">
        <p><strong>Gambar saat ini:</strong></p>
        <img src="<?= $img ?>" alt="Gambar" class="profile-pic mb-2">
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">
      <?= $action == 'edit' ? 'Update' : 'Simpan' ?>
    </button>
    <?php if ($action == 'edit'): ?>
      <a href="adminvsgame.php" class="btn btn-secondary">Batal</a>
    <?php endif; ?>
  </form>

  <hr>

  <!-- Daftar Game -->
  <h4>Daftar Game</h4>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Gambar</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM game ORDER BY id DESC");
      while ($row = $result->fetch_assoc()):
      ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td>
            <?php if (!empty($row['img'])): ?>
              <img src="<?= $row['img'] ?>" alt="Gambar" class="profile-pic">
            <?php else: ?>
              <span>-</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="adminvsgame.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="adminvsgame.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>