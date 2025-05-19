  <?php
  session_start();
  include_once 'components/koneksi.php';
  $query = "SELECT * FROM `game`;";
  $result = $conn->query($query);
   
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World Of Games</title>
    <link rel="stylesheet" href="css/style1.css">
  </head>

  <body>
    <header aria-label="Page header" class="header" role="banner">
      <div class="header-left">
        <i aria-hidden="true" class="fas fa-flag">
        </i>
        <span>
          SeaGames
        </span>
      </div>
      <div class="header-right">
        <?php
        if (isset($_SESSION['username'])) {  ?>
          <button aria-label="Help" class="btn-help" type="button">
            Help
          </button>
          <button aria-label="Calendar" class="icon-btn">
            <i aria-hidden="true" class="fas fa-calendar-alt">
            </i>
          </button>
          <a href="/UKL_TELKOM/page/login/login.php">
            <img alt="User avatar profile picture" class="avatar" height="28" src="https://storage.googleapis.com/a1aa/image/a1ba30a2-9abb-47c1-c182-f913647f5108.jpg" width="28" />
          </a>
        <?php } else { ?>
          <button aria-label="Help" class="btn-register" type="button">
            <a href="page/login/login.php">Login</a>
          </button>
        <?php } ?>
      </div>
    </header>
    <main>
      <section aria-label="Top up your game credits banner" class="top-banner">
        <img src="img/game/ytta1.jpg" alt="Landscape background with mountains and trees in warm brown tones" height="120" width="480" />
        <div class="top-banner-text">
          Top Up Your Games
        </div>
      </section>
      <section aria-labelledby="popular-games-title">
        <h2 class="section-title" id="popular-games-title">
          Popular games
        </h2>
        <div class="games-grid" role="list">

          <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="game-item" role="listitem">
              <a href="page/transaksi/index.php?id_game=<?= $row['id']; ?>">
                <img alt="gambar" height="64" src="img/game/<?= $row['img']; ?>" width="64" />
              </a>
              <div>
                <?= $row['name']; ?>
              </div>
            </div>
          <?php } ?>
      </section>
    </main>
  </body>

  </html>
  </body>

  </html>