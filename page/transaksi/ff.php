<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Top Up Form
  </title>
  <link rel="stylesheet" href="style5.css">
 </head>
 <body>
  <div class="container">
   <div class="row">
    <div style="flex:1;">
     <label for="playerId">
      Masukkan Player ID
     </label>
     <input id="playerId" name="playerId" placeholder="USER ID" type="text"/>
    </div>
    <div style="flex:1;">
     <label for="server">
      Pilih Server
     </label>
     <input id="server" name="server" placeholder="Pilih Server" type="text"/>
    </div>
   </div>
   <div>
    <label for="whatsapp">
     Nomor WhatsApp
    </label>
    <input class="full-width" id="whatsapp" name="whatsapp" placeholder="08xxxxxxxx" type="text"/>
   </div>
   <h2>
    Pilih Nominal Top Up
   </h2>
   <div class="topup-grid" id="topupGrid">
    <div class="topup-item" tabindex="0" data-id="60">
     <img alt="Oneiric shard crystal glowing with blue light" height="80" src="https://storage.googleapis.com/a1aa/image/b452c5f9-8090-400f-b995-323b46f3f368.jpg" width="180"/>
     <div class="topup-text">
      60 Oneiric Shard
     </div>
     <div class="topup-subtext">
      Rp 15.000,-
     </div>
    </div>
    <div class="topup-item" tabindex="0" data-id="330">
     <img alt="Oneiric shard crystal glowing with red and blue light" height="80" src="https://storage.googleapis.com/a1aa/image/da02b9aa-be1b-41c4-6bca-1a267188ea81.jpg" width="180"/>
     <div class="topup-text">
      300+30 Oneiric Shard
     </div>
     <div class="topup-subtext">
      Rp 60.000,-
     </div>
    </div>
    <div class="topup-item" tabindex="0" data-id="1090">
     <img alt="Oneiric shard crystal glowing with cyan light" height="80" src="https://storage.googleapis.com/a1aa/image/52a865b4-933f-4a1d-3b64-fbdc538bd9bf.jpg" width="180"/>
     <div class="topup-text">
      980+110 Oneiric Shard
     </div>
     <div class="topup-subtext">
      Rp 185.000,-
     </div>
    </div>
    <div class="topup-item" tabindex="0" data-id="2240">
     <img alt="Oneiric shard crystal glowing with gray light" height="80" src="https://storage.googleapis.com/a1aa/image/70b375c0-2171-453d-cf2a-aeb28666383d.jpg" width="180"/>
     <div class="topup-text">
      1980+260 Oneiric Shard
     </div>
     <div class="topup-subtext">
      Rp 400.000,-
     </div>
    </div>
    <div class="topup-item" tabindex="0" data-id="3880">
     <img alt="Oneiric shard crystal glowing with blue and white light" height="80" src="https://storage.googleapis.com/a1aa/image/54aec24f-0a10-43c2-26be-8455d4420962.jpg" width="180"/>
     <div class="topup-text">
      3280+600 Oneiric Shard
     </div>
     <div class="topup-subtext">
      Rp 620.000,-
     </div>
    </div>
    <div class="topup-item" tabindex="0" data-id="8080">
     <img alt="Oneiric shard crystal glowing with colorful light" height="80" src="https://storage.googleapis.com/a1aa/image/a2cac440-d813-4cdc-3bcc-ca5d96b95e9e.jpg" width="180"/>
     <div class="topup-text">
      6480+1600 Oneiric Shard
     </div>
     <div class="topup-subtext">
      Rp 1.210.000,-
     </div>
    </div>
   </div>
   <div class="payment-section">
    <div class="payment-label">
     Pilih Pembayaran
    </div>
    <div class="radio-group">
     <label class="radio-item">
      <input checked="" name="payment" type="radio" value="qris"/>
      QRIS
      <i class="fas fa-chevron-down arrow-down"></i>
     </label>
     <label class="radio-item">
      <input name="payment" type="radio" value="ewallet"/>
      E-Wallet
      <i class="fas fa-chevron-down arrow-down"></i>
     </label>
    </div>
   </div>
   <button class="confirm-btn" type="button">
    Konfirmasi
   </button>
  </div>
  <script>
   const topupGrid = document.getElementById('topupGrid');
   const items = topupGrid.querySelectorAll('.topup-item');

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