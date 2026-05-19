# 🎫 Manual Book Kasir - Wisata Gunung Sanggabuana
> Panduan Pengoperasian Loket Tiket POS dan Monitoring Pengunjung untuk Kasir Lapangan.
> 
> *Versi 1.0 (Mei 2026)*

---

## 📌 Daftar Isi
1. [🔐 Akses & Login Kasir](#-akses--login-kasir)
2. [📊 Dasbor Kasir & Peta Persebaran](#-dasbor-kasir--peta-persebaran)
3. [🎫 Penjualan Tiket (POS Loket)](#-penjualan-tiket-pos-loket)
   - [A. Formulir Gunung Sanggabuana (Pendakian & Camping)](#a-formulir-gunung-sanggabuana-pendakian--camping)
   - [B. Formulir Destinasi Umum](#b-formulir-destinasi-umum)
   - [C. Cetak E-Ticket & QR Code](#c-cetak-e-ticket--qr-code)
4. [👁️ Monitoring Pengunjung & Check-Out](#%EF%B8%8F-monitoring-pengunjung--check-out)
5. [🏔️ Kelola Destinasi Tugas Saya](#%EF%B8%8F-kelola-destinasi-tugas-saya)

---

## 🔐 Akses & Login Kasir

Setiap Kasir terikat secara langsung dengan destinasi wisata tertentu tempat mereka bertugas. Anda harus melakukan login untuk mengakses sistem POS Loket.

### 🌐 Alamat Login Portal
Buka web browser di perangkat Anda, lalu akses URL berikut:
```http
http://domain-website-anda.com/admin/login
```

### 🔑 Kredensial Akun Kasir Bawaan
* **Email Kasir:** `kasir.[slug-destinasi]@sanggabuana.com`
  * *Contoh Gunung Sanggabuana:* `kasir.gunung-sanggabuana@sanggabuana.com`
  * *Contoh Curug Cigentis:* `kasir.curug-cigentis@sanggabuana.com`
* **Password:** `kasir123`

```
┌──────────────────────────────────────────────┐
┌──────────────────────────────────────────────┐
│                  LOGIN KASIR                 │
├──────────────────────────────────────────────┤
│  Email: kasir.gunung-sanggabuana@...         │
│  Password: ••••••••                          │
│                                              │
│               [ MASUK LOKET ]                │
└──────────────────────────────────────────────┘
```

> ⚠️ **PENTING:** Kredensial di atas merupakan data bawaan (seeder). Jika terdapat perubahan akun, pastikan Anda meminta pembaruan email/password dari Superadmin.

---

## 📊 Dasbor Kasir & Peta Persebaran

Setelah login berhasil, Anda akan masuk ke halaman **Statistik Pengunjung** destinasi tugas Anda.

### 📈 Ringkasan Laporan Harian
Dasbor menyajikan data statistik real-time khusus untuk destinasi tempat Anda bertugas:
* **Total Transaksi & Pendapatan**: Akumulasi omzet penjualan tiket.
* **Metode Pembayaran**: Distribusi pembayaran tunai, QRIS, atau transfer.
* **Kategori Usia & Gender**: Grafik lingkar rasio pengunjung Dewasa/Anak-anak dan Laki-laki/Perempuan.

### 🗺️ Peta Persebaran Asal Pengunjung
Peta interaktif Leaflet di dasbor Anda dirancang khusus untuk memetakan asal wilayah pengunjung:
* **Fokus Otomatis (Auto-Focus)**: Peta akan otomatis memusatkan kamera (*auto-center* dan *zoom-in* 10) tepat pada titik koordinat destinasi tugas Anda.
* **Filter Peta**: Anda dapat memilih untuk melihat persebaran per **Kota di Indonesia** atau per **Negara di Dunia** melalui dropdown di kanan atas peta.
* **Sistem Pencocokan Otomatis**: Jika Anda mendaftarkan negara mancanegara (seperti menulis "Jepang" atau "USA"), peta dunia otomatis menerjemahkan dan mewarnai peta negara tersebut secara presisi.

---

## 🎫 Penjualan Tiket (POS Loket)

Menu **POS Tiket** digunakan saat menerima pendaftaran rombongan atau perorangan yang membeli tiket masuk loket secara langsung di lapangan. Formulir isian akan beradaptasi secara dinamis sesuai lokasi tugas Anda.

### A. Formulir Gunung Sanggabuana (Pendakian & Camping)
Khusus untuk loket Gunung Sanggabuana, formulir memuat parameter keselamatan & logistik pendakian:
1. **Nama Penanggung Jawab**: Nama ketua rombongan.
2. **Alamat Pengunjung**: Pilih Provinsi, Kabupaten, Kecamatan, dan Kelurahan asal ketua rombongan.
3. **Nama Komunitas** *(Opsional)*: Nama klub pendaki atau rombongan.
4. **Tujuan Kunjungan**: Pilih salah satu dari *Hiking & Camping*, *Trail Running*, atau *Wisata Religi/Ziarah*.
5. **Jumlah Anggota Laki-laki & Perempuan** *(Angka)*: Mengatur kalkulasi total rombongan.
6. **Rata-rata Usia**: Menentukan kelompok usia rombongan.
7. **Metode Pembayaran**: Pilih *Tunai*, *QRIS*, atau *Transfer*.
8. **Harga Tiket (Manual Override)**: Harga standar tiket destinasi akan muncul. Namun, Anda dapat mengetikkan harga tiket lain secara manual jika rombongan berhak atas potongan harga (diskon kelompok).

### B. Formulir Destinasi Umum
Untuk destinasi selain pendakian gunung (seperti Curug atau Taman), formulir disederhanakan:
1. **Nama Pengunjung**: Nama penanggung jawab tiket.
2. **Alamat Pengunjung**: Provinsi, Kabupaten/Kota asal.
3. **Jumlah Anggota**: Total tiket yang dibeli.
4. **Metode Pembayaran**: Tunai, QRIS, atau Transfer.
5. **Harga Tiket (Manual Override)**: Dapat disesuaikan secara manual jika ada perubahan harga loket temporer.

### C. Cetak E-Ticket & QR Code
Setelah menekan tombol **Proses & Cetak Tiket**, sistem akan menampilkan pop-up detail **Struk E-Ticket** lengkap dengan QR Code simulasi:
* Cetak struk belanja atau catat nomor tiket jika pengunjung memerlukan bukti fisik.
* QR Code unik diterbitkan untuk proses pemindaian check-in atau check-out manual.

---

## 👁️ Monitoring Pengunjung & Check-Out

Menu **Monitoring** berfungsi untuk memantau status keramaian wisatawan di lokasi wisata Anda secara real-time demi keamanan kawasan.

### 📥 Status Kunjungan
1. **Di Dalam Lokasi** (Status: `in`): Pengunjung yang terdaftar di loket POS dan saat ini masih berada di dalam area wisata.
2. **Selesai** (Status: `out`): Rombongan yang sudah keluar pintu gerbang wisata.

### 📤 Alur Check-Out Pengunjung
* Apabila rombongan pengunjung hendak pulang dan meninggalkan lokasi, Anda harus mencatat kepulangan mereka.
* Cari nama rombongan di bilah pencarian menu monitoring.
* Klik tombol **"Check Out"** berwarna merah di sisi kanan tabel.
* Setelah konfirmasi disetujui, status pengunjung akan berubah menjadi *Selesai* dan sistem otomatis menghitung **Durasi Kunjungan** rombongan tersebut (misal: *3 jam 40 mnt*).

---

## 🏔️ Kelola Destinasi Tugas Saya

Menu **Destinasi Saya** memberi hak akses terbatas bagi Kasir untuk memperbarui detail operasional lokasi wisata yang menjadi tanggung jawabnya secara mandiri tanpa perlu menghubungi Admin pusat.

### ⚙️ Hal-hal yang Dapat Kasir Ubah:
1. **Koordinat Peta**: Mengatur titik garis lintang (latitude) dan garis bujur (longitude) lokasi loket agar marker koordinat titik merah di peta dasbor dan landing page tampil akurat.
2. **Jam & Hari Operasional**: Memperbarui jadwal hari operasional loket dan jam buka-tutup pelayanan.
3. **Detail Informasi Lainnya**: Memperbarui harga dasar tiket, deskripsi operasional, estimasi durasi kunjungan, serta daftar kontak WhatsApp/Sosial Media destinasi tugas secara mandiri.
