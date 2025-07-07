<?php
// Data contoh produk untuk UKM jika database tidak memiliki data produk

$ukm_products = [
    // UKM 1: Bakso
    1 => [
        ['name' => 'Bakso Jumbo', 'description' => 'Bakso berukuran besar dengan isian daging sapi pilihan', 'price' => 15000, 'stock' => 50],
        ['name' => 'Bakso Urat', 'description' => 'Bakso dengan campuran urat sapi yang kenyal dan lezat', 'price' => 12000, 'stock' => 60],
        ['name' => 'Bakso Puyuh', 'description' => 'Bakso dengan isian telur puyuh yang gurih', 'price' => 14000, 'stock' => 60],
        ['name' => 'Mie Ayam Bakso', 'description' => 'Kombinasi mie ayam dengan bakso sapi pilihan', 'price' => 18000, 'stock' => 40],
    ],
    
    // UKM 2: Kerajinan Bambu
    2 => [
        ['name' => 'Keranjang Bambu', 'description' => 'Keranjang multifungsi dari bambu pilihan', 'price' => 75000, 'stock' => 20],
        ['name' => 'Lampu Hias Bambu', 'description' => 'Lampu hias dari bambu dengan desain elegan', 'price' => 120000, 'stock' => 15],
        ['name' => 'Vas Bunga Bambu', 'description' => 'Vas bunga dekoratif dari bambu untuk hiasan rumah', 'price' => 85000, 'stock' => 25],
        ['name' => 'Gantungan Kunci Bambu', 'description' => 'Gantungan kunci unik dari potongan bambu', 'price' => 15000, 'stock' => 100],
    ],
    
    // UKM 3: Tani Makmur
    3 => [
        ['name' => 'Sayur Selada', 'description' => 'Selada segar hidroponik per ikat', 'price' => 8000, 'stock' => 100],
        ['name' => 'Sayur Kangkung', 'description' => 'Kangkung segar organik per ikat', 'price' => 5000, 'stock' => 150],
        ['name' => 'Bayam', 'description' => 'Bayam segar organik per ikat', 'price' => 4000, 'stock' => 120],
        ['name' => 'Daun Bawang', 'description' => 'Daun Bawang segar organik per ikat', 'price' => 2000, 'stock' => 130],
    ],
    
    // UKM 4: Batik
    4 => [
        ['name' => 'Kain Batik', 'description' => 'Kain Batik didesain dengan tangan sendiri dengan motif tradisional', 'price' => 1000000, 'stock' => 15],
        ['name' => 'Sarung Batik', 'description' => 'Sarung Batik dengan kualitas premium dan pewarna alami', 'price' => 500000, 'stock' => 20],
        ['name' => 'Baju Batik Pria', 'description' => 'Kemeja batik pria dengan desain modern', 'price' => 350000, 'stock' => 35],
        ['name' => 'Dress Batik', 'description' => 'Dress batik wanita dengan desain elegan', 'price' => 450000, 'stock' => 25],
    ],
    
    // UKM 5: Kue dan Jajanan
    5 => [
        ['name' => 'Nastar', 'description' => 'Nastar lembut dengan isian nanas pilihan, satu toples', 'price' => 15000, 'stock' => 100],
        ['name' => 'Kastangel', 'description' => 'Kastangel dengan keju premium, satu toples', 'price' => 14000, 'stock' => 79],
        ['name' => 'Putri Salju', 'description' => 'Putri Salju dengan taburan gula halus, satu toples', 'price' => 20000, 'stock' => 30],
        ['name' => 'Biskuit', 'description' => 'Biskuit renyah aneka rasa, satu toples', 'price' => 10000, 'stock' => 50],
        ['name' => 'Keripik', 'description' => 'Keripik singkong aneka rasa, per 1 ons', 'price' => 4000, 'stock' => 100],
    ],
    
    // UKM 6-10 data
    6 => [
        ['name' => 'Susu Sapi Original', 'description' => 'Susu sapi murni 500 ml per botol', 'price' => 4000, 'stock' => 100],
        ['name' => 'Susu Sapi Coklat', 'description' => 'Susu sapi dengan rasa coklat 500 ml per botol', 'price' => 5000, 'stock' => 80],
        ['name' => 'Susu Sapi Stroberi', 'description' => 'Susu sapi dengan rasa stroberi 500 ml per botol', 'price' => 5000, 'stock' => 60],
        ['name' => 'Yogurt Plain', 'description' => 'Yogurt dari susu sapi organik 250 ml', 'price' => 8000, 'stock' => 40],
    ],
    
    7 => [
        ['name' => 'Wedang Jahe', 'description' => 'Minuman jahe hangat dengan rempah pilihan', 'price' => 5000, 'stock' => 100],
        ['name' => 'Bandrek', 'description' => 'Minuman tradisional dengan cita rasa rempah', 'price' => 7000, 'stock' => 80],
        ['name' => 'Sekoteng', 'description' => 'Minuman hangat dengan kacang hijau dan rempah', 'price' => 8000, 'stock' => 75],
        ['name' => 'Es Cincau', 'description' => 'Minuman segar dengan cincau hitam dan gula aren', 'price' => 6000, 'stock' => 90],
    ],
    
    8 => [
        ['name' => 'Tas Anyaman', 'description' => 'Tas anyaman dari pandan dengan motif tradisional', 'price' => 150000, 'stock' => 20],
        ['name' => 'Tikar Pandan', 'description' => 'Tikar dari anyaman pandan ukuran 2x2 meter', 'price' => 200000, 'stock' => 15],
        ['name' => 'Topi Anyaman', 'description' => 'Topi dari anyaman bambu untuk pelindung sinar matahari', 'price' => 75000, 'stock' => 30],
        ['name' => 'Wadah Serbaguna', 'description' => 'Wadah anyaman untuk menyimpan berbagai barang', 'price' => 45000, 'stock' => 40],
    ],
    
    9 => [
        ['name' => 'Mie Ayam Original', 'description' => 'Mie ayam dengan topping ayam cincang yang gurih', 'price' => 10000, 'stock' => 100],
        ['name' => 'Mie Ayam Bakso', 'description' => 'Mie ayam dengan tambahan bakso sapi', 'price' => 15000, 'stock' => 80],
        ['name' => 'Mie Ayam Jamur', 'description' => 'Mie ayam dengan tambahan jamur kancing', 'price' => 12000, 'stock' => 70],
        ['name' => 'Mie Ayam Spesial', 'description' => 'Mie ayam dengan topping komplit', 'price' => 18000, 'stock' => 50],
    ],
    
    10 => [
        ['name' => 'Telur Ayam', 'description' => 'Telur ayam kampung segar per butir', 'price' => 2500, 'stock' => 200],
        ['name' => 'Ayam Potong', 'description' => 'Ayam potong segar per ekor', 'price' => 45000, 'stock' => 50],
        ['name' => 'Bibit Ayam', 'description' => 'Bibit ayam kampung berkualitas', 'price' => 15000, 'stock' => 100],
        ['name' => 'Pupuk Organik', 'description' => 'Pupuk organik dari kotoran ayam per karung', 'price' => 30000, 'stock' => 30],
    ],
];

// Fungsi untuk mendapatkan produk default berdasarkan kategori
function getDefaultProducts($kategori = '') {
    switch ($kategori) {
        case 'Makanan':
            return [
                ['name' => 'Produk Makanan 1', 'description' => 'Makanan khas daerah dengan cita rasa lezat', 'price' => 20000, 'stock' => 50],
                ['name' => 'Produk Makanan 2', 'description' => 'Makanan tradisional dengan bahan pilihan', 'price' => 15000, 'stock' => 60],
                ['name' => 'Produk Makanan 3', 'description' => 'Makanan sehat untuk keluarga', 'price' => 25000, 'stock' => 40],
            ];
        
        case 'Minuman':
            return [
                ['name' => 'Minuman Tradisional', 'description' => 'Minuman khas daerah dengan rempah pilihan', 'price' => 8000, 'stock' => 100],
                ['name' => 'Minuman Herbal', 'description' => 'Minuman sehat dengan bahan alami', 'price' => 12000, 'stock' => 80],
                ['name' => 'Minuman Segar', 'description' => 'Minuman menyegarkan untuk cuaca panas', 'price' => 6000, 'stock' => 120],
            ];
            
        case 'Kerajinan':
            return [
                ['name' => 'Kerajinan Tangan', 'description' => 'Dibuat dengan ketelitian dan keterampilan', 'price' => 100000, 'stock' => 20],
                ['name' => 'Souvenir Daerah', 'description' => 'Kenang-kenangan khas daerah', 'price' => 50000, 'stock' => 30],
                ['name' => 'Aksesori Rumah', 'description' => 'Hiasan rumah dengan sentuhan tradisional', 'price' => 75000, 'stock' => 25],
            ];
            
        default:
            return [
                ['name' => 'Bayam 10Kg', 'description' => 'Produk unggulan UKM Tani', 'price' => 500000, 'stock' => 30],
                ['name' => 'Cabai', 'description' => 'Kualitas terbaik dengan harga terjangkau', 'price' => 35000, 'stock' => 40],
                ['name' => 'Kangkung', 'description' => 'Produk andalan dengan bahan pilihan', 'price' => 45000, 'stock' => 35],
            ];
    }
} 