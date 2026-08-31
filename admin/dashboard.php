<?php
require_once __DIR__ . '/header.php';

// Ambil Statistik Counter
try {
    // 1. Total Menu
    $total_menu = $pdo->query("SELECT COUNT(*) FROM menu")->fetchColumn();

    // 2. Total Pesanan
    $total_pesanan = $pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();

    // 3. Total Kategori
    $total_kategori = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();

    // 4. Total Omset (Pesanan Selesai / Diproses / Siap Diambil / Menunggu Verifikasi)
    $total_omset = $pdo->query("SELECT SUM(total) FROM pesanan WHERE status != 'Dibatalkan'")->fetchColumn() ?: 0;

    // 5. Ambil 5 Pesanan Terbaru
    $stmt_recent = $pdo->query("SELECT * FROM pesanan ORDER BY id_pesanan DESC LIMIT 5");
    $recent_orders = $stmt_recent->fetchAll();

} catch (Exception $e) {
    $total_menu = $total_pesanan = $total_kategori = $total_omset = 0;
    $recent_orders = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-heading fw-bold text-dark-green mb-0">Dashboard Overview</h2>
        <p class="text-muted small mb-0">Ringkasan aktivitas restoran dan laporan pemesanan hari ini</p>
    </div>
    <span class="badge bg-dark-green text-gold p-2 font-monospace">
        <i class="bi bi-calendar3 me-1"></i> <?= date('d F Y') ?>
    </span>
</div>

<!-- Stat Cards Row -->
<div class="row g-4 mb-4">
    <!-- Stat 1: Total Menu -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-white p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-light-green text-white">
                    <i class="bi bi-egg-fried"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total Menu</span>
                    <h3 class="fw-bold text-dark-green mb-0"><?= number_format($total_menu) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Total Pesanan -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-white p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-dark-green text-gold">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total Pesanan</span>
                    <h3 class="fw-bold text-dark-green mb-0"><?= number_format($total_pesanan) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Total Kategori -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-white p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-gold text-dark-green">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Kategori Menu</span>
                    <h3 class="fw-bold text-dark-green mb-0"><?= number_format($total_kategori) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Omset -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-white p-3 border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success text-white">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Est. Pendapatan</span>
                    <h4 class="fw-bold text-success mb-0"><?= format_rupiah($total_omset) ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Pesanan Terbaru -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold font-heading text-dark-green mb-0">Pesanan Terbaru</h5>
        <a href="pesanan.php" class="btn btn-outline-custom btn-sm">
            Lihat Semua Pesanan <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small">
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Tipe</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recent_orders) > 0): ?>
                    <?php foreach ($recent_orders as $ro): ?>
                        <tr>
                            <td class="fw-bold text-dark-green font-monospace"><?= htmlspecialchars($ro['kode_pesanan']) ?></td>
                            <td>
                                <div><strong><?= htmlspecialchars($ro['nama_pelanggan']) ?></strong></div>
                                <small class="text-muted"><?= htmlspecialchars($ro['no_hp']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($ro['tipe_pesanan']) ?></span>
                                <?php if ($ro['tipe_pesanan'] === 'Dine In'): ?>
                                    <small class="d-block text-success font-monospace"><?= htmlspecialchars($ro['nomor_meja']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-gold text-dark-green"><?= htmlspecialchars($ro['metode_pembayaran']) ?></span>
                            </td>
                            <td class="fw-bold text-dark-green"><?= format_rupiah($ro['total']) ?></td>
                            <td>
                                <span class="badge bg-dark-green text-gold px-2 py-1"><?= htmlspecialchars($ro['status']) ?></span>
                            </td>
                            <td class="small text-muted"><?= date('H:i, d M', strtotime($ro['tanggal'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada pesanan masuk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
