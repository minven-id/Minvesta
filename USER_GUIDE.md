# Minvesta - User Guide Lengkap
## Bank Sampah Management System

---

## 📖 DAFTAR ISI

1. [Pendahuluan](#pendahuluan)
2. [Cara Login](#cara-login)
3. [Dashboard Overview](#dashboard-overview)
4. [Data Utama](#data-utama)
   - [Manajemen Nasabah](#manajemen-nasabah)
   - [Jenis Sampah & Harga](#jenis-sampah--harga)
   - [Pengepul / Kontak](#pengepul--kontak)
   - [Dompet / Rekening](#dompet--rekening)
5. [Operasional](#operasional)
   - [Setoran Sampah](#setoran-sampah)
   - [Penarikan Saldo](#penarikan-saldo)
   - [Penjualan Sampah](#penjualan-sampah)
   - [Transfer Antar Dompet](#transfer-antar-dompet)
   - [Mutasi Nasabah](#mutasi-nasabah)
   - [Catatan Keuangan](#catatan-keuangan)
   - [Pembiayaan / Biaya](#pembiayaan--biaya)
6. [Riwayat Operasional](#riwayat-operasional)
7. [Laporan](#laporan)
8. [Pengaturan Sistem](#pengaturan-sistem)
9. [Fitur Export](#fitur-export)

---

## 🎯 PENDAHULUAN

**Minvesta** adalah sistem manajemen Bank Sampah yang komprehensif untuk mengelola operasional bank sampah secara digital. Sistem ini mencakup:

- ✅ Manajemen nasabah dan saldo tabungan
- ✅ Pencatatan setoran sampah dengan berat dan nilai
- ✅ Penjualan sampah ke pengepul
- ✅ Penarikan saldo nasabah
- ✅ Manajemen dompet/rekening kas
- ✅ Laporan keuangan lengkap
- ✅ Export data ke Excel dan PDF

---

## 🔐 CARA LOGIN

### Langkah 1: Akses Halaman Login
1. Buka browser dan akses URL aplikasi Minvesta
2. Halaman login akan muncul dengan logo Minvesta

### Langkah 2: Masukkan Kredensial
```
Username: [username Anda]
Password: [password Anda]
```

### Langkah 3: Klik Tombol "Masuk"
- Jika kredensial benar, akan diarahkan ke Dashboard
- Jika salah, akan muncul pesan error

> **💡 Tips:** Pastikan Caps Lock tidak aktif saat memasukkan password

---

## 📊 DASHBOARD OVERVIEW

Dashboard memberikan ringkasan operasional bank sampah secara real-time:

### Bagian-Bagian Dashboard:

#### 1. Statistik Operasional Bulan Ini
- **Nasabah Aktif**: Jumlah nasabah yang terdaftar dan aktif
- **Saldo Kas Total**: Total saldo di semua dompet/rekening
- **Setoran Bulan Ini**: Total nilai setoran bulan berjalan
- **Penjualan Bulan Ini**: Total pendapatan dari penjualan
- **Pembiayaan/Biaya**: Total pengeluaran operasional
- **Pencairan Bulan Ini**: Total penarikan nasabah
- **Estimasi Saldo Nasabah**: Total saldo tabungan semua nasabah
- **Berat Terkumpul**: Total berat sampah yang terkumpul
- **Jumlah Dompet**: Jumlah dompet/rekening yang aktif
- **Pergerakan Bersih**: Selisih pemasukan dan pengeluaran
- **Nilai Stok Siap Jual**: Estimasi nilai stok sampah
- **Penjualan Belum Lunas**: Jumlah penjualan yang belum dibayar

#### 2. Dompet & Rekening
- Menampilkan kartu dompet dengan saldo terkini
- Klik dompet untuk melihat detail transaksi

#### 3. Setoran Terbaru
- Tabel 8 setoran terakhir
- Kolom: Tanggal, No. Setoran, Nasabah, Berat, Nilai, Status
- Tombol untuk riwayat lengkap dan setoran baru

#### 4. Komposisi Transaksi
- Chart donut visualisasi setoran vs penjualan vs pencairan
- Legend dengan persentase masing-masing

---

## 📁 DATA UTAMA

### 👥 MANAJEMEN NASABAH

#### Cara Menambah Nasabah Baru:
1. Klik menu **Nasabah** di sidebar
2. Klik tombol **+ Tambah Nasabah**
3. Isi form:
   - **Nama**: Nama lengkap nasabah
   - **No. HP**: Nomor telepon (untuk notifikasi)
   - **Alamat**: Alamat lengkap
   - **Status**: Pilih "Aktif" atau "Non-aktif"
4. Klik **Simpan**

#### Cara Edit Nasabah:
1. Buka menu **Nasabah**
2. Cari nasabah yang ingin diedit
3. Klik tombol **Edit** pada baris nasabah tersebut
4. Ubah data yang diperlukan
5. Klik **Simpan**

#### Cara Hapus Nasabah:
1. Buka menu **Nasabah**
2. Klik tombol **Hapus** pada nasabah yang ingin dihapus
3. Konfirmasi penghapusan

> **⚠️ Perhatian:** Nasabah yang memiliki saldo tidak dapat dihapus

#### View Detail Nasabah:
- Klik nama nasabah untuk melihat:
  - Riwayat setoran
  - Riwayat penarikan
  - Saldo tabungan saat ini

---

### ♻️ JENIS SAMPAH & HARGA

#### Cara Menambah Jenis Sampah:
1. Klik menu **Jenis Sampah & Harga**
2. Klik **+ Tambah Jenis**
3. Isi form:
   - **Nama**: Nama jenis sampah (contoh: Botol Plastik, Kertas, Kaleng)
   - **Satuan**: Satuan berat (kg, ton, dll)
   - **Harga Beli**: Harga beli dari nasabah (Rp/kg)
   - **Harga Jual**: Harga jual ke pengepul (Rp/kg)
   - **Stok**: Stok awal (kg)
4. Klik **Simpan**

#### Cara Update Harga:
1. Buka menu **Jenis Sampah & Harga**
2. Klik **Edit** pada jenis sampah yang ingin diupdate
3. Ubah harga beli/jual
4. Klik **Simpan**

> **💡 Tips:** Selalu update harga secara berkala sesuai pasar

---

### 📞 PENGEpul / KONTAK

#### Cara Menambah Pengepul:
1. Klik menu **Pengepul / Kontak**
2. Klik **+ Tambah Kontak**
3. Isi form:
   - **Nama**: Nama pengepul/perusahaan
   - **No. HP**: Nomor telepon
   - **Alamat**: Alamat
   - **Catatan**: Catatan tambahan
4. Klik **Simpan**

Pengepul digunakan saat input penjualan sampah.

---

### 💳 DOMPET / REKENING

#### Cara Menambah Dompet:
1. Klik menu **Dompet / Rekening**
2. Klik **+ Tambah Dompet**
3. Isi form:
   - **Nama**: Nama dompet (contoh: Kas Utama, Bank BCA)
   - **Tipe**: Tunai atau Bank
   - **Saldo Awal**: Saldo awal dompet
   - **No. Rekening**: (opsional) Nomor rekening
4. Klik **Simpan**

#### Transfer Antar Dompet:
1. Klik menu **Transfer Antar Dompet**
2. Pilih dompet sumber dan tujuan
3. Masukkan jumlah transfer
4. Tambahkan keterangan
5. Klik **Transfer**

---

## 💸 OPERASIONAL

### 📥 SETORAN SAMPAH

#### Cara Input Setoran:
1. Klik menu **Setoran Sampah**
2. Isi form:
   - **Tanggal**: Tanggal setoran (default hari ini)
   - **Nasabah**: Pilih nasabah dari dropdown
   - **Dompet**: Pilih dompet untuk mencatat saldo
   - **Status**: "Proses" atau "Posted"
3. Tambahkan detail sampah:
   - Klik **+ Tambah Item**
   - Pilih jenis sampah
   - Masukkan berat (kg)
   - Sistem otomatis menghitung nilai
4. Total nilai akan dihitung otomatis
5. Klik **Simpan**

#### Status Setoran:
- **Proses**: Setoran masih dalam proses verifikasi
- **Posted**: Setoran sudah final dan saldo nasabah bertambah

> **💡 Tips:** Gunakan status "Proses" untuk verifikasi dulu, lalu ubah ke "Posted" setelah diverifikasi

---

### 💸 PENARIKAN SALDO

#### Cara Input Penarikan:
1. Klik menu **Penarikan Saldo**
2. Isi form:
   - **Tanggal**: Tanggal penarikan
   - **Nasabah**: Pilih nasabah
   - **Dompet**: Pilih dompet sumber dana
   - **Jumlah**: Masukkan jumlah penarikan
   - **Keterangan**: Alasan penarikan
3. Cek saldo nasabah (akan muncul otomatis)
4. Klik **Proses Penarikan**

#### Validasi Penarikan:
- Sistem akan mengecek apakah saldo mencukupi
- Jika saldo tidak cukup, penarikan akan ditolak

---

### 🛒 PENJUALAN SAMPAH

#### Cara Input Penjualan:
1. Klik menu **Penjualan Sampah**
2. Isi form:
   - **Tanggal**: Tanggal penjualan
   - **Pengepul**: Pilih pengepul dari kontak
   - **Dompet**: Pilih dompet untuk menerima pembayaran
   - **Status**: "Lunas" atau "Pending"
3. Tambahkan detail sampah:
   - Klik **+ Tambah Item**
   - Pilih jenis sampah
   - Masukkan berat (kg)
   - Sistem otomatis menghitung nilai berdasarkan harga jual
4. Total akan dihitung otomatis
5. Klik **Simpan**

#### Status Penjualan:
- **Lunas**: Pembayaran sudah diterima penuh
- **Pending**: Penjualan tapi pembayaran belum diterima (piutang)

> **💡 Tips:** Stok sampah akan berkurang otomatis saat penjualan disimpan

---

### 💸 TRANSFER ANTAR DOMPET

#### Cara Transfer:
1. Klik menu **Transfer Antar Dompet**
2. Pilih **Dompet Sumber**
3. Pilih **Dompet Tujuan**
4. Masukkan **Jumlah Transfer**
5. Tambahkan **Keterangan** (opsional)
6. Klik **Transfer**

#### Validasi Transfer:
- Sistem mengecek saldo dompet sumber
- Transfer tidak bisa melebihi saldo yang tersedia

---

### 🔄 MUTASI NASABAH

Mutasi nasabah digunakan untuk menyesuaikan saldo nasabah secara manual.

#### Cara Input Mutasi:
1. Klik menu **Mutasi Nasabah**
2. Pilih **Nasabah**
3. Pilih **Jenis Mutasi**:
   - **Tambah**: Menambah saldo nasabah
   - **Kurang**: Mengurangi saldo nasabah
4. Masukkan **Jumlah**
5. Tambahkan **Keterangan**
6. Klik **Proses**

> **⚠️ Perhatian:** Gunakan fitur ini hanya untuk koreksi, bukan untuk transaksi normal

---

### 📝 CATATAN KEUANGAN

Catatan keuangan untuk mencatat pemasukan/pengeluaran lain di luar operasional bank sampah.

#### Cara Input Catatan:
1. Klik menu **Catatan Keuangan**
2. Pilih **Dompet**
3. Pilih **Tipe**:
   - **Income**: Pemasukan lain
   - **Expense**: Pengeluaran lain
4. Masukkan **Jumlah**
5. Pilih **Kategori** (jika ada)
6. Tambahkan **Deskripsi**
7. Klik **Simpan**

---

### 💰 PEMBIAYAAN / BIAYA

Khusus untuk mencatat biaya operasional.

#### Cara Input Biaya:
1. Klik menu **Pembiayaan / Biaya**
2. Pilih **Dompet** sumber dana
3. Masukkan **Jumlah**
4. Pilih **Kategori**:
   - Transportasi
   - Peralatan
   - Gaji
   - Lainnya
5. Tambahkan **Deskripsi**
6. Pilih **Tanggal**
7. Klik **Simpan**

---

## 📜 RIWAYAT OPERASIONAL

### 📥 RIWAYAT SETORAN

- Menampilkan semua riwayat setoran
- Filter berdasarkan tanggal dan nasabah
- Status: Proses, Posted, Batal
- Bisa edit atau batalkan setoran yang masih "Proses"

### 💸 RIWAYAT PENARIKAN
- Menampilkan semua riwayat penarikan
- Filter berdasarkan tanggal dan nasabah
- Detail: nasabah, jumlah, dompet, tanggal

### 🛒 RIWAYAT PENJUALAN
- Menampilkan semua riwayat penjualan
- Filter berdasarkan tanggal dan pengepul
- Status: Lunas, Pending
- Bisa update status dari Pending ke Lunas

---

## 📈 LAPORAN

Semua laporan memiliki fitur export ke Excel, PDF, dan Print.

### 💰 RINGKASAN KEUANGAN (Arus Kas)

#### Cara Menggunakan:
1. Klik menu **Laporan → Ringkasan Keuangan**
2. Pilih periode tanggal:
   - **Dari**: Tanggal awal
   - **Sampai**: Tanggal akhir
3. Klik **Tampilkan**
4. Data akan muncul berupa:
   - Tabel arus kas (masuk/keluar)
   - Total kas masuk, keluar, surplus/defisit
   - Saldo dompet saat ini

#### Export:
- **📊 Excel**: Export data raw untuk analisis
- **📄 PDF**: Export laporan siap cetak
- **🖨️ Print**: Print langsung dari browser

---

### 👥 LAPORAN NASABAH

Menampilkan daftar seluruh nasabah dengan saldo tabungan.

#### Cara Menggunakan:
1. Klik menu **Laporan → Nasabah**
2. Data akan muncul otomatis
3. Kolom: No, Nama, HP, Status, Saldo
4. Klik tombol export untuk download

---

### 📥 LAPORAN SETORAN

#### Cara Menggunakan:
1. Klik menu **Laporan → Setoran**
2. Pilih periode tanggal
3. Klik **Tampilkan**
4. Dua tabel akan muncul:
   - **Rekap per Nasabah (Top 15)**: Nasabah dengan setoran terbanyak
   - **Rekap per Tanggal**: Ringkasan setoran harian

#### Export Terpisah:
- Excel/PDF untuk tabel nasabah
- Excel/PDF untuk tabel tanggal

---

### 🛒 LAPORAN PENJUALAN

#### Cara Menggunakan:
1. Klik menu **Laporan → Penjualan**
2. Pilih periode tanggal
3. Klik **Tampilkan**
4. Dua tabel akan muncul:
   - **Rekap per Pembeli**: Pengepul dengan pembelian terbanyak
   - **Rekap per Tanggal**: Ringkasan penjualan harian

---

### 📦 LAPORAN STOK

Menampilkan stok sampah siap jual dan estimasi nilai.

#### Cara Menggunakan:
1. Klik menu **Laporan → Stok**
2. Data akan muncul otomatis
3. Kolom: Jenis, Satuan, Stok, Harga Beli, Harga Jual, Estimasi Nilai
4. Total stok dan nilai dihitung otomatis

---

### 💳 LAPORAN DOMPET

Menampilkan saldo, pemasukan, dan pengeluaran per dompet.

#### Cara Menggunakan:
1. Klik menu **Laporan → Dompet**
2. Data akan muncul otomatis
3. Kolom: Dompet, Tipe, Saldo, Pemasukan, Pengeluaran

---

### 📅 LAPORAN HARIAN

Ringkasan aktivitas untuk satu tanggal tertentu.

#### Cara Menggunakan:
1. Klik menu **Laporan → Harian**
2. Pilih tanggal
3. Klik **Tampilkan**
4. Data yang muncul:
   - Statistik: Setoran, Penjualan, Penarikan, Pergerakan Bersih
   - Aktivitas per kategori
   - Piutang penjualan
   - Catatan operasional

---

## ⚙️ PENGATURAN SISTEM

### 🔧 SETUP UTAMA

Untuk konfigurasi awal sistem:
1. Klik menu **Pengaturan Sistem → Setup Utama**
2. Isi data perusahaan:
   - Nama perusahaan
   - Alamat
   - No. Telepon
   - Email
3. Klik **Simpan**

---

### 👤 PROFIL SAYA

Untuk mengubah profil pengguna:
1. Klik menu **Pengaturan Sistem → Profil Saya**
2. Ubah data yang diperlukan:
   - Nama
   - Email
   - Password (opsional)
3. Klik **Simpan**

---

### 👥 MANAJEMEN USER (Admin Only)

Untuk menambah user baru:
1. Klik menu **Pengaturan Sistem → Manajemen User**
2. Klik **+ Tambah User**
3. Isi form:
   - Username
   - Password
   - Nama
   - Role (Admin/Staff)
4. Klik **Simpan**

---

### 📂 KATEGORI KEUANGAN

Untuk mengelola kategori transaksi:
1. Klik menu **Pengaturan Sistem → Kategori Keuangan**
2. Tambah/edit kategori untuk income dan expense
3. Kategori akan muncul saat input catatan keuangan

---

### ⚖️ PENYESUAIAN SALDO

Untuk koreksi saldo dompet:
1. Klik menu **Pengaturan Sistem → Penyesuaian Saldo**
2. Pilih dompet
3. Pilih jenis penyesuaian (Tambah/Kurang)
4. Masukkan jumlah
5. Tambahkan keterangan
6. Klik **Proses**

> **⚠️ Perhatian:** Gunakan hanya untuk koreksi kesalahan sistem

---

### 📦 PENYESUAIAN STOK

Untuk koreksi stok sampah:
1. Klik menu **Pengaturan Sistem → Penyesuaian Stok**
2. Pilih jenis sampah
3. Pilih jenis penyesuaian (Tambah/Kurang)
4. Masukkan jumlah (kg)
5. Tambahkan keterangan
6. Klik **Proses**

---

## 📤 FITUR EXPORT

### Export Excel (.xlsx)
- Digunakan untuk analisis data lebih lanjut
- Format .xlsx kompatibel dengan Microsoft Excel
- Data raw tanpa formatting

### Export PDF (.pdf)
- Digunakan untuk dokumentasi dan cetak
- Branding Minvesta di header
- Table formatting otomatis
- Ukuran font kecil untuk data banyak

### Print
- Print langsung dari browser
- Menggunakan printer default
- Bisa save as PDF dari dialog print

### Cara Export:
1. Buka laporan yang diinginkan
2. Klik tombol export (📊 Excel / 📄 PDF / 🖨️ Print)
3. File akan otomatis didownload
4. Filename format: `laporan-[jenis]-[tanggal].xlsx/pdf`

---

## 💡 TIPS & BEST PRACTICES

### 📝 Pencatatan Harian
- Lakukan pencatatan setoran setiap hari
- Gunakan status "Proses" dulu untuk verifikasi
- Setelah verifikasi, ubah ke "Posted"

### 💰 Manajemen Kas
- Selalu cek saldo dompet sebelum transfer
- Rekon kas fisik dengan sistem secara berkala
- Gunakan penyesuaian saldo hanya jika perlu

### 📦 Manajemen Stok
- Update harga sampah sesuai pasar
- Cek stok fisik vs sistem mingguan
- Gunakan penyesuaian stok untuk koreksi

### 📊 Laporan
- Export laporan bulanan untuk arsip
- Gunakan laporan harian untuk monitoring operasional
- Laporan keuangan untuk analisis profitabilitas

### 🔐 Keamanan
- Ganti password secara berkala
- Batasi akses admin hanya untuk user terpercaya
- Logout setelah selesai menggunakan sistem

---

## 🆘 TROUBLESHOOTING

### Masalah Login
- **Error username/password**: Cek kembali kredensial
- **Halaman blank**: Clear cache browser
- **Session expired**: Login kembali

### Masalah Transaksi
- **Saldo tidak cukup**: Cek saldo dompet/nasabah
- **Data tidak tersimpan**: Cek koneksi internet
- **Error input**: Pastikan semua field wajib diisi

### Masalah Export
- **File tidak terdownload**: Cek browser download settings
- **PDF error**: Pastikan library terload dengan benar
- **Excel kosong**: Refresh halaman dan coba lagi

---

## 📞 SUPPORT

Untuk bantuan lebih lanjut:
- Email: support@minvesta.com
- Telepon: [Nomor kontak admin]
- Dokumentasi online: [URL dokumentasi]

---

## 📝 CHANGELOG

### Versi 1.0 (September 2026)
- ✅ Fitur lengkap manajemen bank sampah
- ✅ Dashboard real-time
- ✅ Export Excel/PDF/Print
- ✅ Responsive design
- ✅ Multi-user dengan role management

---

**© 2026 Minvesta - Bank Sampah Management System**
*Versi Dokumentasi: 1.0*
*Terakhir Update: September 2026*