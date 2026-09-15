# BANK SAMPAH - STRUKTUR LENGKAP PHP + MYSQL

Versi ini disusun mengikuti pola layar pada referensi pengguna:

SET UP (5)
1. Setup Utama
2. Perusahaan / Cabang
3. User & Hak Akses
4. Pengaturan Harga Sampah
5. Pengaturan Dompet

MASTER
- Nasabah
- Jenis Sampah
- Pengepul / Kontak
- Kategori Keuangan
- Dompet / Rekening

TAMBAH
- Tambah Nasabah
- Tambah Setoran
- Tambah Jenis Sampah
- Tambah Pengepul

DOMPET
- Dompet 1
- Dompet 2
- Dompet 3
- Dompet 4
- Dompet 5

TRANSAKSI
- Setoran Sampah
- Penarikan Saldo
- Penjualan Sampah
- Pemasukan
- Pengeluaran
- Semua Transaksi
- Transfer Dompet
- Penyesuaian Saldo
- Penyesuaian Stok
- Riwayat Setoran
- Riwayat Penarikan
- Riwayat Penjualan
- Mutasi Nasabah

LAPORAN
- Laporan Keuangan
- Laporan Nasabah
- Laporan Stok
- Laporan Setoran
- Laporan Penjualan
- Laporan Arus Kas

Pemisahan perusahaan memakai company_id pada data operasional.


## Dompet Custom
Menu DOMPET sekarang tidak lagi dibatasi Dompet 1-5.
Admin dapat:
- membuat dompet sebanyak yang diperlukan;
- menentukan nama sendiri (Kas Utama, Kas Operasional, BCA, Mandiri, QRIS, Dana, dll.);
- memilih tipe Cash/Bank/E-Wallet/Kas Kecil/Lainnya;
- menentukan saldo awal;
- mengubah nama dan tipe;
- membuka detail/mutasi;
- menghapus dompet yang belum pernah dipakai transaksi.

Sidebar DOMPET akan otomatis mengikuti daftar dompet milik perusahaan aktif.
