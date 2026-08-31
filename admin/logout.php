<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus seluruh session admin
unset($_SESSION['admin_logged']);
unset($_SESSION['admin_user']);
unset($_SESSION['admin_id']);

session_destroy();

// Redirect ke halaman login admin
header("Location: login.php");
exit;
?>
