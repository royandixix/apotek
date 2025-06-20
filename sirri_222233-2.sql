-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 20, 2025 at 04:23 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sirri_222233`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_222233`
--

CREATE TABLE `admin_222233` (
  `admin_id_222233` int(11) NOT NULL,
  `user_id_222233` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_222233`
--

INSERT INTO `admin_222233` (`admin_id_222233`, `user_id_222233`) VALUES
(1, 1),
(2, 8),
(3, 13),
(4, 19),
(5, 23),
(6, 24),
(7, 31);

-- --------------------------------------------------------

--
-- Table structure for table `kasir_222233`
--

CREATE TABLE `kasir_222233` (
  `kasir_id_222233` int(11) NOT NULL,
  `user_id_222233` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kasir_222233`
--

INSERT INTO `kasir_222233` (`kasir_id_222233`, `user_id_222233`) VALUES
(1, 2),
(2, 10),
(4, 14),
(5, 17),
(6, 35),
(7, 36);

-- --------------------------------------------------------

--
-- Table structure for table `keranjang_222233`
--

CREATE TABLE `keranjang_222233` (
  `keranjang_id_222233` int(11) NOT NULL,
  `user_id_222233` int(11) NOT NULL,
  `transaksi_id_222233` int(11) DEFAULT NULL,
  `obat_id_222233` int(11) DEFAULT NULL,
  `harga_222233` decimal(10,2) DEFAULT NULL,
  `jumlah_222233` int(11) DEFAULT NULL,
  `is_deleted_222233` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keranjang_222233`
--

INSERT INTO `keranjang_222233` (`keranjang_id_222233`, `user_id_222233`, `transaksi_id_222233`, `obat_id_222233`, `harga_222233`, `jumlah_222233`, `is_deleted_222233`) VALUES
(25, 16, 15, 27, 4000.00, 2, 1),
(26, 16, 16, 25, 3500.00, 1, 0),
(27, 32, 17, 24, 3000.00, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `obat_222233`
--

CREATE TABLE `obat_222233` (
  `obat_id_222233` int(11) NOT NULL,
  `nama_obat_222233` varchar(255) NOT NULL,
  `jenis_obat_222233` varchar(100) NOT NULL,
  `stok_222233` int(11) NOT NULL,
  `harga_222233` decimal(10,2) NOT NULL,
  `tanggal_kadaluarsa_222233` date NOT NULL,
  `kategori_222233` enum('Tablet','Sirup','Kapsul','Salep','Obat Tetes','Injeksi') NOT NULL DEFAULT 'Tablet',
  `gambar_obat_222233` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat_222233`
--

INSERT INTO `obat_222233` (`obat_id_222233`, `nama_obat_222233`, `jenis_obat_222233`, `stok_222233`, `harga_222233`, `tanggal_kadaluarsa_222233`, `kategori_222233`, `gambar_obat_222233`) VALUES
(24, 'Paramex', 'Ringan', 7, 3000.00, '2026-09-13', 'Tablet', 'obat_684b7c763afa9.jpeg'),
(25, 'Neozep Forte', 'Sakit Kepala', 8, 3500.00, '2026-06-13', 'Tablet', 'obat_684b86173095e8.90083645.jpeg'),
(26, 'omeparasol', 'antibiotik', 10, 8000.00, '2026-06-13', 'Tablet', 'obat_684b8e1e745689.36999677.jpeg'),
(27, 'Procold', 'simptomatik', 0, 4000.00, '2027-05-16', 'Tablet', 'obat_68502ff0e8a16.jpeg'),
(28, 'Minyak Kayu Putih', 'Herbal', 20, 10000.00, '2026-06-17', 'Obat Tetes', 'obat_6850c9f786ae20.85329061.jpeg'),
(1750312878, 'roy', 'roy', 10, 100000.00, '2025-06-12', 'Kapsul', 'obat_6853a7aeb71c7.png'),
(1750315139, 'bodrex', 'ringan', 10, 2000000.00, '2025-06-30', 'Obat Tetes', 'obat_6853b0a0b07de.png');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_222233`
--

CREATE TABLE `pembayaran_222233` (
  `pembayaran_id_222233` int(11) NOT NULL,
  `keranjang_id_222233` int(11) NOT NULL,
  `transaksi_id_222233` int(11) NOT NULL,
  `metode_pembayaran_222233` enum('transfer','cod','qris','tunai') NOT NULL,
  `status_pembayaran_222233` enum('belum_dibayar','sudah_dibayar','dibatalkan') NOT NULL DEFAULT 'belum_dibayar',
  `tanggal_pembayaran_222233` datetime DEFAULT NULL,
  `jumlah_pembayaran_222233` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran_222233`
--

INSERT INTO `pembayaran_222233` (`pembayaran_id_222233`, `keranjang_id_222233`, `transaksi_id_222233`, `metode_pembayaran_222233`, `status_pembayaran_222233`, `tanggal_pembayaran_222233`, `jumlah_pembayaran_222233`) VALUES
(14, 25, 15, 'cod', 'sudah_dibayar', '2025-06-17 09:43:06', 8000.00),
(15, 26, 16, 'transfer', 'sudah_dibayar', '2025-06-17 09:47:40', 3500.00),
(16, 27, 17, 'transfer', 'belum_dibayar', '2025-06-18 08:30:15', 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pembeli_222233`
--

CREATE TABLE `pembeli_222233` (
  `pembeli_id_222233` int(11) NOT NULL,
  `user_id_222233` int(11) NOT NULL,
  `alamat_222233` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembeli_222233`
--

INSERT INTO `pembeli_222233` (`pembeli_id_222233`, `user_id_222233`, `alamat_222233`) VALUES
(1, 4, 'Jl. Suci\r\n'),
(3, 9, 'Jl. Daniel'),
(4, 12, 'pembeli1'),
(5, 16, 'aspol'),
(6, 32, 'btp');

-- --------------------------------------------------------

--
-- Table structure for table `penawaran_obat_222233`
--

CREATE TABLE `penawaran_obat_222233` (
  `penawaran_id_222233` int(11) NOT NULL,
  `supplier_id_222233` int(11) NOT NULL,
  `nama_obat_222233` varchar(100) NOT NULL,
  `jenis_obat_222233` varchar(100) NOT NULL,
  `kategori_222233` enum('Tablet','Sirup','Kapsul','Salep','Obat Tetes','Injeksi') NOT NULL DEFAULT 'Tablet',
  `jumlah_222233` int(11) NOT NULL,
  `harga_satuan_222233` decimal(10,2) NOT NULL,
  `tanggal_penawaran_222233` datetime DEFAULT current_timestamp(),
  `status_penawaran_222233` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `gambar_obat_222233` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penawaran_obat_222233`
--

INSERT INTO `penawaran_obat_222233` (`penawaran_id_222233`, `supplier_id_222233`, `nama_obat_222233`, `jenis_obat_222233`, `kategori_222233`, `jumlah_222233`, `harga_satuan_222233`, `tanggal_penawaran_222233`, `status_penawaran_222233`, `gambar_obat_222233`) VALUES
(16, 1, 'Masako', 'Asin', 'Sirup', 22, 222.00, '2025-06-12 21:10:02', 'diterima', 'obat_684ad1aa9a2500.62047178.jpeg'),
(20, 5, 'Neozep Forte', 'Sakit Kepala', 'Tablet', 10, 3500.00, '2025-06-13 09:59:51', 'diterima', 'obat_684b86173095e8.90083645.jpeg'),
(21, 6, 'omeparasol', 'antibiotik', 'Tablet', 10, 8000.00, '2025-06-13 10:34:06', 'pending', 'obat_684b8e1e745689.36999677.jpeg'),
(22, 6, 'Procold', 'simptomatik', 'Tablet', 11, 4000.00, '2025-06-16 22:59:14', 'diterima', NULL),
(23, 6, 'Minyak Kayu Putih', 'Herbal', 'Obat Tetes', 20, 10000.00, '2025-06-17 09:50:47', 'diterima', 'obat_6850c9f786ae20.85329061.jpeg'),
(24, 8, 'Procold', 'simptomatik', 'Tablet', 5, 4000.00, '2025-06-18 08:27:41', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman_obat_222233`
--

CREATE TABLE `pengiriman_obat_222233` (
  `pengiriman_id_222233` int(11) NOT NULL,
  `obat_id_222233` int(11) DEFAULT NULL,
  `jumlah_222233` int(11) NOT NULL,
  `tanggal_pengiriman_222233` date NOT NULL,
  `status_pengiriman_222233` enum('diproses','dikirim','diterima') NOT NULL,
  `supplier_id_222233` int(11) DEFAULT NULL,
  `penawaran_id_222233` int(11) DEFAULT NULL,
  `gambar_obat_222233` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman_obat_222233`
--

INSERT INTO `pengiriman_obat_222233` (`pengiriman_id_222233`, `obat_id_222233`, `jumlah_222233`, `tanggal_pengiriman_222233`, `status_pengiriman_222233`, `supplier_id_222233`, `penawaran_id_222233`, `gambar_obat_222233`) VALUES
(21, 25, 10, '2025-06-13', 'diterima', 5, 20, 'obat_684b86173095e8.90083645.jpeg'),
(22, 26, 10, '2025-06-16', 'diterima', 6, 21, 'obat_684b8e1e745689.36999677.jpeg'),
(23, 28, 20, '2025-06-21', 'diterima', 6, 23, 'obat_6850c9f786ae20.85329061.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_222233`
--

CREATE TABLE `supplier_222233` (
  `supplier_id_222233` int(11) NOT NULL,
  `user_id_222233` int(11) NOT NULL,
  `nama_perusahaan_222233` varchar(255) NOT NULL,
  `alamat_222233` text NOT NULL,
  `no_telp_222233` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_222233`
--

INSERT INTO `supplier_222233` (`supplier_id_222233`, `user_id_222233`, `nama_perusahaan_222233`, `alamat_222233`, `no_telp_222233`) VALUES
(1, 3, 'PT. Akmal', 'sriwijaya', '081234567890'),
(5, 15, 'supplier', '', '123000'),
(6, 18, 'pt borlindo', '', '08123'),
(7, 25, 'pt farma', '', '08123'),
(8, 33, 'pt abc', '', '08123');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_222233`
--

CREATE TABLE `transaksi_222233` (
  `transaksi_id_222233` int(11) NOT NULL,
  `kasir_id_222233` int(11) DEFAULT NULL,
  `user_id_222233` int(11) NOT NULL,
  `tanggal_transaksi_222233` datetime NOT NULL,
  `status_transaksi_222233` enum('pending','sukses','batal') DEFAULT 'pending',
  `total_harga_222233` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_222233`
--

INSERT INTO `transaksi_222233` (`transaksi_id_222233`, `kasir_id_222233`, `user_id_222233`, `tanggal_transaksi_222233`, `status_transaksi_222233`, `total_harga_222233`) VALUES
(15, 5, 16, '2025-06-17 09:43:06', 'sukses', 8000.00),
(16, 5, 16, '2025-06-17 09:47:40', 'sukses', 3500.00),
(17, NULL, 32, '2025-06-18 08:30:15', 'pending', 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users_222233`
--

CREATE TABLE `users_222233` (
  `user_id_222233` int(11) NOT NULL,
  `nama_222233` varchar(255) NOT NULL,
  `username_222233` varchar(100) NOT NULL,
  `email_222233` varchar(100) NOT NULL,
  `password_222233` varchar(255) NOT NULL,
  `role_222233` enum('admin','kasir','pembeli','supplier') NOT NULL,
  `created_at_222233` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_222233`
--

INSERT INTO `users_222233` (`user_id_222233`, `nama_222233`, `username_222233`, `email_222233`, `password_222233`, `role_222233`, `created_at_222233`) VALUES
(1, 'abdul', 'abdul', 'abdul@gmail.com', '$2y$10$I7XCpgdZcsnwbz5znvGQIOCqr8.oFWD7TvkAnK1KwBjk/J6TSndUq', 'admin', '2025-06-10 05:26:54'),
(2, 'thesa', 'thesa', 'thesa@gmail.com', '$2y$10$HtTT.pQ8FmbaEHZ5gULEjeDaFfmM/BncBTms2sjRZo7hc6m..dVky', 'kasir', '2025-06-10 06:04:36'),
(3, 'akmal', 'akmal', 'akmal@gmail.com', '$2y$10$GNLA8pDKR20cVqEON/40Ae4ZTBYOlq9KPqRrN.eetxA4kdikVaTZ2', 'supplier', '2025-06-10 06:49:42'),
(4, 'suci tahir', 'suci', 'suci@gmail.com', '$2y$10$7e5/aB6/LLxYVYRrU5.f3Oh9LTupclIC8czVFzhXyt1GRwoxxwtLa', 'pembeli', '2025-06-10 07:37:50'),
(8, 'aril maulana', 'aril', 'aril@gmail.com', '$2y$10$aNqz4xcVLxi.4l4s.Q6YuegVqPyzYc7Et5tKxUseNw/96vv4vdnUC', 'admin', '2025-06-11 01:54:15'),
(9, 'daniel', 'daniel', 'daniel@gmail.com', '$2y$10$pMlUCGh0VTDTnERiELd8g.uis6IjcRKUFqCOXj24ocgqXu5dK1l7G', 'pembeli', '2025-06-11 01:56:03'),
(10, 'ocang', 'ocang', 'ocang@gmail.com', '$2y$10$BJEga27AEAPS2ooyxvTBg.qIILnhe.7XVaLryU5VlR..b77m9cv6q', 'kasir', '2025-06-11 01:56:48'),
(12, 'pembeli1', 'pembeli', 'pembeli@gmail.com', '$2y$10$vuPprK55tGBxLExAbUsO6O34566z3wAR77lY.Ias0DD5RRQDvopN6', 'pembeli', '2025-06-13 01:16:04'),
(13, 'admin', 'admin', 'admin@gmail.com', '$2y$10$5N1NwgztIanIP72uxaGhyO6NeqSK.uHdw7bzPt/5s..PmxZtuI5kO', 'admin', '2025-06-13 01:16:47'),
(14, 'kasir', 'kasir', 'kasir@gmail.com', '$2y$10$3L5ZavAC6k0t/z5VHVTsAOEY5ucGzLxiQ2ITldes7S/MmfPwqNaz2', 'kasir', '2025-06-13 01:19:59'),
(15, 'supplier', 'supplier', 'supplier@gmail.com', '$2y$10$gYRd2s7JJ6cdRQW0QyKR2uCHSrr6Veuug4pGI9IdSqpZdeCZTENgS', 'supplier', '2025-06-13 01:21:32'),
(16, 'esi', 'esipembeli', 'esipembeli@gmail.com', '$2y$10$v.V.npMVCwYY1rcDWiESNe2gHDA42EKdBp.edm7IuFdLRlao52Ouu', 'pembeli', '2025-06-13 02:26:44'),
(17, 'esi', 'esikasir', 'esikasir@gmail.com', '$2y$10$rlcoFrDTfdaGANYHCPbVketzlhcq6ox4cx.9Zwa8fJjjUPucFbDmy', 'kasir', '2025-06-13 02:28:23'),
(18, 'esi', 'esisupplier', 'esisupplier@gmail.com', '$2y$10$TT7XoGsO85DFPpZRAAA/HeNMcF9ePytwc6pSpvxaE02nbf.rL0Qhq', 'supplier', '2025-06-13 02:31:36'),
(19, 'esi', 'esiadmin', 'esiadmin@gmail.com', '$2y$10$sr/MBpHcdZBhQHOvRGp9O.GBOJ6tnjmUQlmLePH2Ew.YKq9ZwMub.', 'admin', '2025-06-13 02:34:53'),
(23, 'nanaadmin', 'nanaadmin', 'nanaadmin@gmail.com', '$2y$10$niAMwRnXtNrHQtc6T0f9NeyRf10uwfAYcmvMTKBPkhQrCSBiitqwu', 'admin', '2025-06-16 02:44:14'),
(24, 'esi', 'esi1admin', 'esi1admin@gmail.com', '$2y$10$diqn9F7k6MjHGE44s2FRCud6TT/bLeKLpmM1Yf.nRwsHFd9ruiHjS', 'admin', '2025-06-16 02:47:57'),
(25, 'esi', 'esi1supplier', 'esi1supplier@gmail.com', '$2y$10$aqBjh28x03Dxncv1x1.vTOTz4zIWZ1omKYD2Ok9Q8/v0gAQ3uRp1K', 'supplier', '2025-06-16 03:42:51'),
(31, 'esi', 'esiadmin1', 'esiadmin1@gmail.com', '$2y$10$Ykshy7ws23sPaOqsydqKNu6r6JmTBXrEdZvzdBRHII.tgFvRGneXW', 'admin', '2025-06-18 00:14:17'),
(32, 'esi', 'esipembeli1', 'esipembeli1@gmail.com', '$2y$10$bXFdcCOpX.wWcNvESP21ouX8M1FBy4DCHuyUwv5ANuxVgQtipTxCS', 'pembeli', '2025-06-18 00:20:50'),
(33, 'esi', 'esisupplier1', 'esisupplier1@gmail.com', '$2y$10$.hdsuLdwuiyeR3lhA6GyTuLaMoqJr1TJqFSEFrQRdvGzcLpSQmRu.', 'supplier', '2025-06-18 00:26:16'),
(35, 'esi', 'esi1kasir', 'esi1kasir@gmail.com', '$2y$10$R1t8tHVfhGzvgByVgvr7DupN12DL3.6Apy567CYdo8lUW7x6Heax2', 'kasir', '2025-06-18 00:29:01'),
(36, 'esi', 'esikasir1', 'esikasir1@gmail.com', '$2y$10$SlF8nLJSbleuZQz8nW0ozOFGW1Begp39diA1fcVTRvyQq6y1YWCg.', 'kasir', '2025-06-18 00:31:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_222233`
--
ALTER TABLE `admin_222233`
  ADD PRIMARY KEY (`admin_id_222233`),
  ADD KEY `user_id_222233` (`user_id_222233`);

--
-- Indexes for table `kasir_222233`
--
ALTER TABLE `kasir_222233`
  ADD PRIMARY KEY (`kasir_id_222233`),
  ADD KEY `user_id_222233` (`user_id_222233`);

--
-- Indexes for table `keranjang_222233`
--
ALTER TABLE `keranjang_222233`
  ADD PRIMARY KEY (`keranjang_id_222233`),
  ADD KEY `fk_user_keranjang` (`user_id_222233`),
  ADD KEY `fk_obat_keranjang` (`obat_id_222233`),
  ADD KEY `fk_transaksi_keranjang` (`transaksi_id_222233`);

--
-- Indexes for table `obat_222233`
--
ALTER TABLE `obat_222233`
  ADD PRIMARY KEY (`obat_id_222233`);

--
-- Indexes for table `pembayaran_222233`
--
ALTER TABLE `pembayaran_222233`
  ADD PRIMARY KEY (`pembayaran_id_222233`),
  ADD KEY `keranjang_id_222233` (`keranjang_id_222233`),
  ADD KEY `transaksi_id_222233` (`transaksi_id_222233`);

--
-- Indexes for table `pembeli_222233`
--
ALTER TABLE `pembeli_222233`
  ADD PRIMARY KEY (`pembeli_id_222233`),
  ADD KEY `user_id_222233` (`user_id_222233`);

--
-- Indexes for table `penawaran_obat_222233`
--
ALTER TABLE `penawaran_obat_222233`
  ADD PRIMARY KEY (`penawaran_id_222233`),
  ADD KEY `fk_penawaran_supplier` (`supplier_id_222233`);

--
-- Indexes for table `pengiriman_obat_222233`
--
ALTER TABLE `pengiriman_obat_222233`
  ADD PRIMARY KEY (`pengiriman_id_222233`),
  ADD KEY `fk_pengiriman_supplier` (`supplier_id_222233`),
  ADD KEY `fk_pengiriman_penawaran` (`penawaran_id_222233`),
  ADD KEY `pengiriman_obat_222233_ibfk_1` (`obat_id_222233`);

--
-- Indexes for table `supplier_222233`
--
ALTER TABLE `supplier_222233`
  ADD PRIMARY KEY (`supplier_id_222233`),
  ADD KEY `user_id_222233` (`user_id_222233`);

--
-- Indexes for table `transaksi_222233`
--
ALTER TABLE `transaksi_222233`
  ADD PRIMARY KEY (`transaksi_id_222233`),
  ADD KEY `transaksi_222233_ibfk_1` (`kasir_id_222233`),
  ADD KEY `transaksi_222233_ibfk_2` (`user_id_222233`);

--
-- Indexes for table `users_222233`
--
ALTER TABLE `users_222233`
  ADD PRIMARY KEY (`user_id_222233`),
  ADD UNIQUE KEY `username_222233` (`username_222233`),
  ADD UNIQUE KEY `email_222233` (`email_222233`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_222233`
--
ALTER TABLE `admin_222233`
  ADD CONSTRAINT `admin_222233_ibfk_1` FOREIGN KEY (`user_id_222233`) REFERENCES `users_222233` (`user_id_222233`);

--
-- Constraints for table `kasir_222233`
--
ALTER TABLE `kasir_222233`
  ADD CONSTRAINT `kasir_222233_ibfk_1` FOREIGN KEY (`user_id_222233`) REFERENCES `users_222233` (`user_id_222233`);

--
-- Constraints for table `keranjang_222233`
--
ALTER TABLE `keranjang_222233`
  ADD CONSTRAINT `fk_obat_keranjang` FOREIGN KEY (`obat_id_222233`) REFERENCES `obat_222233` (`obat_id_222233`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transaksi_keranjang` FOREIGN KEY (`transaksi_id_222233`) REFERENCES `transaksi_222233` (`transaksi_id_222233`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_keranjang` FOREIGN KEY (`user_id_222233`) REFERENCES `users_222233` (`user_id_222233`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran_222233`
--
ALTER TABLE `pembayaran_222233`
  ADD CONSTRAINT `pembayaran_222233_ibfk_1` FOREIGN KEY (`keranjang_id_222233`) REFERENCES `keranjang_222233` (`keranjang_id_222233`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_222233_ibfk_2` FOREIGN KEY (`transaksi_id_222233`) REFERENCES `transaksi_222233` (`transaksi_id_222233`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembeli_222233`
--
ALTER TABLE `pembeli_222233`
  ADD CONSTRAINT `pembeli_222233_ibfk_1` FOREIGN KEY (`user_id_222233`) REFERENCES `users_222233` (`user_id_222233`);

--
-- Constraints for table `penawaran_obat_222233`
--
ALTER TABLE `penawaran_obat_222233`
  ADD CONSTRAINT `fk_penawaran_supplier` FOREIGN KEY (`supplier_id_222233`) REFERENCES `supplier_222233` (`supplier_id_222233`) ON DELETE CASCADE;

--
-- Constraints for table `pengiriman_obat_222233`
--
ALTER TABLE `pengiriman_obat_222233`
  ADD CONSTRAINT `fk_pengiriman_penawaran` FOREIGN KEY (`penawaran_id_222233`) REFERENCES `penawaran_obat_222233` (`penawaran_id_222233`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengiriman_supplier` FOREIGN KEY (`supplier_id_222233`) REFERENCES `supplier_222233` (`supplier_id_222233`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengiriman_obat_222233_ibfk_1` FOREIGN KEY (`obat_id_222233`) REFERENCES `obat_222233` (`obat_id_222233`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_222233`
--
ALTER TABLE `supplier_222233`
  ADD CONSTRAINT `supplier_222233_ibfk_1` FOREIGN KEY (`user_id_222233`) REFERENCES `users_222233` (`user_id_222233`);

--
-- Constraints for table `transaksi_222233`
--
ALTER TABLE `transaksi_222233`
  ADD CONSTRAINT `transaksi_222233_ibfk_1` FOREIGN KEY (`kasir_id_222233`) REFERENCES `kasir_222233` (`kasir_id_222233`),
  ADD CONSTRAINT `transaksi_222233_ibfk_2` FOREIGN KEY (`user_id_222233`) REFERENCES `users_222233` (`user_id_222233`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
