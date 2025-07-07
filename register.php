<?php
require_once 'config/database.php';
session_start();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal harus 6 karakter!";
    } else {
        try {
            // Make sure the users table exists
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    role ENUM('admin', 'user') DEFAULT 'user',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                $error = "Username sudah digunakan!";
            } else {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->rowCount() > 0) {
                    $error = "Email sudah digunakan!";
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new user
                    $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $username, $email, $hashed_password]);
                    
                    $success = "Pendaftaran berhasil! Silakan login dengan akun Anda.";
                    
                    // Redirect to login page after 2 seconds
                    header("refresh:2;url=login.php");
                }
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .register-container {
            max-width: 550px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-header i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .register-header h2 {
            color: var(--dark-color);
        }
        .register-form .form-group {
            margin-bottom: 1.5rem;
        }
        .register-form input {
            width: 100%;
        }
        .register-form button {
            width: 100%;
            margin-top: 1rem;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .error-message {
            background-color: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            border-left: 4px solid var(--danger-color);
        }
        .success-message {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            border-left: 4px solid var(--success-color);
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
                    <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <main class="container animate-fade">
        <div class="register-container">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h2>Registrasi SIPUDESA</h2>
                <p>Daftar untuk dapat mengelola UKM Anda di SIPUDESA</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form class="register-form" method="POST" action="">
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="name" name="name" required placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="form-group">
                    <label for="username"><i class="fas fa-user-tag"></i> Username</label>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required placeholder="Masukkan email">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Konfirmasi password">
                </div>
                
                <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Daftar</button>
            </form>
            
            <div class="login-link">
                <p>Sudah memiliki akun? <a href="login.php">Login sekarang</a></p>
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
                        <a href="index.php">Beranda</a>
                        <a href="daftar_ukm.php">Daftar UKM</a>
                        <a href="tentang.php">Tentang</a>
                        <a href="login.php">Login</a>
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