# 🗺️ Manual Book Admin - Wisata Gunung Sanggabuana
> Panduan Pengelolaan Portal Informasi Wisata Gunung Sanggabuana untuk Administrator.
> 
> *Versi 1.0 (Mei 2026)*

---

## 📌 Daftar Isi
1. [🔐 Akses & Autentikasi](#-akses--autentikasi)
2. [📊 Dasbor Utama (Dashboard)](#-dasbor-utama-dashboard)
3. [🏔️ Pengelolaan Destinasi Wisata](#%EF%B8%8F-pengelolaan-destinasi-wisata)
4. [📸 Pengelolaan Galeri Foto](#-pengelolaan-galeri-foto)
5. [💬 Pengelolaan Testimoni & Ulasan](#-pengelolaan-testimoni--ulasan)
6. [📄 Pengelolaan Halaman Informasi](#-pengelolaan-halaman-informasi)
7. [✉️ Manajemen Pesan Masuk (Kontak)](#%EF%B8%8F-manajemen-pesan-masuk-kontak)
8. [⚙️ Pengaturan Global (Settings)](#%EF%B8%8F-pengaturan-global-settings)
   - [A. Pengaturan Umum & Media Sosial](#a-pengaturan-umum--media-sosial)
   - [B. Visual Hero Banner & Video Profil](#b-visual-hero-banner--video-profil)
   - [C. Kondisi Jalur & Siaga Cuaca (Penting!)](#c-kondisi-jalur--siaga-cuaca-penting)
9. [👥 Role-Based Access Control (RBAC) & Manajemen Kasir](#-role-based-access-control-rbac--manajemen-kasir)

---

## 🔐 Akses & Autentikasi

Untuk mengelola seluruh konten dan pengaturan website Wisata Gunung Sanggabuana, Administrator harus masuk terlebih dahulu melalui panel login khusus.

### 🌐 Alamat Login Admin
Akses URL berikut pada browser Anda:
```http
http://domain-website-anda.com/admin/login
```
*(Ganti `domain-website-anda.com` dengan alamat domain server lokal/produksi Anda).*

### 🔑 Kredensial Default
> **PENTING:** Masukkan kredensial berikut untuk masuk ke dashboard admin:
> - **Email:** `admin@sanggabuana.com`
> - **Password:** `admin123`

```
┌──────────────────────────────────────────────┐
│                  LOGIN ADMIN                 │
├──────────────────────────────────────────────┤
│  Email: admin@sanggabuana.com                │
│  Password: ••••••••                          │
│                                              │
│               [ MASUK ]                      │
└──────────────────────────────────────────────┘
```

> ⚠️ **PERINGATAN:** Demi keamanan sistem, pastikan password diganti secara berkala apabila sistem ini telah diintegrasikan dengan modul kelola user di masa mendatang.

---

## 📊 Dasbor Utama (Dashboard)

Setelah berhasil masuk, Anda akan diarahkan ke halaman **Dasbor Utama**. Dasbor ini dirancang untuk memberikan gambaran cepat status website dalam satu layar.

### 📈 Kartu Statistik Cepat
Di bagian paling atas, Anda akan melihat 5 kartu ringkasan status real-time:
1. **Destinasi**: Total destinasi alam yang terdaftar dalam sistem.
2. **Galeri Foto**: Jumlah koleksi foto yang diunggah.
3. **Pesan Masuk**: Jumlah pesan yang dikirim oleh pengunjung melalui formulir kontak.
4. **Testimoni**: Jumlah ulasan dari pengunjung yang masuk ke database.
5. **Status Web**: Indikator apakah website berstatus online beserta tautan cepat untuk melihat tampilan frontend website secara langsung.

### 🔔 Notifikasi & Aktivitas Terbaru
Dasbor juga menampilkan ringkasan data terbaru agar Anda tidak melewatkan informasi:
- **Pesan Terbaru**: Menampilkan daftar pesan terbaru yang belum dibaca dari pengunjung. Pesan yang belum dibaca akan memiliki tanda titik/label khusus di sampingnya.
- **Destinasi Terbaru**: Menampilkan daftar lokasi wisata yang baru saja diperbarui atau dibuat beserta status publikasinya (Aktif / Draft).

---

## 🏔️ Pengelolaan Destinasi Wisata

Menu **Destinasi** berfungsi untuk menambah, memperbarui, atau menghapus objek-objek wisata alam di kawasan Gunung Sanggabuana (contoh: Puncak Sanggabuana, Curug Cigentis, Hutan Pinus, dll).

### 📝 Parameter Form Destinasi
Saat mengelola destinasi, berikut kolom-kolom informasi yang dapat Anda isi:

| Nama Field | Tipe Input | Deskripsi / Contoh |
| :--- | :--- | :--- |
| **Nama Destinasi** | Teks | Nama lokasi wisata, misal: *Curug Cigentis* (Slug akan terisi otomatis). |
| **Deskripsi Pendek** | Teks Area | Ringkasan singkat daya tarik (maksimal 500 karakter). |
| **Deskripsi Lengkap** | Rich Text / HTML | Artikel penjelasan lengkap mengenai keunikan lokasi dan rute perjalanan. |
| **Foto Utama** | File Gambar | Unggah foto pemandangan terbaik (Format JPG/PNG/WebP, Max 5MB). |
| **Lokasi Fisik** | Teks | Nama wilayah/alamat ringkas, misal: *Karawang, Jawa Barat*. |
| **Ketinggian (Altitude)**| Teks | Ketinggian lokasi, misal: *800 mdpl* atau *1.291 mdpl*. |
| **Hari Operasional** | Teks | Hari kunjungan, misal: *Senin - Minggu* atau *Sabtu - Minggu*. |
| **Jam Operasional** | Teks | Rentang waktu buka, misal: *08:00 - 17:00* atau *24 Jam*. |
| **Harga Tiket** | Angka | Tarif masuk dalam rupiah tanpa simbol, misal: *15000*. |
| **Durasi Perjalanan** | Teks | Estimasi waktu tempuh, misal: *2-3 jam trekking*. |
| **Garis Lintang (Latitude)** | Angka Desimal | Koordinat lintang destinasi untuk pemetaan peta interaktif (contoh: `-6.7275`). |
| **Garis Bujur (Longitude)** | Angka Desimal | Koordinat bujur destinasi untuk pemetaan peta interaktif (contoh: `107.0394`). |
| **Urutan Tampil** | Angka | Urutan penyusunan di halaman depan (misal: urutan `1` tampil pertama). |
| **Status Aktif** | Switch/Checkbox | Aktifkan agar destinasi langsung tampil di website. Matikan untuk menyimpan sebagai draft. |
| **Rekomendasi Utama**| Switch/Checkbox | Jika dicentang, destinasi akan masuk ke bagian "Destinasi Unggulan" di halaman utama. |
| **Kontak Pengelola** | Dinamis (Platform & Value) | Menambahkan jalur komunikasi langsung, seperti WhatsApp atau Instagram per destinasi. |

---

## 📸 Pengelolaan Galeri Foto

Galeri Foto digunakan untuk memperkaya konten visual website. Foto-foto di sini akan ditampilkan di halaman utama dan dapat dihubungkan langsung dengan destinasi wisata tertentu.

### ➕ Langkah Menambah Galeri Baru:
1. Klik menu **Galeri** di sidebar kiri.
2. Tekan tombol **Tambah Galeri**.
3. Lengkapi formulir berikut:
   - **Destinasi Terkait**: Pilih salah satu destinasi yang sesuai dari menu drop-down agar foto muncul pada detail destinasi tersebut (bisa dikosongkan untuk galeri umum).
   - **Judul Foto**: Nama/judul dari foto yang diunggah.
   - **Unggah Gambar**: Pilih file foto berkualitas tinggi (Max 5MB).
   - **Keterangan (Caption)**: Penjelasan singkat tentang foto (maksimal 500 karakter).
   - **Urutan Tampil**: Atur prioritas penampilan foto.
   - **Status Aktif**: Centang agar foto langsung dipublikasikan.
4. Klik **Simpan**.

---

## 💬 Pengelolaan Testimoni & Ulasan

Ulasan dari pengunjung sangat penting untuk membangun kepercayaan publik. Anda memiliki kontrol penuh atas ulasan mana yang layak dipublikasikan ke halaman beranda.

### ⚙️ Fitur Utama Testimoni:
* **Tambah Testimoni Manual**: Admin dapat menuliskan langsung feedback dari tokoh penting atau perwakilan pengunjung (Isi nama, profesi/role, isi ulasan, rating 1-5, dan unggah foto profil/avatar).
* **Toggle Status Aktif (Instant-Switch)**: Pada baris data testimoni, klik tombol toggle hijau/abu-abu untuk mengaktifkan atau menonaktifkan tampilan ulasan di halaman utama secara instan.
* **Auto-Read Status**: Setiap kali Anda membuka menu Testimoni, semua testimoni baru yang dikirim oleh pengunjung otomatis ditandai sebagai "Sudah Dibaca" untuk merapikan dasbor notifikasi Anda.

---

## 📄 Pengelolaan Halaman Informasi

Selain destinasi wisata, website juga memerlukan halaman statis berisi informasi penting seperti **Tentang Kami**, **Syarat & Ketentuan**, serta **Panduan Keselamatan**.

### 🛠️ Fitur Halaman Statis:
1. **Editor Teks Kaya (WYSIWYG)**: Tulis konten informasi secara rapi menggunakan paragraf, daftar list (`ul`/`ol`), tabel, atau cetak tebal.
2. **SEO Friendly**: Dilengkapi kolom **Meta Description** untuk mengoptimalkan kata kunci halaman agar lebih mudah dicari oleh Google dan mesin pencari lainnya.
3. **URL Slug Otomatis**: Sistem secara otomatis mengubah judul halaman menjadi URL yang ramah SEO (Contoh: judul *"Syarat & Ketentuan"* akan otomatis diakses di `/halaman/syarat-ketentuan`).

---

## ✉️ Manajemen Pesan Masuk (Kontak)

Pengunjung website dapat mengirimkan pertanyaan, keluhan, atau kerja sama melalui formulir kontak di frontend.

### 📥 Alur Kerja Penanganan Pesan:
1. **Notifikasi Masuk**: Jika ada pesan baru yang belum dibaca, ikon email di dasbor akan menyala merah terang.
2. **Membaca Detail**: Klik pesan pada daftar pesan untuk membuka isinya secara lengkap. Sistem akan otomatis menandai pesan tersebut sebagai "Sudah Dibaca".
3. **Penghapusan**: Bersihkan kotak masuk Anda dari pesan spam dengan menekan tombol **Hapus** setelah pesan selesai diproses/dibalas.

---

## ⚙️ Pengaturan Global (Settings)

Menu **Pengaturan** adalah pusat kendali utama website. Di sini Anda dapat mengubah seluruh teks statis, gambar, video, hingga status operasional website secara fleksibel.

### A. Pengaturan Umum & Media Sosial
* **Nama & Tagline Situs**: Menentukan teks header, tab title di browser, dan sub-judul identitas branding website.
* **Email & Telepon**: Kontak resmi pengelola yang akan terpampang di footer dan halaman kontak.
* **Akun WhatsApp**: Masukkan nomor WhatsApp resmi tanpa simbol `+` atau spasi, cukup format angka lengkap (contoh: `6281234567890`) untuk fitur tombol chat langsung di frontend.
* **Media Sosial**: Tautan lengkap ke akun Instagram, Facebook, dan YouTube resmi destinasi.

### B. Visual Hero Banner & Video Profil
* **Hero Background & Floating Image**: Ganti gambar latar belakang besar di halaman beranda serta gambar mengambang di samping teks sambutan. Cukup unggah file gambar baru berukuran proporsional.
* **Profil Video**:
  - **Tipe Sumber**: Anda bisa memilih tipe sumber berupa **Link** atau **Upload File Video**.
  - **Link Video**: Jika menggunakan tipe Link, Anda dapat memasukkan link YouTube resmi atau tautan direct-video lainnya.
  - **Unggah File**: Jika menggunakan tipe file video, Anda dapat mengunggah file video mp4 lokal secara langsung ke server melalui panel ini.
* **Daftar Fitur Tentang Kami**: Susunan kartu keunggulan di bagian "Tentang Kami". Anda dapat menambahkan item baru (Isi Judul & Deskripsi Singkat, misal: *Aman & Nyaman: Jalur terkelola baik*), merubah isinya, atau menghapusnya secara fleksinibel.

---

### C. Kondisi Jalur & Siaga Cuaca (Penting!)

> **PENTING:** Fitur ini didesain khusus untuk mengamankan pengunjung Gunung Sanggabuana terkait kondisi cuaca ekstrem di area pegunungan dan kelayakan jalur trekking.

Terdapat 2 Mode Operasional Status Jalur yang dapat dipilih:

#### 1. Mode Otomatis (`Auto`)
* Sistem akan memantau kondisi cuaca secara otomatis menggunakan integrasi **Weather API** berdasarkan titik koordinat **Latitude** dan **Longitude** yang Anda tetapkan pada pengaturan cuaca.
* Widget cuaca di halaman depan akan menampilkan informasi suhu, tingkat kelembapan, dan prakiraan cuaca secara real-time.

#### 2. Mode Manual / Darurat (`Manual`)
> ⚠️ **PERINGATAN:** Gunakan mode ini apabila kondisi di lapangan sangat berbahaya (seperti badai, tanah longsor, kabut tebal, atau penutupan jalur berkala).

* **Aktifkan Mode Manual**: Ubah dropdown "Mode Status Cuaca" menjadi **Manual**.
* **Status Darurat**: Tulis pesan peringatan yang ringkas dan jelas pada kolom **Status Manual / Darurat** (misal: *Jalur Ditutup Sementara*).
* **Deskripsi Detail**: Tulis penyebab atau instruksi penanganan pada kolom **Deskripsi Kondisi Manual** (misal: *Ditutup sementara dari tanggal 18-20 Mei untuk pemulihan ekosistem hutan dan pembersihan jalur pendakian akibat badai*).
* **Ikon Siaga**: Pilih tipe ikon peringatan yang sesuai (seperti ikon peringatan segitiga / `alert-triangle`) agar menonjol di halaman utama frontend.

---

> 💡 *Tips: Setiap kali selesai melakukan perubahan pada menu Pengaturan, jangan lupa menekan tombol **"Simpan Pengaturan"** di bagian bawah halaman untuk menerapkan perubahan ke seluruh sistem.*

---

## 👥 Role-Based Access Control (RBAC) & Manajemen Kasir

Sistem pariwisata Gunung Sanggabuana kini dilengkapi dengan fitur keamanan **Role-Based Access Control (RBAC)** yang membagi wewenang admin ke dalam 3 tingkatan peran (role) yang berbeda demi menjaga integritas data masing-masing destinasi wisata.

### 🎭 Tingkatan Peran & Wewenang

| Peran (Role) | Hak Akses Utama | Batasan |
| :--- | :--- | :--- |
| **Superadmin** | Seluruh fitur sistem + Kelola Pengguna & Peran (RBAC) | Tidak ada batasan akses. |
| **Admin** | Mengelola seluruh Konten Wisata, Interaksi, Halaman, dan Pengaturan | Tidak dapat mengelola user/peran baru (Menu Kelola Pengguna disembunyikan). |
| **Kasir** | Mengakses loket POS tiket, monitoring pengunjung aktif (in/out) & mengedit destinasi tugasnya | Terbatas pada menu Data Statistik, POS Tiket, Monitoring Pengunjung, dan Destinasi Saya. |

---

### 🔑 Kredensial Default Peran Baru
Untuk pengujian awal, sistem secara otomatis telah men-seed pengguna default berikut di database:

1. **Super Administrator (Superadmin)**
   - **Email:** `superadmin@sanggabuana.com`
   - **Password:** `superadmin123`

2. **Administrator (Admin)**
   - **Email:** `admin@sanggabuana.com`
   - **Password:** `admin123`

3. **Kasir Destinasi (Kasir)**
   - **Email:** `kasir.[slug-destinasi]@sanggabuana.com` 
   - *Contoh untuk Gunung Sanggabuana:* `kasir.gunung-sanggabuana@sanggabuana.com`
   - *Contoh untuk Curug Cigentis:* `kasir.curug-cigentis@sanggabuana.com`
   - **Password:** `kasir123`
   - **Tugas:** Terikat langsung ke objek wisata terkait.

---

### 📈 1. Menu Data Statistik (Dashboard Utama)
Dashboard utama kini diubah total menjadi halaman **Data Statistik Pengunjung** yang kaya data analitis dan filterable.
* **Fitur Filter Waktu:** Seluruh data statistik dapat difilter secara fleksibel per Hari Ini (harian), Bulan Ini (bulanan), dan Tahun Ini (tahunan).
* **Filter Destinasi Khusus Admin:** Khusus untuk peran **Superadmin** dan **Admin**, terdapat filter tambahan berupa dropdown **Destinasi Wisata** untuk menganalisis data per lokasi wisata spesifik atau secara agregat (semua destinasi).
* **Komponen Visual Premium:**
  - **Peta Persebaran Pengunjung (Leaflet Map):**
    - Peta interaktif yang otomatis beradaptasi. Jika Admin menyaring data ke destinasi tertentu atau jika pengguna masuk sebagai Kasir, peta akan otomatis berfokus (*auto-center* dan *zoom-in* level 10) pada koordinat lokasi destinasi tersebut.
    - **Filter Wilayah (Peta):** Pengguna dapat menukar tampilan visual peta menggunakan dropdown filter wilayah:
      * **🇮🇩 Peta Indonesia (Kota):** Mewarnai wilayah berdasarkan intensitas kunjungan per Kabupaten/Kota asal pengunjung di Indonesia.
      * **🌎 Peta Dunia (Negara):** Mewarnai wilayah berdasarkan negara asal pengunjung di seluruh dunia secara merata (termasuk Indonesia secara utuh).
      * **Auto-Translation Bahasa:** Sistem secara cerdas memetakan nama negara yang diinput kasir dalam bahasa Indonesia (seperti "Jepang", "Jerman", "Singapura", "Belanda") atau bentuk singkatan ("USA", "US") ke standar data peta dunia resmi agar wilayah terwarnai dengan tepat.
  - **Rasio Gender:** Diagram kemajuan perbandingan jumlah pengunjung Laki-laki vs Perempuan.
  - **Kategori Usia:** Grafik lingkar perbandingan jumlah pengunjung Dewasa vs Anak-anak.
  - **Tujuan Kunjungan (Khusus Sanggabuana):** Grafik distribusi tujuan (Hiking & Camping, Trail Running, Wisata Religi).
  - **Metode Pembayaran:** Ringkasan efektivitas loket digital (Tunai, QRIS, Transfer).
  - **Grafik Tren Pengunjung:** Visualisasi tren harian/bulanan dan omzet pendapatan tiket.

---

### 🎫 2. Menu POS Tiket (Adaptive Input Form)
Menu **POS Tiket** dirancang khusus untuk memproses pendaftaran pengunjung di lapangan dengan formulir dinamis yang beradaptasi secara cerdas berdasarkan destinasi tugas aktif kasir:

#### A. Formulir Khusus Gunung Sanggabuana:
Untuk menjamin keselamatan pendakian dan pencatatan logistik, formulir akan memuat kolom-kolom detail berikut:
1. **Nama Penanggung Jawab**
2. **Alamat Pengunjung**
3. **Nama Komunitas** (Opsional)
4. **Tujuan Kunjungan** (Hiking & Camping, Trail Running, Wisata Religi/Ziarah)
5. **Jumlah Anggota Laki-laki** (Angka)
6. **Jumlah Anggota Perempuan** (Angka)
7. **Rata-rata Usia Pengunjung** (Menentukan rata-rata kelompok)
8. **Total Rombongan:** Otomatis menghitung `Laki-laki + Perempuan + 1 (Penanggung Jawab)` secara real-time.
9. **Metode Pembayaran:** Tunai, QRIS, atau Transfer Bank.
10. **Harga Per Tiket (HTM Manual Override):** Defaultnya adalah harga tiket destinasi, namun **dapat diedit secara manual** jika ada diskon kelompok atau kebijakan khusus loket.

#### B. Formulir Destinasi Lain:
Formulir disederhanakan dengan isian standar:
1. **Nama Pengunjung**
2. **Alamat Pengunjung**
3. **Jumlah Anggota** (Angka)
4. **Rata-rata Usia Pengunjung**
5. **Metode Pembayaran**
6. **Harga Per Tiket (HTM Manual Override)**

*Setelah menekan tombol **"Proses & Cetak Tiket"**, sistem akan menerbitkan E-Ticket resmi lengkap dengan QR Code simulasi dan rekap log transaksi masuk.*

---

### 👁️ 3. Menu Monitoring Pengunjung
Menu **Monitoring Pengunjung** adalah pintu gerbang keluar masuk wisatawan untuk menjamin keamanan di dalam lokasi wisata.
* **Status Pengunjung:**
  - `Di Dalam Lokasi` (Status: `in`): Pengunjung yang baru check-in dan saat ini masih berada di kawasan wisata.
  - `Selesai` (Status: `out`): Pengunjung yang sudah keluar. Sistem otomatis menghitung dan mencatat **Durasi Kunjungan** mereka secara rinci (misal: *2 jam 15 mnt*).
* **Gerbang Check-Out:** Kasir cukup mengeklik tombol **"Check Out"** berwarna merah yang memicu konfirmasi SweetAlert. Seketika status pengunjung akan berubah menjadi selesai dan jam keluar tercatat presisi.
* **Pencarian Cepat:** Dilengkapi filter status (in/out) serta bilah pencarian instan berdasarkan Nama, Nomor Tiket, atau Nama Komunitas.

