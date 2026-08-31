<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/koneksi.php';

// Hitung total item di keranjang
$cart_count = 0;
if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $item) {
        $cart_count += $item['qty'];
    }
}

// Helper untuk deteksi Halaman Aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resto Nusantara - Cita Rasa Autentik & Modern</title>
    <meta name="description" content="Restoran keluarga modern dengan pilihan menu khas nusantara dan western berkualitas tinggi. Pesan online dengan mudah.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Sticky Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
               <i class="bi bi-shop text-gold fs-3"></i>
               <span>
               <span class="text-gold">Khoja</span>Restaurant</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'menu.php' || $current_page == 'detail_menu.php') ? 'active' : '' ?>" href="menu.php">
                            <i class="bi bi-journal-richtext me-1"></i> Daftar Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'keranjang.php') ? 'active' : '' ?>" href="keranjang.php">
                            <i class="bi bi-cart3 me-1"></i> Keranjang
                            <?php if ($cart_count > 0): ?>
                                <span class="cart-badge"><?= $cart_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="keranjang.php" class="btn btn-outline-light btn-sm rounded-pill d-lg-none mb-2">
                        <i class="bi bi-cart3"></i> Keranjang (<?= $cart_count ?>)
                    </a>
                    <a href="admin/login.php" class="btn btn-gold-custom btn-sm">
                        <i class="bi bi-shield-lock me-1"></i> Admin Login
                    </a>
                </div>
            </div>
        </div>
    </nav>
