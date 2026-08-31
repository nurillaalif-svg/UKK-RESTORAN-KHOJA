-- Database MySQL Restoran Modern
-- Siap diimport di phpMyAdmin / XAMPP

CREATE DATABASE IF NOT EXISTS `restoran` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `restoran`;

-- --------------------------------------------------------
-- Structure for Table `admin`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detail_pesanan`;
DROP TABLE IF EXISTS `pesanan`;
DROP TABLE IF EXISTS `menu`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id_admin` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Admin (Username: admin, Password: admin123)
-- Password hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$4.kQ4qE5vH8s4sW2h8G4uexPzZJ7J/8jQn1L7Z.QyN3l5a0rF.EOG');

-- --------------------------------------------------------
-- Structure for Table `kategori`
-- --------------------------------------------------------
CREATE TABLE `kategori` (
  `id_kategori` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Kategori
INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Makanan Utama'),
(2, 'Minuman Segar'),
(3, 'Camilan & Dessert'),
(4, 'Paket Spesial');

-- --------------------------------------------------------
-- Structure for Table `menu`
-- --------------------------------------------------------
CREATE TABLE `menu` (
  `id_menu` INT(11) NOT NULL AUTO_INCREMENT,
  `id_kategori` INT(11) NOT NULL,
  `nama_menu` VARCHAR(100) NOT NULL,
  `harga` DECIMAL(12,2) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_menu`),
  KEY `fk_menu_kategori` (`id_kategori`),
  CONSTRAINT `fk_menu_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Menu Makanan
INSERT INTO `menu` (`id_menu`, `id_kategori`, `nama_menu`, `harga`, `deskripsi`, `gambar`) VALUES
(1, 1, 'Nasi Goreng Wagyu Spesial', 45000.00, 'Nasi goreng harum rempah nusantara dipadu potongan daging wagyu empuk, telur mata sapi, dan kerupuk udang.', 'nasi_goreng_wagyu.jpg'),
(2, 1, 'Rendang Daging Sapi Premium', 52000.00, 'Daging sapi pilihan dimasak perlahan 8 jam dengan bumbu rendang autentik Minang yang kaya rasa.', 'rendang_daging.jpg'),
(3, 1, 'Ayam Bakar Madu Gurih', 38000.00, 'Ayam kampung bakar dioles madu murni dan bumbu khas restoran, disajikan dengan sambal terasi dan lalapan.', 'ayam_bakar_madu.jpg'),
(4, 2, 'Es Matcha Latte Gold', 28000.00, 'Seduhan matcha organik Jepang berkualitas tinggi dicampur susu segar dan gula aren murni.', 'matcha_latte.jpg'),
(5, 2, 'Es Teh Lemongrass Mint', 18000.00, 'Teh hitam premium infused batang serai dan daun mint segar yang memberikan sensasi dahaga sejuk.', 'teh_lemongrass.jpg'),
(6, 3, 'Pisang Goreng Keju Karamel', 25000.00, 'Pisang raja manis digoreng renyah, ditaburi keju melimpah dan saus karamel homemade.', 'pisang_goreng.jpg'),
(7, 3, 'Panna Cotta Mangga Spesial', 26000.00, 'Dessert Italia lembut meluncur di lidah disiram coulis mangga harum manis alami.', 'panna_cotta.jpg'),
(8, 4, 'Paket Hemat Berdua', 85000.00, '2 Nasi Goreng Wagyu + 2 Es Teh Lemongrass Mint. Lebih hemat dan kenyang maksimal!', 'paket_berdua.jpg');

-- --------------------------------------------------------
-- Structure for Table `pesanan`
-- --------------------------------------------------------
CREATE TABLE `pesanan` (
  `id_pesanan` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_pesanan` VARCHAR(20) NOT NULL,
  `nama_pelanggan` VARCHAR(100) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `tipe_pesanan` ENUM('Dine In','Take Away') NOT NULL,
  `nomor_meja` VARCHAR(10) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `metode_pembayaran` ENUM('QRIS','Cash') NOT NULL,
  `catatan` TEXT DEFAULT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  `status` ENUM('Menunggu Pembayaran','Menunggu Verifikasi','Diproses','Siap Diambil','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu Pembayaran',
  `tanggal` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pesanan`),
  UNIQUE KEY `kode_pesanan` (`kode_pesanan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Pesanan
INSERT INTO `pesanan` (`id_pesanan`, `kode_pesanan`, `nama_pelanggan`, `no_hp`, `tipe_pesanan`, `nomor_meja`, `alamat`, `metode_pembayaran`, `catatan`, `total`, `status`, `tanggal`) VALUES
(1, 'RST-20260806-001', 'Budi Santoso', '081234567890', 'Dine In', 'Meja 05', NULL, 'QRIS', 'Tolong pedas sedang ya', 73000.00, 'Diproses', NOW());

-- --------------------------------------------------------
-- Structure for Table `detail_pesanan`
-- --------------------------------------------------------
CREATE TABLE `detail_pesanan` (
  `id_detail` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pesanan` INT(11) NOT NULL,
  `id_menu` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `fk_detail_pesanan` (`id_pesanan`),
  KEY `fk_detail_menu` (`id_menu`),
  CONSTRAINT `fk_detail_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Detail Pesanan
INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `qty`, `subtotal`) VALUES
(1, 1, 1, 1, 45000.00),
(2, 1, 4, 1, 28000.00);
