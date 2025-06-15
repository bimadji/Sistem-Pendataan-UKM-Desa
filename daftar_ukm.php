<?php
require_once 'config/database.php';
session_start();

// Inisialisasi variabel pencarian dan filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Check for delete notification
$deleteNotification = '';
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $deleteNotification = 'Data UKM berhasil dihapus!';
}

// Query untuk mengambil data UKM
$query = "SELECT u.*, p.nama as nama_pemilik, p.telepon as telepon_pemilik, k.nama_kategori as kategori 
          FROM ukm u 
          LEFT JOIN pemilik_ukm p ON u.pemilik_id = p.id 
          LEFT JOIN kategori_ukm k ON u.kategori_id = k.id 
          WHERE 1=1";

// Tambahkan filter pencarian jika ada
if (!empty($search)) {
    $query .= " AND (u.nama_ukm LIKE :search OR p.nama LIKE :search OR u.alamat LIKE :search)";
}

// Tambahkan filter kategori jika ada
if (!empty($kategori)) {
    $query .= " AND k.nama_kategori = :kategori";
}

$query .= " ORDER BY u.nama_ukm ASC";

try {
    $stmt = $pdo->prepare($query);
    
    if (!empty($search)) {
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);
    }
    
    if (!empty($kategori)) {
        $stmt->bindParam(':kategori', $kategori);
    }
    
    $stmt->execute();
    $ukms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = $e->getMessage();
}

// Fungsi untuk mendapatkan warna badge berdasarkan kategori
function getCategoryBadgeClass($category) {
    switch ($category) {
        case 'Makanan':
            return 'badge-success';
        case 'Minuman':
            return 'badge-info';
        case 'Kerajinan':
            return 'badge-primary';
        case 'Pertanian':
            return 'badge-warning';
        case 'Peternakan':
            return 'badge-danger';
        default:
            return 'badge-primary';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar UKM - SIPUDESA</title>
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
        .badge-info {
            background-color: #17a2b8;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: flex-start;
        }
        .btn-info {
            background-color: #17a2b8;
            color: white;
        }
        .btn-info:hover {
            background-color: #138496;
            color: white;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
        }
        .btn-edit {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }
        .table tbody td {
            vertical-align: middle;
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
        <section class="search-section">
            <h2><i class="fas fa-search"></i> Daftar UKM</h2>
            
            <?php if (!empty($deleteNotification)): ?>
                <div class="notification success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $deleteNotification; ?>
                </div>
            <?php endif; ?>
            
            <form method="GET" action="" class="search-form">
                <div class="form-group">
                    <label for="search"><i class="fas fa-search"></i> Cari UKM</label>
                    <input type="text" id="search" name="search" placeholder="Masukkan nama UKM, pemilik, atau alamat..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="form-group">
                    <label for="kategori"><i class="fas fa-tag"></i> Kategori</label>
                    <select name="kategori" id="kategori">
                        <option value="">Semua Kategori</option>
                        <option value="Makanan" <?php echo $kategori == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                        <option value="Minuman" <?php echo $kategori == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                        <option value="Kerajinan" <?php echo $kategori == 'Kerajinan' ? 'selected' : ''; ?>>Kerajinan</option>
                        <option value="Pertanian" <?php echo $kategori == 'Pertanian' ? 'selected' : ''; ?>>Pertanian</option>
                        <option value="Peternakan" <?php echo $kategori == 'Peternakan' ? 'selected' : ''; ?>>Peternakan</option>
                        <option value="Lainnya" <?php echo $kategori == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                    </select>
                </div>
                
                <button type="submit" class="btn"><i class="fas fa-search"></i> Cari</button>
                <?php if (!empty($search) || !empty($kategori)): ?>
                    <a href="daftar_ukm.php" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
                <?php endif; ?>
            </form>

            <div class="result-summary">
                <p>
                    <?php if (count($ukms) > 0): ?>
                        <i class="fas fa-info-circle"></i> Menampilkan <?php echo count($ukms); ?> UKM
                        <?php if (!empty($search)): ?>
                            dengan kata kunci "<?php echo htmlspecialchars($search); ?>"
                        <?php endif; ?>
                        <?php if (!empty($kategori)): ?>
                            dalam kategori "<?php echo htmlspecialchars($kategori); ?>"
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="ukm-actions">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="input_ukm.php" class="btn"><i class="fas fa-plus"></i> Tambah UKM</a>
                    <a href="export_ukm.php" class="btn"><i class="fas fa-download"></i> Export Data</a>
                <?php endif; ?>
            </div>

            <div class="ukm-list">
                <?php if (empty($ukms)): ?>
                    <div class="empty-state">
                        <i class="fas fa-search fa-3x" style="color: var(--gray-color); margin-bottom: 1rem;"></i>
                        <p>Tidak ada data UKM yang ditemukan.</p>
                        <a href="input_ukm.php" class="btn"><i class="fas fa-plus-circle"></i> Tambah UKM Baru</a>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama UKM</th>
                                <th>Pemilik</th>
                                <th>Telepon</th>
                                <th>Kategori</th>
                                <th>Alamat</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ukms as $ukm): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ukm['nama_ukm']); ?></td>
                                    <td><?php echo htmlspecialchars($ukm['nama_pemilik'] ?? 'Tidak ada data'); ?></td>
                                    <td><?php echo htmlspecialchars($ukm['telepon_pemilik'] ?? 'Tidak ada data'); ?></td>
                                    <td><?php echo htmlspecialchars($ukm['kategori'] ?? 'Tidak ada data'); ?></td>
                                    <td><?php echo htmlspecialchars($ukm['alamat'] ?? 'Tidak ada data'); ?></td>
                                    <td><?php echo htmlspecialchars($ukm['deskripsi'] ?? 'Tidak ada data'); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm btn-edit" title="Edit UKM">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm btn-outline" title="Hapus UKM">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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