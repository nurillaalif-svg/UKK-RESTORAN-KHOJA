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

$kode = isset($_GET['kode']) ? trim($_GET['kode']) : '';

if (empty($kode)) {
    header("Location: index.php");
    exit;
}

try {
    // Ambil Header Pesanan
    $stmt = $pdo->prepare("SELECT * FROM pesanan WHERE kode_pesanan = :kode");
    $stmt->execute([':kode' => $kode]);
    $pesanan = $stmt->fetch();

    if (!$pesanan) {
        header("Location: index.php");
        exit;
    }

    // Ambil Detail Items
    $stmt_detail = $pdo->prepare("SELECT d.*, m.nama_menu, m.gambar, m.harga 
        FROM detail_pesanan d 
        JOIN menu m ON d.id_menu = m.id_menu 
        WHERE d.id_pesanan = :id");
    $stmt_detail->execute([':id' => $pesanan['id_pesanan']]);
    $details = $stmt_detail->fetchAll();

} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}

// Badge Status Color
function get_status_badge($status) {
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

<main class="py-5">
    <div class="container">
        
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <!-- Alert Success -->
                <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 text-center">
                    <i class="bi bi-check-circle-fill text-success display-4 d-block mb-2"></i>
                    <h3 class="fw-bold font-heading mb-1 text-dark-green">Pesanan Berhasil Dibuat!</h3>
                    <p class="mb-0 text-muted">Terima kasih, pesanan Anda sedang kami catat dan diproses oleh sistem Resto Nusantara.</p>
                </div>

                <!-- Receipt Card -->
                <div class="order-receipt">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="text-muted small">Nomor Pesanan:</span>
                            <h3 class="fw-bold font-heading text-dark-green mb-0"><?= htmlspecialchars($pesanan['kode_pesanan']) ?></h3>
                        </div>
                        <div class="mt-2 mt-md-0 text-md-end">
                            <span class="text-muted small">Status Pesanan:</span>
                            <div>
                                <span class="badge <?= get_status_badge($pesanan['status']) ?> px-3 py-2 rounded-pill fs-6">
                                    <i class="bi bi-info-circle me-1"></i> <?= htmlspecialchars($pesanan['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- Left Info -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark-green font-heading mb-3"><i class="bi bi-person me-2 text-gold"></i> Detail Pemesan</h6>
                            <table class="table table-borderless table-sm small">
                                <tr>
                                    <td class="text-muted" style="width: 120px;">Nama:</td>
                                    <td class="fw-bold text-dark-green"><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. WhatsApp:</td>
                                    <td class="fw-bold text-dark-green"><?= htmlspecialchars($pesanan['no_hp']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tipe Pesanan:</td>
                                    <td><span class="badge bg-dark-green text-gold px-2 py-1"><?= htmlspecialchars($pesanan['tipe_pesanan']) ?></span></td>
                                </tr>
                                <?php if ($pesanan['tipe_pesanan'] === 'Dine In'): ?>
                                    <tr>
                                        <td class="text-muted">Nomor Meja:</td>
                                        <td class="fw-bold text-success"><?= htmlspecialchars($pesanan['nomor_meja']) ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td class="text-muted">Alamat:</td>
                                        <td class="fw-bold text-dark-green"><?= nl2br(htmlspecialchars($pesanan['alamat'])) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="text-muted">Waktu Pesan:</td>
                                    <td><?= date('d M Y - H:i', strtotime($pesanan['tanggal'])) ?> WIB</td>
                                </tr>
                                <?php if (!empty($pesanan['catatan'])): ?>
                                    <tr>
                                        <td class="text-muted">Catatan:</td>
                                        <td class="fst-italic text-secondary"><?= htmlspecialchars($pesanan['catatan']) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <!-- Right Payment & QR Code Section -->
                        <div class="col-md-6 text-center">
                            <div class="qr-container w-100">
                                <h6 class="fw-bold text-dark-green mb-2">
                                    <i class="bi bi-qr-code me-1 text-gold"></i> 
                                    <?= ($pesanan['metode_pembayaran'] === 'QRIS') ? 'QR Code Pembayaran QRIS' : 'QR Code Tiket Pesanan (Kasir)' ?>
                                </h6>
                                <p class="small text-muted mb-3">
                                    <?= ($pesanan['metode_pembayaran'] === 'QRIS') 
                                        ? 'Silakan scan QRIS ini menggunakan aplikasi e-Wallet atau Mobile Banking Anda.' 
                                        : 'Tunjukkan QR Code ini kepada kasir/waiter saat melakukan pembayaran tunai.' ?>
                                </p>
                                
                                <div id="qrcode" class="d-flex justify-content-center my-3"></div>

                                <div class="mt-2">
                                    <small class="fw-bold text-dark-green">Metode: <?= htmlspecialchars($pesanan['metode_pembayaran']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Items Table -->
                    <h6 class="fw-bold text-dark-green font-heading mb-3"><i class="bi bi-list-check me-2 text-gold"></i> Rincian Pesanan Menu</h6>
                    <div class="table-responsive mb-4">
                        <table class="table align-middle">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $d): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= get_menu_image($d['gambar']) ?>" alt="<?= htmlspecialchars($d['nama_menu']) ?>" class="rounded-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <span class="fw-semibold text-dark-green"><?= htmlspecialchars($d['nama_menu']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-secondary"><?= format_rupiah($d['harga']) ?></td>
                                        <td class="text-center font-monospace fw-bold"><?= $d['qty'] ?></td>
                                        <td class="text-end fw-bold text-dark-green"><?= format_rupiah($d['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="border-top-2">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold fs-5 text-dark-green">Total Biaya:</td>
                                    <td class="text-end fw-bold fs-4 text-gold"><?= format_rupiah($pesanan['total']) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top gap-2">
                        <a href="index.php" class="btn btn-outline-custom">
                            <i class="bi bi-house me-1"></i> Kembali ke Beranda
                        </a>
                        <button onclick="window.print();" class="btn btn-dark-custom">
                            <i class="bi bi-printer me-1"></i> Cetak Struk / Simpan PDF
                        </button>
                    </div>

                </div>

            </div>
        </div>

    </div>
</main>

<!-- JS QR Code Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qrContainer = document.getElementById('qrcode');
    if (qrContainer && typeof QRCode !== 'undefined') {
        const qrData = "<?= htmlspecialchars($pesanan['kode_pesanan']) ?>";
        new QRCode(qrContainer, {
            text: qrData,
            width: 160,
            height: 160,
            colorDark : "#3E472D",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
