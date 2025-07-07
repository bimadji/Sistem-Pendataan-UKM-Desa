# SIPUDESA - Sistem Informasi Pendataan UKM Desa

SIPUDESA adalah aplikasi berbasis web untuk pendataan, pengelolaan, dan pemantauan Usaha Kecil Menengah (UKM) di desa. Aplikasi ini dirancang untuk membantu pemerintah desa dalam mendata dan mengelola UKM di wilayahnya.

## Fitur Utama

- **Pendataan UKM**: Input data UKM baru ke dalam sistem
- **Pencarian & Filtering**: Temukan UKM berdasarkan nama, alamat, atau kategori
- **Tampilan Modern**: Antarmuka yang modern dan responsif
- **Database Terstruktur**: Struktur database yang komprehensif untuk menyimpan data UKM dan produk

## Teknologi yang Digunakan

- **Frontend**: HTML, CSS
- **Backend**: PHP
- **Database**: MySQL
- **Icon**: Font Awesome

## Persyaratan Sistem

- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web server (Apache/Nginx)

## Instalasi

1. **Clone repositori ini ke direktori web server Anda**

```
git clone https://github.com/username/sipudesa.git
```

2. **Buat database dan import struktur**

```
mysql -u username -p < database.sql
```

3. **Konfigurasi database**

Edit file `config/database.php` dan sesuaikan pengaturan koneksi database:

```php
$host = 'localhost';
$dbname = 'ukm_desa1';
$username = 'root'; // Ganti dengan username MySQL Anda
$password = ''; // Ganti dengan password MySQL Anda
```

4. **Akses aplikasi melalui browser**

```
http://localhost/ukm_desa1
```

## Struktur Direktori

- `config/` - File konfigurasi aplikasi
- `css/` - File stylesheet
- `database.sql` - File SQL untuk pembuatan database dan tabel

## Penggunaan

### Input UKM Baru

1. Klik menu "Input UKM" pada navigasi
2. Isi semua informasi yang diperlukan
3. Klik tombol "Simpan Data"

### Pencarian UKM

1. Klik menu "Daftar UKM" pada navigasi
2. Gunakan form pencarian untuk mencari UKM berdasarkan nama, alamat, atau pemilik
3. Gunakan dropdown kategori untuk memfilter berdasarkan kategori UKM

## Pengembangan Lebih Lanjut

Beberapa ide untuk pengembangan lebih lanjut:

- Penambahan fitur manajemen produk UKM
- Dashboard analitik untuk melihat statistik UKM
- Sistem otentikasi pengguna dengan berbagai level akses
- Integrasi dengan peta untuk melihat lokasi UKM
- Fitur ekspor data ke PDF atau Excel

## Lisensi

Aplikasi ini bersifat open source dan tersedia di bawah [Lisensi MIT](LICENSE).

## Kontributor

- Bima Adji Kusuma - bimaadjikusuma@gmail.com

---

&copy; 2024 SIPUDESA - Sistem Informasi Pendataan UKM Desa # Sistem-Pendataan-UKM-Desa
