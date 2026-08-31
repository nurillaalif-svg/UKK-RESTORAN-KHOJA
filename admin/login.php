<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/koneksi.php';

// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_msg = "Username dan Password wajib diisi!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :u");
            $stmt->execute([':u' => $username]);
            $admin = $stmt->fetch();

            if ($admin) {
                // Verifikasi Password (Password Hash & Plain Text Fallback untuk Kemudahan Testing)
                $isValidPassword = password_verify($password, $admin['password']) || ($password === $admin['password']);

                if ($isValidPassword) {
                    $_SESSION['admin_logged'] = true;
                    $_SESSION['admin_user']   = $admin['username'];
                    $_SESSION['admin_id']     = $admin['id_admin'];

                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error_msg = "Password yang Anda masukkan salah!";
                }
            } else {
                $error_msg = "Username tidak ditemukan!";
            }
        } catch (Exception $e) {
            $error_msg = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Resto Nusantara</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-bg d-flex align-items-center justify-content-center min-vh-100 py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-dark-green text-center text-white p-4 border-bottom border-gold border-3">
                        <i class="bi bi-shield-lock text-gold display-4 d-block mb-2"></i>
                        <h4 class="font-heading fw-bold mb-0">
                           <span class="text-gold">Khoja</span>Restaurant</h4>
                        <small class="text-white-50">Panel Administrator Login</small>
                    </div>
                    <div class="card-body p-4 bg-white">
                        
                        <?php if (!empty($error_msg)): ?>
                            <div class="alert alert-danger alert-dismissible fade show rounded-3 small" role="alert">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i> <?= htmlspecialchars($error_msg) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold text-dark-green small">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="username" id="username" class="form-control border-start-0" placeholder="Masukkan username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? 'admin') ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-dark-green small">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Masukkan password" required value="admin123">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark-custom w-100 py-2 font-heading shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sistem
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">Default Login: <strong>admin</strong> / <strong>admin123</strong></small>
                            <div class="mt-2">
                                <a href="../index.php" class="text-decoration-none small text-gold fw-semibold">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Website Utama
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
