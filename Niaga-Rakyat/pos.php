<?php
require_once 'config.php';
if (!isLogin()) redirect('index.php');

$products = $pdo->query("SELECT * FROM products WHERE stok > 0 ORDER BY nama_produk")->fetchAll();

if (isset($_POST['process_sale'])) {
    $cart = json_decode($_POST['cart_data'], true);
    $total = str_replace('.', '', $_POST['total']);
    $diskon = str_replace('.', '', $_POST['diskon']);
    $pajak = str_replace('.', '', $_POST['pajak']);
    $uang_bayar = str_replace('.', '', $_POST['uang_bayar']);
    $uang_kembali = str_replace('.', '', $_POST['uang_kembali']);
    $invoice = generateInvoice();
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO sales (invoice, user_id, total_harga, diskon, pajak, total_bayar, uang_bayar, uang_kembali) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$invoice, $_SESSION['user_id'], $total, $diskon, $pajak, $total, $uang_bayar, $uang_kembali]);
        $sale_id = $pdo->lastInsertId();
        
        foreach ($cart as $item) {
            $stmt = $pdo->prepare("INSERT INTO sale_details (sale_id, product_id, harga_jual, jumlah, subtotal) VALUES (?,?,?,?,?)");
            $stmt->execute([$sale_id, $item['id'], $item['harga_jual'], $item['jumlah'], $item['subtotal']]);
            
            $stmt = $pdo->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");
            $stmt->execute([$item['jumlah'], $item['id']]);
        }
        
        $pdo->commit();
        redirect('transactions.php?action=detail&id=' . $sale_id);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Transaksi gagal: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" sizes="64x64" href="assets/logo/logoicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Niaga Rakyat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

    <div class="p-4 sm:p-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-8">Halaman Kasir</h1>
        
        <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="searchProduct" placeholder="Cari produk (barcode / nama)..." 
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-blue-800">
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4 max-h-96 overflow-y-auto" id="productList">
                        <?php foreach ($products as $p): ?>
                        <div class="product-card border rounded-lg p-4 hover:shadow-lg cursor-pointer hover:border-blue-800 transition" 
                             onclick="addToCart(<?= $p['id'] ?>, '<?= $p['nama_produk'] ?>', <?= $p['harga_jual'] ?>, <?= $p['stok'] ?>)">
                            <p class="font-semibold truncate"><?= $p['nama_produk'] ?></p>
                            <p class="text-sm text-blue-600 font-bold"><?= rupiah($p['harga_jual']) ?></p>
                            <p class="text-xs text-gray-500">Stok: <?= $p['stok'] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <h2 class="text-xl font-bold">Keranjang Belanja</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full mb-4">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Produk</th>
                                    <th class="text-center py-2">Harga</th>
                                    <th class="text-center py-2">Jumlah</th>
                                    <th class="text-center py-2">Subtotal</th>
                                    <th class="text-center py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cartItems"></tbody>
                        </table>
                    </div>
                    <button onclick="clearCart()" class="text-red-600 text-sm hover:underline">
                        <i class="fas fa-trash mr-1"></i>Kosongkan Keranjang
                    </button>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 sticky top-6">
                    <h2 class="text-xl font-bold mb-4">Pembayaran</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Total Belanja</label>
                            <div class="text-2xl sm:text-3xl font-bold text-blue-800" id="displayTotal">Rp 0</div>
                            <input type="hidden" id="total" value="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Diskon</label>
                            <input type="text" id="diskon" onkeyup="calculateTotal()" 
                                class="w-full border rounded-lg px-4 py-2" placeholder="0" value="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Pajak (10%)</label>
                            <input type="text" id="pajak" readonly 
                                class="w-full border rounded-lg px-4 py-2 bg-gray-100" value="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Total Bayar</label>
                            <div class="text-2xl sm:text-3xl font-bold text-green-600" id="displayTotalBayar">Rp 0</div>
                            <input type="hidden" id="total_bayar" value="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Uang Bayar</label>
                            <input type="text" id="uang_bayar" onkeyup="calculateChange()" 
                                class="w-full border rounded-lg px-4 py-2" placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Kembalian</label>
                            <div class="text-xl sm:text-2xl font-bold text-purple-600" id="displayKembalian">Rp 0</div>
                            <input type="hidden" id="uang_kembali" value="0">
                        </div>
                        
                        <button onclick="processSale()" 
                            class="w-full bg-blue-800 text-white py-3 rounded-lg hover:bg-blue-900 font-bold transition">
                            <i class="fas fa-check mr-2"></i>Proses Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/script.js"></script>
<script>
    let cart = [];
    
    function addToCart(id, name, price, stock) {
        let existing = cart.find(item => item.id === id);
        
        if (existing) {
            if (existing.jumlah < stock) {
                existing.jumlah++;
                existing.subtotal = existing.jumlah * existing.harga_jual;
            } else {
                alert('Stok tidak mencukupi!');
                return;
            }
        } else {
            if (stock > 0) {
                cart.push({ id, nama: name, harga_jual: price, jumlah: 1, subtotal: price });
            } else {
                alert('Stok habis!');
                return;
            }
        }
        updateCart();
    }
    
    function updateCart() {
        let html = '';
        if (cart.length === 0) {
            html = '<tr><td colspan="5" class="text-center py-4 text-gray-500">Keranjang masih kosong</td></tr>';
        } else {
            cart.forEach((item, index) => {
                html += `<tr>
                    <td class="py-2">${item.nama}</td>
                    <td class="text-center">${rupiah(item.harga_jual)}</td>
                    <td class="text-center">
                        <input type="number" value="${item.jumlah}" min="1" 
                            onchange="updateQuantity(${index}, this.value)" 
                            class="w-16 text-center border rounded">
                    </td>
                    <td class="text-center">${rupiah(item.subtotal)}</td>
                    <td class="text-center">
                        <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }
        document.getElementById('cartItems').innerHTML = html;
        calculateTotal();
    }
    
    function updateQuantity(index, quantity) {
        quantity = parseInt(quantity);
        if (quantity > 0) {
            cart[index].jumlah = quantity;
            cart[index].subtotal = quantity * cart[index].harga_jual;
            updateCart();
        }
    }
    
    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCart();
    }
    
    function clearCart() {
        if (cart.length > 0 && confirm('Yakin ingin mengosongkan keranjang?')) {
            cart = [];
            updateCart();
        }
    }
    
    function calculateTotal() {
        let subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
        let diskon = parseFloat(document.getElementById('diskon').value.replace(/[^0-9]/g, '')) || 0;
        let pajak = Math.round(subtotal * 0.1);
        
        document.getElementById('pajak').value = formatNumber(pajak);
        
        let total_bayar = subtotal - diskon + pajak;
        
        document.getElementById('displayTotal').innerText = rupiah(subtotal);
        document.getElementById('displayTotalBayar').innerText = rupiah(total_bayar);
        document.getElementById('total').value = subtotal;
        document.getElementById('total_bayar').value = total_bayar;
        
        calculateChange();
    }
    
    function calculateChange() {
        let total_bayar = parseFloat(document.getElementById('total_bayar').value) || 0;
        let uang_bayar = parseFloat(document.getElementById('uang_bayar').value.replace(/[^0-9]/g, '')) || 0;
        let kembalian = uang_bayar - total_bayar;
        
        document.getElementById('displayKembalian').innerText = rupiah(kembalian);
        document.getElementById('uang_kembali').value = kembalian;
    }
    
    function processSale() {
        if (cart.length === 0) { alert('Keranjang masih kosong!'); return; }
        
        let uang_bayar = parseFloat(document.getElementById('uang_bayar').value.replace(/[^0-9]/g, '')) || 0;
        let total_bayar = parseFloat(document.getElementById('total_bayar').value) || 0;
        
        if (uang_bayar < total_bayar) { alert('Uang bayar kurang!'); return; }
        
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="cart_data" value='${JSON.stringify(cart)}'>
            <input type="hidden" name="total" value="${document.getElementById('total').value}">
            <input type="hidden" name="diskon" value="${document.getElementById('diskon').value.replace(/[^0-9]/g, '')}">
            <input type="hidden" name="pajak" value="${document.getElementById('pajak').value.replace(/[^0-9]/g, '')}">
            <input type="hidden" name="uang_bayar" value="${uang_bayar}">
            <input type="hidden" name="uang_kembali" value="${document.getElementById('uang_kembali').value}">
            <input type="hidden" name="process_sale" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
    
    function rupiah(angka) { return 'Rp ' + (angka < 0 ? 0 : angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function formatNumber(angka) { return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    
    document.getElementById('searchProduct').addEventListener('keyup', function() {
        let search = this.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(search) ? 'block' : 'none';
        });
    });
</script>
</body>
</html>