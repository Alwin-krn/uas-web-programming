# 🎓 UAS Desain dan Pemrograman Web - UIA

> **Sistem Informasi Wilayah Terpadu** dengan Premium Dark Theme dan Cascading Dropdown 91.000+ data daerah se-Indonesia.

Repositori ini berisi source code proyek Ujian Akhir Semester (UAS) mata kuliah Desain dan Pemrograman Web untuk Universitas Islam As-Syafi'iyah. Proyek ini dibangun menggunakan **PHP Native** dan **Vanilla CSS/JS** tanpa framework eksternal, berfokus pada desain UI/UX yang modern dan optimasi performa.

---

## ✨ Fitur Utama

- **🔐 Sistem Autentikasi Aman:** Fitur Login dan Register menggunakan *session-based authentication* dan sistem *password hashing* (`password_verify`).
- **🎨 Premium UI/UX:** Antarmuka responsif mengusung *Dark Theme* eksklusif, dipadukan dengan efek *Glassmorphism* dan micro-animations yang halus. Dapat menyesuaikan dengan preferensi sistem secara otomatis.
- **📍 Cascading Region Dropdown:** Form dinamis bersarang menggunakan AJAX/Fetch API untuk memuat data wilayah Indonesia mulai dari **Provinsi -> Kabupaten/Kota -> Kecamatan -> Kelurahan/Desa** secara *realtime* (Total > 91.000 data wilayah).
- **📊 Interactive Dashboard:** Menampilkan statistik pengguna, total region, dan status kelengkapan profil secara informatif.
- **👥 Teams Page:** Halaman responsif yang didedikasikan untuk menampilkan kontribusi masing-masing anggota kelompok.

## 💻 Teknologi yang Digunakan

- **Frontend:** HTML5, Vanilla CSS3 (Custom Variables, Flexbox/Grid, Keyframes), Vanilla JavaScript (DOM Manipulation, Fetch API).
- **Backend:** PHP 8+ (Native / Procedural).
- **Database:** MySQL dengan ekstensi **PDO** (menggunakan *Prepared Statements* untuk mencegah SQL Injection).

## 📂 Struktur Direktori

```text
├── api/
│   └── get_region.php          # API endpoint untuk request data wilayah (JSON)
├── assets/
│   ├── css/style.css           # Premium dark theme stylesheet
│   ├── js/app.js               # Logic Cascading Dropdown, UI Interactions & Validation
│   └── images/                 # Aset gambar (Logo & Foto Tim)
├── config/
│   └── database.php            # Konfigurasi koneksi PDO MySQL (Singleton Pattern)
├── includes/
│   ├── header.php              # Komponen Navbar dinamis
│   └── footer.php              # Komponen Footer
├── index.php                   # Halaman Login
├── register.php                # Halaman Pendaftaran Akun
├── dashboard.php               # Halaman Dashboard Utama
├── profile.php                 # Manajemen Profil & Form Cascading Dropdown
├── teams.php                   # Halaman Informasi Tim Pengembang
├── logout.php                  # Logic Session Destroy
├── import_region.php           # Script otomasi import database wilayah raksasa
└── setup_database.sql          # Skema tabel Users dan profil
```

## 🚀 Cara Instalasi & Menjalankan Project

1. **Clone repositori** ini ke lokal komputer Anda:
   ```bash
   git clone https://github.com/username-anda/nama-repo.git
   ```
2. **Pindahkan folder** repositori ke dalam *document root* server lokal Anda (contoh: folder `htdocs` jika Anda menggunakan **XAMPP**).
3. Nyalakan modul **Apache** dan **MySQL** pada XAMPP Control Panel.
4. Buka phpMyAdmin (`http://localhost/phpmyadmin`) dan **buat database baru** dengan nama:
   ```text
   uas_web
   ```
5. **Import** file `setup_database.sql` dan `region.sql` ke dalam database `uas_web` yang baru saja dibuat.
6. Akses aplikasi melalui browser web:
   ```text
   http://localhost/nama-folder-repo/
   ```

## 👥 Tim Pengembang

- **Alwin Dwi Kurniawan** (3420240019) - *UI/UX Design, Database Schema, Layouting, Mobile Interactions.*
- **Budi Riswandy** (3420240006) - *Backend Logic, Auth System, Database Queries, API & AJAX Region Integration.*

---
*Dibuat untuk memenuhi tugas Ujian Akhir Semester - Universitas Islam As-Syafi'iyah.*
