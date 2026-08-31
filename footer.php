    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row g-4">
                <!-- Resto Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-shop text-gold fs-2"></i>
                        <span class="font-heading fs-3 fw-bold">
                        <span class="text-gold">Khoja</span><span class="text-white">Restaurant</span></span>
                    </div>
                    <p class="small text-white-50">
                        Menyajikan kelezatan kuliner Timur-Tengah dengan bahan-bahan dan rempah-rempah segar berkualitas terbaik, dipadu dengan suasana restoran ala Timur-Tengah dan ramah keluarga.
                    </p>
                    <div class="d-flex gap-3 fs-5 mt-3">
                        <a href="#" class="text-gold"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-gold"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-gold"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="text-gold"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <!-- Navigasi Cepat -->
                <div class="col-lg-2 col-md-6">
                    <h5>Navigasi</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="index.php" class="footer-link"><i class="bi bi-chevron-right me-1 text-gold"></i> Beranda</a></li>
                        <li><a href="menu.php" class="footer-link"><i class="bi bi-chevron-right me-1 text-gold"></i> Daftar Menu</a></li>
                        <li><a href="keranjang.php" class="footer-link"><i class="bi bi-chevron-right me-1 text-gold"></i> Keranjang Saya</a></li>
                        <li><a href="checkout.php" class="footer-link"><i class="bi bi-chevron-right me-1 text-gold"></i> Checkout</a></li>
                        <li><a href="admin/login.php" class="footer-link"><i class="bi bi-chevron-right me-1 text-gold"></i> Panel Admin</a></li>
                    </ul>
                </div>

                <!-- Jam Operasional -->
                <div class="col-lg-3 col-md-6 pe-lg-4">
                    <h5>Jam Operasional</h5>
                    <ul class="list-unstyled small text-white-50 d-flex flex-column gap-2">
                        <li class="d-flex justify-content-between">
                            <span>Senin - Jumat:</span>
                            <span class="text-gold fw-semibold">10:00 - 22:00</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Sabtu - Minggu:</span>
                            <span class="text-gold fw-semibold">09:00 - 23:00</span>
                        </li>
                        <li class="mt-2 text-white-50">
                            <i class="bi bi-geo-alt me-1 text-gold"></i> Jl. Diponegoro No.34, RT.01/RW.10, Tegalsari, Kec. Candisari, Kota Semarang, Jawa Tengah 50231, Indonesia
                        </li>
                    </ul>
                </div>

                <!-- Kontak & Pemesanan -->
                <div class="col-lg-3 col-md-6 ps-lg-4">
                    <h5>Hubungi Kami</h5>
                    <ul class="list-unstyled small text-white-50 d-flex flex-column gap-2">
                        <li><i class="bi bi-telephone-fill me-2 text-gold"></i> +62 816-669-603</li>
                        <li><i class="bi bi-whatsapp me-2 text-gold"></i> +62 816-669-603</li>
                        <li><i class="bi bi-envelope-fill me-2 text-gold"></i> restokhoja@gmail.com</li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary my-4 opacity-25">

            <div class="row align-items-center copyright">
                <div class="col-md-6 text-center text-md-start small text-white-50">
                    &copy; <?= date('Y') ?> Resto Khoja. All rights reserved. Made with <i class="bi bi-heart-fill text-danger"></i>.
                </div>
                <div class="col-md-6 text-center text-md-end small text-white-50 mt-2 mt-md-0">
                    PHP Native &bull; MySQL &bull; Bootstrap 5
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- QRCode JS Library CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
