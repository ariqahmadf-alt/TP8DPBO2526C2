-- Database: tp_mvc25
CREATE DATABASE IF NOT EXISTS `tp_mvc25` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tp_mvc25`;

DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `lecturers`;
DROP TABLE IF EXISTS `departments`;

-- Program studi (tabel baru)
CREATE TABLE `departments` (
  `id`   INT NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(10) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_departments_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dosen (tabel awal, ditambah kolom department_id)
CREATE TABLE `lecturers` (
  `id`            INT NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100) NOT NULL,
  `nidn`          VARCHAR(20) NOT NULL,
  `phone`         VARCHAR(20) NOT NULL,
  `join_date`     DATE NOT NULL,
  `department_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lecturers_nidn` (`nidn`),
  KEY `idx_lecturers_department` (`department_id`),
  CONSTRAINT `fk_lecturers_department` FOREIGN KEY (`department_id`)
    REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mata kuliah (tabel baru)
CREATE TABLE `courses` (
  `id`          INT NOT NULL AUTO_INCREMENT,
  `code`        VARCHAR(10) NOT NULL,
  `name`        VARCHAR(100) NOT NULL,
  `credits`     TINYINT UNSIGNED NOT NULL,
  `semester`    TINYINT UNSIGNED NOT NULL,
  `lecturer_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_courses_code` (`code`),
  KEY `idx_courses_lecturer` (`lecturer_id`),
  CONSTRAINT `fk_courses_lecturer` FOREIGN KEY (`lecturer_id`)
    REFERENCES `lecturers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `departments` (`id`, `code`, `name`) VALUES
(1, 'IK',  'Ilmu Komputer'),
(2, 'SI',  'Sistem Informasi'),
(3, 'RPL', 'Rekayasa Perangkat Lunak');

INSERT INTO `lecturers` (`id`, `name`, `nidn`, `phone`, `join_date`, `department_id`) VALUES
(1, 'Dr. Rina Kartika, M.Kom.',  '0412058801', '081234567801', '2015-08-17', 1),
(2, 'Budi Santoso, M.T.',        '0423078302', '081234567802', '2012-02-01', 1),
(3, 'Dewi Lestari, M.Cs.',       '0415099003', '081234567803', '2019-09-02', 2),
(4, 'Agus Prasetyo, Ph.D.',      '0430107504', '081234567804', '2010-03-15', 3),
(5, 'Maya Anggraini, M.Kom.',    '0408119205', '081234567805', '2021-01-11', NULL);

INSERT INTO `courses` (`id`, `code`, `name`, `credits`, `semester`, `lecturer_id`) VALUES
(1, 'IK201',  'Desain dan Pemrograman Berbasis Objek', 3, 3, 1),
(2, 'IK202',  'Struktur Data',                        3, 3, 2),
(3, 'IK301',  'Kecerdasan Buatan',                    3, 5, 1),
(4, 'SI210',  'Basis Data',                           3, 3, 3),
(5, 'RPL220', 'Rekayasa Perangkat Lunak',             3, 4, 4),
(6, 'IK305',  'Pemrograman Web dan Mobile',           3, 5, NULL);
