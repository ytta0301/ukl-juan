<?php
session_start();
include_once '../../components/koneksi.php';

// Validate id_game from URL
if (!isset($_GET['id_game'])) {
    header('Location: ../index.php');
    exit;
}

$id_game = intval($_GET['id_game']); // Safe conversion to integer

// Fetch game info
$query_game = "SELECT * FROM game WHERE id = $id_game";
$result_game = mysqli_query($conn, $query_game);
$game = mysqli_fetch_assoc($result_game);

// Fetch payment options (diamonds)
$query_diamond = "SELECT * FROM pembayaran WHERE id_game = $id_game ORDER BY price ASC";
$result_diamond = mysqli_query($conn, $query_diamond);
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

  <!-- Player ID & Server -->
  <div class="row">
    <div style="flex:1;">
      <label for="playerId">Masukkan Player ID</label>
      <input id="playerId" name="playerId" placeholder="USER ID" type="text"/>
    </div>
    <div style="flex:1;">
      <label for="server">Pilih Server</label>
      <input id="server" name="server" placeholder="Pilih Server" type="text"/>
    </div>
  </div>

  <!-- WhatsApp -->
  <div>
    <label for="whatsapp">Nomor WhatsApp</label>
    <input class="full-width" id="whatsapp" name="whatsapp" placeholder="08xxxxxxxx" type="text"/>
  </div>

  <!-- Top Up Section -->
  <h2>Pilih Nominal Top Up</h2>

  <?php if ($game): ?>
    <div style="margin-bottom: 16px; text-align: center;">
      <img src="../../img/game/<?= htmlspecialchars($game['img']); ?>" alt="<?= htmlspecialchars($game['name']); ?>" width="60" height="60" style="border-radius: 12px;">
      <h3 style="font-size: 16px; margin-top: 8px;"><?= htmlspecialchars($game['name']); ?></h3>
    </div>
  <?php endif; ?>

  <!-- Grid of Top Up Options -->
  <div class="topup-grid" id="topupGrid">
    <?php while ($diamond = mysqli_fetch_assoc($result_diamond)): ?>
      <a href="metode_pembayaran.php?id=<?= $diamond['id']; ?>" class="topup-item">
        <div style="padding: 12px;">
          <img src="../../img/game/<?= trim(htmlspecialchars($diamond['img'])); ?>" alt="<?= htmlspecialchars($diamond['name']); ?>">
          <div class="topup-text"><?= htmlspecialchars($diamond['name']); ?></div>
          <div class="topup-subtext">Rp<?= number_format($diamond['price'], 0, ',', '.'); ?></div>
        </div>
      </a>
    <?php endwhile; ?>
  </div>

  <!-- Confirm Button -->
  <button class="confirm-btn" type="button">Konfirmasi</button>

</div>

<!-- Optional JavaScript for Selection -->
<script>
  const items = document.querySelectorAll('.topup-item');

  items.forEach(item => {
    item.addEventListener('click', () => {
      items.forEach(i => i.classList.remove('selected'));
      item.classList.add('selected');
    });

    item.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        items.forEach(i => i.classList.remove('selected'));
        item.classList.add('selected');
      }
    });
  });
</script>

</body>
</html>