<?php
require_once 'config/database.php';

// Cek apakah vendor/autoload.php ada
if (!file_exists('vendor/autoload.php')) {
    die('Error: Library belum diinstall. Silakan install library dengan menjalankan perintah berikut di terminal:<br>
    composer require phpoffice/phpspreadsheet tecnickcom/tcpdf');
}

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF as TCPDF;

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fungsi untuk export ke Excel
function exportToExcel($data) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set header
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Nama UKM');
    $sheet->setCellValue('C1', 'Pemilik');
    $sheet->setCellValue('D1', 'Telepon');
    $sheet->setCellValue('E1', 'Alamat');
    $sheet->setCellValue('F1', 'Kategori');
    $sheet->setCellValue('G1', 'Jumlah Karyawan');
    $sheet->setCellValue('H1', 'Tahun Berdiri');
    $sheet->setCellValue('I1', 'Status');
    
    // Isi data
    $row = 2;
    foreach ($data as $index => $ukm) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $ukm['nama_ukm']);
        $sheet->setCellValue('C' . $row, $ukm['nama_pemilik']);
        $sheet->setCellValue('D' . $row, $ukm['telepon_pemilik']);
        $sheet->setCellValue('E' . $row, $ukm['alamat']);
        $sheet->setCellValue('F' . $row, $ukm['nama_kategori']);
        $sheet->setCellValue('G' . $row, $ukm['jumlah_karyawan']);
        $sheet->setCellValue('H' . $row, $ukm['tahun_berdiri']);
        $sheet->setCellValue('I' . $row, $ukm['status']);
        $row++;
    }
    
    // Set lebar kolom otomatis
    foreach (range('A', 'I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Set header untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Data_UKM.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Fungsi untuk export ke PDF
function exportToPDF($data) {
    // Buat instance TCPDF
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
    
    // Set informasi dokumen
    $pdf->SetCreator('SIPUDESA');
    $pdf->SetAuthor('SIPUDESA');
    $pdf->SetTitle('Data UKM');
    
    // Set margin
    $pdf->SetMargins(15, 15, 15);
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Data UKM Desa', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Header tabel
    $pdf->SetFont('helvetica', 'B', 10);
    $header = array('No', 'Nama UKM', 'Pemilik', 'Telepon', 'Alamat', 'Kategori', 'Jumlah Karyawan', 'Tahun Berdiri', 'Status');
    $w = array(10, 40, 30, 25, 60, 25, 25, 25, 25);
    
    // Header
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    }
    $pdf->Ln();
    
    // Data
    $pdf->SetFont('helvetica', '', 9);
    foreach($data as $index => $ukm) {
        $pdf->Cell($w[0], 6, $index + 1, 1);
        $pdf->Cell($w[1], 6, $ukm['nama_ukm'], 1);
        $pdf->Cell($w[2], 6, $ukm['nama_pemilik'], 1);
        $pdf->Cell($w[3], 6, $ukm['telepon_pemilik'], 1);
        $pdf->Cell($w[4], 6, $ukm['alamat'], 1);
        $pdf->Cell($w[5], 6, $ukm['nama_kategori'], 1);
        $pdf->Cell($w[6], 6, $ukm['jumlah_karyawan'], 1);
        $pdf->Cell($w[7], 6, $ukm['tahun_berdiri'], 1);
        $pdf->Cell($w[8], 6, $ukm['status'], 1);
        $pdf->Ln();
    }
    
    // Output PDF
    $pdf->Output('Data_UKM.pdf', 'D');
    exit;
}

// Ambil data UKM
try {
    $stmt = $pdo->query("
        SELECT 
            u.*,
            p.nama as nama_pemilik,
            p.telepon as telepon_pemilik,
            k.nama_kategori
        FROM ukm u
        JOIN pemilik_ukm p ON u.pemilik_id = p.id
        JOIN kategori_ukm k ON u.kategori_id = k.id
        ORDER BY u.nama_ukm
    ");
    $data = $stmt->fetchAll();
    
    // Export berdasarkan format yang dipilih
    if (isset($_GET['format'])) {
        switch ($_GET['format']) {
            case 'excel':
                exportToExcel($data);
                break;
            case 'pdf':
                exportToPDF($data);
                break;
            default:
                header("Location: daftar_ukm.php");
                exit;
        }
    }
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
    <title>Export Data UKM - SIPUDESA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <div class="export-container">
            <h2>Export Data UKM</h2>
            <div class="export-options">
                <a href="?format=excel" class="btn"><i class="fas fa-file-excel"></i> Export ke Excel</a>
                <a href="?format=pdf" class="btn"><i class="fas fa-file-pdf"></i> Export ke PDF</a>
                <a href="daftar_ukm.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
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