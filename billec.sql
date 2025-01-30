-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 07:31 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `billec`
--
CREATE DATABASE IF NOT EXISTS `billec` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `billec`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `get_pelanggan_belum_bayar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_pelanggan_belum_bayar` ()   BEGIN
    SELECT 
        p.id_pelanggan,
        p.nama_pelanggan,
        t.bulan,
        t.tahun,
        t.jumlah_meter,
        t.status
    FROM 
        pelanggan p
    JOIN 
        tagihan t ON p.id_pelanggan = t.id_pelanggan
    WHERE 
        t.status = 'Belum Bayar'; 
END$$

DROP PROCEDURE IF EXISTS `get_pelanggan_daya`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_pelanggan_daya` (IN `daya` INT)   BEGIN
    SELECT 
        p.id_pelanggan,
        p.nama_pelanggan,
        t.daya
    FROM 
        pelanggan p
    JOIN 
        tarif t ON p.id_tarif = t.id_tarif
    WHERE 
        t.daya = daya;
END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `hitung_tarif_perkwh`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `hitung_tarif_perkwh` (`id_pelanggan_input` INT) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE tarif_perkwh DECIMAL(10,2);
    SELECT 
        tarifperkwh INTO tarif_perkwh
    FROM 
        tarif
    WHERE 
        id_tarif = (SELECT id_tarif FROM pelanggan WHERE id_pelanggan = id_pelanggan_input);
    RETURN tarif_perkwh;
END$$

DROP FUNCTION IF EXISTS `hitung_total_penggunaan`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `hitung_total_penggunaan` (`id_pelanggan_input` INT, `bulan_input` INT, `tahun_input` INT) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total_penggunaan INT;
    SELECT 
        SUM(meter_akhir - meter_awal) INTO total_penggunaan
    FROM 
        penggunaan
    WHERE 
        id_pelanggan = id_pelanggan_input 
        AND bulan = bulan_input 
        AND tahun = tahun_input;
    RETURN total_penggunaan;
END$$

DROP FUNCTION IF EXISTS `hitung_total_tagihan`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `hitung_total_tagihan` (`id_pelanggan_input` INT, `bulan_input` INT, `tahun_input` INT) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE total_tagihan DECIMAL(10,2);
    SELECT 
        hitung_total_penggunaan * hitung_tarif_perkwh + 2500 INTO total_tagihan
    FROM 
        penggunaan
    WHERE 
        id_pelanggan = id_pelanggan_input 
        AND bulan = bulan_input 
        AND tahun = tahun_input;
    RETURN total_tagihan;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

DROP TABLE IF EXISTS `level`;
CREATE TABLE IF NOT EXISTS `level` (
  `id_level` int(11) NOT NULL AUTO_INCREMENT,
  `nama_level` varchar(50) NOT NULL,
  PRIMARY KEY (`id_level`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`id_level`, `nama_level`) VALUES
(1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

DROP TABLE IF EXISTS `pelanggan`;
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nomor_kwh` varchar(20) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `id_tarif` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_pelanggan`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nomor_kwh` (`nomor_kwh`),
  KEY `idx_pelanggan_id_tarif` (`id_tarif`),
  KEY `idx_pelanggan_username` (`username`),
  KEY `idx_id_tarif` (`id_tarif`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `username`, `password`, `nomor_kwh`, `nama_pelanggan`, `alamat`, `id_tarif`) VALUES
(1, 'arifachan', '$2y$10$hWB6cRLAm792Xz18eFDoeuZ3nFcvlRzcnp1MGtHH3M.NS07gzqOc2', '19215277', 'Arifa Chan', 'Bogor', 2),
(2, 'prilly', '$2y$10$66waQW.uysvlSUQvPq2Kj.IeRm6GjAbpCCrjceEpO5ATskddh1G3q', '19215278', 'Prilly Pricillia Alda', 'Palembang', 4),
(3, 'fimel', '$2y$10$S5sSgqhCyNvAck7jZBbXDeDw9IiyCDRaRUt8yaO0syAgmWTo3niJK', '19215280', 'Fitri meliani', 'Tasikmalaya', 3),
(4, 'budi', '$2y$10$dKzVbjj9UUlCUt60Mm7b8OXSFaFZjOa3EDKuROz3Zton2HXvqWaka', '19215279', 'Budi', 'Jakarta', 5),
(5, 'brandon', '$2y$10$ROpeHHxqSxZwL3xKrKHQQub9BTnxA/f83gRNRNwX94/R07xHqjBW2', '19213362', 'Brandon Jevri Wanta', 'Medan', 6);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT,
  `id_tagihan` int(11) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `tanggal_pembayaran` date NOT NULL,
  `bulan_bayar` tinyint(4) NOT NULL CHECK (`bulan_bayar` between 1 and 12),
  `biaya_admin` decimal(10,2) NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  KEY `id_user` (`id_user`),
  KEY `idx_id_tagihan` (`id_tagihan`),
  KEY `idx_id_pelanggan_pembayaran` (`id_pelanggan`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_tagihan`, `id_pelanggan`, `tanggal_pembayaran`, `bulan_bayar`, `biaya_admin`, `total_bayar`, `id_user`) VALUES
(1, 1, 1, '2024-01-03', 1, '2500.00', '270400.00', 1),
(2, 2, 2, '2024-01-01', 1, '2500.00', '576280.00', 1),
(3, 3, 3, '2024-01-01', 1, '2500.00', '433410.00', 1),
(4, 1, 1, '2025-01-27', 1, '2500.00', '272900.00', 1),
(5, 2, 2, '2025-01-27', 1, '2500.00', '578780.00', 1),
(6, 3, 3, '2025-01-29', 1, '2500.00', '435910.00', 1),
(7, 27, 5, '2025-01-30', 1, '2500.00', '367898.95', 1);

-- --------------------------------------------------------

--
-- Table structure for table `penggunaan`
--

DROP TABLE IF EXISTS `penggunaan`;
CREATE TABLE IF NOT EXISTS `penggunaan` (
  `id_penggunaan` int(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int(11) DEFAULT NULL,
  `bulan` tinyint(4) NOT NULL CHECK (`bulan` between 1 and 12),
  `tahun` year(4) NOT NULL,
  `meter_awal` int(11) NOT NULL,
  `meter_akhir` int(11) NOT NULL,
  PRIMARY KEY (`id_penggunaan`),
  KEY `idx_id_pelanggan` (`id_pelanggan`),
  KEY `idx_bulan_tahun` (`bulan`,`tahun`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `penggunaan`
--

INSERT INTO `penggunaan` (`id_penggunaan`, `id_pelanggan`, `bulan`, `tahun`, `meter_awal`, `meter_akhir`) VALUES
(1, 1, 12, 2024, 0, 200),
(2, 2, 12, 2024, 0, 400),
(3, 3, 12, 2024, 0, 300),
(22, 4, 12, 2024, 0, 350),
(23, 1, 1, 2025, 200, 354),
(24, 2, 1, 2025, 400, 767),
(25, 3, 1, 2025, 300, 675),
(26, 4, 1, 2025, 350, 722),
(30, 5, 12, 2024, 0, 215);

--
-- Triggers `penggunaan`
--
DROP TRIGGER IF EXISTS `after_insert_penggunaan`;
DELIMITER $$
CREATE TRIGGER `after_insert_penggunaan` AFTER INSERT ON `penggunaan` FOR EACH ROW BEGIN
    INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status, total_tagihan)
    VALUES (NEW.id_penggunaan, NEW.id_pelanggan, NEW.bulan, NEW.tahun, (NEW.meter_akhir - NEW.meter_awal), 'belum bayar', (NEW.meter_akhir - NEW.meter_awal) * (SELECT tarifperkwh FROM tarif WHERE id_tarif = (SELECT id_tarif FROM pelanggan WHERE id_pelanggan = NEW.id_pelanggan)));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

DROP TABLE IF EXISTS `tagihan`;
CREATE TABLE IF NOT EXISTS `tagihan` (
  `id_tagihan` int(11) NOT NULL AUTO_INCREMENT,
  `id_penggunaan` int(11) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `bulan` tinyint(4) NOT NULL CHECK (`bulan` between 1 and 12),
  `tahun` year(4) NOT NULL,
  `jumlah_meter` int(11) NOT NULL,
  `status` enum('belum bayar','lunas') NOT NULL DEFAULT 'belum bayar',
  `total_tagihan` decimal(10,2) DEFAULT 0.00,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_tagihan`),
  KEY `idx_id_penggunaan` (`id_penggunaan`),
  KEY `idx_id_pelanggan_tagihan` (`id_pelanggan`),
  KEY `idx_bulan_tahun_tagihan` (`bulan`,`tahun`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tagihan`
--

INSERT INTO `tagihan` (`id_tagihan`, `id_penggunaan`, `id_pelanggan`, `bulan`, `tahun`, `jumlah_meter`, `status`, `total_tagihan`, `bukti_pembayaran`) VALUES
(1, 1, 1, 12, 2024, 200, 'lunas', '270400.00', 'default.jpg'),
(2, 2, 2, 12, 2024, 400, 'lunas', '576280.00', 'default.jpg'),
(3, 3, 3, 12, 2024, 300, 'lunas', '433410.00', 'bukti_3_1738138098.jpg'),
(22, 22, 4, 12, 2024, 350, 'belum bayar', '594835.50', 'bukti_22_1738144728.jpg'),
(23, 23, 1, 1, 2025, 154, 'belum bayar', '208208.00', ''),
(24, 24, 2, 1, 2025, 367, 'belum bayar', '530204.90', ''),
(25, 25, 3, 1, 2025, 375, 'belum bayar', '541762.50', ''),
(26, 26, 4, 1, 2025, 372, 'belum bayar', '632225.16', ''),
(27, 30, 5, 12, 2024, 215, 'lunas', '365398.95', 'bukti_27_1738218245.jpg');

--
-- Triggers `tagihan`
--
DROP TRIGGER IF EXISTS `after_update_tagihan`;
DELIMITER $$
CREATE TRIGGER `after_update_tagihan` AFTER UPDATE ON `tagihan` FOR EACH ROW BEGIN
    IF NEW.status = 'lunas' THEN
        INSERT INTO pembayaran (id_tagihan, id_pelanggan, tanggal_pembayaran, bulan_bayar, biaya_admin, total_bayar, id_user)
        VALUES (NEW.id_tagihan, NEW.id_pelanggan, CURDATE(), MONTH(CURDATE()), 2500, NEW.total_tagihan + 2500, 1);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tarif`
--

DROP TABLE IF EXISTS `tarif`;
CREATE TABLE IF NOT EXISTS `tarif` (
  `id_tarif` int(11) NOT NULL AUTO_INCREMENT,
  `daya` int(11) NOT NULL,
  `tarifperkwh` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_tarif`),
  UNIQUE KEY `daya_unique` (`daya`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tarif`
--

INSERT INTO `tarif` (`id_tarif`, `daya`, `tarifperkwh`) VALUES
(1, 450, '415.00'),
(2, 900, '1352.00'),
(3, 1300, '1444.70'),
(4, 2300, '1444.70'),
(5, 3500, '1699.53'),
(6, 5500, '1699.53');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `id_level` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  KEY `id_level` (`id_level`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_admin`, `id_level`) VALUES
(1, 'admin', '$2y$10$0zQ3BGWebmU83g74c1kxw.qqrp87xmzM0DvC02GRo4pUt7k2HqGS6', 'Administrator', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_laporan_pembayaran`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_laporan_pembayaran`;
CREATE TABLE IF NOT EXISTS `view_laporan_pembayaran` (
`id_pembayaran` int(11)
,`id_tagihan` int(11)
,`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`nomor_kwh` varchar(20)
,`daya_listrik` int(11)
,`bulan` tinyint(4)
,`tahun` year(4)
,`jumlah_meter` int(11)
,`biaya_admin` decimal(10,2)
,`tanggal_pembayaran` date
,`total_bayar` decimal(10,2)
,`nama_admin` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pelanggan_relevan`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_pelanggan_relevan`;
CREATE TABLE IF NOT EXISTS `view_pelanggan_relevan` (
`id_pelanggan` int(11)
,`username` varchar(50)
,`nomor_kwh` varchar(20)
,`nama_pelanggan` varchar(100)
,`alamat` text
,`daya_listrik` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_penggunaan_listrik`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_penggunaan_listrik`;
CREATE TABLE IF NOT EXISTS `view_penggunaan_listrik` (
`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`bulan` tinyint(4)
,`tahun` year(4)
,`meter_awal` int(11)
,`meter_akhir` int(11)
,`total_penggunaan` bigint(12)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_tagihan_informatif`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_tagihan_informatif`;
CREATE TABLE IF NOT EXISTS `view_tagihan_informatif` (
`id_tagihan` int(11)
,`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`nomor_kwh` varchar(20)
,`daya_listrik` int(11)
,`bulan` tinyint(4)
,`tahun` year(4)
,`jumlah_meter` int(11)
,`total_tagihan` decimal(10,2)
,`status` enum('belum bayar','lunas')
,`bukti_pembayaran` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `view_laporan_pembayaran`
--
DROP TABLE IF EXISTS `view_laporan_pembayaran`;

DROP VIEW IF EXISTS `view_laporan_pembayaran`;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_laporan_pembayaran`  AS SELECT `p`.`id_pembayaran` AS `id_pembayaran`, `t`.`id_tagihan` AS `id_tagihan`, `pel`.`id_pelanggan` AS `id_pelanggan`, `pel`.`nama_pelanggan` AS `nama_pelanggan`, `pel`.`nomor_kwh` AS `nomor_kwh`, `tarif`.`daya` AS `daya_listrik`, `t`.`bulan` AS `bulan`, `t`.`tahun` AS `tahun`, `t`.`jumlah_meter` AS `jumlah_meter`, `p`.`biaya_admin` AS `biaya_admin`, `p`.`tanggal_pembayaran` AS `tanggal_pembayaran`, `p`.`total_bayar` AS `total_bayar`, `u`.`nama_admin` AS `nama_admin` FROM ((((`pembayaran` `p` join `tagihan` `t` on(`p`.`id_tagihan` = `t`.`id_tagihan`)) join `pelanggan` `pel` on(`p`.`id_pelanggan` = `pel`.`id_pelanggan`)) join `tarif` on(`pel`.`id_tarif` = `tarif`.`id_tarif`)) join `user` `u` on(`p`.`id_user` = `u`.`id_user`))  ;

-- --------------------------------------------------------

--
-- Structure for view `view_pelanggan_relevan`
--
DROP TABLE IF EXISTS `view_pelanggan_relevan`;

DROP VIEW IF EXISTS `view_pelanggan_relevan`;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pelanggan_relevan`  AS SELECT `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`username` AS `username`, `p`.`nomor_kwh` AS `nomor_kwh`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `p`.`alamat` AS `alamat`, `t`.`daya` AS `daya_listrik` FROM (`pelanggan` `p` join `tarif` `t` on(`p`.`id_tarif` = `t`.`id_tarif`))  ;

-- --------------------------------------------------------

--
-- Structure for view `view_penggunaan_listrik`
--
DROP TABLE IF EXISTS `view_penggunaan_listrik`;

DROP VIEW IF EXISTS `view_penggunaan_listrik`;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_penggunaan_listrik`  AS SELECT `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `pg`.`bulan` AS `bulan`, `pg`.`tahun` AS `tahun`, `pg`.`meter_awal` AS `meter_awal`, `pg`.`meter_akhir` AS `meter_akhir`, `pg`.`meter_akhir`- `pg`.`meter_awal` AS `total_penggunaan` FROM (`pelanggan` `p` join `penggunaan` `pg` on(`p`.`id_pelanggan` = `pg`.`id_pelanggan`))  ;

-- --------------------------------------------------------

--
-- Structure for view `view_tagihan_informatif`
--
DROP TABLE IF EXISTS `view_tagihan_informatif`;

DROP VIEW IF EXISTS `view_tagihan_informatif`;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_tagihan_informatif`  AS SELECT `t`.`id_tagihan` AS `id_tagihan`, `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `p`.`nomor_kwh` AS `nomor_kwh`, `tarif`.`daya` AS `daya_listrik`, `t`.`bulan` AS `bulan`, `t`.`tahun` AS `tahun`, `t`.`jumlah_meter` AS `jumlah_meter`, `t`.`total_tagihan` AS `total_tagihan`, `t`.`status` AS `status`, `t`.`bukti_pembayaran` AS `bukti_pembayaran` FROM ((`tagihan` `t` join `pelanggan` `p` on(`t`.`id_pelanggan` = `p`.`id_pelanggan`)) join `tarif` on(`p`.`id_tarif` = `tarif`.`id_tarif`))  ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`id_tarif`) REFERENCES `tarif` (`id_tarif`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `pembayaran_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `penggunaan`
--
ALTER TABLE `penggunaan`
  ADD CONSTRAINT `penggunaan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

--
-- Constraints for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `tagihan_ibfk_1` FOREIGN KEY (`id_penggunaan`) REFERENCES `penggunaan` (`id_penggunaan`),
  ADD CONSTRAINT `tagihan_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_level`) REFERENCES `level` (`id_level`);

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `penggunaan_bulanan`$$
CREATE DEFINER=`root`@`localhost` EVENT `penggunaan_bulanan` ON SCHEDULE EVERY 1 MONTH STARTS '2024-02-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Update data penggunaan untuk pelanggan yang sudah memiliki data bulan berjalan
    UPDATE penggunaan p
    JOIN (
        SELECT 
            id_pelanggan,
            meter_akhir AS meter_awal_baru,
            meter_akhir + FLOOR(RAND() * 300 + 100) AS meter_akhir_baru
        FROM penggunaan
        WHERE 
            bulan = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
            AND tahun = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
    ) temp ON p.id_pelanggan = temp.id_pelanggan
       AND p.bulan = MONTH(CURRENT_DATE)
       AND p.tahun = YEAR(CURRENT_DATE)
    SET 
        p.meter_awal = temp.meter_awal_baru,
        p.meter_akhir = temp.meter_akhir_baru;

    -- Insert data penggunaan baru untuk pelanggan yang belum memiliki data bulan berjalan
    INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir)
    SELECT 
        id_pelanggan,
        MONTH(CURRENT_DATE) AS bulan,
        YEAR(CURRENT_DATE) AS tahun,
        meter_akhir AS meter_awal,
        meter_akhir + FLOOR(RAND() * 300 + 100) AS meter_akhir
    FROM penggunaan
    WHERE 
        bulan = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
        AND tahun = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
        AND NOT EXISTS (
            SELECT 1
            FROM penggunaan u
            WHERE u.id_pelanggan = penggunaan.id_pelanggan
              AND u.bulan = MONTH(CURRENT_DATE)
              AND u.tahun = YEAR(CURRENT_DATE)
        );

    -- Insert data penggunaan untuk pelanggan baru yang belum memiliki data penggunaan sama sekali
    INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir)
    SELECT 
        p.id_pelanggan,
        MONTH(CURRENT_DATE) AS bulan,
        YEAR(CURRENT_DATE) AS tahun,
        0 AS meter_awal,
        FLOOR(RAND() * 300 + 100) AS meter_akhir
    FROM pelanggan p
    WHERE NOT EXISTS (
        SELECT 1
        FROM penggunaan u
        WHERE u.id_pelanggan = p.id_pelanggan
    );
END$$

DROP EVENT IF EXISTS `tagihan_bulanan`$$
CREATE DEFINER=`root`@`localhost` EVENT `tagihan_bulanan` ON SCHEDULE EVERY 1 MONTH STARTS '2024-02-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Update tagihan jika data sudah ada untuk pelanggan lama
    UPDATE tagihan t
    JOIN (
        SELECT 
            p.id_penggunaan,
            p.id_pelanggan,
            MONTH(CURRENT_DATE) AS bulan,
            YEAR(CURRENT_DATE) AS tahun,
            (p.meter_akhir - p.meter_awal) AS jumlah_meter,
            (p.meter_akhir - p.meter_awal) * tf.tarifperkwh AS total_tagihan
        FROM 
            penggunaan p
        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        JOIN tarif tf ON pl.id_tarif = tf.id_tarif
        WHERE 
            p.bulan = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
            AND p.tahun = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
    ) temp ON t.id_pelanggan = temp.id_pelanggan 
          AND t.bulan = MONTH(CURRENT_DATE)
          AND t.tahun = YEAR(CURRENT_DATE)
    SET 
        t.jumlah_meter = temp.jumlah_meter,
        t.total_tagihan = temp.total_tagihan,
        t.status = 'belum bayar';

    -- Insert tagihan untuk pelanggan baru atau jika belum ada data tagihan
    INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status, total_tagihan)
    SELECT 
        p.id_penggunaan,
        p.id_pelanggan,
        MONTH(CURRENT_DATE) AS bulan,
        YEAR(CURRENT_DATE) AS tahun,
        (p.meter_akhir - p.meter_awal) AS jumlah_meter,
        'belum bayar' AS status,
        (p.meter_akhir - p.meter_awal) * tf.tarifperkwh AS total_tagihan
    FROM 
        penggunaan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN tarif tf ON pl.id_tarif = tf.id_tarif
    WHERE 
        p.bulan = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
        AND p.tahun = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
        AND NOT EXISTS (
            SELECT 1
            FROM tagihan t
            WHERE t.id_pelanggan = p.id_pelanggan
              AND t.bulan = MONTH(CURRENT_DATE)
              AND t.tahun = YEAR(CURRENT_DATE)
        );
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
