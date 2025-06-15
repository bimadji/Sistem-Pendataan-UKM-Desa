<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <?php session_start(); ?>
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
                    <li><a href="tentang.php" class="active"><i class="fas fa-info-circle"></i> Tentang</a></li>
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
        <section class="about-section">
            <div class="about-header">
                <h2>Tentang SIPUDESA</h2>
                <p>Sistem Informasi Pendataan UKM Desa</p>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <h3>Visi Kami</h3>
                    <p>Menciptakan ekosistem digital yang memudahkan pendataan dan pengelolaan UKM di desa sebagai landasan pembangunan ekonomi lokal yang berkelanjutan.</p>
                    
                    <h3 style="margin-top: 20px;">Misi Kami</h3>
                    <ul style="list-style-type: disc; padding-left: 20px; margin-bottom: 20px;">
                        <li>Menyediakan platform pendataan UKM yang mudah digunakan oleh semua pihak</li>
                        <li>Membantu pemerintah desa dalam memetakan potensi ekonomi lokal</li>
                        <li>Mendukung peningkatan kapasitas pelaku UKM melalui pendataan yang terstruktur</li>
                        <li>Menjadi jembatan informasi antara UKM dengan potensi pasar yang lebih luas</li>
                    </ul>
                    
                    <h3>Sejarah Pengembangan</h3>
                    <p>SIPUDESA dikembangkan pada tahun 2024 sebagai solusi dari permasalahan pendataan UKM di desa yang masih manual dan tidak terstruktur. Aplikasi ini didesain dengan memperhatikan kebutuhan pemerintah desa dan masyarakat dalam mengelola data UKM secara efektif.</p>
                </div>
                <div class="about-image">
                    <img src="https://source.unsplash.com/random/600x800/?village,business,market" alt="UKM Desa">
                </div>
            </div>
        </section>
        
        <section class="about-section" style="margin-top: 30px;">
            <div class="about-header">
                <h2>Fitur Utama</h2>
                <p>Yang membuat SIPUDESA istimewa</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 30px;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>Pendataan Terstruktur</h3>
                    <p>Sistem database yang terstruktur untuk menyimpan informasi UKM secara detail dan terorganisir.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Pencarian Cepat</h3>
                    <p>Fitur pencarian dan filter berdasarkan berbagai parameter untuk menemukan UKM dengan mudah.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Responsif</h3>
                    <p>Tampilan yang responsif, dapat diakses melalui berbagai perangkat seperti komputer, tablet, dan smartphone.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analisis Data</h3>
                    <p>Visualisasi data UKM untuk memudahkan analisis perkembangan ekonomi desa.</p>
                </div>
            </div>
        </section>
        
        <section class="team-section">
            <div class="about-section">
                <div class="team-header">
                    <h2>Tim Pengembang</h2>
                    <p>Orang-orang hebat di balik SIPUDESA</p>
                </div>
                
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="images/1.png" alt="Bima Adji Kusuma" class="team-img">
                        </div>
                        <div class="member-info">
                            <h3>Bima Adji Kusuma</h3>
                            <p>Mahasiswa 1</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-member">
                        <div class="member-image">
                            <img src="images/22.jpg" alt="Ryan Putra Nurmawan" class="team-img">
                        </div>
                        <div class="member-info">
                            <h3>Ryan Putra Nurmawan</h3>
                            <p>Mahasiswa 2</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-behance"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-member">
                        <div class="member-image">
                            <img src="images/3.png" alt="Insan Latief Saga Mahyana" class="team-img">
                        </div>
                        <div class="member-info">
                            <h3>Insan Latief Saga Mahyana</h3>
                            <p>Mahasiswa 3</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
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
        
        <section class="about-section" style="margin-top: 30px; margin-bottom: 30px;">
            <div class="about-header">
                <h2>Hubungi Kami</h2>
                <p>Punya pertanyaan atau saran? Jangan ragu untuk menghubungi kami</p>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; margin-bottom: 30px;">
                    <div style="flex: 1; min-width: 250px; max-width: 300px;">
                        <div style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 10px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Email</h3>
                        <p>info@sipudesa.id</p>
                    </div>
                    
                    <div style="flex: 1; min-width: 250px; max-width: 300px;">
                        <div style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 10px;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Telepon</h3>
                        <p>+62 123 4567 890</p>
                    </div>
                    
                    <div style="flex: 1; min-width: 250px; max-width: 300px;">
                        <div style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 10px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Alamat</h3>
                        <p>Jl. Desa Digital No. 123, Sukamaju, Indonesia</p>
                    </div>
                </div>
                
                <a href="#" class="btn"><i class="fas fa-paper-plane"></i> Kirim Pesan</a>
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