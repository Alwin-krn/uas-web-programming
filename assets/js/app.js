/**
 * ============================================================================
 * Main JavaScript - UAS Desain dan Pemrograman Web
 * Handles cascading dropdowns, form validation, and UI interactions
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Mobile menu, UI interactions, form validation
 *   Budi Riswandy (3420240006) - Cascading dropdown logic, AJAX region loading, pre-population
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {

    // ========================================================================
    // Profile Dropdown Toggle
    // ========================================================================
    const profileToggle = document.getElementById('profileDropdownToggle');
    const profileMenu = document.getElementById('profileDropdownMenu');

    if (profileToggle && profileMenu) {
        profileToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });

        // Tutup dropdown jika klik di luar area
        document.addEventListener('click', function (e) {
            if (!profileToggle.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
        });
    }

    // ========================================================================
    // Theme Toggle (Dark / Light Mode)
    // ========================================================================
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const iconSun = document.getElementById('iconSun');
    const iconMoon = document.getElementById('iconMoon');

    // Cek localStorage, default ke dark theme
    const currentTheme = localStorage.getItem('theme') || 'dark';
    
    if (currentTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        if(iconSun && iconMoon) {
            iconSun.style.display = 'block';
            iconMoon.style.display = 'none';
        }
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'light') {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'dark');
                iconSun.style.display = 'none';
                iconMoon.style.display = 'block';
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                iconSun.style.display = 'block';
                iconMoon.style.display = 'none';
            }
        });
    }

    // ========================================================================
    // Cascading Region Dropdowns
    // Kontribusi: Budi Riswandy (3420240006)
    // ========================================================================
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    const villageSelect = document.getElementById('village');

    // Hanya jalankan jika di halaman profile (ada dropdown region)
    if (provinceSelect && citySelect && districtSelect && villageSelect) {
        initRegionDropdowns();
    }

    /**
     * Inisialisasi cascading dropdown region
     * Memuat data provinsi dan mengatur event listener untuk setiap dropdown
     * 
     * Kontribusi: Budi Riswandy (3420240006)
     */
    function initRegionDropdowns() {
        // Ambil nilai yang sudah tersimpan dari data attributes
        const savedProvince = provinceSelect.dataset.saved || '';
        const savedCity = citySelect.dataset.saved || '';
        const savedDistrict = districtSelect.dataset.saved || '';
        const savedVillage = villageSelect.dataset.saved || '';

        // Muat data provinsi (level 1) saat halaman dibuka
        loadRegion(1, '', provinceSelect, savedProvince).then(() => {
            // Jika ada provinsi tersimpan, muat kab/kota
            if (savedProvince) {
                return loadRegion(2, savedProvince, citySelect, savedCity);
            }
        }).then(() => {
            // Jika ada kab/kota tersimpan, muat kecamatan
            if (savedCity) {
                return loadRegion(3, savedCity, districtSelect, savedDistrict);
            }
        }).then(() => {
            // Jika ada kecamatan tersimpan, muat kelurahan
            if (savedDistrict) {
                return loadRegion(4, savedDistrict, villageSelect, savedVillage);
            }
        });

        // Event: Saat provinsi berubah → muat kab/kota, kosongkan kecamatan & kelurahan
        provinceSelect.addEventListener('change', function () {
            const code = this.value;
            resetSelect(citySelect, 'Pilih Kab/Kota...');
            resetSelect(districtSelect, 'Pilih Kecamatan...');
            resetSelect(villageSelect, 'Pilih Kelurahan/Desa...');

            if (code) {
                citySelect.disabled = false;
                loadRegion(2, code, citySelect);
            } else {
                citySelect.disabled = true;
                districtSelect.disabled = true;
                villageSelect.disabled = true;
            }
        });

        // Event: Saat kab/kota berubah → muat kecamatan, kosongkan kelurahan
        citySelect.addEventListener('change', function () {
            const code = this.value;
            resetSelect(districtSelect, 'Pilih Kecamatan...');
            resetSelect(villageSelect, 'Pilih Kelurahan/Desa...');

            if (code) {
                districtSelect.disabled = false;
                loadRegion(3, code, districtSelect);
            } else {
                districtSelect.disabled = true;
                villageSelect.disabled = true;
            }
        });

        // Event: Saat kecamatan berubah → muat kelurahan
        districtSelect.addEventListener('change', function () {
            const code = this.value;
            resetSelect(villageSelect, 'Pilih Kelurahan/Desa...');

            if (code) {
                villageSelect.disabled = false;
                loadRegion(4, code, villageSelect);
            } else {
                villageSelect.disabled = true;
            }
        });
    }

    /**
     * Memuat data region dari API menggunakan Fetch
     * 
     * Kontribusi: Budi Riswandy (3420240006)
     * 
     * @param {number} level - Level region (1=Provinsi, 2=Kab/Kota, 3=Kecamatan, 4=Kelurahan)
     * @param {string} parentCode - Kode parent untuk filter
     * @param {HTMLSelectElement} targetSelect - Elemen select yang akan diisi
     * @param {string} savedValue - Nilai yang tersimpan untuk pre-select
     * @returns {Promise}
     */
    function loadRegion(level, parentCode, targetSelect, savedValue = '') {
        // Tampilkan loading state
        targetSelect.disabled = true;
        const loadingOption = targetSelect.querySelector('option');
        if (loadingOption) {
            loadingOption.textContent = 'Memuat data...';
        }
        targetSelect.parentElement.classList.add('select-loading');

        // Build URL untuk API request
        let url = `api/get_region.php?level=${level}`;
        if (parentCode) {
            url += `&parent_code=${parentCode}`;
        }

        return fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Kosongkan select dan tambahkan placeholder
                targetSelect.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = getPlaceholder(level);
                targetSelect.appendChild(placeholder);

                // Isi dengan data region
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.code;
                    option.textContent = item.name;

                    // Pre-select jika sesuai dengan nilai tersimpan
                    if (savedValue && item.code === savedValue) {
                        option.selected = true;
                    }
                    targetSelect.appendChild(option);
                });

                // Enable select
                targetSelect.disabled = false;
                targetSelect.parentElement.classList.remove('select-loading');
            })
            .catch(error => {
                console.error('Error loading region data:', error);
                targetSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                targetSelect.parentElement.classList.remove('select-loading');
            });
    }

    /**
     * Mendapatkan teks placeholder berdasarkan level region
     * 
     * Kontribusi: Budi Riswandy (3420240006)
     * 
     * @param {number} level - Level region
     * @returns {string} Teks placeholder
     */
    function getPlaceholder(level) {
        switch (level) {
            case 1: return 'Pilih Provinsi...';
            case 2: return 'Pilih Kab/Kota...';
            case 3: return 'Pilih Kecamatan...';
            case 4: return 'Pilih Kelurahan/Desa...';
            default: return 'Pilih...';
        }
    }

    /**
     * Reset select ke state awal
     * 
     * Kontribusi: Budi Riswandy (3420240006)
     * 
     * @param {HTMLSelectElement} selectElement - Elemen select yang akan direset
     * @param {string} placeholderText - Teks placeholder
     */
    function resetSelect(selectElement, placeholderText) {
        selectElement.innerHTML = `<option value="">${placeholderText}</option>`;
        selectElement.disabled = true;
    }

    // ========================================================================
    // Form Validation
    // Kontribusi: Alwin Dwi Kurniawan (3420240019)
    // ========================================================================
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            // Validasi dasar: pastikan ada alamat yang diisi
            const address = document.getElementById('address');
            if (address && address.value.trim() === '') {
                // Tidak wajib, tapi beri visual feedback jika kosong
            }
        });
    }

    // ========================================================================
    // Auto-hide alerts setelah 5 detik
    // Kontribusi: Alwin Dwi Kurniawan (3420240019)
    // ========================================================================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ========================================================================
    // Toggle Password Visibility
    // ========================================================================
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    // Switch to eye-off icon
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                } else {
                    input.type = 'password';
                    // Switch to eye icon
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                }
            }
        });
    });

});
