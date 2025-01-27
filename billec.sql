CREATE DATABASE billec;
USE billec;

-- Tabel Level
CREATE TABLE level (
    id_level INT AUTO_INCREMENT PRIMARY KEY,
    nama_level VARCHAR(50) NOT NULL
);

-- Tabel User
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    nama_admin VARCHAR(100) NOT NULL,
    id_level INT,
    FOREIGN KEY (id_level) REFERENCES level(id_level)
);

-- Tabel Tarif
CREATE TABLE tarif (
    id_tarif INT AUTO_INCREMENT PRIMARY KEY,
    daya INT NOT NULL UNIQUE,
    tarifperkwh DECIMAL(10,2) NOT NULL
);

-- Tabel Pelanggan
CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    nomor_kwh VARCHAR(20) NOT NULL UNIQUE,
    nama_pelanggan VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    id_tarif INT,
    FOREIGN KEY (id_tarif) REFERENCES tarif(id_tarif)
);

-- Tabel Penggunaan
CREATE TABLE penggunaan (
    id_penggunaan INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT,
    bulan TINYINT NOT NULL CHECK (bulan BETWEEN 1 AND 12),
    tahun YEAR NOT NULL,
    meter_awal INT NOT NULL,
    meter_akhir INT NOT NULL,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan)
);

-- Tabel Tagihan
CREATE TABLE tagihan (
    id_tagihan INT AUTO_INCREMENT PRIMARY KEY,
    id_penggunaan INT,
    id_pelanggan INT,
    bulan TINYINT NOT NULL CHECK (bulan BETWEEN 1 AND 12),
    tahun YEAR NOT NULL,
    jumlah_meter INT NOT NULL,
    status ENUM('belum bayar', 'lunas') NOT NULL DEFAULT 'belum bayar',
    FOREIGN KEY (id_penggunaan) REFERENCES penggunaan(id_penggunaan),
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan)
);

-- Tabel Pembayaran
CREATE TABLE pembayaran (
    id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
    id_tagihan INT,
    id_pelanggan INT,
    tanggal_pembayaran DATE NOT NULL,
    bulan_bayar TINYINT NOT NULL CHECK (bulan_bayar BETWEEN 1 AND 12),
    biaya_admin DECIMAL(10,2) NOT NULL,
    total_bayar DECIMAL(10,2) NOT NULL,
    id_user INT,
    FOREIGN KEY (id_tagihan) REFERENCES tagihan(id_tagihan),
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
);

ALTER TABLE tarif ADD CONSTRAINT daya_unique UNIQUE (daya);

ALTER TABLE tagihan ADD COLUMN bukti_pembayaran VARCHAR(255) DEFAULT 'default.jpg';

-- Insert Data
INSERT INTO level (nama_level) VALUES ('admin');

INSERT INTO tarif (daya, tarifperkwh) VALUES (450, 415), (900, 1352), (1300, 1444.70), (2300, 1444.70), (3500, 1699.53), (5500, 1699.53);

INSERT INTO user (username, password, nama_admin, id_level) VALUES ('admin', 'admin', 'Admin', 1);

INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif) VALUES
 ('arifachan', '123', '19215277', 'Arifa Chan', 'Bogor', 2),
 ('prilly', '123', '19215278', 'Prilly Pricillia Alda', 'Palembang', 4),
 ('fimel', '123', '19215280', 'Fitri meliani', 'Tasikmalaya', 3);

INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) VALUES
 (1, 12, 2024, 0, 200),
 (2, 12, 2024, 0, 400),
 (3, 12, 2024, 0, 300);

INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter) VALUES
 (1, 1, 12, 2024, 200),
 (2, 2, 12, 2024, 400),
 (3, 3, 12, 2024, 300);

INSERT INTO pembayaran (id_tagihan, id_pelanggan, tanggal_pembayaran, bulan_bayar, biaya_admin, total_bayar, id_user) VALUES
 (1, 1, '2024-01-03', 1, 2500, 270400, 1),
 (2, 2, '2024-01-01', 1, 2500, 576280, 1),
 (3, 3, '2024-01-01', 1, 2500, 433410, 1);

-- View
CREATE VIEW view_penggunaan_listrik AS
SELECT 
    p.id_pelanggan,
    p.nama_pelanggan,
    pg.bulan,
    pg.tahun,
    pg.meter_awal,
    pg.meter_akhir,
    (pg.meter_akhir - pg.meter_awal) AS total_penggunaan
FROM 
    pelanggan p
JOIN 
    penggunaan pg ON p.id_pelanggan = pg.id_pelanggan;

-- Procedure
DELIMITER //
CREATE PROCEDURE get_pelanggan_daya(IN daya INT)
BEGIN
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
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE get_pelanggan_belum_bayar()
BEGIN
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
        t.status = 'Belum Bayar'; -- Status "Belum Bayar" harus sesuai dengan nilai di database
END //

DELIMITER ;

-- Function
DELIMITER //
CREATE FUNCTION hitung_total_penggunaan(id_pelanggan_input INT, bulan_input INT, tahun_input INT)
RETURNS INT
DETERMINISTIC
BEGIN
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
END //
DELIMITER ;

DELIMITER //
CREATE FUNCTION hitung_tarif_perkwh(id_pelanggan_input INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE tarif_perkwh DECIMAL(10,2);
    SELECT 
        tarifperkwh INTO tarif_perkwh
    FROM 
        tarif
    WHERE 
        id_tarif = (SELECT id_tarif FROM pelanggan WHERE id_pelanggan = id_pelanggan_input);
    RETURN tarif_perkwh;
END //
DELIMITER ;

DELIMITER //
CREATE FUNCTION hitung_total_tagihan(id_pelanggan_input INT, bulan_input INT, tahun_input INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
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
END //
DELIMITER ;

-- Trigger
DELIMITER //
CREATE TRIGGER after_insert_penggunaan
AFTER INSERT ON penggunaan
FOR EACH ROW
BEGIN
    INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status, total_tagihan)
    VALUES (NEW.id_penggunaan, NEW.id_pelanggan, NEW.bulan, NEW.tahun, hitung_total_penggunaan(NEW.id_pelanggan, NEW.bulan, NEW.tahun), 'belum bayar', 
    hitung_total_tagihan(NEW.id_pelanggan, NEW.bulan, NEW.tahun));
END //
DELIMITER ;


DELIMITER //
CREATE TRIGGER after_update_tagihan
AFTER UPDATE ON tagihan
FOR EACH ROW
BEGIN
    IF NEW.status = 'lunas' THEN
        INSERT INTO pembayaran (id_tagihan, id_pelanggan, tanggal_pembayaran, bulan_bayar, biaya_admin, total_bayar, id_user)
        VALUES (NEW.id_tagihan, NEW.id_pelanggan, CURDATE(), MONTH(CURDATE()), 2500, NEW.total_tagihan, 1);
    END IF;
END //
DELIMITER ;

-- Commit example
START TRANSACTION;
INSERT INTO tarif (daya, tarifperkwh) VALUES (6600, 1699.53);
COMMIT;

-- Rollback example
START TRANSACTION;
DELETE FROM Pelanggan WHERE id_pelanggan = 4;
ROLLBACK;

-- Event
DELIMITER //
CREATE EVENT penggunaan_bulanan
ON SCHEDULE EVERY 1 MONTH
STARTS '2024-02-01 00:00:00'
DO
IF NOT EXISTS (SELECT * FROM penggunaan WHERE bulan = MONTH(CURRENT_DATE) AND tahun = YEAR(CURRENT_DATE)) THEN
    INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir)
    SELECT 
        id_pelanggan,
        MONTH(CURRENT_DATE) as bulan,
        YEAR(CURRENT_DATE) as tahun,
        meter_akhir,
        meter_akhir + FLOOR(RAND() * 300 + 100) AS meter_akhir
    FROM
        pelanggan
    WHERE
        bulan = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND tahun = YEAR(CURRENT_DATE - INTERVAL 1 MONTH);
END IF;
END //
DELIMITER ;

DELIMITER //
CREATE EVENT penggunaan_bulanan
ON SCHEDULE EVERY 1 MONTH
STARTS '2024-02-01 00:00:00'
DO
BEGIN
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
END //
DELIMITER ;


DELIMITER //
CREATE EVENT tagihan_bulanan
ON SCHEDULE EVERY 1 MONTH
STARTS '2024-02-01 00:00:00'
DO
BEGIN
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
END //
DELIMITER ;
