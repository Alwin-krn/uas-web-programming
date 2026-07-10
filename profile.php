<?php
/**
 * ============================================================================
 * Halaman User Profile (profile.php)
 * UAS Desain dan Pemrograman Web
 * 
 * Fitur utama [50 poin]:
 * - Menampilkan dan mengedit profil pengguna
 * - Field alamat (address)
 * - 4 cascading dropdown kewilayahan di bawah alamat:
 *   1. Provinsi (level 1)
 *   2. Kab/Kota (level 2) - filter berdasarkan provinsi
 *   3. Kecamatan (level 3) - filter berdasarkan kab/kota
 *   4. Kelurahan/Desa (level 4) - filter berdasarkan kecamatan
 * - Data disimpan di tabel user_profiles
 * - Data tersimpan ditampilkan kembali saat halaman dibuka
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Layout profil, header, form structure
 *   Budi Riswandy (3420240006) - Logic CRUD profil, cascading dropdown, save/load data
 * ============================================================================
 */

$pageTitle = 'Profil Saya';
require_once 'includes/header.php';

$pdo = getDBConnection();
$success = '';
$error = '';

// ============================================================================
// Proses simpan profil saat form disubmit
// Kontribusi: Budi Riswandy (3420240006)
// ============================================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $birthDate = $_POST['birth_date'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $address = trim($_POST['address'] ?? '');
        $provinceCode = $_POST['province'] ?? '';
        $cityCode = $_POST['city'] ?? '';
        $districtCode = $_POST['district'] ?? '';
        $villageCode = $_POST['village'] ?? '';
        $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        if (strlen($fullName) < 2 || strlen($fullName) > 50) {
            $error = 'Panjang Nama Lengkap harus antara 2 hingga 50 karakter.';
        } elseif (strlen($phone) < 10 || strlen($phone) > 14) {
            $error = 'Panjang karakter tidak sesuai';
        } elseif (!preg_match('/^[0-9]+$/', $phone)) {
            $error = 'Format Nomor HP tidak valid';
        }

        if (empty($error)) {
            try {
                $pdo->beginTransaction();

            // Handle Photo Upload (Base64 dari Cropper atau fallback ke file asli)
            $photoPath = null;
            
            // 1. Cek apakah ada data base64 hasil crop
            if (!empty($_POST['cropped_photo'])) {
                // Batasi ukuran base64 maksimal ~5MB untuk mencegah DoS
                if (strlen($_POST['cropped_photo']) > 5 * 1024 * 1024 * 1.37) { // 1.37 is base64 overhead
                    throw new Exception('Ukuran foto terlalu besar (maksimal 5MB).');
                }
                $uploadDir = __DIR__ . '/assets/images/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $image_parts = explode(";base64,", $_POST['cropped_photo']);
                if (count($image_parts) == 2) {
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    
                    if (in_array($image_type, ['png', 'jpg', 'jpeg', 'gif'])) {
                        $image_base64 = base64_decode($image_parts[1]);
                        $newFileName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $image_type;
                        $targetFile = $uploadDir . $newFileName;
                        
                        if (file_put_contents($targetFile, $image_base64)) {
                            $photoPath = 'assets/images/uploads/' . $newFileName;
                        }
                    } else {
                        throw new Exception('Format file gambar tidak diizinkan.');
                    }
                }
            } 
            // 2. Fallback: Jika upload langsung tanpa crop (misal JS gagal)
            elseif (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                // Cek ukuran file maksimal 5MB
                if ($_FILES['profile_photo']['size'] > 5 * 1024 * 1024) {
                    throw new Exception('Ukuran foto terlalu besar (maksimal 5MB).');
                }

                $uploadDir = __DIR__ . '/assets/images/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileInfo = pathinfo($_FILES['profile_photo']['name']);
                $ext = strtolower($fileInfo['extension']);
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($ext, $allowed)) {
                    $newFileName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    $targetFile = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
                        $photoPath = 'assets/images/uploads/' . $newFileName;
                    }
                } else {
                    throw new Exception('Format file gambar tidak diizinkan (hanya JPG, PNG, GIF).');
                }
            }

            // Update nama lengkap di tabel users
            $stmtUser = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            $stmtUser->execute([$fullName, $phone, $_SESSION['user_id']]);
            $_SESSION['full_name'] = $fullName;

            // Update profil termasuk data kewilayahan dan foto
            if ($photoPath) {
                $stmtProfile = $pdo->prepare("
                    UPDATE user_profiles 
                    SET address = ?, province_code = ?, city_code = ?, district_code = ?, village_code = ?, profile_photo = ?, gender = ?, birth_date = ?, is_active = ?
                    WHERE user_id = ?
                ");
                $stmtProfile->execute([
                    $address, $provinceCode, $cityCode, $districtCode, $villageCode, $photoPath, $gender, $birthDate, $isActive, $_SESSION['user_id']
                ]);
            } else {
                $stmtProfile = $pdo->prepare("
                    UPDATE user_profiles 
                    SET address = ?, province_code = ?, city_code = ?, district_code = ?, village_code = ?, gender = ?, birth_date = ?, is_active = ?
                    WHERE user_id = ?
                ");
                $stmtProfile->execute([
                    $address, $provinceCode, $cityCode, $districtCode, $villageCode, $gender, $birthDate, $isActive, $_SESSION['user_id']
                ]);
            }

            $pdo->commit();
            $success = 'Profil berhasil disimpan!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage() !== '' && strpos($e->getMessage(), 'SQLSTATE') === false 
                ? $e->getMessage() 
                : 'Gagal menyimpan profil. Silakan coba lagi.';
        }
    }
}

// ============================================================================
// Ambil data profil yang sudah tersimpan untuk ditampilkan di form
// Kontribusi: Budi Riswandy (3420240006)
// ============================================================================
$stmt = $pdo->prepare("
    SELECT u.full_name, u.email, u.username, u.phone,
           p.address, p.province_code, p.city_code, p.district_code, p.village_code, p.profile_photo, p.gender, p.birth_date, p.is_active
    FROM users u 
    LEFT JOIN user_profiles p ON u.id = p.user_id 
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

// Ambil nama wilayah yang tersimpan untuk ditampilkan sebagai info
// Kontribusi: Budi Riswandy (3420240006)
$regionNames = ['province' => '', 'city' => '', 'district' => '', 'village' => ''];

if ($profile['province_code']) {
    $stmtReg = $pdo->prepare("SELECT name FROM tb_region WHERE code = ?");
    
    $stmtReg->execute([$profile['province_code']]);
    $reg = $stmtReg->fetch();
    if ($reg) $regionNames['province'] = $reg['name'];
    
    if ($profile['city_code']) {
        $stmtReg->execute([$profile['city_code']]);
        $reg = $stmtReg->fetch();
        if ($reg) $regionNames['city'] = $reg['name'];
    }
    
    if ($profile['district_code']) {
        $stmtReg->execute([$profile['district_code']]);
        $reg = $stmtReg->fetch();
        if ($reg) $regionNames['district'] = $reg['name'];
    }
    
    if ($profile['village_code']) {
        $stmtReg->execute([$profile['village_code']]);
        $reg = $stmtReg->fetch();
        if ($reg) $regionNames['village'] = $reg['name'];
    }
}

// Tentukan foto profil (gunakan default jika belum ada)
// Kontribusi: Alwin Dwi Kurniawan (3420240019)
$avatarUrl = ($profile['profile_photo']) 
    ? $profile['profile_photo'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($profile['full_name']) . '&background=6366f1&color=fff&size=200';
?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <!-- Modal Crop Foto -->
    <div id="cropModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(5px);">
        <div class="glass-card" style="padding:20px; border-radius:var(--radius-md); max-width:500px; width:90%;">
            <h3 style="margin-bottom:15px; color:var(--text-primary);">Sesuaikan Foto Profil</h3>
            <div style="max-height:60vh; overflow:hidden; background:#111; border-radius:8px; display:flex; justify-content:center;">
                <img id="imageToCrop" style="max-width:100%; max-height:100%; display:block;">
            </div>
            <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline" id="btnCancelCrop">Batal</button>
                <button type="button" class="btn btn-primary" id="btnApplyCrop">Selesai Crop</button>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Pesan Success/Error -->
        <!-- Kontribusi: Budi Riswandy (3420240006) -->
        <?php if ($success): ?>
            <div class="alert alert-success" id="profileSuccess"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error" id="profileError"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="glass-card fade-in-up">
            <!-- Form Profil -->
            <form method="POST" action="profile" id="profileForm" enctype="multipart/form-data">
                
                <!-- Profile Header -->
                <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
                <div class="profile-header" style="margin-bottom: 2rem;">
                    <div class="profile-avatar">
                        <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" id="profileAvatar">
                        <div class="avatar-badge" title="Ubah Foto" style="display:flex; justify-content:center; align-items:center; cursor:pointer;" onclick="document.getElementById('profile_photo').click();">
                            <span style="color:white; font-size:12px;">📷</span>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/png, image/jpeg, image/jpg" style="display:none;">
                            <input type="hidden" name="cropped_photo" id="croppedPhotoInput">
                        </div>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($profile['full_name']); ?></h2>
                        <p>@<?php echo htmlspecialchars($profile['username']); ?> · <?php echo htmlspecialchars($profile['email']); ?></p>
                        <small style="color: var(--text-accent); cursor: pointer;" onclick="document.getElementById('profile_photo').click();">Klik ikon kamera untuk ganti foto</small>
                    </div>
                </div>

                <div class="glass-card-header">
                    <h2>Edit Profil</h2>
                    <p>Perbarui informasi profil dan data kewilayahan Anda</p>
                </div>

                <div class="profile-form-grid">
                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap <span style="color: var(--accent-rose);">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="form-control" 
                               value="<?php echo htmlspecialchars($profile['full_name']); ?>" required>
                    </div>

                    <!-- Email (read-only) -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control" 
                               value="<?php echo htmlspecialchars($profile['email']); ?>" disabled>
                    </div>

                    <!-- No HP -->
                    <div class="form-group">
                        <label for="phone">No HP <span style="color: var(--accent-rose);">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control" 
                               placeholder="Masukkan nomor HP" 
                               value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>" required>
                    </div>
                    
                    <!-- Tanggal Lahir -->
                    <div class="form-group">
                        <label for="birth_date">Tanggal Lahir <span style="color: var(--accent-rose);">*</span></label>
                        <input type="date" id="birth_date" name="birth_date" class="form-control" 
                               value="<?php echo htmlspecialchars($profile['birth_date'] ?? ''); ?>" required>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin <span style="color: var(--accent-rose);">*</span></label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="">Pilih Jenis Kelamin...</option>
                            <option value="L" <?php echo (isset($profile['gender']) && $profile['gender'] === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?php echo (isset($profile['gender']) && $profile['gender'] === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>

                    <!-- Alamat - Field di atas dropdown kewilayahan -->
                    <!-- Kontribusi: Budi Riswandy (3420240006) -->
                    <div class="form-group full-width">
                        <label for="address">Alamat <span style="color: var(--accent-rose);">*</span></label>
                        <textarea id="address" name="address" class="form-control" 
                                  placeholder="Masukkan alamat lengkap Anda" required><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                    </div>

                    <!-- Is Active? -->
                    <div class="form-group full-width">
                        <label>Is Active? <span style="color: var(--accent-rose);">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="is_active" value="1" <?php echo (!isset($profile['is_active']) || $profile['is_active'] == 1) ? 'checked' : ''; ?>> True
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="is_active" value="0" <?php echo (isset($profile['is_active']) && $profile['is_active'] == 0) ? 'checked' : ''; ?>> False
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ================================================================
                     Dropdown Kewilayahan [50 poin]
                     4 dropdown cascading di bawah field alamat
                     Kontribusi: Budi Riswandy (3420240006)
                     ================================================================ -->
                <div class="region-section">
                    <h4>Data Kewilayahan <span style="color: var(--accent-rose); font-size: 0.8em; margin-left: 5px;">* Wajib diisi</span></h4>
                    <div class="region-grid">
                        <!-- Dropdown Provinsi (Level 1) -->
                        <div class="form-group">
                            <label for="province">Provinsi <span style="color: var(--accent-rose);">*</span></label>
                            <select id="province" name="province" class="form-control" required
                                    data-saved="<?php echo htmlspecialchars($profile['province_code'] ?? ''); ?>">
                                <option value="">Pilih Provinsi...</option>
                            </select>
                        </div>

                        <!-- Dropdown Kabupaten/Kota (Level 2) - sesuai provinsi -->
                        <div class="form-group">
                            <label for="city">Kabupaten/Kota <span style="color: var(--accent-rose);">*</span></label>
                            <select id="city" name="city" class="form-control" disabled required
                                    data-saved="<?php echo htmlspecialchars($profile['city_code'] ?? ''); ?>">
                                <option value="">Pilih Kab/Kota...</option>
                            </select>
                        </div>

                        <!-- Dropdown Kecamatan (Level 3) - sesuai kab/kota -->
                        <div class="form-group">
                            <label for="district">Kecamatan <span style="color: var(--accent-rose);">*</span></label>
                            <select id="district" name="district" class="form-control" disabled required
                                    data-saved="<?php echo htmlspecialchars($profile['district_code'] ?? ''); ?>">
                                <option value="">Pilih Kecamatan...</option>
                            </select>
                        </div>

                        <!-- Dropdown Kelurahan/Desa (Level 4) - sesuai kecamatan -->
                        <div class="form-group">
                            <label for="village">Kelurahan/Desa <span style="color: var(--accent-rose);">*</span></label>
                            <select id="village" name="village" class="form-control" disabled required
                                    data-saved="<?php echo htmlspecialchars($profile['village_code'] ?? ''); ?>">
                                <option value="">Pilih Kelurahan/Desa...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary" id="btnSaveProfile">
                        Simpan Profil
                    </button>
                    <a href="dashboard" class="btn btn-outline" id="btnCancel">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>

<!-- Script inisialisasi Cropper.js -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profilePhoto = document.getElementById('profile_photo');
    const cropModal = document.getElementById('cropModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const btnCancelCrop = document.getElementById('btnCancelCrop');
    const btnApplyCrop = document.getElementById('btnApplyCrop');
    const profileAvatar = document.getElementById('profileAvatar');
    const croppedPhotoInput = document.getElementById('croppedPhotoInput');
    
    let cropper = null;

    profilePhoto.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            
            // Pastikan yang dipilih adalah gambar
            if (!file.type.match('image.*')) return;
            
            // Baca file dan tampilkan di modal
            const reader = new FileReader();
            reader.onload = function(event) {
                imageToCrop.src = event.target.result;
                cropModal.style.display = 'flex';
                
                // Inisialisasi cropper
                if (cropper) { cropper.destroy(); }
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1, // Kunci rasio potong ke persegi
                    viewMode: 1,
                    autoCropArea: 0.8,
                    background: false
                });
            };
            reader.readAsDataURL(file);
        }
    });

    btnCancelCrop.addEventListener('click', function() {
        cropModal.style.display = 'none';
        if (cropper) { cropper.destroy(); cropper = null; }
        profilePhoto.value = ''; // Reset file input
    });

    btnApplyCrop.addEventListener('click', function() {
        if (!cropper) return;
        
        // Dapatkan hasil potongan (canvas)
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400
        });
        
        // Konversi canvas ke base64 (format JPEG dengan kualitas 90%)
        const base64Image = canvas.toDataURL('image/jpeg', 0.9);
        
        // Simpan base64 ke hidden input untuk dikirim ke PHP
        croppedPhotoInput.value = base64Image;
        
        // Update gambar di tampilan halaman
        profileAvatar.src = base64Image;
        
        // Sembunyikan modal dan bersihkan cropper
        cropModal.style.display = 'none';
        cropper.destroy();
        cropper = null;
    });
});
</script>
