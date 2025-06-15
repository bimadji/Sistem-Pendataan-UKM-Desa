<?php
require_once '../config/database.php';
session_start();

// Debug session
var_dump($_SESSION);
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$notification = '';
$notificationType = '';

// Verify table structure
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM ukm");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>UKM Table Columns: ";
    print_r($columns);
    echo "</pre>";
    
    // Get user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Get UKMs created by this user - using direct column name
    $stmt = $pdo->prepare("SELECT ukm.*, kategori_ukm.nama_kategori as kategori_nama 
                          FROM ukm 
                          LEFT JOIN kategori_ukm ON ukm.kategori_id = kategori_ukm.id 
                          WHERE ukm.user_id = ? 
                          ORDER BY ukm.created_at DESC");
    $stmt->execute([$user_id]);
    $user_ukms = $stmt->fetchAll();
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    echo "<br>SQL State: " . $e->errorInfo[0];
    echo "<br>Error Code: " . $e->errorInfo[1];
    echo "<br>Error Message: " . $e->errorInfo[2];
    exit;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation
    if (empty($name) || empty($email)) {
        $notification = "Nama dan email tidak boleh kosong!";
        $notificationType = "error";
    } else {
        try {
            // Get current user info
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            // Check if email already exists (excluding current user)
            if ($email !== $user['email']) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $user_id]);
                if ($stmt->rowCount() > 0) {
                    $notification = "Email sudah digunakan oleh pengguna lain!";
                    $notificationType = "error";
                }
            }
            
            // If no error with email, continue
            if (empty($notification)) {
                // If user wants to change password
                if (!empty($current_password)) {
                    // Verify current password
                    if (!password_verify($current_password, $user['password'])) {
                        $notification = "Password saat ini tidak valid!";
                        $notificationType = "error";
                    } elseif (empty($new_password) || empty($confirm_password)) {
                        $notification = "Password baru dan konfirmasi password harus diisi!";
                        $notificationType = "error";
                    } elseif ($new_password !== $confirm_password) {
                        $notification = "Password baru dan konfirmasi password tidak cocok!";
                        $notificationType = "error";
                    } elseif (strlen($new_password) < 6) {
                        $notification = "Password baru minimal harus 6 karakter!";
                        $notificationType = "error";
                    } else {
                        // Update profile with new password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                        $stmt->execute([$name, $email, $hashed_password, $user_id]);
                        
                        // Update session info
                        $_SESSION['user_name'] = $name;
                        
                        $notification = "Profil berhasil diperbarui dengan password baru!";
                        $notificationType = "success";
                    }
                } else {
                    // Update profile without changing password
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $user_id]);
                    
                    // Update session info
                    $_SESSION['user_name'] = $name;
                    
                    $notification = "Profil berhasil diperbarui!";
                    $notificationType = "success";
                }
            }
        } catch(PDOException $e) {
            $notification = "Error: " . $e->getMessage();
            $notificationType = "error";
        }
    }
}

// Get user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Get UKMs created by this user - simplified query
    $stmt = $pdo->prepare("SELECT * FROM ukm WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_ukms = $stmt->fetchAll();
    
    // Get categories for the UKMs
    if (!empty($user_ukms)) {
        $ukm_ids = array_column($user_ukms, 'id');
        $placeholders = str_repeat('?,', count($ukm_ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT ukm_id, nama_kategori FROM kategori_ukm k 
                              JOIN ukm u ON k.id = u.kategori_id 
                              WHERE u.id IN ($placeholders)");
        $stmt->execute($ukm_ids);
        $categories = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Add category names to UKMs
        foreach ($user_ukms as &$ukm) {
            $ukm['kategori_nama'] = $categories[$ukm['id']] ?? 'Unknown';
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
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
    <title>Profil Pengguna - SIPUDESA</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .profile-container {
                grid-template-columns: 350px 1fr;
            }
        }
        .profile-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
        }
        .profile-name {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }
        .profile-role {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        .profile-meta {
            margin-bottom: 1.5rem;
        }
        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .profile-meta-item i {
            width: 20px;
            color: var(--primary-color);
        }
        .tab-container {
            margin-bottom: 1.5rem;
        }
        .tab-links {
            display: flex;
            gap: 1rem;
            border-bottom: 1px solid #eee;
            margin-bottom: 1.5rem;
        }
        .tab-link {
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .tab-link.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
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
        .ukm-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
            padding: 1rem;
        }
        .ukm-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .ukm-actions {
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
                    <li><a href="profile.php" class="active"><i class="fas fa-user"></i> Profil</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <main class="container animate-fade">
        <h2><i class="fas fa-user"></i> Profil Pengguna</h2>
        
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
        
        <div class="profile-container">
            <div>
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h3>
                        <span class="profile-role"><?php echo $user['role'] === 'admin' ? 'Administrator' : 'Pengguna'; ?></span>
                    </div>
                    
                    <div class="profile-meta">
                        <div class="profile-meta-item">
                            <i class="fas fa-user-tag"></i>
                            <div><?php echo htmlspecialchars($user['username']); ?></div>
                        </div>
                        <div class="profile-meta-item">
                            <i class="fas fa-envelope"></i>
                            <div><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="profile-meta-item">
                            <i class="fas fa-calendar"></i>
                            <div>Bergabung: <?php echo date('d F Y', strtotime($user['created_at'])); ?></div>
                        </div>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="../input_ukm.php" class="btn"><i class="fas fa-plus-circle"></i> Tambah UKM Baru</a>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="profile-card">
                    <div class="tab-container">
                        <div class="tab-links">
                            <div class="tab-link active" data-tab="my-ukms"><i class="fas fa-store"></i> UKM Saya</div>
                            <div class="tab-link" data-tab="edit-profile"><i class="fas fa-edit"></i> Edit Profil</div>
                        </div>
                        
                        <div id="my-ukms" class="tab-content active">
                            <h3>UKM yang Saya Daftarkan</h3>
                            
                            <?php if (empty($user_ukms)): ?>
                                <div class="empty-state" style="text-align: center; padding: 2rem 0;">
                                    <i class="fas fa-store fa-3x" style="color: var(--gray-color); margin-bottom: 1rem;"></i>
                                    <p>Anda belum mendaftarkan UKM apapun.</p>
                                    <a href="../input_ukm.php" class="btn"><i class="fas fa-plus-circle"></i> Tambah UKM Baru</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($user_ukms as $ukm): ?>
                                    <div class="ukm-card">
                                        <div class="ukm-header">
                                            <div>
                                                <h4 style="margin-top: 0;"><?php echo htmlspecialchars($ukm['nama_ukm']); ?></h4>
                                                <span class="badge <?php echo getCategoryBadgeClass($ukm['kategori_nama']); ?>">
                                                    <?php echo htmlspecialchars($ukm['kategori_nama']); ?>
                                                </span>
                                                <span style="font-size: 0.875rem; color: var(--text-color); margin-left: 0.5rem;">
                                                    <i class="fas fa-eye"></i> <?php echo $ukm['visits']; ?> kunjungan
                                                </span>
                                            </div>
                                            <div class="ukm-actions">
                                                <a href="../view_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="../edit_ukm.php?id=<?php echo $ukm['id']; ?>" class="btn btn-sm" title="Edit UKM">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div>
                                            <p style="margin-bottom: 0.5rem;"><i class="fas fa-user" style="width: 20px;"></i> <?php echo htmlspecialchars($ukm['pemilik']); ?></p>
                                            <p style="margin-bottom: 0.5rem;"><i class="fas fa-map-marker-alt" style="width: 20px;"></i> <?php echo htmlspecialchars($ukm['alamat']); ?></p>
                                            <p style="margin-bottom: 0.5rem;"><i class="fas fa-phone" style="width: 20px;"></i> <?php echo htmlspecialchars($ukm['telepon']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div id="edit-profile" class="tab-content">
                            <h3>Edit Profil</h3>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="name"><i class="fas fa-user"></i> Nama Lengkap</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="username"><i class="fas fa-user-tag"></i> Username</label>
                                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <small>Username tidak dapat diubah.</small>
                                </div>
                                
                                <hr style="margin: 1.5rem 0;">
                                <h4>Ubah Password</h4>
                                <p>Kosongkan semua field password jika Anda tidak ingin mengubah password.</p>
                                
                                <div class="form-group">
                                    <label for="current_password"><i class="fas fa-lock"></i> Password Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password">
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password"><i class="fas fa-key"></i> Password Baru</label>
                                    <input type="password" id="new_password" name="new_password">
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password"><i class="fas fa-check-circle"></i> Konfirmasi Password Baru</label>
                                    <input type="password" id="confirm_password" name="confirm_password">
                                </div>
                                
                                <input type="hidden" name="update_profile" value="1">
                                <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
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
        
        // Tab switching
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all links and contents
                tabLinks.forEach(link => link.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to the clicked link and corresponding content
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html> 