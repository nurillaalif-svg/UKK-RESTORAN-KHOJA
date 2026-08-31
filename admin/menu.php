<?php
require_once __DIR__ . '/header.php';

// Helper image fallback
if (!function_exists('get_menu_image_admin')) {
    function get_menu_image_admin($gambar) {
        $path = "../assets/images/" . $gambar;
        if (!empty($gambar) && file_exists(__DIR__ . "/" . $path)) {
            return $path;
        }
        if (stristr($gambar, 'matcha') || stristr($gambar, 'teh') || stristr($gambar, 'minuman')) {
            return 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400&q=80';
        } elseif (stristr($gambar, 'pisang') || stristr($gambar, 'panna') || stristr($gambar, 'dessert')) {
            return 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=400&q=80';
        } elseif (stristr($gambar, 'wagyu') || stristr($gambar, 'rendang') || stristr($gambar, 'ayam')) {
            return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80';
        }
        return 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&q=80';
    }
}

$success_msg = "";
$error_msg   = "";

// Ambil Kategori untuk Dropdown Select
try {
    $categories = $pdo->query("SELECT * FROM kategori ORDER BY id_kategori ASC")->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

// 1. Handle Action Tambah Menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nama_menu   = trim($_POST['nama_menu'] ?? '');
    $id_kategori = intval($_POST['id_kategori'] ?? 0);
    $harga       = floatval($_POST['harga'] ?? 0);
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $gambar_nama = 'default.jpg';

    if (empty($nama_menu) || $id_kategori <= 0 || $harga <= 0) {
        $error_msg = "Nama Menu, Kategori, dan Harga wajib diisi!";
    } else {
        // Upload Gambar Handling
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_' . uniqid() . '.' . $ext;
                $target_dir   = __DIR__ . '/../assets/images/';
                
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $new_filename)) {
                    $gambar_nama = $new_filename;
                }
            } else {
                $error_msg = "Format gambar tidak didukung! Gunakan JPG, PNG, atau WEBP.";
            }
        }

        if (empty($error_msg)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO menu (id_kategori, nama_menu, harga, deskripsi, gambar) VALUES (:kat, :nama, :harga, :desk, :img)");
                $stmt->execute([
                    ':kat'   => $id_kategori,
                    ':nama'  => $nama_menu,
                    ':harga' => $harga,
                    ':desk'  => $deskripsi,
                    ':img'   => $gambar_nama
                ]);
                $success_msg = "Menu '$nama_menu' berhasil ditambahkan!";
            } catch (Exception $e) {
                $error_msg = "Gagal menambahkan menu: " . $e->getMessage();
            }
        }
    }
}

// 2. Handle Action Edit Menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id_menu     = intval($_POST['id_menu'] ?? 0);
    $nama_menu   = trim($_POST['nama_menu'] ?? '');
    $id_kategori = intval($_POST['id_kategori'] ?? 0);
    $harga       = floatval($_POST['harga'] ?? 0);
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $gambar_lama = trim($_POST['gambar_lama'] ?? 'default.jpg');
    $gambar_nama = $gambar_lama;

    if ($id_menu > 0 && !empty($nama_menu) && $id_kategori > 0 && $harga > 0) {
        // Upload Gambar Baru (Jika ada)
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_' . uniqid() . '.' . $ext;
                $target_dir   = __DIR__ . '/../assets/images/';

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $new_filename)) {
                    $gambar_nama = $new_filename;
                }
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE menu SET id_kategori = :kat, nama_menu = :nama, harga = :harga, deskripsi = :desk, gambar = :img WHERE id_menu = :id");
            $stmt->execute([
                ':kat'   => $id_kategori,
                ':nama'  => $nama_menu,
                ':harga' => $harga,
                ':desk'  => $deskripsi,
                ':img'   => $gambar_nama,
                ':id'    => $id_menu
            ]);
            $success_msg = "Data menu berhasil diperbarui!";
        } catch (Exception $e) {
            $error_msg = "Gagal mengedit menu: " . $e->getMessage();
        }
    } else {
        $error_msg = "Mohon isi semua data menu secara lengkap!";
    }
}

// 3. Handle Action Hapus Menu
if (isset($_GET['delete'])) {
    $id_del = intval($_GET['delete']);
    if ($id_del > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM menu WHERE id_menu = :id");
            $stmt->execute([':id' => $id_del]);
            $success_msg = "Menu berhasil dihapus!";
        } catch (Exception $e) {
            $error_msg = "Gagal menghapus menu: " . $e->getMessage();
        }
    }
}

// Ambil Seluruh Data Menu
try {
    $stmt_m = $pdo->query("SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori ORDER BY m.id_menu DESC");
    $menus = $stmt_m->fetchAll();
} catch (Exception $e) {
    $menus = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-heading fw-bold text-dark-green mb-0">Kelola Menu Kuliner</h2>
        <p class="text-muted small mb-0">Manajemen makanan, minuman, dan gambar menu restoran</p>
    </div>
    <button type="button" class="btn btn-dark-custom" data-bs-toggle="modal" data-bs-target="#modalTambahMenu">
        <i class="bi bi-plus-lg me-1"></i> Tambah Menu Baru
    </button>
</div>

<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-2"></i> <?= htmlspecialchars($error_msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tabel Kelola Menu -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small">
                <tr>
                    <th style="width: 70px;">Foto</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($menus) > 0): ?>
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td>
                                <img src="<?= get_menu_image_admin($m['gambar']) ?>" alt="<?= htmlspecialchars($m['nama_menu']) ?>" class="rounded-3" style="width: 55px; height: 55px; object-fit: cover;">
                            </td>
                            <td class="fw-bold text-dark-green"><?= htmlspecialchars($m['nama_menu']) ?></td>
                            <td>
                                <span class="badge bg-gold text-dark-green fw-bold"><?= htmlspecialchars($m['nama_kategori']) ?></span>
                            </td>
                            <td class="fw-semibold text-success"><?= format_rupiah($m['harga']) ?></td>
                            <td class="small text-muted" style="max-width: 250px;">
                                <?= htmlspecialchars(mb_strimwidth($m['deskripsi'], 0, 70, "...")) ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning rounded-circle p-2 me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditMenu<?= $m['id_menu'] ?>" 
                                        title="Edit Menu">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="menu.php?delete=<?= $m['id_menu'] ?>" 
                                   class="btn btn-sm btn-outline-danger rounded-circle p-2" 
                                   onclick="return confirm('Yakin ingin menghapus menu ini?');" 
                                   title="Hapus Menu">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit Menu -->
                        <div class="modal fade" id="modalEditMenu<?= $m['id_menu'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="menu.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($m['gambar']) ?>">
                                        
                                        <div class="modal-header bg-dark-green text-white">
                                            <h5 class="modal-title font-heading fw-bold">Edit Menu</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark-green">Nama Menu</label>
                                                        <input type="text" name="nama_menu" class="form-control" value="<?= htmlspecialchars($m['nama_menu']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark-green">Kategori</label>
                                                        <select name="id_kategori" class="form-select" required>
                                                            <?php foreach ($categories as $cat): ?>
                                                                <option value="<?= $cat['id_kategori'] ?>" <?= ($cat['id_kategori'] == $m['id_kategori']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($cat['nama_kategori']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark-green">Harga (Rp)</label>
                                                        <input type="number" name="harga" class="form-control" value="<?= $m['harga'] ?>" min="0" step="500" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark-green">Ganti Gambar (Opsional)</label>
                                                        <input type="file" name="gambar" class="form-control" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark-green">Deskripsi Menu</label>
                                                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($m['deskripsi']) ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-dark-custom">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada menu tersimpan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Menu Baru -->
<div class="modal fade" id="modalTambahMenu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="menu.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-header bg-dark-green text-white">
                    <h5 class="modal-title font-heading fw-bold">Tambah Menu Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark-green">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text" name="nama_menu" class="form-control" placeholder="Contoh: Sate Ayam Madura" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark-green">Kategori <span class="text-danger">*</span></label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id_kategori'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark-green">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga" class="form-control" placeholder="Contoh: 35000" min="0" step="500" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark-green">Foto Menu</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark-green">Deskripsi Menu</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan citarasa, porsi, atau keunikan menu..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark-custom">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
