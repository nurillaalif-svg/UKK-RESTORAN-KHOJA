<?php
require_once __DIR__ . '/header.php';

$success_msg = "";
$error_msg   = "";

// 1. Handle Action Tambah Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if (empty($nama_kategori)) {
        $error_msg = "Nama Kategori tidak boleh kosong!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori) VALUES (:nama)");
            $stmt->execute([':nama' => $nama_kategori]);
            $success_msg = "Kategori '$nama_kategori' berhasil ditambahkan!";
        } catch (Exception $e) {
            $error_msg = "Gagal menambahkan kategori: " . $e->getMessage();
        }
    }
}

// 2. Handle Action Edit Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id_kategori   = intval($_POST['id_kategori'] ?? 0);
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if ($id_kategori > 0 && !empty($nama_kategori)) {
        try {
            $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = :nama WHERE id_kategori = :id");
            $stmt->execute([':nama' => $nama_kategori, ':id' => $id_kategori]);
            $success_msg = "Kategori berhasil diperbarui!";
        } catch (Exception $e) {
            $error_msg = "Gagal mengedit kategori: " . $e->getMessage();
        }
    } else {
        $error_msg = "Data kategori tidak valid!";
    }
}

// 3. Handle Action Hapus Kategori
if (isset($_GET['delete'])) {
    $id_del = intval($_GET['delete']);
    if ($id_del > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = :id");
            $stmt->execute([':id' => $id_del]);
            $success_msg = "Kategori berhasil dihapus!";
        } catch (Exception $e) {
            $error_msg = "Gagal menghapus kategori: " . $e->getMessage();
        }
    }
}

// Ambil Seluruh Data Kategori
try {
    $categories = $pdo->query("SELECT k.*, (SELECT COUNT(*) FROM menu m WHERE m.id_kategori = k.id_kategori) as jumlah_menu FROM kategori k ORDER BY k.id_kategori ASC")->fetchAll();
} catch (Exception $e) {
    $categories = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-heading fw-bold text-dark-green mb-0">Kelola Kategori Menu</h2>
        <p class="text-muted small mb-0">Manajemen kategori hidangan restoran (Makanan, Minuman, Dessert, dll)</p>
    </div>
    <button type="button" class="btn btn-dark-custom" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
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

<!-- Tabel Kategori -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">No</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Menu Terkait</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categories) > 0): ?>
                    <?php $no = 1; foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-bold text-dark-green"><?= htmlspecialchars($cat['nama_kategori']) ?></td>
                            <td>
                                <span class="badge bg-light-green text-white px-3 py-1 rounded-pill">
                                    <?= $cat['jumlah_menu'] ?> Menu
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning rounded-circle p-2 me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditKategori<?= $cat['id_kategori'] ?>" 
                                        title="Edit Kategori">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="kategori.php?delete=<?= $cat['id_kategori'] ?>" 
                                   class="btn btn-sm btn-outline-danger rounded-circle p-2" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Seluruh menu di kategori ini juga akan terhapus!');" 
                                   title="Hapus Kategori">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit Kategori -->
                        <div class="modal fade" id="modalEditKategori<?= $cat['id_kategori'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="kategori.php" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id_kategori" value="<?= $cat['id_kategori'] ?>">
                                        <div class="modal-header bg-dark-green text-white">
                                            <h5 class="modal-title font-heading fw-bold">Edit Kategori</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark-green">Nama Kategori</label>
                                                <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($cat['nama_kategori']) ?>" required>
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
                        <td colspan="4" class="text-center text-muted py-4">Belum ada kategori ditambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form action="kategori.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header bg-dark-green text-white">
                    <h5 class="modal-title font-heading fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark-green">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Makanan Utama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark-custom">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
