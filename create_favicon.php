<?php
/**
 * Script untuk membuat logo menjadi bulat dan menyimpannya sebagai favicon.png
 */

$source_path = 'assets/images/logo-uia.png';
$dest_path = 'assets/images/favicon.png';

if (!file_exists($source_path)) {
    die("Error: File sumber ($source_path) tidak ditemukan.");
}

$src = @imagecreatefromstring(file_get_contents($source_path));
if (!$src) {
    die("Error: Tidak dapat membaca format gambar.");
}

$w = imagesx($src);
$h = imagesy($src);

// Buat ukuran persegi berdasarkan sisi terpendek
$size = min($w, $h);
$cx = $w / 2;
$cy = $h / 2;
$r = $size / 2;

$dst = imagecreatetruecolor($size, $size);
imagesavealpha($dst, true);
$transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
imagefill($dst, 0, 0, $transparent);

// Potong menjadi lingkaran (border-radius: 50%)
for ($x = 0; $x < $size; $x++) {
    for ($y = 0; $y < $size; $y++) {
        // Hitung jarak dari pusat
        $dx = $x - ($size / 2);
        $dy = $y - ($size / 2);
        if (($dx * $dx + $dy * $dy) <= ($r * $r)) {
            // Mapping koordinat ke gambar asli
            $sx = round($cx - ($size / 2) + $x);
            $sy = round($cy - ($size / 2) + $y);
            
            // Hindari out of bounds
            if ($sx >= 0 && $sx < $w && $sy >= 0 && $sy < $h) {
                $color = imagecolorat($src, $sx, $sy);
                imagesetpixel($dst, $x, $y, $color);
            }
        }
    }
}

imagepng($dst, $dest_path);
imagedestroy($src);
imagedestroy($dst);

echo "<h3>✅ Favicon berhasil dipotong menjadi bulat sempurna!</h3>";
echo "<img src='$dest_path' width='100' style='border: 1px solid #ccc; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin: 20px 0;'>";
echo "<p>Silakan <a href='index.php'>kembali ke Web Anda</a> lalu tekan <b>Ctrl + F5</b> untuk melihat favicon yang baru di tab browser.</p>";
?>
