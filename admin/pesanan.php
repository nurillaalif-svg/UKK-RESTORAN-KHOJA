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

// 1. Handle Update Status Pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id_pesanan = intval($_POST['id_pesanan'] ?? 0);
    $status_baru = trim($_POST['status_baru'] ?? '');

    $allowed_status = ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Siap Diambil', 'Selesai', 'Dibatalkan'];

    if ($id_pesanan > 0 && in_array($status_baru, $allowed_status)) {
        try {
            $stmt = $pdo->prepare("UPDATE pesanan SET status = :st WHERE id_pesanan = :id");
            $stmt->execute([':st' => $status_baru, ':id' => $id_pesanan]);
            $success_msg = "Status pesanan berhasil diubah menjadi '$status_baru'!";
        } catch (Exception $e) {
            $error_msg = "Gagal memperbarui status: " . $e->getMessage();
        }
    }
}

// Filter Status Param
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build Query
$query = "SELECT * FROM pesanan WHERE 1=1";
$params = [];

if (!empty($filter_status)) {
    $query .= " AND status = :st";
    $params[':st'] = $filter_status;
}

$query .= " ORDER BY id_pesanan DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
} catch (Exception $e) {
    $orders = [];
}

// Badge Status Helper
function get_status_badge_admin($status) {
    switch ($status) {
        case 'Menunggu Pembayaran': return 'bg-warning text-dark';
        case 'Menunggu Verifikasi': return 'bg-info text-dark';
        case 'Diproses': return 'bg-primary text-white';
        case 'Siap Diambil': return 'bg-success text-white';
        case 'Selesai': return 'bg-dark text-gold';
        case 'Dibatalkan': return 'bg-danger text-white';
        default: return 'bg-secondary text-white';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-heading fw-bold text-dark-green mb-0">Kelola Pesanan Pelanggan</h2>
        <p class="text-muted small mb-0">Manajemen status transaksi, verifikasi pembayaran, dan detail pesanan</p>
    </div>
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

<!-- Filter Pills Status -->
<div class="mb-4">
    <div class="d-flex flex-wrap gap-2">
        <a href="pesanan.php" class="btn btn-sm <?= empty($filter_status) ? 'btn-dark-custom' : 'btn-outline-secondary' ?> rounded-pill">Semua Status</a>
        <a href="pesanan.php?status=Menunggu+Pembayaran" class="btn btn-sm <?= ($filter_status === 'Menunggu Pembayaran') ? 'btn-dark-custom' : 'btn-outline-secondary' ?> rounded-pill">Menunggu Pembayaran</a>
        <a href="pesanan.php?status=Menunggu+Verifikasi" class="btn btn-sm <?= ($filter_status === 'Menunggu Verifikasi') ? 'btn-dark-custom' : 'btn-outline-secondary' ?> rounded-pill">Menunggu Verifikasi</a>
        <a href="pesanan.php?status=Diproses" class="btn btn-sm <?= ($filter_status === 'Diproses') ? 'btn-dark-custom' : 'btn-outline-secondary' ?> rounded-pill">Diproses</a>
        <a href="pesanan.php?status=Siap+Diambil" class="btn btn-sm <?= ($filter_status === 'Siap Diambil') ? 'btn-dark-custom' : 'btn-outline-secondary' ?> rounded-pill">Siap Diambil</a>
        <a href="pesanan.php?status=Selesai" class="btn btn-sm <?= ($filter_status === 'Selesai') ? 'btn-dark-custom' : 'btn-outline-secondary' ?> rounded-pill">Selesai</a>
        <a href="pesanan.php?status=Dibatalkan" class="btn btn-sm <?= ($filter_status === 'Dibatalkan') ? 'btn-dark-custom' : 'btn-outline-secondary' ?> rounded-pill">Dibatalkan</a>
    </div>
</div>

<!-- Tabel Pesanan -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small">
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Tipe / Lokasi</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Status Saat Ini</th>
                    <th>Ubah Status</th>
                    <th class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $ord): 
                        // Ambil Detail Items
                        $stmt_det = $pdo->prepare("SELECT d.*, m.nama_menu, m.gambar, m.harga FROM detail_pesanan d JOIN menu m ON d.id_menu = m.id_menu WHERE d.id_pesanan = :id");
                        $stmt_det->execute([':id' => $ord['id_pesanan']]);
                        $items = $stmt_det->fetchAll();
                    ?>
                        <tr>
                            <td class="fw-bold text-dark-green font-monospace">
                                <?= htmlspecialchars($ord['kode_pesanan']) ?>
                                <small class="d-block text-muted font-sans-serif fw-normal"><?= date('d/m/Y H:i', strtotime($ord['tanggal'])) ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($ord['nama_pelanggan']) ?></strong>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $ord['no_hp']) ?>" target="_blank" class="d-block small text-success text-decoration-none">
                                    <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($ord['no_hp']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($ord['tipe_pesanan']) ?></span>
                                <?php if ($ord['tipe_pesanan'] === 'Dine In'): ?>
                                    <small class="d-block text-success fw-bold"><?= htmlspecialchars($ord['nomor_meja']) ?></small>
                                <?php else: ?>
                                    <small class="d-block text-muted" style="max-width: 150px;"><?= htmlspecialchars(mb_strimwidth($ord['alamat'], 0, 40, "...")) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-gold text-dark-green fw-bold"><?= htmlspecialchars($ord['metode_pembayaran']) ?></span>
                            </td>
                            <td class="fw-bold text-dark-green"><?= format_rupiah($ord['total']) ?></td>
                            <td>
                                <span class="badge <?= get_status_badge_admin($ord['status']) ?> px-2 py-1">
                                    <?= htmlspecialchars($ord['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form action="pesanan.php<?= !empty($filter_status) ? '?status='.urlencode($filter_status) : '' ?>" method="POST" class="d-flex align-items-center gap-1">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="id_pesanan" value="<?= $ord['id_pesanan'] ?>">
                                    <select name="status_baru" class="form-select form-select-sm" style="min-width: 140px;">
                                        <option value="Menunggu Pembayaran" <?= ($ord['status'] === 'Menunggu Pembayaran') ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                                        <option value="Menunggu Verifikasi" <?= ($ord['status'] === 'Menunggu Verifikasi') ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                                        <option value="Diproses" <?= ($ord['status'] === 'Diproses') ? 'selected' : '' ?>>Diproses</option>
                                        <option value="Siap Diambil" <?= ($ord['status'] === 'Siap Diambil') ? 'selected' : '' ?>>Siap Diambil</option>
                                        <option value="Selesai" <?= ($ord['status'] === 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                        <option value="Dibatalkan" <?= ($ord['status'] === 'Dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-dark-custom" title="Update Status">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-info rounded-circle p-2" data-bs-toggle="modal" data-bs-target="#modalDetailPesanan<?= $ord['id_pesanan'] ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail Pesanan -->
                        <div class="modal fade" id="modalDetailPesanan<?= $ord['id_pesanan'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header bg-dark-green text-white">
                                        <h5 class="modal-title font-heading fw-bold">Detail Pesanan #<?= htmlspecialchars($ord['kode_pesanan']) ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Nama Pelanggan:</small>
                                                <strong><?= htmlspecialchars($ord['nama_pelanggan']) ?> (<?= htmlspecialchars($ord['no_hp']) ?>)</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Tipe Pemesanan:</small>
                                                <strong><?= htmlspecialchars($ord['tipe_pesanan']) ?> <?= ($ord['tipe_pesanan'] === 'Dine In') ? '('.htmlspecialchars($ord['nomor_meja']).')' : '' ?></strong>
                                            </div>
                                            <?php if ($ord['tipe_pesanan'] === 'Take Away'): ?>
                                                <div class="col-12">
                                                    <small class="text-muted d-block">Alamat Pengiriman:</small>
                                                    <div><?= nl2br(htmlspecialchars($ord['alamat'])) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($ord['catatan'])): ?>
                                                <div class="col-12">
                                                    <small class="text-muted d-block">Catatan Tambahan:</small>
                                                    <em class="text-secondary"><?= htmlspecialchars($ord['catatan']) ?></em>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <h6 class="fw-bold text-dark-green font-heading mb-2">Daftar Menu Dipesan:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Item Menu</th>
                                                        <th>Harga</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-end">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($items as $it): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <img src="<?= get_menu_image_admin($it['gambar']) ?>" alt="" style="width: 35px; height: 35px; object-fit: cover;" class="rounded">
                                                                    <span><?= htmlspecialchars($it['nama_menu']) ?></span>
                                                                </div>
                                                            </td>
                                                            <td><?= format_rupiah($it['harga']) ?></td>
                                                            <td class="text-center fw-bold"><?= $it['qty'] ?></td>
                                                            <td class="text-end fw-bold"><?= format_rupiah($it['subtotal']) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold">Total Pembayaran:</td>
                                                        <td class="text-end fw-bold fs-5 text-gold"><?= format_rupiah($ord['total']) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada pesanan ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
