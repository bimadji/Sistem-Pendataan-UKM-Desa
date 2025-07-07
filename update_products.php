<?php
require_once 'config/database.php';

// Map of UKM IDs and their products
$ukm_products = [
    // UKM 1: Bakso Pak Didi (Makanan)
    1 => [
        ['name' => 'Bakso Jumbo', 'description' => 'Bakso berukuran besar dengan isian daging sapi pilihan', 'price' => 15000, 'stock' => 50],
        ['name' => 'Bakso Urat', 'description' => 'Bakso dengan campuran urat sapi yang kenyal dan lezat', 'price' => 12000, 'stock' => 60],
        ['name' => 'Bakso Puyuh', 'description' => 'Bakso dengan isian telur puyuh yang gurih', 'price' => 14000, 'stock' => 60],
        ['name' => 'Mie Ayam Bakso', 'description' => 'Kombinasi mie ayam dengan bakso sapi pilihan', 'price' => 18000, 'stock' => 40],
    ],
    
    // UKM 2: Kerajinan Bamboo (Kerajinan)
    2 => [
        ['name' => 'Keranjang Bambu', 'description' => 'Keranjang multifungsi dari bambu pilihan', 'price' => 75000, 'stock' => 20],
        ['name' => 'Lampu Hias Bambu', 'description' => 'Lampu hias dari bambu dengan desain elegan', 'price' => 120000, 'stock' => 15],
        ['name' => 'Vas Bunga Bambu', 'description' => 'Vas bunga dekoratif dari bambu untuk hiasan rumah', 'price' => 85000, 'stock' => 25],
        ['name' => 'Gantungan Kunci Bambu', 'description' => 'Gantungan kunci unik dari potongan bambu', 'price' => 15000, 'stock' => 100],
    ],
    
    // UKM 3: Tani Makmur (Pertanian)
    3 => [
        ['name' => 'Sayur Selada', 'description' => 'Selada segar hidroponik per ikat', 'price' => 8000, 'stock' => 100],
        ['name' => 'Sayur Kangkung', 'description' => 'Kangkung segar organik per ikat', 'price' => 5000, 'stock' => 150],
        ['name' => 'Bayam', 'description' => 'Bayam segar organik per ikat', 'price' => 4000, 'stock' => 120],
        ['name' => 'Daun Bawang', 'description' => 'Daun Bawang segar organik per ikat', 'price' => 2000, 'stock' => 130],
    ],
    
    // UKM 4: Batik Sangar (Kerajinan)
    4 => [
        ['name' => 'Kain Batik', 'description' => 'Kain Batik didesain dengan tangan sendiri dengan motif tradisional', 'price' => 1000000, 'stock' => 15],
        ['name' => 'Sarung Batik', 'description' => 'Sarung Batik dengan kualitas premium dan pewarna alami', 'price' => 500000, 'stock' => 20],
        ['name' => 'Baju Batik Pria', 'description' => 'Kemeja batik pria dengan desain modern', 'price' => 350000, 'stock' => 35],
        ['name' => 'Dress Batik', 'description' => 'Dress batik wanita dengan desain elegan', 'price' => 450000, 'stock' => 25],
    ],
    
    // UKM 5: Yuk Jajan (Makanan)
    5 => [
        ['name' => 'Nastar', 'description' => 'Nastar lembut dengan isian nanas pilihan, satu toples', 'price' => 15000, 'stock' => 100],
        ['name' => 'Kastangel', 'description' => 'Kastangel dengan keju premium, satu toples', 'price' => 14000, 'stock' => 79],
        ['name' => 'Putri Salju', 'description' => 'Putri Salju dengan taburan gula halus, satu toples', 'price' => 20000, 'stock' => 30],
        ['name' => 'Biskuit', 'description' => 'Biskuit renyah aneka rasa, satu toples', 'price' => 10000, 'stock' => 50],
        ['name' => 'Keripik', 'description' => 'Keripik singkong aneka rasa, per 1 ons', 'price' => 4000, 'stock' => 100],
    ],
    
    // UKM 6: Susu Sapi Pak Anto (Peternakan)
    6 => [
        ['name' => 'Susu Sapi Original', 'description' => 'Susu sapi murni 500 ml per botol', 'price' => 4000, 'stock' => 100],
        ['name' => 'Susu Sapi Coklat', 'description' => 'Susu sapi dengan rasa coklat 500 ml per botol', 'price' => 5000, 'stock' => 80],
        ['name' => 'Susu Sapi Stroberi', 'description' => 'Susu sapi dengan rasa stroberi 500 ml per botol', 'price' => 5000, 'stock' => 60],
        ['name' => 'Yogurt Plain', 'description' => 'Yogurt dari susu sapi organik 250 ml', 'price' => 8000, 'stock' => 40],
        ['name' => 'Keju Segar', 'description' => 'Keju segar dari susu sapi pilihan 100 gram', 'price' => 20000, 'stock' => 25],
    ],
];

$success = 0;
$errors = 0;
$messages = [];

try {
    // First check if the products table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() == 0) {
        // Create products table if it doesn't exist
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
        $messages[] = "Created products table successfully";
    }
    
    // Clear existing products
    $stmt = $pdo->query("TRUNCATE TABLE products");
    $messages[] = "Cleared existing products data";
    
    // Get categories for each UKM
    $stmt = $pdo->prepare("SELECT id, kategori FROM ukm WHERE id = ?");
    
    // Insert products for each UKM
    foreach ($ukm_products as $ukm_id => $products) {
        // Get UKM category
        $stmt->execute([$ukm_id]);
        $ukm = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$ukm) {
            $messages[] = "Warning: UKM with ID $ukm_id not found";
            $errors++;
            continue;
        }
        
        $category = $ukm['kategori'];
        
        // Insert products
        $insertStmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, ukm_id, category) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($products as $product) {
            try {
                $insertStmt->execute([
                    $product['name'],
                    $product['description'],
                    $product['price'],
                    $product['stock'],
                    $ukm_id,
                    $category
                ]);
                $success++;
            } catch(PDOException $e) {
                $messages[] = "Error adding product {$product['name']} to UKM $ukm_id: " . $e->getMessage();
                $errors++;
            }
        }
    }
    
    $messages[] = "Successfully added $success products to UKMs";
    if ($errors > 0) {
        $messages[] = "Encountered $errors errors";
    }
    
} catch(PDOException $e) {
    $messages[] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Products - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .update-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .update-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .update-header i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .message {
            background-color: #f5f5f5;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: var(--border-radius);
        }
        .success-message {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        .error-message {
            background-color: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        .warning-message {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ff9800;
            border-left: 4px solid #ff9800;
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
        <div class="update-container">
            <div class="update-header">
                <i class="fas fa-box"></i>
                <h2>Update Produk UKM</h2>
                <p>Mengisi data produk untuk setiap UKM</p>
            </div>
            
            <div class="messages">
                <?php foreach ($messages as $message): ?>
                    <?php 
                    $class = "message";
                    if (strpos($message, "Successfully") !== false || strpos($message, "Created") !== false) {
                        $class .= " success-message";
                    } else if (strpos($message, "Error") !== false) {
                        $class .= " error-message";
                    } else if (strpos($message, "Warning") !== false) {
                        $class .= " warning-message";
                    }
                    ?>
                    <div class="<?php echo $class; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <p><strong>Total products added:</strong> <?php echo $success; ?></p>
                <a href="index.php" class="btn">Back to Home</a>
                <a href="daftar_ukm.php" class="btn btn-outline">View UKMs</a>
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