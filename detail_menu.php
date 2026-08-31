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

$id_menu = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_menu <= 0) {
    header("Location: menu.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori WHERE m.id_menu = :id");
    $stmt->execute([':id' => $id_menu]);
    $menu = $stmt->fetch();

    if (!$menu) {
        header("Location: menu.php");
        exit;
    }

    // Ambil Menu Terkait (Kategori sama)
    $stmt_rel = $pdo->prepare("SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori WHERE m.id_kategori = :kat AND m.id_menu != :id LIMIT 3");
    $stmt_rel->execute([':kat' => $menu['id_kategori'], ':id' => $id_menu]);
    $related = $stmt_rel->fetchAll();

} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

<main class="py-5">
    <div class="container">
        
        <!-- Breadcrumb Nav -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-dark-green">Beranda</a></li>
                <li class="breadcrumb-item"><a href="menu.php" class="text-dark-green">Daftar Menu</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($menu['nama_menu']) ?></li>
            </ol>
        </nav>

        <!-- Detail Product Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="position-relative h-100" style="min-height: 380px;">
                        <img src="<?= get_menu_image($menu['gambar']) ?>" alt="<?= htmlspecialchars($menu['nama_menu']) ?>" class="w-100 h-100 object-fit-cover">
                        <span class="badge bg-dark-green text-gold position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill fs-6">
                            <?= htmlspecialchars($menu['nama_kategori']) ?>
                        </span>
                    </div>
                </div>
                <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-between">
                    <div>
                        <span class="text-gold font-heading fw-bold">Detail Kuliner</span>
                        <h1 class="display-6 font-heading fw-bold text-dark-green mb-2"><?= htmlspecialchars($menu['nama_menu']) ?></h1>
                        
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="fs-2 fw-bold text-dark-green"><?= format_rupiah($menu['harga']) ?></span>
                            <span class="badge bg-light-green-bg text-light-green border border-success px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Tersedia
                            </span>
                        </div>

                        <hr>

                        <h6 class="fw-bold text-dark-green">Deskripsi Menu:</h6>
                        <p class="text-muted leading-relaxed mb-4">
                            <?= nl2br(htmlspecialchars($menu['deskripsi'])) ?>
                        </p>
                    </div>

                    <!-- Form Tambah ke Keranjang -->
                    <form action="keranjang.php" method="GET" class="pt-3 border-top">
                        <input type="hidden" name="act" value="add">
                        <input type="hidden" name="id" value="<?= $menu['id_menu'] ?>">

                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="qty" class="col-form-label fw-bold text-dark-green">Jumlah:</label>
                            </div>
                            <div class="col-auto">
                                <div class="input-group" style="width: 130px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="var q=document.getElementById('qty'); if(q.value>1) q.value--;">-</button>
                                    <input type="number" id="qty" name="qty" class="form-control text-center fw-bold" value="1" min="1" max="50">
                                    <button class="btn btn-outline-secondary" type="button" onclick="var q=document.getElementById('qty'); q.value++;">+</button>
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-dark-custom w-100 py-2">
                                    <i class="bi bi-cart-plus me-2"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Menu Terkait -->
        <?php if (count($related) > 0): ?>
            <div class="mt-5">
                <h3 class="font-heading fw-bold text-dark-green mb-4">Menu Sejenis Lainnya</h3>
                <div class="row g-4">
                    <?php foreach ($related as $r): ?>
                        <div class="col-md-4">
                            <div class="menu-card">
                                <div class="menu-img-wrapper">
                                    <img src="<?= get_menu_image($r['gambar']) ?>" alt="<?= htmlspecialchars($r['nama_menu']) ?>">
                                    <span class="menu-category-badge"><?= htmlspecialchars($r['nama_kategori']) ?></span>
                                </div>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="fw-bold mb-1 font-heading"><?= htmlspecialchars($r['nama_menu']) ?></h5>
                                    <p class="text-muted small mb-3 flex-grow-1"><?= htmlspecialchars(mb_strimwidth($r['deskripsi'], 0, 70, "...")) ?></p>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                        <span class="menu-price-tag"><?= format_rupiah($r['harga']) ?></span>
                                        <a href="detail_menu.php?id=<?= $r['id_menu'] ?>" class="btn btn-primary-custom btn-sm">
                                            Detail & Pesan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
