<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get UKM ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: daftar_ukm.php");
    exit;
}

$id = $_GET['id'];
$notification = '';
$notificationType = '';
$ukm = null;

// Fetch UKM data
try {
    $stmt = $pdo->prepare("SELECT * FROM ukm WHERE id = ?");
    $stmt->execute([$id]);
    $ukm = $stmt->fetch();
    
    if (!$ukm) {
        header("Location: daftar_ukm.php");
        exit;
    }
    
    // Only admin or owner can delete
    if ($_SESSION['user_role'] !== 'admin' && $ukm['user_id'] != $_SESSION['user_id']) {
        header("Location: daftar_ukm.php");
        exit;
    }
} catch(PDOException $e) {
    $notification = "Error: " . $e->getMessage();
    $notificationType = "error";
}

// Process deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM ukm WHERE id = ?");
        $stmt->execute([$id]);
        
        // Redirect with success notification
        header("Location: daftar_ukm.php?deleted=1");
        exit;
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
    <title>Hapus UKM - SIPUDESA</title>
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
        .confirmation-box {
            background-color: rgba(255, 192, 0, 0.1);
            border-left: 4px solid var(--warning-color);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
        }
        .confirmation-box i {
            color: var(--warning-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .confirmation-box h3 {
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        .confirmation-box p {
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        .btn-danger:hover {
            background-color: var(--danger-color-dark);
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
                    <li><a href="daftar_ukm.php" class="active"><i class="fas fa-list"></i> Daftar UKM</a></li>
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
            <h2><i class="fas fa-trash"></i> Hapus Data UKM</h2>
            
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
            
            <div class="confirmation-box">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Konfirmasi Penghapusan</h3>
                <p>Apakah Anda yakin ingin menghapus UKM <strong><?php echo htmlspecialchars($ukm['nama_ukm']); ?></strong> milik <strong><?php echo htmlspecialchars($ukm['pemilik']); ?></strong>?</p>
                <p>Tindakan ini tidak dapat dibatalkan dan semua data terkait UKM ini akan dihapus secara permanen.</p>
                
                <form method="POST" action="">
                    <input type="hidden" name="confirm_delete" value="1">
                    <div class="button-group">
                        <a href="daftar_ukm.php" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus Permanen</button>
                    </div>
                </form>
            </div>
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