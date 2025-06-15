<?php
require_once 'config/database.php';

// Set a flag to track if setup was successful
$setup_success = true;
$messages = [];

try {
    // First run database.sql script to create base tables
    $sql = file_get_contents('db/database.sql');
    $pdo->exec($sql);
    $messages[] = "Base tables created successfully";
    
    // Then run users.sql to add users table
    $sql = file_get_contents('db/users.sql');
    
    // Execute each SQL statement separately
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    $messages[] = "User authentication tables created successfully";
    
    // Verify admin user exists
    $stmt = $pdo->query("SELECT * FROM users WHERE username = 'admin'");
    $admin = $stmt->fetch();
    
    if (!$admin) {
        // Re-insert admin if not exists
        $stmt = $pdo->prepare("INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', '$2y$10$x5L.E0O4bCwPj4VyAXUVgOCPW.zz57bvXnxl3FJ4z0pTwfcZNUaVC', 'Administrator', 'admin@sipudesa.id', 'admin']);
        $messages[] = "Admin user created successfully";
    } else {
        $messages[] = "Admin user already exists";
    }
    
    // Check if ukm table has user_id column
    $stmt = $pdo->query("SHOW COLUMNS FROM ukm LIKE 'user_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE ukm ADD COLUMN user_id INT");
        $pdo->exec("ALTER TABLE ukm ADD CONSTRAINT fk_ukm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
        $messages[] = "Added user_id column to UKM table";
    } else {
        $messages[] = "UKM table already has user_id column";
    }
    
    // Check if ukm table has visits column
    $stmt = $pdo->query("SHOW COLUMNS FROM ukm LIKE 'visits'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE ukm ADD COLUMN visits INT DEFAULT 0");
        $messages[] = "Added visits column to UKM table";
    } else {
        $messages[] = "UKM table already has visits column";
    }
    
    // Check if products table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() == 0) {
        try {
            // Create the products table structure
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    description TEXT,
                    price DECIMAL(10, 2) NOT NULL,
                    stock INT DEFAULT 0,
                    ukm_id INT,
                    category VARCHAR(50) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT fk_product_ukm FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            $messages[] = "Created products table structure";
            
            // Add sample products for UKM 1 (assuming it's a food UKM)
            $stmt = $pdo->prepare("INSERT INTO products (ukm_id, name, description, price, stock, category) VALUES 
                (1, 'Bakso Jumbo', 'Bakso berukuran besar dengan isian daging', 15000, 50, 'Makanan'),
                (1, 'Bakso Urat', 'Bakso dengan campuran urat sapi', 12000, 60, 'Makanan'),
                (1, 'Bakso Puyuh', 'Bakso dengan isian telur puyuh', 14000, 60, 'Makanan')
            ");
            $stmt->execute();
            
            // Add more sample products if UKM 2 exists (kerajinan)
            $stmt = $pdo->query("SELECT id FROM ukm WHERE id = 2");
            if ($stmt->rowCount() > 0) {
                $pdo->exec("INSERT INTO products (ukm_id, name, description, price, stock, category) VALUES 
                    (2, 'Keranjang Bambu', 'Keranjang multifungsi dari bambu pilihan', 75000, 20, 'Kerajinan'),
                    (2, 'Lampu Hias Bambu', 'Lampu hias dari bambu dengan desain elegan', 120000, 15, 'Kerajinan')
                ");
            }
            
            // Add more sample products if UKM 3 exists (pertanian)
            $stmt = $pdo->query("SELECT id FROM ukm WHERE id = 3");
            if ($stmt->rowCount() > 0) {
                $pdo->exec("INSERT INTO products (ukm_id, name, description, price, stock, category) VALUES 
                    (3, 'Sayur Selada', 'Selada segar hidroponik per ikat', 8000, 100, 'Pertanian'),
                    (3, 'Sayur Kangkung', 'Kangkung segar organik per ikat', 5000, 150, 'Pertanian'),
                    (3, 'Bayam', 'Bayam segar organik per ikat', 4000, 120, 'Pertanian')
                ");
            }
            
            $messages[] = "Added sample product data";
            
        } catch(PDOException $e) {
            $messages[] = "Error handling products table: " . $e->getMessage();
        }
    } else {
        $messages[] = "Products table already exists";
    }
    
} catch(PDOException $e) {
    $setup_success = false;
    $messages[] = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .setup-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .setup-header i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .success-message {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--success-color);
        }
        .error-message {
            background-color: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--danger-color);
        }
        .setup-step {
            background-color: #f9f9f9;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            border-left: 4px solid #ddd;
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
            </div>
        </nav>
    </header>

    <main class="container animate-fade">
        <div class="setup-container">
            <div class="setup-header">
                <i class="fas fa-database"></i>
                <h2>Database Setup</h2>
                <p>Setting up database for SIPUDESA</p>
            </div>
            
            <?php if ($setup_success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Database setup completed successfully!
                </div>
            <?php else: ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> Database setup encountered errors.
                </div>
            <?php endif; ?>
            
            <div class="setup-results">
                <h3>Setup Log</h3>
                <?php foreach ($messages as $index => $message): ?>
                    <div class="setup-step">
                        <strong>Step <?php echo $index + 1; ?>:</strong> <?php echo $message; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="login.php" class="btn">Proceed to Login</a>
                <a href="index.php" class="btn btn-outline">Back to Home</a>
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
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 SIPUDESA - Sistem Informasi Pendataan UKM Desa</p>
            </div>
        </div>
    </footer>
</body>
</html> 