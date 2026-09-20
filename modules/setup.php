<?php require_once __DIR__.'/../config/config.php';$active='setup';$title='Setup';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div>
    <h1>Setup Minvesta</h1>
    <p class="muted" style="margin:4px 0 0;padding-left:0">Konfigurasi operasional inti untuk menjaga data, transaksi, dan laporan tetap rapi, akurat, dan siap digunakan.</p>
  </div>
  <a class="btn ghost sm" href="../index.php">← Kembali ke Dashboard</a>
</div>

<div class="grid">
  <div class="card">
    <span class="badge sage">Data master</span>
    <h3 style="margin:14px 0 10px">1. Harga Sampah</h3>
    <p class="muted" style="margin:0 0 14px">Atur jenis sampah, satuan, harga beli, dan harga jual agar pencatatan transaksi konsisten.</p>
    <a class="btn ghost sm" href="waste_types.php">Buka Jenis Sampah</a>
  </div>
  <div class="card">
    <span class="badge sage">Perkiraan kas</span>
    <h3 style="margin:14px 0 10px">2. Dompet &amp; Rekening</h3>
    <p class="muted" style="margin:0 0 14px">Kelola rekening kas, bank, dan rekening operasional dengan struktur yang lebih profesional.</p>
    <a class="btn ghost sm" href="wallets.php">Buka Kelola Dompet</a>
  </div>
  <div class="card">
    <span class="badge sage">Kustomer</span>
    <h3 style="margin:14px 0 10px">3. Data Nasabah</h3>
    <p class="muted" style="margin:0 0 14px">Kelola profil nasabah, status, saldo, dan riwayat aktif secara terorganisir.</p>
    <a class="btn ghost sm" href="customers.php">Buka Nasabah</a>
  </div>
  <div class="card">
    <span class="badge sage">Pihak terkait</span>
    <h3 style="margin:14px 0 10px">4. Pengepul / Kontak</h3>
    <p class="muted" style="margin:0 0 14px">Simpan data kontak pengepul agar proses penjualan sampah lebih efisien dan akuntabel.</p>
    <a class="btn ghost sm" href="contacts.php">Buka Kontak</a>
  </div>
</div>
<div class="grid section">
  <div class="card">
    <span class="badge sage">Keuangan</span>
    <h3 style="margin:14px 0 10px">5. Kategori Keuangan</h3>
    <p class="muted" style="margin:0 0 14px">Buat kategori pemasukan dan pengeluaran agar pelaporan keuangan lebih rapi dan standar.</p>
    <a class="btn ghost sm" href="categories.php">Buka Kategori</a>
  </div>
  <div class="card">
    <span class="badge warning">Koreksi</span>
    <h3 style="margin:14px 0 10px">6. Penyesuaian Saldo</h3>
    <p class="muted" style="margin:0 0 14px">Koreksi saldo rekening bila ada selisih fisik, transfer awal, atau kebutuhan audit.</p>
    <a class="btn ghost sm" href="adjustment.php">Buka Penyesuaian Saldo</a>
  </div>
  <div class="card">
    <span class="badge warning">Koreksi</span>
    <h3 style="margin:14px 0 10px">7. Penyesuaian Stok</h3>
    <p class="muted" style="margin:0 0 14px">Atur stok fisik sampah bila terdapat perbedaan hitung atau koreksi operasional.</p>
    <a class="btn ghost sm" href="stock_adjustment.php">Buka Penyesuaian Stok</a>
  </div>
  <div class="card">
    <span class="badge success">Laporan</span>
    <h3 style="margin:14px 0 10px">8. Ringkasan Keuangan</h3>
    <p class="muted" style="margin:0 0 14px">Pantau arus kas, saldo, dan performa operasional tanpa perlu menavigasi menu yang berulang.</p>
    <a class="btn ghost sm" href="report_cashflow.php">Buka Laporan</a>
  </div>
</div>
<?php require __DIR__.'/../includes/footer.php';?>
