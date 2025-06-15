<?php
session_start();

// Konfigurasi koneksi database
$host = "localhost";
$user = "root"; // Ganti sesuai pengaturan XAMPP/WAMP
$pass = "";     // Sesuaikan password MySQL kamu
$db   = "data_website_top_up";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


// Ambil semua data game dari tabel 'game'
$query = "SELECT * FROM game";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>World Of Games</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #2c3e50;
      color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-right a, .header-right button {
      text-decoration: none;
      color: white;
      margin-left: 15px;
    }

    .btn-register {
      background-color: #3498db;
      border: none;
      padding: 10px 15px;
      color: white;
      cursor: pointer;
      border-radius: 5px;
    }

    .top-banner {
      text-align: center;
      padding: 20px;
      background: #fff;
      border-bottom: 1px solid #ddd;
    }

    .top-banner img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
    }

    .top-banner-text {
      font-size: 24px;
      font-weight: bold;
      margin-top: 10px;
    }

    .section-title {
      text-align: center;
      margin-top: 30px;
      font-size: 20px;
      color: #333;
    }

    .games-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      padding: 20px;
    }

    .game-item {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 10px;
      width: 120px;
      text-align: center;
      padding: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .game-item img {
      width: 64px;
      height: 64px;
      object-fit: cover;
      border-radius: 8px;
    }

    .game-item div {
      margin-top: 10px;
      font-size: 14px;
      color: #333;
    }
  </style>
</head>

<body>

  <!-- Header -->
  <header class="header">
    <div class="header-left">
      <span>SeaGames</span>
    </div>
    <div class="header-right">  
      <?php if (isset($_SESSION['username'])) { ?>
        <a href="page/login/logout.php"><button class="btn-register">Logout</button></a>
        <a href="page/login/profil.php"><img src="" alt="profile" width="28" height="28"></a>
      <?php } else { ?>
        <button class="btn-register"><a href="page/login/login.php" style="color:white; text-decoration:none;">Login</a></button>
      <?php } ?>
    </div>
  </header>

  <!-- Banner -->
  <section class="top-banner">
    <img src="img/game/ytta1.jpg" alt="Top Up Banner" />
    <div class="top-banner-text">
      Top Up Your Games
    </div>
  </section>

  <!-- Daftar Game -->
  <section aria-labelledby="popular-games-title">
    <h2 id="popular-games-title" class="section-title">Popular games</h2>
    <div class="games-grid">

      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): 
          $imageName = $row['img'];
          $imagePath = "img/game/" . $imageName;
          $gameName = $row['name'];
          $gameId = intval($row['id']);
        ?>
          <div class="game-item">
            <a href="page/transaksi/index.php?id_game=<?= $gameId ?>">
              <?php if (!empty($imageName) && file_exists($imagePath)): ?>
                <img src="<?= $imagePath ?>" alt="<?= $gameName ?>" width="64" height="64">
              <?php else: ?>
                <div style="width:64px;height:64px;background:#eee;color:#aaa;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                  No Image
                </div>
              <?php endif; ?>
            </a>
            <div><?= $gameName ?></div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; width:100%;">Tidak ada game tersedia.</p>
      <?php endif; ?>

    </div>
  </section>

</body>
</html>