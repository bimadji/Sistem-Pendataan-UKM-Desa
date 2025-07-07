<?php
require_once 'config/database.php';
session_start();

// Check if UKM ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: daftar_ukm.php');
    exit;
}

$ukm_id = $_GET['id'];

// Get UKM details
try {
    $stmt = $pdo->prepare("SELECT * FROM ukm WHERE id = ?");
    $stmt->execute([$ukm_id]);
    $ukm = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ukm) {
        header('Location: daftar_ukm.php');
        exit;
    }
    
    // Check if current user can edit this UKM
    $can_edit = false;
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['user_role'] === 'admin' || $ukm['user_id'] == $_SESSION['user_id']) {
            $can_edit = true;
        }
    }
    
} catch(PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}

// Handle product operations
$notification = '';
$notification_type = '';

// Product Add
if (isset($_POST['add_product'])) {
    if (isset($_SESSION['user_id']) && $can_edit) {
        $nama_produk = $_POST['nama_produk'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        
        try {
            // Create products table if not exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS products (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                ukm_id INT(11) NOT NULL,
                nama_produk VARCHAR(255) NOT NULL,
                deskripsi TEXT,
                harga DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
            )");
            
            $stmt = $pdo->prepare("INSERT INTO products (ukm_id, nama_produk, deskripsi, harga) VALUES (?, ?, ?, ?)");
            $stmt->execute([$ukm_id, $nama_produk, $deskripsi, $harga]);
            
            $notification = "Produk berhasil ditambahkan!";
            $notification_type = "success";
        } catch(PDOException $e) {
            $notification = "Error: " . $e->getMessage();
            $notification_type = "error";
        }
    } else {
        $notification = "Anda tidak memiliki izin untuk menambahkan produk!";
        $notification_type = "error";
    }
}

// Product Delete
if (isset($_GET['delete_product']) && isset($_SESSION['user_id']) && $can_edit) {
    $product_id = $_GET['delete_product'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND ukm_id = ?");
        $stmt->execute([$product_id, $ukm_id]);
        
        $notification = "Produk berhasil dihapus!";
        $notification_type = "success";
    } catch(PDOException $e) {
        $notification = "Error: " . $e->getMessage();
        $notification_type = "error";
    }
}

// Get products for this UKM
try {
    // Check if products table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'products'");
    $stmt->execute();
    $table_exists = $stmt->rowCount() > 0;
    
    $products = [];
    if ($table_exists) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE ukm_id = ? ORDER BY created_at DESC");
        $stmt->execute([$ukm_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $notification = "Error: " . $e->getMessage();
    $notification_type = "error";
    $products = [];
}

// Format currency
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk <?php echo htmlspecialchars($ukm['nama_ukm']); ?> - SIPUDESA</title>
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
        .product-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .product-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            position: relative;
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-title {
            margin-top: 0;
            font-size: 1.2rem;
            color: var(--primary-color);
        }
        .product-price {
            font-weight: bold;
            color: var(--success-color);
            margin: 0.5rem 0;
            font-size: 1.2rem;
        }
        .product-desc {
            color: var(--text-color);
            margin-bottom: 1rem;
        }
        .product-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }
        .add-product-form {
            background-color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
        }
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-row .form-group {
            flex: 1;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
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
        <section>
            <div class="breadcrumb">
                <a href="daftar_ukm.php"><i class="fas fa-arrow-left"></i> Kembali ke Daftar UKM</a>
            </div>
            
            <h2><i class="fas fa-box"></i> Produk <?php echo htmlspecialchars($ukm['nama_ukm']); ?></h2>
            
            <?php if (!empty($notification)): ?>
                <div class="notification <?php echo $notification_type; ?>">
                    <?php if ($notification_type === 'success'): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle"></i>
                    <?php endif; ?>
                    <?php echo $notification; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($can_edit): ?>
                <div class="add-product-form">
                    <h3><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h3>
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama_produk"><i class="fas fa-tag"></i> Nama Produk</label>
                                <input type="text" id="nama_produk" name="nama_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="harga"><i class="fas fa-money-bill-wave"></i> Harga (Rp)</label>
                                <input type="number" id="harga" name="harga" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi"><i class="fas fa-align-left"></i> Deskripsi Produk</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <button type="submit" name="add_product" class="btn"><i class="fas fa-plus-circle"></i> Tambah Produk</button>
                    </form>
                </div>
            <?php endif; ?>
            
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open fa-3x" style="color: var(--gray-color); margin-bottom: 1rem;"></i>
                    <p>Belum ada produk untuk UKM ini.</p>
                    <?php if ($can_edit): ?>
                        <p>Silahkan tambahkan produk menggunakan form di atas.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="product-cards">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h3>
                            <p class="product-price"><?php echo formatRupiah($product['harga']); ?></p>
                            <p class="product-desc"><?php echo htmlspecialchars($product['deskripsi']); ?></p>
                            
                            <?php if ($can_edit): ?>
                                <div class="product-actions">
                                    <a href="produk_ukm.php?id=<?php echo $ukm_id; ?>&delete_product=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-delete" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            <?php endif; ?>
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