<?php
/**
 * ============================================================================
 * Halaman Teams (teams.php) [20 poin]
 * UAS Desain dan Pemrograman Web
 * 
 * Menampilkan informasi tim yang mengerjakan UAS:
 * - NIM setiap anggota
 * - Nama lengkap
 * - Foto profil
 * - Kontribusi masing-masing anggota
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Layout teams page, card design
 *   Budi Riswandy (3420240006) - Data kontribusi, responsive grid, animasi
 * ============================================================================
 */

$pageTitle = 'Tim UAS';
require_once 'includes/header.php';
?>

    <div class="container">
        <!-- Teams Header -->
        <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
        <div class="teams-header fade-in-up" style="text-align: center; margin-bottom: 3rem;">
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: var(--text-primary);">Tim Pengembang</h1>
            <p style="color: var(--text-accent); font-weight: 600; font-size: 1.1rem; margin-bottom: 0.2rem;">UAS Desain dan Pemrograman Web</p>
            <p style="color: var(--text-secondary); font-size: 0.95rem;">Universitas Islam As-Syafi'iyah</p>
        </div>

        <!-- Teams Grid -->
        <div class="teams-grid">

            <!-- ============================================================
                 Card Anggota 1: Alwin Dwi Kurniawan
                 Kontribusi: Alwin Dwi Kurniawan (3420240019) - Layout card
                 ============================================================ -->
            <div class="team-card fade-in-up stagger-1" id="teamAlwin">
                <img src="assets/images/alwin.jpg" alt="Foto Alwin Dwi Kurniawan" class="team-photo">
                <h2 class="team-name">Alwin Dwi Kurniawan</h2>
                <p class="team-nim">NIM: 3420240019</p>
                
                <p class="team-contrib-title">Kontribusi</p>
                <ul class="team-contrib-list">
                    <li>Merancang struktur database (setup_database.sql)</li>
                    <li>Konfigurasi koneksi database PDO (config/database.php)</li>
                    <li>Design system CSS: tokens, layout, navbar, cards (style.css)</li>
                    <li>Struktur HTML halaman login (index.php)</li>
                    <li>Struktur HTML halaman register (register.php)</li>
                    <li>Layout dashboard, stat cards, welcome section (dashboard.php)</li>
                    <li>Layout profil, header, form structure (profile.php)</li>
                    <li>Layout teams page dan card design (teams.php)</li>
                    <li>Struktur navbar: logo UIA dan navigasi (header.php)</li>
                    <li>Struktur footer dan copyright (footer.php)</li>
                    <li>Mobile menu dan UI interactions (app.js)</li>
                    <li>Form validation client-side (app.js)</li>
                </ul>
            </div>

            <!-- ============================================================
                 Card Anggota 2: Budi Riswandy
                 Kontribusi: Budi Riswandy (3420240006) - Layout card
                 ============================================================ -->
            <div class="team-card fade-in-up stagger-2" id="teamBudi">
                <img src="assets/images/budi.jpg" alt="Foto Budi Riswandy" class="team-photo">
                <h2 class="team-name">Budi Riswandy</h2>
                <p class="team-nim">NIM: 3420240006</p>
                
                <p class="team-contrib-title">Kontribusi</p>
                <ul class="team-contrib-list">
                    <li>Merancang tabel region dan relasi (setup_database.sql)</li>
                    <li>Error handling koneksi database (config/database.php)</li>
                    <li>Form styles, animasi, responsive CSS (style.css)</li>
                    <li>Logic autentikasi, validasi, session (index.php)</li>
                    <li>Logic registrasi, password hashing (register.php)</li>
                    <li>Quick action cards dan data query (dashboard.php)</li>
                    <li>Logic CRUD profil dan cascading dropdown (profile.php)</li>
                    <li>Data kontribusi dan responsive grid (teams.php)</li>
                    <li>Foto profil navbar dan responsive menu (header.php)</li>
                    <li>API endpoint cascading region (api/get_region.php)</li>
                    <li>Cascading dropdown JavaScript logic (app.js)</li>
                    <li>AJAX region loading dan pre-population (app.js)</li>
                </ul>
            </div>

        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
