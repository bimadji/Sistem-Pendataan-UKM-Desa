<?php
require_once '../config/database.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get statistics
try {
    // Total UKMs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ukm");
    $total_ukm = $stmt->fetch()['total'];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
    $total_users = $stmt->fetch()['total'];
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk");
    $total_products = $stmt->fetch()['total'];
    
    // Total categories
    $stmt = $pdo->query("SELECT COUNT(DISTINCT nama_kategori) as total FROM v_ukm_produk");
    $total_categories = $stmt->fetch()['total'];
    
    // Most popular UKM
    $stmt = $pdo->query("SELECT nama_ukm, jumlah_produk as visits FROM v_ukm_produk ORDER BY jumlah_produk DESC LIMIT 1");
    $most_popular = $stmt->fetch();
    
    // Recent UKMs
    $stmt = $pdo->query("SELECT u.id, u.nama_ukm, v.nama_pemilik as pemilik, v.nama_kategori as kategori, u.created_at 
                        FROM ukm u 
                        JOIN v_ukm_produk v ON u.id = v.id 
                        ORDER BY u.created_at DESC LIMIT 5");
    $recent_ukms = $stmt->fetchAll();
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIPUDESA</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: var(--text-color);
        }
        .dashboard-section {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .dashboard-section h3 {
            margin-top: 0;
            margin-bottom: 1rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .badge-info {
            background-color: #17a2b8;
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
                    <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="../input_ukm.php"><i class="fas fa-plus-circle"></i> Input UKM</a></li>
                    <li><a href="../daftar_ukm.php"><i class="fas fa-list"></i> Daftar UKM</a></li>
                    <li><a href="../popular_ukm.php"><i class="fas fa-fire"></i> UKM Populer</a></li>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <main class="container animate-fade">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h2>
        <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>! Berikut adalah ringkasan data SIPUDESA.</p>
        
        <div class="dashboard-container">
            <div class="stat-card">
                <i class="fas fa-store"></i>
                <div class="stat-value"><?php echo $total_ukm; ?></div>
                <div class="stat-label">Total UKM Terdaftar</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Pengguna</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div class="stat-value"><?php echo $total_products; ?></div>
                <div class="stat-label">Total Produk</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-tags"></i>
                <div class="stat-value"><?php echo $total_categories; ?></div>
                <div class="stat-label">Kategori UKM</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-fire"></i>
                <div class="stat-value"><?php echo $most_popular ? $most_popular['visits'] : 0; ?></div>
                <div class="stat-label">Kunjungan UKM Terpopuler</div>
                <?php if ($most_popular): ?>
                <small><?php echo htmlspecialchars($most_popular['nama_ukm']); ?></small>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="dashboard-section">
            <h3><i class="fas fa-clock"></i> UKM Terbaru</h3>
            
            <?php if (empty($recent_ukms)): ?>
                <p>Belum ada UKM yang terdaftar.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nama UKM</th>
                            <th>Pemilik</th>
                            <th>Kategori</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_ukms as $ukm): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ukm['nama_ukm']); ?></td>
                                <td><?php echo htmlspecialchars($ukm['pemilik']); ?></td>
                                <td><?php echo htmlspecialchars($ukm['kategori']); ?></td>
                                <td><?php echo date('d M Y', strtotime($ukm['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="../view_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="../edit_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm btn-edit" title="Edit UKM">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../delete_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm btn-outline" title="Hapus UKM">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="text-align: right; margin-top: 1rem;">
                    <a href="../daftar_ukm.php" class="btn btn-outline">Lihat Semua UKM</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-section">
            <h3><i class="fas fa-cog"></i> Tindakan Cepat</h3>
            <div class="button-group">
                <a href="../input_ukm.php" class="btn"><i class="fas fa-plus-circle"></i> Tambah UKM Baru</a>
                <a href="../popular_ukm.php" class="btn btn-outline"><i class="fas fa-fire"></i> Lihat UKM Populer</a>
            </div>
        </div>
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
                        <a href="../index.php">Beranda</a>
                        <a href="../input_ukm.php">Input UKM</a>
                        <a href="../daftar_ukm.php">Daftar UKM</a>
                        <a href="../popular_ukm.php">UKM Populer</a>
                        <a href="dashboard.php">Dashboard</a>
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