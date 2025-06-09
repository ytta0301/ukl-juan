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

  if (isset($_POST['submit'])) {
    $playerId = mysqli_real_escape_string($conn, $_POST['playerId']);
    // $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);

    // Validasi sederhana
    if (!empty($playerId)) {
        $query = "INSERT INTO transaksi (id_user) VALUES ('$playerId')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Data berhasil disimpan!";
            header("Location: ".$_SERVER['PHP_SELF']."?id_game=".$id_game);
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
   <form action="" method="POST" class="login-email">
        
        <div class="row">
      <div style="flex:1;">
        <label for="playerId">Masukkan ID game</label>
        <input id="playerId" name="playerId" placeholder="USER ID" type="text"/>
      </div>
      <div style="flex:1;">
  
      </div>
    </div>
        <div class="input-group"><button name="submit" class="btn">Register</button></div>
        <!-- <div>
          <label for="whatsapp">Nomor WhatsApp</label>
          <input class="full-width" id="whatsapp" name="whatsapp" placeholder="08xxxxxxxx" type="text"/>
        </div> -->

       
       </form>
    <!-- Player ID & Server -->
    <!-- <div class="row">
      <div style="flex:1;">
        <label for="playerId">Masukkan ID game</label>
        <input id="playerId" name="playerId" placeholder="USER ID" type="text"/>
      </div>
      <div style="flex:1;">
  
      </div>
    </div> -->

    <!-- WhatsApp -->

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
      selectedId = item.getAttribute('data-id');
    });
  });

  document.getElementById('confirmBtn').addEventListener('click', () => {
    if (selectedId) {
      // Bisa juga tambahkan &method=bank di sini kalau perlu
      window.location.href = `../../pembayaran/index.php?id=${selectedId}`;
    } else {
      alert("Silakan pilih nominal top-up terlebih dahulu.");
    }
  });
</script>


  </body>
  </html>