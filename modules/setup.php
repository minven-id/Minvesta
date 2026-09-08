<?php require_once __DIR__.'/../config/config.php';$active='setup';$title='Setup';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Setup Bank Sampah</h1><p class="muted" style="margin:4px 0 0;padding-left:0">Panduan setup bertahap: mulai dari data sampah, dompet, kategori keuangan, hingga penyesuaian saldo dan stok awal.</p></div>
  <a class="btn ghost sm" href="../index.php">← Kembali ke Dashboard</a>
</div>
<p class="page-subtitle" style="display:none"></p>

<div class="grid">
  <div class="card">
    <h3>1. Harga Sampah</h3>
    <p class="muted" style="margin:8px 0 14px">Daftar jenis sampah beserta harga beli (dari nasabah) dan harga jual (ke pengepul).</p>
    <a class="btn ghost sm" href="waste_types.php">Buka Jenis Sampah</a>
  </div>
  <div class="card">
    <h3>2. Dompet &amp; Rekening</h3>
    <p class="muted" style="margin:8px 0 14px">Kas tunai, rekening bank, dan rekening operasional lainnya.</p>
    <a class="btn ghost sm" href="wallets.php">Buka Kelola Dompet</a>
  </div>
  <div class="card">
    <h3>3. Data Nasabah</h3>
    <p class="muted" style="margin:8px 0 14px">Tambah dan kelola data nasabah Bank Sampah.</p>
    <a class="btn ghost sm" href="customers.php">Buka Nasabah</a>
  </div>
  <div class="card">
    <h3>4. Pengepul / Kontak</h3>
    <p class="muted" style="margin:8px 0 14px">Daftar pengepul dan kontak untuk penjualan sampah.</p>
    <a class="btn ghost sm" href="contacts.php">Buka Kontak</a>
  </div>
</div>
<div class="grid section">
  <div class="card">
    <h3>5. Kategori Keuangan</h3>
    <p class="muted" style="margin:8px 0 14px">Kategori pemasukan dan pengeluaran untuk pelaporan keuangan yang rapi.</p>
    <a class="btn ghost sm" href="categories.php">Buka Kategori</a>
  </div>
  <div class="card">
    <h3>6. Penyesuaian Saldo</h3>
    <p class="muted" style="margin:8px 0 14px">Koreksi saldo dompet karena selisih fisik atau transfer awal.</p>
    <a class="btn ghost sm" href="adjustment.php">Buka Penyesuaian Saldo</a>
  </div>
  <div class="card">
    <h3>7. Penyesuaian Stok</h3>
    <p class="muted" style="margin:8px 0 14px">Koreksi stok fisik jenis sampah bila ada perbedaan hitungan.</p>
    <a class="btn ghost sm" href="stock_adjustment.php">Buka Penyesuaian Stok</a>
  </div>
  <div class="card">
    <h3>8. Ringkasan Keuangan</h3>
    <p class="muted" style="margin:8px 0 14px">Lihat laporan pemasukan, pengeluaran, dan selisih periode tertentu.</p>
    <a class="btn ghost sm" href="report_cashflow.php">Buka Laporan</a>
  </div>
</div>
<?php require __DIR__.'/../includes/footer.php';?>
