<?php
require_once __DIR__ . '/header.php';

// Helper image fallback
if (!function_exists('get_menu_image')) {
    function get_menu_image($gambar) {
        $path = "assets/images/" . $gambar;
        if (!empty($gambar) && file_exists(__DIR__ . "/" . $path)) {
            return $path;
        }
        if (stristr($gambar, 'matcha') || stristr($gambar, 'teh') || stristr($gambar, 'minuman')) {
            return 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&q=80';
        } elseif (stristr($gambar, 'pisang') || stristr($gambar, 'panna') || stristr($gambar, 'dessert')) {
            return 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&q=80';
        } elseif (stristr($gambar, 'wagyu') || stristr($gambar, 'rendang') || stristr($gambar, 'ayam')) {
            return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=80';
        }
        return 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&q=80';
    }
}

// Inisialisasi Keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$action = isset($_GET['act']) ? $_GET['act'] : '';

// 1. Action: Tambah Menu ke Keranjang
if ($action === 'add') {
    $id_menu = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $qty_add = isset($_GET['qty']) ? max(1, intval($_GET['qty'])) : 1;

    if ($id_menu > 0) {
        $stmt = $pdo->prepare("SELECT * FROM menu WHERE id_menu = :id");
        $stmt->execute([':id' => $id_menu]);
        $menu = $stmt->fetch();

        if ($menu) {
            if (isset($_SESSION['keranjang'][$id_menu])) {
                $_SESSION['keranjang'][$id_menu]['qty'] += $qty_add;
            } else {
                $_SESSION['keranjang'][$id_menu] = [
                    'id_menu' => $menu['id_menu'],
                    'nama_menu' => $menu['nama_menu'],
                    'harga' => $menu['harga'],
                    'gambar' => $menu['gambar'],
                    'qty' => $qty_add
                ];
            }
        }
    }
    header("Location: keranjang.php");
    exit;
}

// 2. Action: Update Qty
if ($action === 'update') {
    $id_menu = isset($_POST['id_menu']) ? intval($_POST['id_menu']) : 0;
    $qty_new = isset($_POST['qty']) ? intval($_POST['qty']) : 1;

    if ($id_menu > 0 && isset($_SESSION['keranjang'][$id_menu])) {
        if ($qty_new <= 0) {
            unset($_SESSION['keranjang'][$id_menu]);
        } else {
            $_SESSION['keranjang'][$id_menu]['qty'] = $qty_new;
        }
    }
    header("Location: keranjang.php");
    exit;
}

// 3. Action: Hapus Item
if ($action === 'delete') {
    $id_menu = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id_menu > 0 && isset($_SESSION['keranjang'][$id_menu])) {
        unset($_SESSION['keranjang'][$id_menu]);
    }
    header("Location: keranjang.php");
    exit;
}

// 4. Action: Kosongkan Keranjang
if ($action === 'clear') {
    $_SESSION['keranjang'] = [];
    header("Location: keranjang.php");
    exit;
}

// Hitung Grand Total
$grand_total = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $grand_total += ($item['harga'] * $item['qty']);
}
?>

<main class="py-5">
    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="text-gold font-heading fw-bold">Pesanan Anda</span>
                <h1 class="font-heading fw-bold text-dark-green mb-0">Keranjang Belanja</h1>
            </div>
            <?php if (!empty($_SESSION['keranjang'])): ?>
                <a href="keranjang.php?act=clear" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Yakin ingin mengosongkan keranjang?');">
                    <i class="bi bi-trash me-1"></i> Kosongkan Keranjang
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['keranjang'])): ?>
            <div class="row g-4">
                <!-- Daftar Item Tabel -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="table-responsive">
                            <table class="table custom-table align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small border-bottom">
                                        <th>Menu</th>
                                        <th>Harga</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['keranjang'] as $id => $item): 
                                        $subtotal = $item['harga'] * $item['qty'];
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= get_menu_image($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_menu']) ?>" class="rounded-3 object-fit-cover" style="width: 60px; height: 60px;">
                                                    <div>
                                                        <h6 class="fw-bold text-dark-green mb-0"><?= htmlspecialchars($item['nama_menu']) ?></h6>
                                                        <small class="text-muted">ID: #<?= $item['id_menu'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-semibold text-secondary">
                                                <?= format_rupiah($item['harga']) ?>
                                            </td>
                                            <td class="text-center">
                                                <form action="keranjang.php?act=update" method="POST" class="d-inline-flex align-items-center">
                                                    <input type="hidden" name="id_menu" value="<?= $id ?>">
                                                    <div class="qty-control">
                                                        <a href="keranjang.php?act=update&id_menu=<?= $id ?>&qty=<?= $item['qty']-1 ?>" class="qty-btn text-decoration-none">-</a>
                                                        <input type="text" name="qty" class="qty-input" value="<?= $item['qty'] ?>" readonly>
                                                        <a href="keranjang.php?act=update&id_menu=<?= $id ?>&qty=<?= $item['qty']+1 ?>" class="qty-btn text-decoration-none">+</a>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold text-dark-green">
                                                <?= format_rupiah($subtotal) ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="keranjang.php?act=delete&id=<?= $id ?>" class="btn btn-sm btn-light text-danger rounded-circle p-2" title="Hapus Item" onclick="return confirm('Hapus item ini dari keranjang?');">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="menu.php" class="btn btn-outline-custom">
                            <i class="bi bi-arrow-left me-1"></i> Tambah Menu Lain
                        </a>
                    </div>
                </div>

                <!-- Ringkasan Pembayaran & Checkout -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px;">
                        <h5 class="fw-bold font-heading text-dark-green mb-4 pb-2 border-bottom">Ringkasan Pesanan</h5>
                        
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Total Item:</span>
                            <span class="fw-bold text-dark me-1"><?= array_sum(array_column($_SESSION['keranjang'], 'qty')) ?> porsi</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Pajak & Layanan:</span>
                            <span class="text-success fw-bold">Gratis</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-5 text-dark-green">Total Bayar:</span>
                            <span class="fw-bold fs-4 text-gold"><?= format_rupiah($grand_total) ?></span>
                        </div>

                        <a href="checkout.php" class="btn btn-dark-custom btn-lg w-100 py-3 font-heading shadow-sm">
                            Lanjut ke Checkout <i class="bi bi-arrow-right ms-2"></i>
                        </a>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-success me-1"></i> Transaksi Aman & Terverifikasi
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Keranjang Kosong State -->
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-5">
                <div class="stat-icon bg-light-green-bg text-dark-green mx-auto mb-3" style="width: 90px; height: 90px; font-size: 2.8rem;">
                    <i class="bi bi-cart-x"></i>
                </div>
                <h3 class="fw-bold text-dark-green mb-2 font-heading">Keranjang Belanja Anda Kosong</h3>
                <p class="text-muted mb-4">Anda belum menambahkan menu makanan atau minuman ke dalam keranjang.</p>
                <div>
                    <a href="menu.php" class="btn btn-gold-custom btn-lg px-4">
                        <i class="bi bi-journal-richtext me-2"></i> Jelajahi Menu Restoran
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
