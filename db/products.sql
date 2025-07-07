-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    stock INT DEFAULT 0,
    ukm_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_ukm FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample products for Makanan category
INSERT INTO products (name, description, price, category, stock) VALUES
('Bakso Sapi Jumbo', 'Bakso daging sapi pilihan dengan tekstur kenyal dan rasa gurih. Disajikan dengan kuah kaldu yang kaya rasa dan tambahan mie serta sayuran segar.', 25000, 'Makanan', 100),
('Mie Ayam Spesial', 'Mie ayam dengan potongan daging ayam yang tebal, sayuran segar, dan bumbu rahasia turun-temurun. Disajikan dengan kuah kaldu yang gurih.', 18000, 'Makanan', 150),
('Nasi Goreng Kampung', 'Nasi goreng dengan bumbu tradisional, telur, sayuran, dan potongan ayam kampung. Disajikan dengan kerupuk dan acar segar.', 20000, 'Makanan', 200),
('Soto Ayam', 'Soto ayam dengan kuah bening yang kaya rasa, potongan daging ayam, mie soun, tauge, dan telur rebus. Disajikan dengan sambal dan jeruk nipis.', 15000, 'Makanan', 120),
('Ayam Bakar Madu', 'Ayam yang dibakar dengan bumbu khusus dan dilapisi madu asli, memberikan rasa manis gurih yang khas. Disajikan dengan nasi, lalapan, dan sambal terasi.', 30000, 'Makanan', 80);

-- Sample products for Minuman category
INSERT INTO products (name, description, price, category, stock) VALUES
('Es Teh Manis', 'Teh manis segar yang disajikan dengan es batu. Minuman klasik yang cocok untuk menemani santap siang.', 5000, 'Minuman', 200),
('Kopi Tubruk', 'Kopi tradisional Indonesia yang diseduh langsung dengan air panas. Memiliki rasa kuat dan aroma yang pekat.', 8000, 'Minuman', 150),
('Jus Alpukat', 'Jus alpukat segar dengan tambahan susu kental manis, disajikan dengan es batu. Kaya akan nutrisi dan rasanya lezat.', 12000, 'Minuman', 100),
('Es Jeruk Peras', 'Air jeruk peras segar dengan tambahan gula dan es batu. Menyegarkan dan kaya akan vitamin C.', 10000, 'Minuman', 180),
('Wedang Jahe', 'Minuman tradisional dari jahe yang diolah dengan gula merah dan rempah-rempah. Menghangatkan tubuh dan menyehatkan.', 7000, 'Minuman', 120);

-- Sample products for Kerajinan category
INSERT INTO products (name, description, price, category, stock) VALUES
('Tas Anyaman Bambu', 'Tas tangan yang dibuat dari anyaman bambu dengan motif tradisional. Awet dan cocok untuk berbagai kesempatan.', 150000, 'Kerajinan', 50),
('Patung Kayu Jati', 'Patung dekoratif yang diukir dari kayu jati dengan detail yang halus. Menampilkan keahlian pengrajin lokal.', 350000, 'Kerajinan', 20),
('Kain Batik Tulis', 'Kain batik tulis dengan motif eksklusif, dibuat menggunakan teknik tradisional. Setiap kain memiliki pola unik.', 500000, 'Kerajinan', 30),
('Gelang Anyaman Tali', 'Gelang tangan yang dianyam dari tali warna-warni dengan motif etnik. Cocok untuk aksesoris sehari-hari.', 25000, 'Kerajinan', 100),
('Topeng Kayu Dekoratif', 'Topeng kayu ukir dengan motif tradisional, bisa digunakan sebagai hiasan dinding. Dikerjakan dengan detail yang rumit.', 275000, 'Kerajinan', 25);

-- Sample products for Pertanian category
INSERT INTO products (name, description, price, category, stock) VALUES
('Beras Organik 5kg', 'Beras organik premium yang ditanam tanpa pestisida kimia. Memiliki butiran utuh dan rasa yang lebih gurih.', 75000, 'Pertanian', 100),
('Sayuran Hidroponik Mix', 'Paket sayuran hidroponik segar yang terdiri dari selada, bayam, dan kangkung. Ditanam tanpa pestisida.', 35000, 'Pertanian', 50),
('Buah Naga Organik 1kg', 'Buah naga merah organik dengan daging buah yang manis dan segar. Kaya akan vitamin dan antioksidan.', 45000, 'Pertanian', 60),
('Madu Asli 500ml', 'Madu asli dari lebah yang dibudidayakan di hutan lindung. Tidak tercampur dengan bahan tambahan.', 85000, 'Pertanian', 40),
('Pupuk Kompos 10kg', 'Pupuk kompos organik yang terbuat dari bahan-bahan alami. Memperkaya tanah dan meningkatkan hasil panen.', 50000, 'Pertanian', 200);

-- Sample products for Peternakan category
INSERT INTO products (name, description, price, category, stock) VALUES
('Telur Ayam Kampung 1kg', 'Telur ayam kampung segar dari peternakan bebas kandang. Memiliki cita rasa yang lebih kaya.', 40000, 'Peternakan', 100),
('Susu Sapi Murni 1L', 'Susu sapi murni yang dipasteurisasi dengan minimal proses. Kaya kalsium dan nutrisi penting.', 25000, 'Peternakan', 50),
('Daging Sapi Lokal 1kg', 'Daging sapi lokal segar dari sapi yang dibesarkan dengan pakan alami. Cocok untuk berbagai hidangan.', 120000, 'Peternakan', 30),
('Daging Ayam Potong 1kg', 'Daging ayam segar dari peternakan lokal dengan pakan berkualitas. Dipotong sesuai pesanan pelanggan.', 35000, 'Peternakan', 80),
('Yogurt Plain 500ml', 'Yogurt yang dibuat dari susu sapi murni dengan fermentasi alami. Tanpa tambahan gula dan pengawet.', 30000, 'Peternakan', 40);

-- Sample products for Lainnya category
INSERT INTO products (name, description, price, category, stock) VALUES
('Sabun Herbal Handmade', 'Sabun mandi yang dibuat secara manual dengan bahan-bahan herbal alami. Lembut di kulit dan wangi.', 15000, 'Lainnya', 100),
('Lilin Aromaterapi', 'Lilin aromaterapi dengan minyak esensial alami. Menciptakan suasana relaksasi di rumah Anda.', 35000, 'Lainnya', 60),
('Tas Belanja Ramah Lingkungan', 'Tas belanja yang terbuat dari bahan daur ulang. Kuat dan bisa digunakan berulang kali.', 20000, 'Lainnya', 150),
('Potpourri Bunga Kering', 'Campuran bunga kering dan rempah yang harum, cocok untuk pewangi ruangan alami.', 40000, 'Lainnya', 30),
('Celemek Dapur Motif Tradisional', 'Celemek dapur dengan motif tradisional yang dibuat dari kain katun berkualitas. Awet dan nyaman dipakai.', 45000, 'Lainnya', 25); 