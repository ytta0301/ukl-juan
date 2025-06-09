  <?php
  session_start();
  include_once '../components/koneksi.php';

  // Validate id_game from URL
  if (!isset($_GET['id_game'])) {
      // header('Location: ../index.php');
      exit;
  }

  $id_game = intval($_GET['id_game']); // Safe conversion to integer

  // Fetch game info
  $query_game = "SELECT * FROM bank";
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
       
      </div>
      <div style="flex:1;">
  
      </div>
    </div>

    <!-- WhatsApp -->
    <div>
      
    </div>

    <!-- Top Up Section -->
    <h2>Pilih metode pembayaran</h2>

    <?php if ($game): ?>
      <div style="margin-bottom: 16px; text-align: center;">
        <img src="../../img/game/<?= htmlspecialchars($game['img']); ?>" alt="<?= htmlspecialchars($game['name']); ?>" width="60" height="60" style="border-radius: 12px;">
        <h3 style="font-size: 16px; margin-top: 8px;"><?= htmlspecialchars($game['name']); ?></h3>
      </div>
    <?php endif; ?>

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

    <!-- Confirm Button -->
    <button class="confirm-btn" type="button" id="confirmBtn">Konfirmasi</button>
 
    
  </div>

  <!-- Optional JavaScript for Selection -->
<script>
  const items = document.querySelectorAll('.topup-item');
  let selectedId = null;

  items.forEach(item => {
    item.addEventListener('click', () => {
      items.forEach(i => i.classList.remove('selected'));
      item.classList.add('selected');
      selectedId = item.getAttribute('data-id'); // Save selected ID
    });

    item.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        items.forEach(i => i.classList.remove('selected'));
        item.classList.add('selected');
        selectedId = item.getAttribute('data-id'); // Save selected ID
      }
    });
  });

  document.getElementById('confirmBtn').addEventListener('click', () => {
    if (selectedId) {
      window.location.href = `pembayaran/index.php?id=${selectedId}`;
    } else {
      alert("Silakan pilih nominal top-up terlebih dahulu.");
    }
  });
</script>

  </body>
  </html>