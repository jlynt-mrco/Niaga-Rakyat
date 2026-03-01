<?php
require_once 'config.php';

if (!isLogin()) {
    redirect('login.php');
}

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$type = $_GET['type'] ?? '';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->getStyle('A1:Z1')->getFont()->setBold(true);
$sheet->getStyle('A1:Z1')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF4F81BD');

if ($type == 'products') {
    $sheet->setCellValue('A1', 'Barcode');
    $sheet->setCellValue('B1', 'Nama Produk');
    $sheet->setCellValue('C1', 'Kategori');
    $sheet->setCellValue('D1', 'Supplier');
    $sheet->setCellValue('E1', 'Harga Beli');
    $sheet->setCellValue('F1', 'Harga Jual');
    $sheet->setCellValue('G1', 'Stok');
    $sheet->setCellValue('H1', 'Laba');
    
    $products = $pdo->query("
        SELECT p.*, c.nama_kategori, s.nama_supplier 
        FROM products p 
        LEFT JOIN categories c ON p.kategori_id = c.id 
        LEFT JOIN suppliers s ON p.supplier_id = s.id 
        ORDER BY p.id DESC
    ")->fetchAll();
    
    $row = 2;
    foreach ($products as $p) {
        $sheet->setCellValue('A'.$row, $p['barcode']);
        $sheet->setCellValue('B'.$row, $p['nama_produk']);
        $sheet->setCellValue('C'.$row, $p['nama_kategori']);
        $sheet->setCellValue('D'.$row, $p['nama_supplier']);
        $sheet->setCellValue('E'.$row, $p['harga_beli']);
        $sheet->setCellValue('F'.$row, $p['harga_jual']);
        $sheet->setCellValue('G'.$row, $p['stok']);
        $sheet->setCellValue('H'.$row, $p['harga_jual'] - $p['harga_beli']);
        $row++;
    }
    
    $filename = 'Data_Produk_' . date('Ymd') . '.xlsx';
    
} elseif ($type == 'sales') {
    $start = $_GET['start'] ?? date('Y-m-01');
    $end = $_GET['end'] ?? date('Y-m-t');
    
    $sheet->setCellValue('A1', 'Invoice');
    $sheet->setCellValue('B1', 'Tanggal');
    $sheet->setCellValue('C1', 'Kasir');
    $sheet->setCellValue('D1', 'Total');
    $sheet->setCellValue('E1', 'Diskon');
    $sheet->setCellValue('F1', 'Pajak');
    $sheet->setCellValue('G1', 'Total Bayar');
    
    $sales = $pdo->prepare("
        SELECT s.*, u.nama_lengkap 
        FROM sales s 
        JOIN users u ON s.user_id = u.id 
        WHERE DATE(s.created_at) BETWEEN ? AND ?
        ORDER BY s.created_at DESC
    ");
    $sales->execute([$start, $end]);
    
    $row = 2;
    foreach ($sales as $s) {
        $sheet->setCellValue('A'.$row, $s['invoice']);
        $sheet->setCellValue('B'.$row, $s['created_at']);
        $sheet->setCellValue('C'.$row, $s['nama_lengkap']);
        $sheet->setCellValue('D'.$row, $s['total_harga']);
        $sheet->setCellValue('E'.$row, $s['diskon']);
        $sheet->setCellValue('F'.$row, $s['pajak']);
        $sheet->setCellValue('G'.$row, $s['total_bayar']);
        $row++;
    }
    
    $filename = 'Laporan_Penjualan_' . $start . '_' . $end . '.xlsx';
}

foreach(range('A','Z') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>