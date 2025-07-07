<?php
require_once 'config/database.php';
session_start();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

// Check if database setup is needed
$setupNeeded = false;
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        $setupNeeded = true;
    }
} catch(PDOException $e) {
    $setupNeeded = true;
}

// Redirect to setup if needed
if ($setupNeeded) {
    header("Location: setup_database.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        // Get user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // Special case for admin with fixed password "admin123"
        if ($user && $user['username'] === 'admin' && $password === 'admin123') {
            // Start session and store user info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            
            header("Location: index.php");
            exit;
        }
        // For regular users
        elseif ($user && password_verify($password, $user['password'])) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 450px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .login-header h2 {
            color: var(--dark-color);
        }
        .login-form .form-group {
            margin-bottom: 1.5rem;
        }
        .login-form input {
            width: 100%;
        }
        .login-form button {
            width: 100%;
            margin-top: 1rem;
        }
        .register-link {
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
                    <li><a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <main class="container animate-fade">
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-user-circle"></i>
                <h2>Login SIPUDESA</h2>
                <p>Masukkan username dan password Anda untuk melanjutkan</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password">
                </div>
                
                <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>
            
            <div class="register-link">
                <p>Belum memiliki akun? <a href="register.php">Daftar sekarang</a></p>
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