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
$ukm = null;

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: daftar_ukm.php");
    exit;
}

$id = $_GET['id'];

// Fetch UKM data
try {
    $query = "SELECT u.*, p.nama as nama_pemilik, p.telepon as telepon_pemilik, k.nama_kategori as kategori 
              FROM ukm u 
              LEFT JOIN pemilik_ukm p ON u.pemilik_id = p.id 
              LEFT JOIN kategori_ukm k ON u.kategori_id = k.id 
              WHERE u.id = :id";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $ukm = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ukm) {
        header("Location: daftar_ukm.php");
        exit;
    }
    
    // Check if user is authorized to edit this UKM (only admin or owner)
    if ($_SESSION['user_role'] !== 'admin' && $ukm['user_id'] != $_SESSION['user_id']) {
        header("Location: daftar_ukm.php");
        exit;
    }
    
} catch(PDOException $e) {
    $notification = "Error: " . $e->getMessage();
    $notificationType = "error";
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_ukm = $_POST['nama_ukm'];
    $pemilik = $_POST['pemilik'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    try {
        // Update data pemilik_ukm
        $stmt = $pdo->prepare("UPDATE pemilik_ukm SET nama = ?, telepon = ? WHERE id = (SELECT pemilik_id FROM ukm WHERE id = ?)");
        $stmt->execute([$pemilik, $telepon, $id]);
        
        // Update data kategori_ukm
        $stmt = $pdo->prepare("UPDATE kategori_ukm SET nama_kategori = ? WHERE id = (SELECT kategori_id FROM ukm WHERE id = ?)");
        $stmt->execute([$kategori, $id]);
        
        // Update data ukm
        $stmt = $pdo->prepare("UPDATE ukm SET nama_ukm = ?, alamat = ?, deskripsi = ? WHERE id = ?");
        $stmt->execute([$nama_ukm, $alamat, $deskripsi, $id]);
        
        $notification = "Data UKM berhasil diperbarui!";
        $notificationType = "success";
        
        // Reload ukm data after update
        $query = "SELECT u.*, p.nama as nama_pemilik, p.telepon as telepon_pemilik, k.nama_kategori as kategori 
                  FROM ukm u 
                  LEFT JOIN pemilik_ukm p ON u.pemilik_id = p.id 
                  LEFT JOIN kategori_ukm k ON u.kategori_id = k.id 
                  WHERE u.id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $ukm = $stmt->fetch(PDO::FETCH_ASSOC);
        
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
    <title>Edit Data UKM - SIPUDESA</title>
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
                    <li><a href="daftar_ukm.php"><i class="fas fa-list"></i> Daftar UKM</a></li>
                    <li><a href="popular_ukm.php"><i class="fas fa-fire"></i> UKM Populer</a></li>
                    <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="input_ukm.php"><i class="fas fa-plus-circle"></i> Input UKM</a></li>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="user/profile.php"><i class="fas fa-user"></i> Profil</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <main class="container animate-fade">
        <section class="form-section">
            <h2><i class="fas fa-edit"></i> Edit Data UKM</h2>
            
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
                        <input type="text" id="nama_ukm" name="nama_ukm" placeholder="Masukkan nama UKM" value="<?php echo htmlspecialchars($ukm['nama_ukm'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="pemilik"><i class="fas fa-user"></i> Nama Pemilik</label>
                        <input type="text" id="pemilik" name="pemilik" placeholder="Masukkan nama pemilik" value="<?php echo htmlspecialchars($ukm['nama_pemilik'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                    <textarea id="alamat" name="alamat" placeholder="Masukkan alamat lengkap UKM" required><?php echo htmlspecialchars($ukm['alamat'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telepon"><i class="fas fa-phone"></i> Nomor Telepon</label>
                        <input type="tel" id="telepon" name="telepon" placeholder="Contoh: 081234567890" value="<?php echo htmlspecialchars($ukm['telepon_pemilik'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="kategori"><i class="fas fa-tag"></i> Kategori</label>
                        <select id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Makanan" <?php echo ($ukm['kategori'] ?? '') == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                            <option value="Minuman" <?php echo ($ukm['kategori'] ?? '') == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                            <option value="Kerajinan" <?php echo ($ukm['kategori'] ?? '') == 'Kerajinan' ? 'selected' : ''; ?>>Kerajinan</option>
                            <option value="Pertanian" <?php echo ($ukm['kategori'] ?? '') == 'Pertanian' ? 'selected' : ''; ?>>Pertanian</option>
                            <option value="Peternakan" <?php echo ($ukm['kategori'] ?? '') == 'Peternakan' ? 'selected' : ''; ?>>Peternakan</option>
                            <option value="Lainnya" <?php echo ($ukm['kategori'] ?? '') == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi"><i class="fas fa-info-circle"></i> Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi dan informasi tambahan tentang UKM" rows="4"><?php echo htmlspecialchars($ukm['deskripsi'] ?? ''); ?></textarea>
                </div>

                <div style="text-align: right;">
                    <a href="daftar_ukm.php" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
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