<?php
// File Koneksi Database (XAMPP Default)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "restoran";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Fallback koneksi server MySQL tanpa db tertentu jika db belum disetup
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $ex) {
        die("Koneksi Database Gagal: " . $ex->getMessage());
    }
}

// Start Session untuk Keranjang dan Login Admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper Format Rupiah
if (!function_exists('format_rupiah')) {
    function format_rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
?>
