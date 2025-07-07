<?php
require_once 'config/database.php';
session_start();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: daftar_ukm.php');
    exit;
}

$ukm_id = $_GET['id'];
$error_message = null;
$ukm = null;
$products = [];

// Get UKM details
try {
    // First check if the database connection is valid
    $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    
    // Check ukm table structure for required columns
    $required_columns = ['views', 'tanggal_berdiri'];
    $missing_columns = [];
    
    foreach ($required_columns as $column) {
        try {
            $check_column = $pdo->query("SHOW COLUMNS FROM ukm LIKE '$column'");
            if ($check_column->rowCount() == 0) {
                $missing_columns[$column] = true;
            }
        } catch(PDOException $e) {
            $missing_columns[$column] = true;
        }
    }
    
    // Query untuk mengambil detail UKM
    $query = "SELECT u.*, p.nama as nama_pemilik, p.telepon as telepon_pemilik, k.nama_kategori as kategori 
              FROM ukm u 
              LEFT JOIN pemilik_ukm p ON u.pemilik_id = p.id 
              LEFT JOIN kategori_ukm k ON u.kategori_id = k.id 
              WHERE u.id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $ukm_id);
    $stmt->execute();
    $ukm = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ukm) {
        $error_message = "UKM dengan ID tersebut tidak ditemukan.";
    } else {
        // Add default values for missing columns
        if (isset($missing_columns['views'])) {
            $ukm['views'] = 0;
        }
        
        if (isset($missing_columns['tanggal_berdiri']) || empty($ukm['tanggal_berdiri'])) {
            $ukm['tanggal_berdiri'] = null;
        }
        
        // Coba Cara 1: Dapatkan produk dari tabel products (struktur baru/standar)
        $products = [];
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM products 
                WHERE ukm_id = ? 
                ORDER BY id ASC
            ");
            $stmt->execute([$ukm_id]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Gagal query - mungkin struktur tabel berbeda
        }
        
        // Jika produk masih kosong, coba cara lain
        if (empty($products)) {
            try {
                $stmt = $pdo->prepare("
                    SELECT * FROM products 
                    WHERE id_ukm = ? 
                    ORDER BY id ASC
                ");
                $stmt->execute([$ukm_id]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                // Gagal lagi - mungkin tabel tidak ada
            }
        }
        
        // Cara 3: Ambil dari data contoh
        if (empty($products)) {
            include_once 'sample_products.php';
            if (isset($ukm_products) && isset($ukm_products[$ukm_id])) {
                $products = $ukm_products[$ukm_id];
            }
        }
        
        // Masih belum ada produk? Buat contoh produk berdasarkan kategori
        if (empty($products)) {
            $kategori = $ukm['kategori'] ?? '';
            include_once 'sample_products.php';
            if (function_exists('getDefaultProducts')) {
                $products = getDefaultProducts($kategori);
            }
        }
        
        // Simpan jumlah produk
        $ukm['total_products'] = count($products);
        
        // Update view count only if the column exists
        if (!isset($missing_columns['views'])) {
            try {
                $stmt = $pdo->prepare("UPDATE ukm SET views = views + 1 WHERE id = ?");
                $stmt->execute([$ukm_id]);
            } catch(PDOException $e) {
                // Silently fail if update fails
            }
        }
    }
} catch(PDOException $e) {
    $error_message = "Kesalahan Database: " . $e->getMessage();
}

// Get category badge color
function getCategoryBadgeClass($category) {
    switch ($category) {
        case 'Makanan':
            return 'food';
        case 'Minuman':
            return 'drink';
        case 'Kerajinan':
            return 'craft';
        case 'Jasa':
            return 'service';
        default:
            return 'other';
    }
}

// Format currency
function formatRupiah($angka) {
    if (empty($angka)) return 'Rp 0';
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Format tanggal
function formatTanggal($tanggal) {
    if (empty($tanggal)) return 'Tanggal tidak tersedia';
    
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    try {
        $split = explode('-', $tanggal);
        if (count($split) !== 3) return 'Format tanggal tidak valid';
        
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    } catch (Exception $e) {
        return 'Tanggal tidak valid';
    }
}

// Get product name and price fields with fallback options
function getProductName($product) {
    if (!empty($product['nama_produk'])) return $product['nama_produk'];
    if (!empty($product['name'])) return $product['name'];
    if (!empty($product['nama'])) return $product['nama'];
    return 'Produk';
}

function getProductPrice($product) {
    if (isset($product['harga'])) return $product['harga'];
    if (isset($product['price'])) return $product['price'];
    return 0;
}

function getProductDescription($product) {
    if (!empty($product['deskripsi'])) return $product['deskripsi'];
    if (!empty($product['description'])) return $product['description'];
    return '';
}

function getProductStock($product) {
    if (isset($product['stok'])) return $product['stok'];
    if (isset($product['stock'])) return $product['stock'];
    return 0;
}

// Function to add missing columns notification for admin users
function addMissingColumnsAdmin($missing_columns) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' || empty($missing_columns)) {
        return '';
    }
    
    $html = '<div style="margin-top: 1rem;">';
    $html .= '<div class="notification" style="background-color: rgba(255, 193, 7, 0.1); color: #ff9800; border-left: 4px solid #ff9800;">';
    $html .= '<i class="fas fa-exclamation-triangle"></i> Database perlu diperbarui: ';
    $html .= implode(', ', array_keys($missing_columns));
    $html .= '</div>';
    $html .= '<a href="admin/update_db_structure.php" class="btn btn-sm">';
    $html .= '<i class="fas fa-database"></i> Perbarui Struktur Database';
    $html .= '</a>';
    $html .= '</div>';
    
    return $html;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ukm ? htmlspecialchars($ukm['nama_ukm']) : 'Detail UKM'; ?> - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .ukm-header {
            display: flex;
            flex-direction: column;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
        }
        .ukm-name {
            margin-top: 0;
            font-size: 2rem;
            color: var(--primary-color);
        }
        .ukm-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1rem 0;
            color: var(--text-light-color);
        }
        .ukm-meta div {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .ukm-category {
            position: absolute;
            top: 2rem;
            right: 2rem;
        }
        .ukm-description {
            margin: 1.5rem 0;
            line-height: 1.6;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .details-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
        }
        .details-card h3 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .contact-info div {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .contact-info i {
            width: 20px;
            color: var(--primary-color);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
        .product-card {
            background-color: #f9f9f9;
            border-radius: var(--border-radius);
            padding: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid #eee;
        }
        .product-card:hover {
            transform: translateY(-5px);
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .product-name {
            font-weight: bold;
            color: var(--primary-color);
            margin: 0 0 0.5rem 0;
        }
        .product-price {
            color: var(--success-color);
            font-weight: bold;
            font-size: 1.1rem;
        }
        .product-desc {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-color);
        }
        .product-stock {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-light-color);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .product-section {
            background-color: #f9f9f9;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: var(--border-radius);
        }
        .product-section h2 {
            margin-top: 0;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
        }
        .notification {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            font-weight: 500;
        }
        .error {
            background-color: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        .inline-products {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        .inline-products h4 {
            margin-top: 0;
            margin-bottom: 1rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .floating-label {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: var(--border-radius);
            font-size: 0.8rem;
            font-weight: bold;
        }
        .view-all-btn {
            display: inline-block;
            margin-top: 1.5rem;
            text-align: center;
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }
        .view-all-btn:hover {
            background: var(--primary-dark-color);
        }
        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            .ukm-category {
                position: static;
                margin-bottom: 1rem;
            }
            .actions {
                flex-direction: column;
                gap: 1rem;
            }
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
        <section>
            <div class="breadcrumb">
                <a href="daftar_ukm.php"><i class="fas fa-arrow-left"></i> Kembali ke Daftar UKM</a>
            </div>
            
            <?php if ($error_message): ?>
                <div class="notification error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
                <p>Silakan kembali ke <a href="daftar_ukm.php">daftar UKM</a> untuk melihat UKM yang tersedia.</p>
                <?php echo isset($missing_columns) ? addMissingColumnsAdmin($missing_columns) : ''; ?>
            <?php elseif ($ukm): ?>
                <div class="ukm-header">
                    <div class="ukm-category">
                        <span class="badge <?php echo getCategoryBadgeClass($ukm['kategori']); ?>">
                            <?php echo htmlspecialchars($ukm['kategori']); ?>
                        </span>
                    </div>
                    
                    <h1 class="ukm-name"><?php echo htmlspecialchars($ukm['nama_ukm']); ?></h1>
                    
                    <div class="ukm-meta">
                        <?php if (!empty($ukm['tanggal_berdiri'])): ?>
                        <div>
                            <i class="fas fa-calendar-alt"></i>
                            <span>Berdiri sejak: <?php echo formatTanggal($ukm['tanggal_berdiri']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div>
                            <i class="fas fa-eye"></i>
                            <span><?php echo number_format($ukm['views'] ?? 0); ?> kali dilihat</span>
                        </div>
                        <div>
                            <i class="fas fa-box"></i>
                            <span><?php echo $ukm['total_products'] ?? 0; ?> produk</span>
                        </div>
                    </div>
                    
                    <div class="ukm-description">
                        <?php echo nl2br(htmlspecialchars($ukm['deskripsi'])); ?>
                    </div>
                </div>
                
                <!-- Produk Section -->
                <div class="product-section">
                    <h2><i class="fas fa-box"></i> Produk dari <?php echo htmlspecialchars($ukm['nama_ukm']); ?></h2>
                    
                    <?php if (!empty($products)): ?>
                        <div class="product-grid">
                            <?php foreach (array_slice($products, 0, 4) as $index => $product): ?>
                                <div class="product-card">
                                    <?php if ($index === 0): ?>
                                        <div class="floating-label">Unggulan</div>
                                    <?php endif; ?>
                                    <h4 class="product-name"><?php echo htmlspecialchars(getProductName($product)); ?></h4>
                                    <p class="product-price"><?php echo formatRupiah(getProductPrice($product)); ?></p>
                                    <?php if (!empty(getProductDescription($product))): ?>
                                        <p class="product-desc"><?php echo htmlspecialchars(getProductDescription($product)); ?></p>
                                    <?php endif; ?>
                                    <p class="product-stock">
                                        <i class="fas fa-cubes"></i> Stok: <?php echo getProductStock($product); ?> tersedia
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($products) > 4): ?>
                            <a href="produk_ukm.php?id=<?php echo $ukm_id; ?>" class="view-all-btn">
                                <i class="fas fa-list"></i> Lihat Semua Produk (<?php echo count($products); ?>)
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Belum ada produk yang terdaftar untuk UKM ini.</p>
                    <?php endif; ?>
                </div>
                
                <div class="details-grid">
                    <div class="details-card">
                        <h3><i class="fas fa-info-circle"></i> Informasi UKM</h3>
                        <div class="contact-info">
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($ukm['alamat'] ?? $ukm['lokasi'] ?? 'Lokasi tidak tersedia'); ?></span>
                            </div>
                            <div>
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($ukm['telepon_pemilik'] ?? $ukm['telepon'] ?? 'Kontak tidak tersedia'); ?></span>
                            </div>
                            <?php if (!empty($ukm['tanggal_berdiri'])): ?>
                            <div>
                                <i class="fas fa-calendar-alt"></i>
                                <span>Berdiri sejak <?php echo formatTanggal($ukm['tanggal_berdiri']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($ukm['nama_pemilik'])): ?>
                            <div>
                                <i class="fas fa-user"></i>
                                <span>Pemilik: <?php echo htmlspecialchars($ukm['nama_pemilik']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="details-card">
                        <h3><i class="fas fa-store"></i> Tentang UKM Ini</h3>
                        <p>
                            <?php echo htmlspecialchars($ukm['nama_ukm']); ?> adalah UKM dalam kategori <strong><?php echo htmlspecialchars($ukm['kategori']); ?></strong> 
                            yang berlokasi di <?php echo htmlspecialchars($ukm['alamat'] ?? $ukm['lokasi'] ?? 'wilayah sekitar'); ?>.
                            <?php if (!empty($ukm['nama_pemilik'])): ?>
                                UKM ini dikelola oleh <?php echo htmlspecialchars($ukm['nama_pemilik']); ?>.
                            <?php endif; ?>
                        </p>
                        
                        <p>
                            Untuk informasi lebih lanjut dan pemesanan produk, silakan hubungi:
                            <br>
                            <strong><?php echo htmlspecialchars($ukm['telepon_pemilik'] ?? $ukm['telepon'] ?? 'Kontak tidak tersedia'); ?></strong>
                        </p>
                        
                        <?php if (isset($_SESSION['user_id']) && ((isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') || 
                                 (isset($ukm['user_id']) && $ukm['user_id'] == $_SESSION['user_id']))): ?>
                            <div style="margin-top: 1rem;">
                                <a href="produk_ukm.php?id=<?php echo $ukm_id; ?>" class="btn btn-sm">
                                    <i class="fas fa-plus-circle"></i> Kelola Produk
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="actions">
                    <div>
                        <a href="produk_ukm.php?id=<?php echo $ukm_id; ?>" class="btn">
                            <i class="fas fa-box"></i> Lihat Semua Produk
                        </a>
                    </div>
                    
                    <div class="action-buttons">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ((isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') || 
                                    (isset($ukm['user_id']) && $ukm['user_id'] == $_SESSION['user_id'])): ?>
                                <a href="edit_ukm.php?id=<?php echo $ukm_id; ?>" class="btn">
                                    <i class="fas fa-edit"></i> Edit UKM
                                </a>
                                <a href="delete_ukm.php?id=<?php echo $ukm_id; ?>" class="btn btn-danger" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus UKM ini?');">
                                    <i class="fas fa-trash"></i> Hapus UKM
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php echo isset($missing_columns) ? addMissingColumnsAdmin($missing_columns) : ''; ?>
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