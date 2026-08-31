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

// Search & Filter Param
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$kat_id = isset($_GET['kat']) ? intval($_GET['kat']) : 0;

// Ambil Data Kategori
try {
    $stmt_kat = $pdo->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
    $categories = $stmt_kat->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

// Build Dynamic SQL Query for Menu
$query = "SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (m.nama_menu LIKE :q OR m.deskripsi LIKE :q)";
    $params[':q'] = "%$search%";
}

if ($kat_id > 0) {
    $query .= " AND m.id_kategori = :kat";
    $params[':kat'] = $kat_id;
}

$query .= " ORDER BY m.id_menu DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $menus = $stmt->fetchAll();
} catch (Exception $e) {
    $menus = [];
}
?>

<main class="py-5">
    <div class="container">
        
        <!-- Section Header -->
        <div class="text-center mb-4">
            <span class="text-gold font-heading fw-bold">Pilihan Kuliner</span>
            <h1 class="section-title">Daftar Menu Restoran</h1>
            <p class="text-muted">Jelajahi kelezatan hidangan pembuka, makanan utama, hingga penutup manis kami</p>
        </div>

        <!-- Filter & Search Bar Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 bg-white">
            <div class="row g-3 align-items-center">
                <!-- Search Box -->
                <div class="col-lg-5">
                    <form action="menu.php" method="GET" class="d-flex">
                        <?php if ($kat_id > 0): ?>
                            <input type="hidden" name="kat" value="<?= $kat_id ?>">
                        <?php endif; ?>
                        <div class="input-group">
                            <span class="input-group-text bg-light-green-bg border-end-0 text-dark-green rounded-start-pill ps-3">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="q" class="form-control bg-light-green-bg border-start-0 py-2" placeholder="Cari nama menu atau deskripsi..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-dark-custom rounded-end-pill px-4">Cari</button>
                        </div>
                    </form>
                </div>

                <!-- Category Filter Pills -->
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-1">
                        <a href="menu.php<?= !empty($search) ? '?q='.urlencode($search) : '' ?>" class="filter-btn <?= ($kat_id === 0) ? 'active' : '' ?>">
                            <i class="bi bi-grid-fill me-1"></i> Semua
                        </a>
                        <?php foreach ($categories as $c): ?>
                            <?php 
                                $url = "menu.php?kat=" . $c['id_kategori'];
                                if (!empty($search)) {
                                    $url .= "&q=" . urlencode($search);
                                }
                            ?>
                            <a href="<?= $url ?>" class="filter-btn <?= ($kat_id === $c['id_kategori']) ? 'active' : '' ?>">
                                <?= htmlspecialchars($c['nama_kategori']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Grid -->
        <?php if (count($menus) > 0): ?>
            <div class="row g-4">
                <?php foreach ($menus as $m): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="menu-card">
                            <div class="menu-img-wrapper">
                                <img src="<?= get_menu_image($m['gambar']) ?>" alt="<?= htmlspecialchars($m['nama_menu']) ?>">
                                <span class="menu-category-badge"><?= htmlspecialchars($m['nama_kategori']) ?></span>
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h5 class="fw-bold mb-1 font-heading"><?= htmlspecialchars($m['nama_menu']) ?></h5>
                                <p class="text-muted small mb-3 flex-grow-1">
                                    <?= htmlspecialchars(mb_strimwidth($m['deskripsi'], 0, 80, "...")) ?>
                                </p>
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                    <span class="menu-price-tag"><?= format_rupiah($m['harga']) ?></span>
                                    <div class="d-flex gap-1">
                                        <a href="detail_menu.php?id=<?= $m['id_menu'] ?>" class="btn btn-outline-custom btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="keranjang.php?act=add&id=<?= $m['id_menu'] ?>" class="btn btn-primary-custom btn-sm">
                                            <i class="bi bi-cart-plus me-1"></i> Pesan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                <i class="bi bi-emoji-frown text-muted display-1"></i>
                <h4 class="mt-3 fw-bold text-dark-green">Menu Tidak Ditemukan</h4>
                <p class="text-muted">Maaf, menu yang Anda cari tidak tersedia atau belum dimasukkan.</p>
                <a href="menu.php" class="btn btn-gold-custom">Tampilkan Semua Menu</a>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
