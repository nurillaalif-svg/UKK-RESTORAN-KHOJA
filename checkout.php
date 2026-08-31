<?php
require_once __DIR__ . '/header.php';

// Validasi jika keranjang kosong
if (empty($_SESSION['keranjang'])) {
    header("Location: keranjang.php");
    exit;
}

$error_msg = "";

// Hitung Grand Total Keranjang
$grand_total = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $grand_total += ($item['harga'] * $item['qty']);
}

// Prosese Form Submission Checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan    = trim($_POST['nama_pelanggan'] ?? '');
    $no_hp             = trim($_POST['no_hp'] ?? '');
    $tipe_pesanan      = trim($_POST['tipe_pesanan'] ?? 'Dine In');
    $nomor_meja        = trim($_POST['nomor_meja'] ?? '');
    $alamat            = trim($_POST['alamat'] ?? '');
    $metode_pembayaran = trim($_POST['metode_pembayaran'] ?? 'QRIS');
    $catatan           = trim($_POST['catatan'] ?? '');

    // Validasi Sisi Server (PHP)
    if (empty($nama_pelanggan) || empty($no_hp)) {
        $error_msg = "Nama Pelanggan dan Nomor HP wajib diisi!";
    } elseif ($tipe_pesanan === 'Dine In' && empty($nomor_meja)) {
        $error_msg = "Nomor Meja wajib diisi untuk pemesanan Dine In!";
    } elseif ($tipe_pesanan === 'Take Away' && empty($alamat)) {
        $error_msg = "Alamat Pengiriman wajib diisi untuk pemesanan Take Away!";
    } else {
        try {
            $pdo->beginTransaction();

            // Generate Kode Pesanan Unik: RST-YYYYMMDD-XXXX
            $kode_pesanan = "RST-" . date('Ymd') . "-" . strtoupper(substr(uniqid(), -4));

            // Penentuan Status Awal berdasarkan Metode Pembayaran
            // QRIS -> 'Menunggu Verifikasi'
            // Cash -> 'Menunggu Pembayaran'
            $status_awal = ($metode_pembayaran === 'QRIS') ? 'Menunggu Verifikasi' : 'Menunggu Pembayaran';

            // Insert ke tabel `pesanan`
            $stmt_order = $pdo->prepare("INSERT INTO pesanan 
                (kode_pesanan, nama_pelanggan, no_hp, tipe_pesanan, nomor_meja, alamat, metode_pembayaran, catatan, total, status, tanggal)
                VALUES 
                (:kode, :nama, :hp, :tipe, :meja, :alamat, :metode, :catatan, :total, :status, NOW())");

            $stmt_order->execute([
                ':kode'   => $kode_pesanan,
                ':nama'   => $nama_pelanggan,
                ':hp'     => $no_hp,
                ':tipe'   => $tipe_pesanan,
                ':meja'   => ($tipe_pesanan === 'Dine In') ? $nomor_meja : NULL,
                ':alamat' => ($tipe_pesanan === 'Take Away') ? $alamat : NULL,
                ':metode' => $metode_pembayaran,
                ':catatan'=> $catatan,
                ':total'  => $grand_total,
                ':status' => $status_awal
            ]);

            $id_pesanan_baru = $pdo->lastInsertId();

            // Insert ke tabel `detail_pesanan`
            $stmt_detail = $pdo->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, qty, subtotal) VALUES (:id_pesanan, :id_menu, :qty, :subtotal)");

            foreach ($_SESSION['keranjang'] as $item) {
                $subtotal_item = $item['harga'] * $item['qty'];
                $stmt_detail->execute([
                    ':id_pesanan' => $id_pesanan_baru,
                    ':id_menu'    => $item['id_menu'],
                    ':qty'        => $item['qty'],
                    ':subtotal'   => $subtotal_item
                ]);
            }

            $pdo->commit();

            // Kosongkan Keranjang Belanja setelah Checkout Berhasil
            $_SESSION['keranjang'] = [];

            // Redirect ke Halaman Konfirmasi Pesanan
            header("Location: konfirmasi.php?kode=" . urlencode($kode_pesanan));
            exit;

        } catch (Exception $ex) {
            $pdo->rollBack();
            $error_msg = "Gagal memproses pesanan: " . $ex->getMessage();
        }
    }
}
?>

<main class="py-5">
    <div class="container">

        <!-- Header -->
        <div class="text-center mb-5">
            <span class="text-gold font-heading fw-bold">Langkah Terakhir</span>
            <h1 class="section-title">Formulir Checkout Pesanan</h1>
            <p class="text-muted">Isi data pemesanan dan pilih metode pembayaran sesuai keinginan Anda</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" id="checkoutForm">
            <div class="row g-4">
                <!-- Data Pemesan -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h5 class="fw-bold font-heading text-dark-green mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lines-fill text-gold fs-4"></i> Data Pelanggan & Layanan
                        </h5>

                        <div class="mb-3">
                            <label for="nama_pelanggan" class="form-label fw-semibold text-dark-green">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light-green-bg border-0 fs-6" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukkan nama lengkap Anda" required value="<?= htmlspecialchars($_POST['nama_pelanggan'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="no_hp" class="form-label fw-semibold text-dark-green">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control form-control-lg bg-light-green-bg border-0 fs-6" id="no_hp" name="no_hp" placeholder="Contoh: 081234567890" required value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark-green d-block">Tipe Pemesanan <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="tipe_pesanan" id="tipe_dine_in" value="Dine In" checked>
                                    <label class="btn btn-outline-success w-100 py-3 rounded-4 d-flex flex-column align-items-center gap-1" for="tipe_dine_in">
                                        <i class="bi bi-cup-hot fs-3"></i>
                                        <span class="fw-bold">Dine In (Makan di Tempat)</span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="tipe_pesanan" id="tipe_take_away" value="Take Away">
                                    <label class="btn btn-outline-success w-100 py-3 rounded-4 d-flex flex-column align-items-center gap-1" for="tipe_take_away">
                                        <i class="bi bi-bag-check fs-3"></i>
                                        <span class="fw-bold">Take Away (Bawa Pulang)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Input: Dine In -> Nomor Meja -->
                        <div class="mb-3" id="container_nomor_meja">
                            <label for="nomor_meja" class="form-label fw-semibold text-dark-green">Nomor Meja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light-green-bg border-0 fs-6" id="nomor_meja" name="nomor_meja" placeholder="Contoh: Meja 05" value="<?= htmlspecialchars($_POST['nomor_meja'] ?? '') ?>">
                            <small class="text-muted">Silakan lihat nomor pada stiker di meja Anda.</small>
                        </div>

                        <!-- Dynamic Input: Take Away -> Alamat -->
                        <div class="mb-3" id="container_alamat" style="display: none;">
                            <label for="alamat" class="form-label fw-semibold text-dark-green">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-light-green-bg border-0 fs-6" id="alamat" name="alamat" rows="3" placeholder="Masukkan jalan, no rumah, dan patokan alamat..."><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-2">
                            <label for="catatan" class="form-label fw-semibold text-dark-green">Catatan Pesanan (Opsional)</label>
                            <textarea class="form-control bg-light-green-bg border-0 fs-6" id="catatan" name="catatan" rows="2" placeholder="Contoh: Tanpa bawang goreng, sambal dipisah, dll."><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold font-heading text-dark-green mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card-2-front text-gold fs-4"></i> Metode Pembayaran
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="metode_pembayaran" id="pay_qris" value="QRIS" checked>
                                <label class="btn btn-outline-dark w-100 p-3 rounded-4 text-start h-100" for="pay_qris">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-gold text-dark-green fw-bold">Instan / Non-Tunai</span>
                                        <i class="bi bi-qr-code-scan fs-3 text-dark-green"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">QRIS (Scan All e-Wallet)</h6>
                                    <small class="text-muted d-block">Gopay, OVO, Dana, ShopeePay, & Mobile Banking.</small>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="metode_pembayaran" id="pay_cash" value="Cash">
                                <label class="btn btn-outline-dark w-100 p-3 rounded-4 text-start h-100" for="pay_cash">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-secondary text-white fw-bold">Bayar di Kasir</span>
                                        <i class="bi bi-cash-stack fs-3 text-success"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Tunai / Cash</h6>
                                    <small class="text-muted d-block">Scan QR Code Pesanan di Kasir saat kedatangan.</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Items Pesanan -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px;">
                        <h5 class="fw-bold font-heading text-dark-green mb-4 pb-2 border-bottom d-flex align-items-center justify-content-between">
                            <span>Item Dipesan</span>
                            <a href="keranjang.php" class="small text-gold text-decoration-none fw-normal">Edit Keranjang</a>
                        </h5>

                        <div class="pe-1 mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($_SESSION['keranjang'] as $item): 
                                $sub = $item['harga'] * $item['qty'];
                            ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark-green mb-0"><?= htmlspecialchars($item['nama_menu']) ?></h6>
                                        <small class="text-muted"><?= $item['qty'] ?> x <?= format_rupiah($item['harga']) ?></small>
                                    </div>
                                    <span class="fw-semibold text-dark-green"><?= format_rupiah($sub) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-5 text-dark-green">Total Pembayaran:</span>
                            <span class="fw-bold fs-3 text-gold"><?= format_rupiah($grand_total) ?></span>
                        </div>

                        <button type="submit" class="btn btn-dark-custom btn-lg w-100 py-3 font-heading shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> Konfirmasi & Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
