<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "kasir_pos";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("<div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px;'>
         <h3>❌ Koneksi Database Gagal!</h3>
         <p><strong>Error:</strong> " . $e->getMessage() . "</p>
         <p><strong>Host:</strong> $host</p>
         <p><strong>Database:</strong> $db</p>
         <p><strong>User:</strong> $user</p>
         </div>");
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

if (isset($_POST['add_category'])) {
    $nama = sanitize($_POST['nama_kategori']);
    $stmt = $pdo->prepare("INSERT INTO categories (nama_kategori) VALUES (?)");
    $stmt->execute([$nama]);
    $_SESSION['success'] = 'Kategori berhasil ditambahkan';
    header('Location: products.php');
    exit;
}

if (isset($_GET['delete_category'])) {
    $id = $_GET['delete_category'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = 'Kategori berhasil dihapus';
    header('Location: products.php');
    exit;
}

if (isset($_POST['add_supplier'])) {
    $nama = sanitize($_POST['nama_supplier']);
    $no_telp = sanitize($_POST['no_telp']);
    $alamat = sanitize($_POST['alamat']);
    
    $stmt = $pdo->prepare("INSERT INTO suppliers (nama_supplier, no_telp, alamat) VALUES (?,?,?)");
    $stmt->execute([$nama, $no_telp, $alamat]);
    $_SESSION['success'] = 'Supplier berhasil ditambahkan';
    header('Location: products.php');
    exit;
}

if (isset($_GET['delete_supplier'])) {
    $id = $_GET['delete_supplier'];
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = 'Supplier berhasil dihapus';
    header('Location: products.php');
    exit;
}

if (isset($_POST['add_product'])) {
    $nama_produk = $_POST['nama_produk'] ?? '';
    
    if (empty($nama_produk)) {
        $_SESSION['error'] = 'Nama produk wajib diisi';
        header('Location: products.php');
        exit;
    }
    
    $kategori_id = null;
    if (!empty($_POST['kategori_id']) && $_POST['kategori_id'] != 'new' && $_POST['kategori_id'] != '') {
        $kategori_id = $_POST['kategori_id'];
    } elseif (!empty($_POST['kategori_baru'])) {
        $stmt = $pdo->prepare("INSERT INTO categories (nama_kategori) VALUES (?)");
        $stmt->execute([sanitize($_POST['kategori_baru'])]);
        $kategori_id = $pdo->lastInsertId();
    }
    
    $supplier_id = null;
    if (!empty($_POST['supplier_id']) && $_POST['supplier_id'] != 'new' && $_POST['supplier_id'] != '') {
        $supplier_id = $_POST['supplier_id'];
    } elseif (!empty($_POST['supplier_baru'])) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (nama_supplier) VALUES (?)");
        $stmt->execute([sanitize($_POST['supplier_baru'])]);
        $supplier_id = $pdo->lastInsertId();
    }
    
    $harga_beli = !empty($_POST['harga_beli']) ? (int)str_replace('.', '', $_POST['harga_beli']) : 0;
    $harga_jual = !empty($_POST['harga_jual']) ? (int)str_replace('.', '', $_POST['harga_jual']) : 0;
    $stok = !empty($_POST['stok']) ? (int)$_POST['stok'] : 0;
    $barcode = !empty($_POST['barcode']) ? sanitize($_POST['barcode']) : null;
    
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $extension;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $filename)) {
            $gambar = $filename;
        }
    }
    
    try {
        $sql = "INSERT INTO products (nama_produk, harga_beli, harga_jual, stok, barcode, kategori_id, supplier_id, gambar) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $nama_produk, 
            $harga_beli, 
            $harga_jual, 
            $stok, 
            $barcode, 
            $kategori_id, 
            $supplier_id, 
            $gambar
        ]);
        
        if ($result) {
            $_SESSION['success'] = 'Produk "' . $nama_produk . '" berhasil ditambahkan (ID: ' . $pdo->lastInsertId() . ')';
        } else {
            $_SESSION['error'] = 'Gagal menambah produk';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error database: ' . $e->getMessage();
    }
    
    header('Location: products.php');
    exit;
}

if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $barcode = !empty($_POST['barcode']) ? sanitize($_POST['barcode']) : null;
    $nama_produk = sanitize($_POST['nama_produk']);
    
    $kategori_id = null;
    if (!empty($_POST['kategori_id']) && $_POST['kategori_id'] != 'new' && $_POST['kategori_id'] != '') {
        $kategori_id = $_POST['kategori_id'];
    } elseif (!empty($_POST['kategori_baru'])) {
        $stmt = $pdo->prepare("INSERT INTO categories (nama_kategori) VALUES (?)");
        $stmt->execute([sanitize($_POST['kategori_baru'])]);
        $kategori_id = $pdo->lastInsertId();
    }
    
    $supplier_id = null;
    if (!empty($_POST['supplier_id']) && $_POST['supplier_id'] != 'new' && $_POST['supplier_id'] != '') {
        $supplier_id = $_POST['supplier_id'];
    } elseif (!empty($_POST['supplier_baru'])) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (nama_supplier) VALUES (?)");
        $stmt->execute([sanitize($_POST['supplier_baru'])]);
        $supplier_id = $pdo->lastInsertId();
    }
    
    $harga_beli = !empty($_POST['harga_beli']) ? (int)str_replace('.', '', $_POST['harga_beli']) : 0;
    $harga_jual = !empty($_POST['harga_jual']) ? (int)str_replace('.', '', $_POST['harga_jual']) : 0;
    $stok = !empty($_POST['stok']) ? (int)$_POST['stok'] : 0;
    
    try {
        $sql = "UPDATE products SET barcode=?, nama_produk=?, kategori_id=?, supplier_id=?, harga_beli=?, harga_jual=?, stok=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$barcode, $nama_produk, $kategori_id, $supplier_id, $harga_beli, $harga_jual, $stok, $id]);
        
        $_SESSION['success'] = 'Produk berhasil diupdate';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error database: ' . $e->getMessage();
    }
    
    header('Location: products.php');
    exit;
}

if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = 'Produk berhasil dihapus';
    header('Location: products.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY nama_kategori")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY nama_supplier")->fetchAll();
$products = $pdo->query("
    SELECT p.*, 
           COALESCE(c.nama_kategori, '-') as nama_kategori,
           COALESCE(s.nama_supplier, '-') as nama_supplier
    FROM products p 
    LEFT JOIN categories c ON p.kategori_id = c.id 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    ORDER BY p.id DESC
")->fetchAll();

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" sizes="64x64" href="assets/logo/logoicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - <?= "Kasir POS" ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.tailwind.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.show {
            display: flex;
        }
        body.modal-open {
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50">

<?php 
$current_page = basename($_SERVER['PHP_SELF']);
$user = getCurrentUser();
?>

<button id="mobileMenuToggle" class="fixed top-4 right-4 z-50 md:hidden bg-blue-600 text-white p-3 rounded-lg shadow-lg">
    <i class="fas fa-bars text-xl"></i>
</button>

<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-blue-900 to-blue-950 text-white shadow-xl z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    
    <div class="p-6 border-b border-blue-800 flex justify-between items-center">
        <h2 class="text-base sm:text-lg font-bold flex items-end max-w-[calc(100%-40px)]">
            <img src="assets/logo/logosidebar.png" alt="<?= APP_NAME ?>" class="h-7 w-auto mr-2 flex-shrink-0">
            <span class="truncate"><?= APP_NAME ?></span>
            <span class="text-[10px] font-normal text-blue-300 ml-1">v.0.1</span>
        </h2>
        <button id="closeSidebarBtn" class="md:hidden text-white hover:text-gray-300 text-2xl flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="px-6 py-2 text-xs text-blue-300 border-b border-blue-800">
        © 2026 Julyant Marco Melandry
    </div>
    
    <nav class="mt-6 px-4">
        <a href="dashboard.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'dashboard.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-home w-6"></i>
            <span class="ml-3">Dashboard</span>
        </a>
        <a href="products.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'products.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-box w-6"></i>
            <span class="ml-3">Produk</span>
        </a>
        <a href="pos.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'pos.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-shopping-cart w-6"></i>
            <span class="ml-3">Kasir</span>
        </a>
        <a href="transactions.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'transactions.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-history w-6"></i>
            <span class="ml-3">Transaksi</span>
        </a>
        <a href="reports.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'reports.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-chart-bar w-6"></i>
            <span class="ml-3">Laporan</span>
        </a>
    </nav>
    
    <div class="absolute bottom-0 w-full p-6 border-t border-blue-800">
        <div class="flex items-center">
            <div class="bg-blue-700 rounded-full p-3">
                <i class="fas fa-user"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="font-medium truncate"><?= $user['nama_lengkap'] ?? 'User' ?></p>
                <p class="text-xs text-blue-300"><?= $user['role'] ?? 'Guest' ?></p>
            </div>
            <a href="index.php?logout=1" class="text-blue-300 hover:text-white">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>

<div class="md:ml-64 min-h-screen bg-gray-50 transition-all duration-300">

<div class="p-4 sm:p-8">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-0">Niaga Rakyat</h1>
        <div class="flex flex-wrap gap-2">
            <button onclick="openModal('addCategoryModal')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                <i class="fas fa-tag mr-2"></i>Tambah Kategori
            </button>
            <button onclick="openModal('addSupplierModal')" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-sm">
                <i class="fas fa-truck mr-2"></i>Tambah Supplier
            </button>
            <button onclick="openModal('addProductModal')" class="bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Produk
            </button>
        </div>
    </div>
    
    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
        <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
        <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    
    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <a href="#products" class="tab-link inline-block p-4 border-b-2 border-blue-600 rounded-t-lg active" data-tab="products">Produk</a>
            </li>
            <li class="mr-2">
                <a href="#categories" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:border-gray-300" data-tab="categories">Kategori</a>
            </li>
            <li class="mr-2">
                <a href="#suppliers" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:border-gray-300" data-tab="suppliers">Supplier</a>
            </li>
        </ul>
    </div>
    
    <div id="products" class="tab-content block">
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 overflow-x-auto">
            <table id="productsTable" class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 sm:px-4 py-3 text-left">Barcode</th>
                        <th class="px-2 sm:px-4 py-3 text-left">Nama Produk</th>
                        <th class="px-2 sm:px-4 py-3 text-left">Kategori</th>
                        <th class="px-2 sm:px-4 py-3 text-right">Harga Beli</th>
                        <th class="px-2 sm:px-4 py-3 text-right">Harga Jual</th>
                        <th class="px-2 sm:px-4 py-3 text-center">Stok</th>
                        <th class="px-2 sm:px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-2 sm:px-4 py-3"><?= htmlspecialchars($p['barcode'] ?? '-') ?></td>
                        <td class="px-2 sm:px-4 py-3 font-medium"><?= htmlspecialchars($p['nama_produk']) ?></td>
                        <td class="px-2 sm:px-4 py-3"><?= htmlspecialchars($p['nama_kategori']) ?></td>
                        <td class="px-2 sm:px-4 py-3 text-right"><?= rupiah($p['harga_beli']) ?></td>
                        <td class="px-2 sm:px-4 py-3 text-right"><?= rupiah($p['harga_jual']) ?></td>
                        <td class="px-2 sm:px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs 
                                <?= $p['stok'] > 10 ? 'bg-green-100 text-green-800' : 
                                   ($p['stok'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                <?= $p['stok'] ?>
                            </span>
                        </td>
                        <td class="px-2 sm:px-4 py-3 text-center">
                            <button onclick="editProduct(<?= $p['id'] ?>)" class="text-blue-600 hover:text-blue-800 mx-1">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete_product=<?= $p['id'] ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')" class="text-red-600 hover:text-red-800 mx-1">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="categories" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left">Nama Kategori</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $c): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3"><?= htmlspecialchars($c['nama_kategori']) ?></td>
                        <td class="px-4 py-3"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                        <td class="px-4 py-3 text-center">
                            <a href="?delete_category=<?= $c['id'] ?>" onclick="return confirm('Yakin ingin menghapus kategori ini?')" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="suppliers" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left">Nama Supplier</th>
                        <th class="px-4 py-3 text-left">No. Telepon</th>
                        <th class="px-4 py-3 text-left">Alamat</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $s): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium"><?= htmlspecialchars($s['nama_supplier']) ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($s['no_telp'] ?? '-') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($s['alamat'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-center">
                            <a href="?delete_supplier=<?= $s['id'] ?>" onclick="return confirm('Yakin ingin menghapus supplier ini?')" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</div>

<div id="addCategoryModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Tambah Kategori</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nama Kategori</label>
                <input type="text" name="nama_kategori" required class="w-full border rounded-lg px-4 py-2">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('addCategoryModal')" class="px-4 py-2 border rounded-lg">Batal</button>
                <button type="submit" name="add_category" class="px-4 py-2 bg-blue-800 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="addSupplierModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Tambah Supplier</h2>
        <form method="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-2">Nama Supplier *</label>
                    <input type="text" name="nama_supplier" required class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">No. Telepon</label>
                    <input type="text" name="no_telp" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="3" class="w-full border rounded-lg px-4 py-2"></textarea>
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <button type="button" onclick="closeModal('addSupplierModal')" class="px-4 py-2 border rounded-lg">Batal</button>
                <button type="submit" name="add_supplier" class="px-4 py-2 bg-blue-800 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="addProductModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Tambah Produk</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2">Barcode</label>
                    <input type="text" name="barcode" class="w-full border rounded-lg px-4 py-2" placeholder="Opsional">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Nama Produk *</label>
                    <input type="text" name="nama_produk" required class="w-full border rounded-lg px-4 py-2" placeholder="Wajib diisi">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Kategori</label>
                    <select name="kategori_id" class="w-full border rounded-lg px-4 py-2" onchange="toggleNewInput(this, 'kategori_baru_container')">
                        <option value="">-- Pilih Kategori (Opsional) --</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                        <option value="new">+ Tambah Baru</option>
                    </select>
                </div>
                <div id="kategori_baru_container" class="hidden">
                    <label class="block text-gray-700 mb-2">Nama Kategori Baru</label>
                    <input type="text" name="kategori_baru" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full border rounded-lg px-4 py-2" onchange="toggleNewInput(this, 'supplier_baru_container')">
                        <option value="">-- Pilih Supplier (Opsional) --</option>
                        <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_supplier']) ?></option>
                        <?php endforeach; ?>
                        <option value="new">+ Tambah Baru</option>
                    </select>
                </div>
                <div id="supplier_baru_container" class="hidden">
                    <label class="block text-gray-700 mb-2">Nama Supplier Baru</label>
                    <input type="text" name="supplier_baru" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Harga Beli</label>
                    <input type="text" name="harga_beli" onkeyup="formatRupiah(this)" class="w-full border rounded-lg px-4 py-2" value="0" placeholder="0 (Opsional)">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Harga Jual</label>
                    <input type="text" name="harga_jual" onkeyup="formatRupiah(this)" class="w-full border rounded-lg px-4 py-2" value="0" placeholder="0 (Opsional)">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Stok Awal</label>
                    <input type="number" name="stok" value="0" min="0" class="w-full border rounded-lg px-4 py-2" placeholder="0 (Opsional)">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Gambar</label>
                    <input type="file" name="gambar" accept="image/*" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <button type="button" onclick="closeModal('addProductModal')" class="px-4 py-2 border rounded-lg">Batal</button>
                <button type="submit" name="add_product" class="px-4 py-2 bg-blue-800 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editProductModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Edit Produk</h2>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2">Barcode</label>
                    <input type="text" name="barcode" id="edit_barcode" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Nama Produk *</label>
                    <input type="text" name="nama_produk" id="edit_nama" required class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Kategori</label>
                    <select name="kategori_id" id="edit_kategori" class="w-full border rounded-lg px-4 py-2">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Supplier</label>
                    <select name="supplier_id" id="edit_supplier" class="w-full border rounded-lg px-4 py-2">
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_supplier']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Harga Beli</label>
                    <input type="text" name="harga_beli" id="edit_harga_beli" onkeyup="formatRupiah(this)" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Harga Jual</label>
                    <input type="text" name="harga_jual" id="edit_harga_jual" onkeyup="formatRupiah(this)" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Stok</label>
                    <input type="number" name="stok" id="edit_stok" min="0" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <button type="button" onclick="closeModal('editProductModal')" class="px-4 py-2 border rounded-lg">Batal</button>
                <button type="submit" name="edit_product" class="px-4 py-2 bg-blue-800 text-white rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.tailwind.min.js"></script>
<script>
    $(document).ready(function() {
        if ($('#productsTable').length) {
            $('#productsTable').DataTable({ 
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' }
            });
        }
        
        $('.tab-link').click(function(e) {
            e.preventDefault();
            $('.tab-link').removeClass('border-blue-600').addClass('border-transparent');
            $(this).removeClass('border-transparent').addClass('border-blue-600');
            $('.tab-content').addClass('hidden');
            $($(this).attr('href')).removeClass('hidden');
        });
    });
    
    function toggleNewInput(select, containerId) {
        document.getElementById(containerId).classList.toggle('hidden', select.value !== 'new');
    }
    
    function formatRupiah(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = parseInt(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }
    
    function formatNumber(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('show');
        document.body.classList.add('modal-open');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
        document.body.classList.remove('modal-open');
    }
    
    function editProduct(id) {
        fetch('api/get_product.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_barcode').value = data.barcode || '';
                document.getElementById('edit_nama').value = data.nama_produk;
                document.getElementById('edit_kategori').value = data.kategori_id || '';
                document.getElementById('edit_supplier').value = data.supplier_id || '';
                document.getElementById('edit_harga_beli').value = formatNumber(data.harga_beli);
                document.getElementById('edit_harga_jual').value = formatNumber(data.harga_jual);
                document.getElementById('edit_stok').value = data.stok;
                openModal('editProductModal');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengambil data produk');
            });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('mobileMenuToggle');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (menuToggle && closeBtn && sidebar && overlay) {
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                menuToggle.classList.add('hidden');
            }
            
            function closeSidebar() {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                menuToggle.classList.remove('hidden');
            }
            
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                openSidebar();
            });
            
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
            
            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
            
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        const href = this.getAttribute('href');
                        if (href && href !== '#' && !href.startsWith('javascript')) {
                            e.preventDefault();
                            closeSidebar();
                            setTimeout(() => {
                                window.location.href = href;
                            }, 300);
                        }
                    }
                });
            });
            
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('-translate-x-full', 'translate-x-0');
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    menuToggle.classList.add('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    menuToggle.classList.remove('hidden');
                }
            });
            
            window.dispatchEvent(new Event('resize'));
        }
    });
</script>
</body>
</html>