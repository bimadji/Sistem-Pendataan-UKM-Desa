<?php 
require_once 'config/database.php';
session_start();

// Get statistics from database
try {
    // Total UKMs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ukm");
    $total_ukm = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total products - check if table exists first
    $total_products = 0;
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Total categories
    $stmt = $pdo->query("SELECT COUNT(DISTINCT kategori) as total FROM ukm");
    $total_categories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch(PDOException $e) {
    // If any errors, use default values
    $total_ukm = 0;
    $total_products = 0;
    $total_categories = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPUDESA - Sistem Informasi Pendataan UKM Desa</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Beranda</a></li>
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
        <section class="hero">
            <div class="hero-content">
                <h2>Sistem Informasi Pendataan UKM Desa</h2>
                <p>Platform digital untuk mendata, mengelola, dan memantau perkembangan Usaha Kecil Menengah (UKM) di desa. Bersama kita dukung pertumbuhan ekonomi lokal dan kemandirian desa.</p>
                <div style="margin-top: 20px;">
                    <a href="daftar_ukm.php" class="btn pulse-animation"><i class="fas fa-search"></i> Lihat Daftar UKM</a>
                    <a href="input_ukm.php" class="btn btn-outline"><i class="fas fa-plus-circle"></i> Tambah UKM Baru</a>
                </div>
            </div>
        </section>

        <section class="stats-preview">
            <div class="stats-container">
                <div class="stat-item">
                    <i class="fas fa-store"></i>
                    <div class="stat-info">
                        <h3><?php echo $total_ukm; ?></h3>
                        <p>Total UKM</p>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-box"></i>
                    <div class="stat-info">
                        <h3><?php echo $total_products; ?></h3>
                        <p>Total Produk</p>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-tags"></i>
                    <div class="stat-info">
                        <h3><?php echo $total_categories; ?></h3>
                        <p>Kategori</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h3>Pendataan UKM</h3>
                <p>Tambahkan data UKM baru ke dalam sistem dengan mudah dan cepat.</p>
                <a href="input_ukm.php" class="btn"><i class="fas fa-arrow-right"></i> Input UKM</a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Pencarian & Filtering</h3>
                <p>Temukan UKM berdasarkan kategori atau kata kunci tertentu.</p>
                <a href="daftar_ukm.php" class="btn"><i class="fas fa-arrow-right"></i> Cari UKM</a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Pantau Perkembangan</h3>
                <p>Dapatkan informasi terkini tentang perkembangan UKM di desa.</p>
                <a href="popular_ukm.php" class="btn"><i class="fas fa-arrow-right"></i> Lihat Data</a>
            </div>
        </section>

        <section class="highlight-section">
            <h2>UKM Unggulan</h2>
            <div class="highlight-container">
                <div class="highlight-card">
                    <div class="highlight-image">
                        <img src="https://source.unsplash.com/random/600x400/?bakery,food" alt="UKM Unggulan">
                    </div>
                    <div class="highlight-content">
                        <span class="highlight-category">Makanan</span>
                        <h3>Bakso Pak Didi</h3>
                        <p>Bakso dengan daging sapi pilihan, berdiri sejak 2010 dan sudah memiliki cabang di beberapa desa tetangga.</p>
                        <a href="daftar_ukm.php" class="btn btn-outline">Lihat Detail</a>
                    </div>
                </div>
                <div class="highlight-card">
                    <div class="highlight-image">
                        <img src="https://source.unsplash.com/random/600x400/?bamboo,craft" alt="UKM Unggulan">
                    </div>
                    <div class="highlight-content">
                        <span class="highlight-category">Kerajinan</span>
                        <h3>Kerajinan Bamboo</h3>
                        <p>Memproduksi berbagai kerajinan dari bambu, seperti tempat tisu, lampu hias, dan perabotan rumah tangga.</p>
                        <a href="daftar_ukm.php" class="btn btn-outline">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="text-center" style="margin-top: 2rem;">
                <a href="daftar_ukm.php" class="btn">Lihat Semua UKM</a>
            </div>
        </section>

        <section class="about-summary">
            <div class="about-section">
                <div class="about-header">
                    <h2>Tentang SIPUDESA</h2>
                    <p>Kenali lebih dekat aplikasi pendataan UKM desa kami</p>
                </div>
                <div class="about-content">
                    <div class="about-text">
                        <h3>Visi Kami</h3>
                        <p>SIPUDESA hadir sebagai solusi digital untuk memajukan ekonomi desa melalui pendataan dan pemantauan UKM yang efektif dan efisien.</p>
                        <p>Dengan pendataan yang akurat, kami bertujuan membantu pemerintah desa dalam mengambil kebijakan untuk mendukung pertumbuhan UKM lokal.</p>
                        <a href="tentang.php" class="btn btn-outline" style="margin-top: 15px;"><i class="fas fa-info-circle"></i> Selengkapnya</a>
                    </div>
                    <div class="about-image">
                        <img src="https://source.unsplash.com/random/600x400/?village,business" alt="UKM Desa">
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content">
                <h2>Daftarkan UKM Anda Sekarang!</h2>
                <p>Tingkatkan visibilitas UKM Anda dan jangkau lebih banyak pelanggan potensial.</p>
                <a href="input_ukm.php" class="btn pulse-animation">Daftar Sekarang</a>
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
                        <a href="#"><i class="fas fa-map-marker-alt"></i> Jl. Pens Poltek No. 1 se Asia Tenggara</a>
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

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add animation to elements when they come into view
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.3
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('section').forEach(section => {
            section.classList.remove('animate-fade');
            observer.observe(section);
        });
    </script>
</body>
</html> 