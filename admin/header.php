<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi.php';

// Proteksi Halaman Admin: Wajib Login
$current_script = basename($_SERVER['PHP_SELF']);
if ($current_script !== 'login.php') {
    if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Khoja Restaurant</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<?php if ($current_script !== 'login.php'): ?>
    <!-- Top Admin Header -->
    <header class="navbar navbar-expand-lg navbar-dark bg-dark-green sticky-top shadow-sm py-2">
        <div class="container-fluid px-4">
            <a class="navbar-brand font-heading fw-bold fs-4 d-flex align-items-center gap-2" href="dashboard.php">
                <i class="bi bi-shield-lock text-gold fs-3"></i>
                <span>Admin<span class="text-gold">Khoja</span></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center gap-2">
                    <li class="nav-item me-3 text-white-50">
                        <i class="bi bi-person-circle text-gold me-1"></i> Halo, <strong class="text-white"><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></strong>
                    </li>
                    <li class="nav-item">
                        <a href="../index.php" target="_blank" class="btn btn-outline-light btn-sm rounded-pill">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Website
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Yakin ingin keluar dari panel admin?');">
                            <i class="bi bi-power me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse px-3">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_script == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_script == 'menu.php') ? 'active' : '' ?>" href="menu.php">
                                <i class="bi bi-egg-fried me-2"></i> Kelola Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_script == 'kategori.php') ? 'active' : '' ?>" href="kategori.php">
                                <i class="bi bi-tags me-2"></i> Kelola Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_script == 'pesanan.php') ? 'active' : '' ?>" href="pesanan.php">
                                <i class="bi bi-cart-check me-2"></i> Kelola Pesanan
                            </a>
                        </li>
                    </ul>

                    <hr class="text-white-50 my-4">

                    <div class="p-3 bg-dark text-white-50 rounded-3 small">
                        <h6 class="text-gold font-heading fw-bold mb-1">Khoja Restaurant</h6>
                        <p class="mb-0">Versi 1.0 &bull; System Panel</p>
                    </div>
                </div>
            </nav>

            <!-- Main Admin Content Container -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
<?php endif; ?>
