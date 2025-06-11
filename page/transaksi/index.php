<?php
session_start();
include_once '../../components/koneksi.php';

// Validasi id_game dari URL
if (!isset($_GET['id_game'])) {
    header('Location: ../index.php');
    exit;
}

$id_game = intval($_GET['id_game']); // Aman dari injection

// Ambil info game
$query_game = "SELECT * FROM game WHERE id = $id_game";
$result_game = mysqli_query($conn, $query_game);
$game = mysqli_fetch_assoc($result_game);

// Jika game tidak ditemukan
if (!$game) {
    $_SESSION['error'] = "Game tidak ditemukan.";
    header("Location: ../index.php");
    exit;
}

// Ambil daftar diamond/top-up
$query_diamond = "SELECT * FROM pembayaran WHERE id_game = $id_game ORDER BY price ASC";
$result_diamond = mysqli_query($conn, $query_diamond);

// Ambil daftar metode pembayaran
$query_bank = "SELECT * FROM bank";
$result_bank = mysqli_query($conn, $query_bank);

// Handle submit form
if (isset($_POST['submit'])) {
    $playerId = mysqli_real_escape_string($conn, $_POST['playerId']);
    $bundleId = mysqli_real_escape_string($conn, $_POST['bundleId']);
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['paymentMethod']);

    if (!empty($playerId) && !empty($bundleId) && !empty($paymentMethod)) {
        // Simpan transaksi
        $query = "INSERT INTO transaksi (id_user, id_bundle, payment_method) VALUES ('$playerId', '$bundleId', '$paymentMethod')";
        if (mysqli_query($conn, $query)) {
            $transaksi_id = mysqli_insert_id($conn);
            $_SESSION['success'] = "Silakan lanjutkan ke pembayaran.";
            header("Location: ../../pembayaran/index.php?id_transaksi=" . $transaksi_id . "&id_game=" . $id_game);
            exit;
        } else {
            $_SESSION['error'] = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Harap lengkapi semua field.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Top Up Form</title>
  <link rel="stylesheet" href="../../css/style5.css">
</head>
<body>

<div class="container">
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?id_game=<?= $id_game ?>" method="POST" class="login-email">

    <!-- Player ID -->
    <div class="row">
      <div style="flex:1;">
        <label for="playerId">Masukkan ID game</label>
        <input id="playerId" name="playerId" placeholder="USER ID" type="text" required/>
      </div>
    </div>

    <!-- Top Up Section -->
    <h2>Pilih Nominal Top Up</h2>

    <div style="margin-bottom: 16px; text-align: center;">
      <img src="../../img/game/<?= htmlspecialchars($game['img']); ?>" alt="<?= htmlspecialchars($game['name']); ?>" width="60" height="60" style="border-radius: 12px;">
      <h3 style="font-size: 16px; margin-top: 8px;"><?= htmlspecialchars($game['name']); ?></h3>
    </div>

    <!-- Grid of Top Up Options -->
    <div class="topup-grid" id="topupGrid">
      <?php while ($diamond = mysqli_fetch_assoc($result_diamond)): ?>
        <a class="topup-item" data-id="<?= $diamond['id']; ?>">
          <div style="padding: 12px;">
            <img src="../../img/game/<?= trim(htmlspecialchars($diamond['img'])); ?>" alt="<?= htmlspecialchars($diamond['name']); ?>">
            <div class="topup-text"><?= htmlspecialchars($diamond['name']); ?></div>
            <div class="topup-subtext">Rp<?= number_format($diamond['price'], 0, ',', '.'); ?></div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>

    <!-- Hidden Input to Store Selected Bundle ID -->
    <input type="hidden" name="bundleId" id="bundleId" />

    <!-- Payment Method -->
    <h2>Pilih Metode Pembayaran</h2>
    <div class="payment-methods">
      <?php while ($bank = mysqli_fetch_assoc($result_bank)): ?>
        <label>
          <input type="radio" name="paymentMethod" value="<?= $bank['id'] ?>" required>
          <?= htmlspecialchars($bank['name']) ?>
        </label><br>
      <?php endwhile; ?>
    </div>

    <!-- Confirm Button -->
    <div class="input-group">
      <button type="submit" name="submit" class="btn">Konfirmasi</button>
    </div>

  </form>
</div>

<!-- Optional JavaScript for Selection -->
<script>
  const items = document.querySelectorAll('.topup-item');
  const bundleInput = document.getElementById('bundleId');

  items.forEach(item => {
    item.addEventListener('click', () => {
      items.forEach(i => i.classList.remove('selected'));
      item.classList.add('selected');
      const selectedId = item.getAttribute('data-id');
      bundleInput.value = selectedId; // Set hidden input value
    });
  });
</script>

</body>
</html>