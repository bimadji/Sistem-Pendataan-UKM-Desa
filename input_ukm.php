<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$notification = '';
$notificationType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_ukm = $_POST['nama_ukm'];
    $pemilik = $_POST['pemilik'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $user_id = $_SESSION['user_id']; // Associate UKM with current user

    try {
        $stmt = $pdo->prepare("INSERT INTO ukm (nama_ukm, pemilik, alamat, telepon, kategori, deskripsi, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama_ukm, $pemilik, $alamat, $telepon, $kategori, $deskripsi, $user_id]);
        $notification = "Data UKM berhasil disimpan!";
        $notificationType = "success";
        
        // Redirect setelah 2 detik
        header("Refresh: 2; URL=daftar_ukm.php");
    } catch(PDOException $e) {
        $notification = "Error: " . $e->getMessage();
        $notificationType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data UKM - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notification {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            font-weight: 500;
        }
        .success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        .error {
            background-color: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <div class="logo">
                    <i class="fas fa-store fa-2x" style="color: var(--primary-color);"></i>
                    <h1>SIPUDESA</h1>
                </div>
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <ul id="navMenu">
                    <li><a href="index.php"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="input_ukm.php" class="active"><i class="fas fa-plus-circle"></i> Input UKM</a></li>
                    <li><a href="daftar_ukm.php"><i class="fas fa-list"></i> Daftar UKM</a></li>
                    <li><a href="popular_ukm.php"><i class="fas fa-fire"></i> UKM Populer</a></li>
                    <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="user/profile.php"><i class="fas fa-user"></i> Profil</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <main class="container animate-fade">
        <section class="form-section">
            <h2><i class="fas fa-plus-circle"></i> Input Data UKM</h2>
            
            <?php if (!empty($notification)): ?>
                <div class="notification <?php echo $notificationType; ?>">
                    <?php if ($notificationType === 'success'): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle"></i>
                    <?php endif; ?>
                    <?php echo $notification; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_ukm"><i class="fas fa-store"></i> Nama UKM</label>
                        <input type="text" id="nama_ukm" name="nama_ukm" placeholder="Masukkan nama UKM" required>
                    </div>

                    <div class="form-group">
                        <label for="pemilik"><i class="fas fa-user"></i> Nama Pemilik</label>
                        <input type="text" id="pemilik" name="pemilik" placeholder="Masukkan nama pemilik" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                    <textarea id="alamat" name="alamat" placeholder="Masukkan alamat lengkap UKM" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telepon"><i class="fas fa-phone"></i> Nomor Telepon</label>
                        <input type="tel" id="telepon" name="telepon" placeholder="Contoh: 081234567890" required>
                    </div>

                    <div class="form-group">
                        <label for="kategori"><i class="fas fa-tag"></i> Kategori</label>
                        <select id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Kerajinan">Kerajinan</option>
                            <option value="Pertanian">Pertanian</option>
                            <option value="Peternakan">Peternakan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi"><i class="fas fa-info-circle"></i> Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi dan informasi tambahan tentang UKM" rows="4" required></textarea>
                </div>

                <div style="text-align: right;">
                    <a href="daftar_ukm.php" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <i class="fas fa-store"></i>
                        <h3>SIPUDESA</h3>
                    </div>
                    <p>Sistem Informasi Pendataan UKM Desa untuk mendukung pertumbuhan ekonomi lokal.</p>
                </div>
                <div class="footer-links-container">
                    <div class="footer-links-column">
                        <h4>Menu</h4>
                        <a href="index.php">Beranda</a>
                        <a href="input_ukm.php">Input UKM</a>
                        <a href="daftar_ukm.php">Daftar UKM</a>
                        <a href="popular_ukm.php">UKM Populer</a>
                        <a href="tentang.php">Tentang</a>
                    </div>
                    <div class="footer-links-column">
                        <h4>Kontak</h4>
                        <a href="mailto:info@sipudesa.id"><i class="fas fa-envelope"></i> info@sipudesa.id</a>
                        <a href="tel:+6281234567890"><i class="fas fa-phone"></i> +62 812 3456 7890</a>
                        <a href="#"><i class="fas fa-map-marker-alt"></i> Jl. Desa Digital No. 123</a>
                    </div>
                    <div class="footer-links-column">
                        <h4>Sosial Media</h4>
                        <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                        <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                        <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                        <a href="#"><i class="fab fa-youtube"></i> YouTube</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 SIPUDESA - Sistem Informasi Pendataan UKM Desa</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.getElementById('navMenu');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

        mobileToggle.addEventListener('click', function() {
            navMenu.classList.toggle('show');
            mobileMenuOverlay.classList.toggle('show');
            document.body.style.overflow = navMenu.classList.contains('show') ? 'hidden' : 'auto';
        });

        mobileMenuOverlay.addEventListener('click', function() {
            navMenu.classList.remove('show');
            mobileMenuOverlay.classList.remove('show');
            document.body.style.overflow = 'auto';
        });
    </script>
</body>
</html> 