<?php
require_once 'config/database.php';
session_start();

// Query untuk mengambil UKM populer berdasarkan jumlah produk
$query = "SELECT * FROM v_ukm_produk ORDER BY jumlah_produk DESC";
$stmt = $pdo->query($query);
$popular_ukms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get badge class
function getCategoryBadgeClass($category) {
    switch ($category) {
        case 'Makanan':
            return 'badge-primary';
        case 'Minuman':
            return 'badge-info';
        case 'Kerajinan':
            return 'badge-success';
        case 'Pertanian':
            return 'badge-warning';
        case 'Peternakan':
            return 'badge-danger';
        default:
            return 'badge-secondary';
    }
}

// Function to get image for UKM
function getUkmImage($category, $id) {
    $categoryImages = [
        'Makanan' => [
            'https://source.unsplash.com/random/800x500/?food,local',
            'https://source.unsplash.com/random/800x500/?bakery,traditional',
            'https://source.unsplash.com/random/800x500/?restaurant,food',
            'https://source.unsplash.com/random/800x500/?cuisine,local'
        ],
        'Minuman' => [
            'https://source.unsplash.com/random/800x500/?drinks,beverage',
            'https://source.unsplash.com/random/800x500/?coffee,tea',
            'https://source.unsplash.com/random/800x500/?juice,traditional',
            'https://source.unsplash.com/random/800x500/?drink,local'
        ],
        'Kerajinan' => [
            'https://source.unsplash.com/random/800x500/?craft,handicraft',
            'https://source.unsplash.com/random/800x500/?artisan,crafting',
            'https://source.unsplash.com/random/800x500/?handmade,craft',
            'https://source.unsplash.com/random/800x500/?traditional,craft'
        ],
        'Pertanian' => [
            'https://source.unsplash.com/random/800x500/?farm,agriculture',
            'https://source.unsplash.com/random/800x500/?farming,crops',
            'https://source.unsplash.com/random/800x500/?rural,farming',
            'https://source.unsplash.com/random/800x500/?fields,agriculture'
        ],
        'Peternakan' => [
            'https://source.unsplash.com/random/800x500/?livestock,farm',
            'https://source.unsplash.com/random/800x500/?cattle,farm',
            'https://source.unsplash.com/random/800x500/?poultry,rural',
            'https://source.unsplash.com/random/800x500/?animals,farm'
        ],
        'Lainnya' => [
            'https://source.unsplash.com/random/800x500/?business,local',
            'https://source.unsplash.com/random/800x500/?shop,small',
            'https://source.unsplash.com/random/800x500/?market,local',
            'https://source.unsplash.com/random/800x500/?village,business'
        ],
    ];
    
    $index = $id % 4;
    $category = isset($categoryImages[$category]) ? $category : 'Lainnya';
    
    return $categoryImages[$category][$index];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UKM Populer - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .popular-section {
            margin: 2rem 0;
        }
        .popular-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        .popular-header h2 {
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        .popular-header p {
            color: var(--text-color);
            max-width: 700px;
            margin: 0 auto;
        }
        .popular-ukm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        .ukm-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .ukm-card:hover {
            transform: translateY(-5px);
        }
        .ukm-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        .ukm-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .ukm-card:hover .ukm-image img {
            transform: scale(1.05);
        }
        .visits-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(33, 37, 41, 0.8);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .visits-badge i {
            margin-right: 0.3rem;
        }
        .category-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            margin-bottom: 0.8rem;
        }
        .ukm-content {
            padding: 1.5rem;
        }
        .ukm-content h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.3rem;
        }
        .ukm-content p {
            color: var(--text-color);
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .ukm-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        .ukm-footer .location {
            font-size: 0.9rem;
            color: var(--text-color);
            display: flex;
            align-items: center;
        }
        .ukm-footer .location i {
            color: var(--primary-color);
            margin-right: 0.3rem;
        }
        .badge-primary {
            background-color: var(--primary-color);
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .no-data {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        .no-data i {
            font-size: 3rem;
            color: var(--text-color-light);
            margin-bottom: 1rem;
        }
        .no-data p {
            color: var(--text-color);
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
                    <li><a href="popular_ukm.php" class="active"><i class="fas fa-fire"></i> UKM Populer</a></li>
                    <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="input_ukm.php"><i class="fas fa-plus-circle"></i> Input UKM</a></li>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
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
        <section class="popular-section">
            <div class="popular-header">
                <h2>UKM Populer</h2>
                <p>Berikut adalah daftar UKM yang paling banyak dikunjungi oleh pengunjung SIPUDESA.</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="notification error">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php elseif (empty($popular_ukms)): ?>
                <div class="no-data">
                    <i class="fas fa-search"></i>
                    <h3>Belum Ada Data UKM</h3>
                    <p>Belum ada UKM yang terdaftar dalam sistem. Silakan tambahkan UKM baru.</p>
                    <a href="input_ukm.php" class="btn">Tambah UKM Baru</a>
                </div>
            <?php else: ?>
                <div class="popular-ukm-grid">
                    <?php foreach ($popular_ukms as $ukm): ?>
                    <div class="ukm-card">
                        <div class="ukm-image">
                            <img src="<?php echo getUkmImage($ukm['kategori'] ?? 'Lainnya', $ukm['id']); ?>" alt="<?php echo htmlspecialchars($ukm['nama_ukm']); ?>">
                            <div class="visits-badge">
                                <i class="fas fa-box"></i> <?php echo number_format($ukm['jumlah_produk'] ?? 0); ?>
                            </div>
                        </div>
                        <div class="ukm-content">
                            <span class="category-badge <?php echo getCategoryBadgeClass($ukm['kategori'] ?? 'Lainnya'); ?>">
                                <?php echo htmlspecialchars($ukm['kategori'] ?? 'Lainnya'); ?>
                            </span>
                            <h3><?php echo htmlspecialchars($ukm['nama_ukm']); ?></h3>
                            <p><?php echo substr(htmlspecialchars($ukm['deskripsi'] ?? 'Tidak ada deskripsi'), 0, 120); ?>...</p>
                            
                            <div class="ukm-footer">
                                <div class="location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($ukm['alamat'] ?? 'Alamat tidak tersedia'); ?>
                                </div>
                                <a href="view_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm">Detail</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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