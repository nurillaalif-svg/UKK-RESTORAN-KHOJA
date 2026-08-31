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

// Ambil Menu Favorit (4 Item)
try {
    $stmt = $pdo->query("SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori ORDER BY m.id_menu ASC LIMIT 4");
    $menu_favorit = $stmt->fetchAll();
} catch (Exception $e) {
    $menu_favorit = [];
}
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="hero-badge">
                        <i class="bi bi-stars me-1"></i> Kuliner Timur Tengah Cita Rasa Tinggi
                    </span>
                    <h1 class="hero-title">
                        Nikmati Kelezatan Masakan Timur Tengah Bersama <span class="text-gold">Khoja</span>
                    </h1>
                    <p class="hero-subtitle">
                        Menghadirkan sajian khas Timur Tengah dengan bahan pilihan, rempah aromatik, dan cita rasa autentik.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                        <a href="menu.php" class="btn btn-gold-custom btn-lg shadow-sm">
                            <i class="bi bi-book-half me-2"></i> Lihat Semua Menu
                        </a>
                        <a href="menu.php" class="btn btn-outline-light btn-lg rounded-pill px-4">
                            <i class="bi bi-bag-plus me-2"></i> Pesan Sekarang
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="position-relative d-inline-block">
                        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=700&q=80" alt="Restoran Interior" class="img-fluid rounded-4 shadow-lg border border-3 border-warning">
                        <div class="position-absolute bottom-0 start-0 translate-middle-y bg-dark-green text-white p-3 rounded-4 shadow-lg ms-n3 d-none d-md-block text-start border border-gold">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-award text-gold fs-1"></i>
                                <div>
                                    <h6 class="mb-0 text-gold fw-bold">Best Culinary 2026</h6>
                                    <small class="text-white-50">Kualitas Terjamin 100%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Keunggulan -->
    <section class="py-5 bg-white border-bottom">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light-green-bg h-100">
                        <div class="stat-icon bg-light-green text-white mx-auto mb-3">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <h5 class="fw-bold text-dark-green">Bahan Organik Segar</h5>
                        <p class="text-muted small mb-0">Setiap bahan dipilih langsung dari petani lokal terbaik setiap harinya untuk menjamin kesegaran rasa.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light-green-bg h-100">
                        <div class="stat-icon bg-dark-green text-gold mx-auto mb-3">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold text-dark-green">Chef Berpengalaman</h5>
                        <p class="text-muted small mb-0">Diracik oleh koki bertaraf internasional yang berdedikasi menjaga keautentikan rasa masakan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light-green-bg h-100">
                        <div class="stat-icon bg-gold text-dark-green mx-auto mb-3">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h5 class="fw-bold text-dark-green">Pemesanan Praktis</h5>
                        <p class="text-muted small mb-0">Pesan meja atau take-away secara online dalam hitungan detik dengan metode pembayaran fleksibel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Restoran -->
    <section class="py-5" id="tentang">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=500&q=80" class="img-fluid rounded-4 shadow mb-3" alt="Resto 1">
                            <img src="https://images.unsplash.com/photo-1544025162-d76694265947?w=500&q=80" class="img-fluid rounded-4 shadow" alt="Resto 2">
                        </div>
                        <div class="col-6 pt-4">
                            <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=500&q=80" class="img-fluid rounded-4 shadow mb-3" alt="Resto 3">
                            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=500&q=80" class="img-fluid rounded-4 shadow" alt="Resto 4">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="text-gold font-heading fw-bold">Tentang Kami</span>
                    <h2 class="section-title text-start mb-3">Kehangatan Suasana & Kelezatan Sejati</h2>
                    <p class="text-muted">
                        Didirikan sejak tahun 2018, <strong>Resto Nusantara</strong> berdedikasi melestarikan kekayaan kuliner Indonesia dengan sentuhan penyajian bernuansa modern. Kami percaya bahwa makanan bukan sekadar hidangan, namun sarana mempererat kehangatan keluarga dan sahabat.
                    </p>
                    <p class="text-muted">
                        Setiap piring diproses secara bersih, hygienis, dan disajikan penuh senyuman hangat dalam ruang bernuansa hijau alami dan elegan.
                    </p>
                    <div class="d-flex align-items-center gap-4 mt-4">
                        <div>
                            <h3 class="fw-bold text-dark-green mb-0">15+</h3>
                            <span class="small text-muted">Penghargaan Kuliner</span>
                        </div>
                        <div class="vr"></div>
                        <div>
                            <h3 class="fw-bold text-dark-green mb-0">50K+</h3>
                            <span class="small text-muted">Pelanggan Puas</span>
                        </div>
                        <div class="vr"></div>
                        <div>
                            <h3 class="fw-bold text-dark-green mb-0">100%</h3>
                            <span class="small text-muted">Halal & Hygienis</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Favorit -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-gold font-heading fw-bold">Menu Pilihan</span>
                <h2 class="section-title">Menu Favorit Pelanggan</h2>
                <p class="text-muted">Hidangan terpopuler yang paling dicari dan direkomendasikan minggu ini</p>
            </div>

            <div class="row g-4">
                <?php foreach ($menu_favorit as $m): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="menu-card">
                            <div class="menu-img-wrapper">
                                <img src="<?= get_menu_image($m['gambar']) ?>" alt="<?= htmlspecialchars($m['nama_menu']) ?>">
                                <span class="menu-category-badge"><?= htmlspecialchars($m['nama_kategori']) ?></span>
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h5 class="fw-bold mb-1 font-heading"><?= htmlspecialchars($m['nama_menu']) ?></h5>
                                <p class="text-muted small mb-3 flex-grow-1">
                                    <?= htmlspecialchars(mb_strimwidth($m['deskripsi'], 0, 75, "...")) ?>
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

            <div class="text-center mt-5">
                <a href="menu.php" class="btn btn-dark-custom btn-lg">
                    Lihat Selengkapnya di Daftar Menu <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimoni -->
    <section class="py-5" style="background-color: var(--color-light-green-bg);">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-gold font-heading fw-bold">Ulasan Pengunjung</span>
                <h2 class="section-title">Apa Kata Mereka?</h2>
                <p class="text-muted">Pengalaman berkesan dari para pelanggan setia kami</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="d-flex text-gold mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted small italic">"Nasi Goreng Wagyu-nya benar-benar juara! Dagingnya empuk luar biasa dan bumbunya meresap sempurna. Tempatnya sangat nyaman untuk makan bersama keluarga."</p>
                        <div class="d-flex align-items-center gap-3 mt-auto pt-3 border-top">
                            <div class="bg-dark-green text-gold rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px;">
                                BS
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark-green">Budi Santoso</h6>
                                <small class="text-muted">Food Blogger</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="d-flex text-gold mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted small italic">"Pelayanannya cepat banget dan sistem pesan online lewat HP saat Dine In sangat efisien. Es Matcha Latte Gold-nya sangat menyegarkan!"</p>
                        <div class="d-flex align-items-center gap-3 mt-auto pt-3 border-top">
                            <div class="bg-light-green text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px;">
                                RS
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark-green">Rina Setyowati</h6>
                                <small class="text-muted">Pelanggan Setia</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="d-flex text-gold mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted small italic">"Rendang Daging Sapi Premiumnya autentik bumbu Minang asli. Anak-anak juga suka Pisang Goreng Keju Karamel-nya. Sangat direkomendasikan!"</p>
                        <div class="d-flex align-items-center gap-3 mt-auto pt-3 border-top">
                            <div class="bg-gold text-dark-green rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px;">
                                HP
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark-green">Hendra Pratama</h6>
                                <small class="text-muted">Pengusaha</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
