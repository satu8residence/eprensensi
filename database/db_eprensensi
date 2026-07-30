-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: eprensensi
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cabang`
--

DROP TABLE IF EXISTS `cabang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cabang` (
  `kode_cabang` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_cabang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi_cabang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `radius_cabang` smallint NOT NULL,
  PRIMARY KEY (`kode_cabang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabang`
--

LOCK TABLES `cabang` WRITE;
/*!40000 ALTER TABLE `cabang` DISABLE KEYS */;
INSERT INTO `cabang` VALUES ('S8','Satu8','-6.187243,106.757976',100);
/*!40000 ALTER TABLE `cabang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departemen`
--

DROP TABLE IF EXISTS `departemen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departemen` (
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dept` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`kode_dept`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departemen`
--

LOCK TABLES `departemen` WRITE;
/*!40000 ALTER TABLE `departemen` DISABLE KEYS */;
INSERT INTO `departemen` VALUES ('BM','Building Management'),('ENG','Engineering'),('FIN','Finance & Accounting'),('GA','General Affair'),('HK','House Keeping'),('HRD','HRD'),('IT','IT'),('TR','Tenant Relation');
/*!40000 ALTER TABLE `departemen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_hak_cuti`
--

DROP TABLE IF EXISTS `hrd_hak_cuti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_hak_cuti` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_cuti` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` int NOT NULL,
  `jml_hari` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hrd_hak_cuti_nik_foreign` (`nik`),
  KEY `hrd_hak_cuti_kode_cuti_foreign` (`kode_cuti`),
  CONSTRAINT `hrd_hak_cuti_kode_cuti_foreign` FOREIGN KEY (`kode_cuti`) REFERENCES `hrd_jeniscuti` (`kode_cuti`) ON DELETE CASCADE,
  CONSTRAINT `hrd_hak_cuti_nik_foreign` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_hak_cuti`
--

LOCK TABLES `hrd_hak_cuti` WRITE;
/*!40000 ALTER TABLE `hrd_hak_cuti` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_hak_cuti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izinabsen`
--

DROP TABLE IF EXISTS `hrd_izinabsen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izinabsen` (
  `kode_izin` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `dari` date NOT NULL,
  `sampai` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `direktur` tinyint NOT NULL DEFAULT '0',
  `id_user` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_izin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izinabsen`
--

LOCK TABLES `hrd_izinabsen` WRITE;
/*!40000 ALTER TABLE `hrd_izinabsen` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_izinabsen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izinabsen_disposisi`
--

DROP TABLE IF EXISTS `hrd_izinabsen_disposisi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izinabsen_disposisi` (
  `kode_disposisi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_izin` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pengirim` int NOT NULL,
  `id_penerima` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_disposisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izinabsen_disposisi`
--

LOCK TABLES `hrd_izinabsen_disposisi` WRITE;
/*!40000 ALTER TABLE `hrd_izinabsen_disposisi` DISABLE KEYS */;
INSERT INTO `hrd_izinabsen_disposisi` VALUES ('DPIA202607240001','IA26070001',1,3,0,'2026-07-24 09:17:35','2026-07-24 09:17:35');
/*!40000 ALTER TABLE `hrd_izinabsen_disposisi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izincuti`
--

DROP TABLE IF EXISTS `hrd_izincuti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izincuti` (
  `kode_izin_cuti` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `dari` date NOT NULL,
  `sampai` date NOT NULL,
  `kode_cuti` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_cuti_khusus` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `direktur` tinyint NOT NULL DEFAULT '0',
  `id_user` int NOT NULL DEFAULT '1',
  `doc_cuti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_izin_cuti`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izincuti`
--

LOCK TABLES `hrd_izincuti` WRITE;
/*!40000 ALTER TABLE `hrd_izincuti` DISABLE KEYS */;
INSERT INTO `hrd_izincuti` VALUES ('IC26070001','231011402424',NULL,'HRD','TGSL','2026-07-29','2026-07-29','2026-07-30','C01',NULL,'healing',0,0,1,NULL,'2026-07-28 09:35:17','2026-07-28 09:35:17'),('IC26080001','231011402424',NULL,'HRD','TGSL','2026-08-01','2026-08-01','2026-10-29','C02',NULL,'bunting',1,0,1,NULL,'2026-07-28 10:19:30','2026-07-28 10:19:30');
/*!40000 ALTER TABLE `hrd_izincuti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izincuti_disposisi`
--

DROP TABLE IF EXISTS `hrd_izincuti_disposisi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izincuti_disposisi` (
  `kode_disposisi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_izin_cuti` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pengirim` int NOT NULL,
  `id_penerima` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_disposisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izincuti_disposisi`
--

LOCK TABLES `hrd_izincuti_disposisi` WRITE;
/*!40000 ALTER TABLE `hrd_izincuti_disposisi` DISABLE KEYS */;
INSERT INTO `hrd_izincuti_disposisi` VALUES ('DPIC202607240001','IC26070001',1,3,0,'2026-07-24 09:18:58','2026-07-24 09:18:58'),('DPIC202607280001','IC26070001',1,3,0,'2026-07-28 09:35:17','2026-07-28 09:35:17'),('DPIC202607280002','IC26080001',1,3,0,'2026-07-28 10:19:30','2026-07-28 10:19:30');
/*!40000 ALTER TABLE `hrd_izincuti_disposisi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izindinas`
--

DROP TABLE IF EXISTS `hrd_izindinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izindinas` (
  `kode_izin_dinas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `dari` date NOT NULL,
  `sampai` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `direktur` tinyint NOT NULL DEFAULT '0',
  `id_user` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_izin_dinas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izindinas`
--

LOCK TABLES `hrd_izindinas` WRITE;
/*!40000 ALTER TABLE `hrd_izindinas` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_izindinas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izinkeluar`
--

DROP TABLE IF EXISTS `hrd_izinkeluar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izinkeluar` (
  `kode_izin_keluar` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_keluar` time NOT NULL,
  `jam_kembali` time DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `direktur` tinyint NOT NULL DEFAULT '0',
  `id_user` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_izin_keluar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izinkeluar`
--

LOCK TABLES `hrd_izinkeluar` WRITE;
/*!40000 ALTER TABLE `hrd_izinkeluar` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_izinkeluar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izinpulang`
--

DROP TABLE IF EXISTS `hrd_izinpulang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izinpulang` (
  `kode_izin_pulang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_pulang` time NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_approved` tinyint NOT NULL DEFAULT '0',
  `direktur` tinyint NOT NULL DEFAULT '0',
  `id_user` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_izin_pulang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izinpulang`
--

LOCK TABLES `hrd_izinpulang` WRITE;
/*!40000 ALTER TABLE `hrd_izinpulang` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_izinpulang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izinsakit`
--

DROP TABLE IF EXISTS `hrd_izinsakit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izinsakit` (
  `kode_izin_sakit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `dari` date NOT NULL,
  `sampai` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `direktur` tinyint NOT NULL DEFAULT '0',
  `id_user` int NOT NULL DEFAULT '1',
  `doc_sid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_izin_sakit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izinsakit`
--

LOCK TABLES `hrd_izinsakit` WRITE;
/*!40000 ALTER TABLE `hrd_izinsakit` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_izinsakit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izinsakit_disposisi`
--

DROP TABLE IF EXISTS `hrd_izinsakit_disposisi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izinsakit_disposisi` (
  `kode_disposisi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_izin_sakit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pengirim` int NOT NULL,
  `id_penerima` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_disposisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izinsakit_disposisi`
--

LOCK TABLES `hrd_izinsakit_disposisi` WRITE;
/*!40000 ALTER TABLE `hrd_izinsakit_disposisi` DISABLE KEYS */;
INSERT INTO `hrd_izinsakit_disposisi` VALUES ('DPIS202607240001','IS26070001',1,3,0,'2026-07-24 09:18:22','2026-07-24 09:18:22');
/*!40000 ALTER TABLE `hrd_izinsakit_disposisi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_izinterlambat`
--

DROP TABLE IF EXISTS `hrd_izinterlambat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_izinterlambat` (
  `kode_izin_terlambat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_terlambat` time NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `direktur` tinyint NOT NULL DEFAULT '0',
  `id_user` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_izin_terlambat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_izinterlambat`
--

LOCK TABLES `hrd_izinterlambat` WRITE;
/*!40000 ALTER TABLE `hrd_izinterlambat` DISABLE KEYS */;
INSERT INTO `hrd_izinterlambat` VALUES ('IT26070001','231011401234',NULL,'IT','TGSL','2026-07-27','10:45:00','ada  keperluan',0,0,1,'2026-07-26 08:58:23','2026-07-26 08:58:23');
/*!40000 ALTER TABLE `hrd_izinterlambat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_jeniscuti`
--

DROP TABLE IF EXISTS `hrd_jeniscuti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_jeniscuti` (
  `kode_cuti` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_cuti` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jml_hari` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_cuti`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_jeniscuti`
--

LOCK TABLES `hrd_jeniscuti` WRITE;
/*!40000 ALTER TABLE `hrd_jeniscuti` DISABLE KEYS */;
INSERT INTO `hrd_jeniscuti` VALUES ('C01','Cuti Tahunan',12,'2026-07-24 08:55:15','2026-07-24 08:55:15'),('C02','Cuti Hamil/Melahirkan',90,'2026-07-24 08:55:15','2026-07-24 08:55:15'),('C03','Cuti Khusus',0,'2026-07-24 08:55:15','2026-07-24 08:55:15');
/*!40000 ALTER TABLE `hrd_jeniscuti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_jeniscuti_khusus`
--

DROP TABLE IF EXISTS `hrd_jeniscuti_khusus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_jeniscuti_khusus` (
  `kode_cuti_khusus` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_cuti_khusus` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jml_hari` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_cuti_khusus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_jeniscuti_khusus`
--

LOCK TABLES `hrd_jeniscuti_khusus` WRITE;
/*!40000 ALTER TABLE `hrd_jeniscuti_khusus` DISABLE KEYS */;
INSERT INTO `hrd_jeniscuti_khusus` VALUES ('CK01','Cuti Menikah',3,'2026-07-24 08:55:15','2026-07-24 08:55:15'),('CK02','Cuti Khitanan/Baptis Anak',2,'2026-07-24 08:55:15','2026-07-24 08:55:15'),('CK03','Cuti Anggota Keluarga Meninggal',3,'2026-07-24 08:55:15','2026-07-24 08:55:15'),('CK04','Cuti Istri Melahirkan',2,'2026-07-24 08:55:15','2026-07-24 08:55:15');
/*!40000 ALTER TABLE `hrd_jeniscuti_khusus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_lembur`
--

DROP TABLE IF EXISTS `hrd_lembur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_lembur` (
  `kode_lembur` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_dari` datetime NOT NULL,
  `tanggal_sampai` datetime NOT NULL,
  `kode_cabang` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `kategori` tinyint NOT NULL DEFAULT '1',
  `istirahat` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_lembur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_lembur`
--

LOCK TABLES `hrd_lembur` WRITE;
/*!40000 ALTER TABLE `hrd_lembur` DISABLE KEYS */;
INSERT INTO `hrd_lembur` VALUES ('L-260728-FZZL','2026-07-28','2026-07-28 17:31:00','2026-07-28 20:00:00',NULL,NULL,'lembur',1,0,1,'2026-07-28 08:00:14','2026-07-28 08:00:14');
/*!40000 ALTER TABLE `hrd_lembur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_lembur_detail`
--

DROP TABLE IF EXISTS `hrd_lembur_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_lembur_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_lembur` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hrd_lembur_detail_kode_lembur_foreign` (`kode_lembur`),
  CONSTRAINT `hrd_lembur_detail_kode_lembur_foreign` FOREIGN KEY (`kode_lembur`) REFERENCES `hrd_lembur` (`kode_lembur`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_lembur_detail`
--

LOCK TABLES `hrd_lembur_detail` WRITE;
/*!40000 ALTER TABLE `hrd_lembur_detail` DISABLE KEYS */;
INSERT INTO `hrd_lembur_detail` VALUES (1,'L-260728-FZZL','231011402424','2026-07-28 08:00:14','2026-07-28 08:00:14');
/*!40000 ALTER TABLE `hrd_lembur_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_presensi_izincuti`
--

DROP TABLE IF EXISTS `hrd_presensi_izincuti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_presensi_izincuti` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_presensi` int NOT NULL,
  `kode_izin_cuti` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_presensi_izincuti`
--

LOCK TABLES `hrd_presensi_izincuti` WRITE;
/*!40000 ALTER TABLE `hrd_presensi_izincuti` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_presensi_izincuti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_presensi_izinkeluar`
--

DROP TABLE IF EXISTS `hrd_presensi_izinkeluar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_presensi_izinkeluar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_presensi` int NOT NULL,
  `kode_izin_keluar` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_presensi_izinkeluar`
--

LOCK TABLES `hrd_presensi_izinkeluar` WRITE;
/*!40000 ALTER TABLE `hrd_presensi_izinkeluar` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_presensi_izinkeluar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_presensi_izinpulang`
--

DROP TABLE IF EXISTS `hrd_presensi_izinpulang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_presensi_izinpulang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_presensi` int NOT NULL,
  `kode_izin_pulang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_presensi_izinpulang`
--

LOCK TABLES `hrd_presensi_izinpulang` WRITE;
/*!40000 ALTER TABLE `hrd_presensi_izinpulang` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_presensi_izinpulang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_presensi_izinsakit`
--

DROP TABLE IF EXISTS `hrd_presensi_izinsakit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_presensi_izinsakit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_presensi` int NOT NULL,
  `kode_izin_sakit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_presensi_izinsakit`
--

LOCK TABLES `hrd_presensi_izinsakit` WRITE;
/*!40000 ALTER TABLE `hrd_presensi_izinsakit` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_presensi_izinsakit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hrd_presensi_izinterlambat`
--

DROP TABLE IF EXISTS `hrd_presensi_izinterlambat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hrd_presensi_izinterlambat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_presensi` int NOT NULL,
  `kode_izin_terlambat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hrd_presensi_izinterlambat`
--

LOCK TABLES `hrd_presensi_izinterlambat` WRITE;
/*!40000 ALTER TABLE `hrd_presensi_izinterlambat` DISABLE KEYS */;
/*!40000 ALTER TABLE `hrd_presensi_izinterlambat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `izinkeluarkantors`
--

DROP TABLE IF EXISTS `izinkeluarkantors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `izinkeluarkantors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `izinkeluarkantors`
--

LOCK TABLES `izinkeluarkantors` WRITE;
/*!40000 ALTER TABLE `izinkeluarkantors` DISABLE KEYS */;
/*!40000 ALTER TABLE `izinkeluarkantors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_kerja`
--

DROP TABLE IF EXISTS `jadwal_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_kerja` (
  `kode_jadwal` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jadwal` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_cabang` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_jadwal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_kerja`
--

LOCK TABLES `jadwal_kerja` WRITE;
/*!40000 ALTER TABLE `jadwal_kerja` DISABLE KEYS */;
INSERT INTO `jadwal_kerja` VALUES ('JD01','Jadwal Standar Tangerang Selatan','TGSL','2026-07-24 03:41:29','2026-07-24 03:41:29'),('JD02','Jadwal Shift 1','TGSL','2026-07-24 04:45:27','2026-07-24 04:45:27'),('JD03','Jadwal Shift 2','TGSL','2026-07-24 04:45:27','2026-07-24 04:45:27'),('JD04','Jadwal Shift 3','TGSL','2026-07-24 04:45:27','2026-07-24 04:45:27');
/*!40000 ALTER TABLE `jadwal_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_kerja_detail`
--

DROP TABLE IF EXISTS `jadwal_kerja_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_kerja_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_jadwal` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hari` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jam_kerja` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_kerja_detail_kode_jadwal_foreign` (`kode_jadwal`),
  KEY `jadwal_kerja_detail_kode_jam_kerja_foreign` (`kode_jam_kerja`),
  CONSTRAINT `jadwal_kerja_detail_kode_jadwal_foreign` FOREIGN KEY (`kode_jadwal`) REFERENCES `jadwal_kerja` (`kode_jadwal`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_kerja_detail_kode_jam_kerja_foreign` FOREIGN KEY (`kode_jam_kerja`) REFERENCES `jam_kerja` (`kode_jam_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_kerja_detail`
--

LOCK TABLES `jadwal_kerja_detail` WRITE;
/*!40000 ALTER TABLE `jadwal_kerja_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `jadwal_kerja_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jam_kerja`
--

DROP TABLE IF EXISTS `jam_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jam_kerja` (
  `kode_jam_kerja` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jam_kerja` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_masuk` time NOT NULL,
  `awal_jam_masuk` time DEFAULT NULL,
  `jam_pulang` time NOT NULL,
  `akhir_jam_masuk` time DEFAULT NULL,
  `lintashari` tinyint NOT NULL DEFAULT '0',
  `total_jam` decimal(5,2) NOT NULL DEFAULT '8.00',
  `istirahat` tinyint NOT NULL DEFAULT '0',
  `jam_awal_istirahat` time DEFAULT NULL,
  `jam_akhir_istirahat` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_jam_kerja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jam_kerja`
--

LOCK TABLES `jam_kerja` WRITE;
/*!40000 ALTER TABLE `jam_kerja` DISABLE KEYS */;
INSERT INTO `jam_kerja` VALUES ('JK ENG 1','Shift 1 Engineering','08:00:00','06:30:00','20:00:00','08:10:00',0,8.00,0,NULL,NULL,NULL,NULL),('JK ENG 2','Shift 2 Engineering','20:00:00','18:30:00','08:00:00','20:10:00',0,8.00,0,NULL,NULL,NULL,NULL),('MOD','On Duty','08:30:00','07:00:00','12:30:00','08:40:00',0,8.00,0,NULL,NULL,NULL,NULL),('REG','Reguler BM','08:30:00','07:00:00','17:30:00','08:40:00',0,8.00,0,NULL,NULL,NULL,NULL),('REG ENG 1','Reguler Engineering 1','08:00:00','06:30:00','17:00:00','08:10:00',0,8.00,0,NULL,NULL,NULL,NULL),('REG ENG 2','Reguler Engineering 2','07:00:00','05:30:00','15:00:00','07:10:00',0,8.00,0,NULL,NULL,NULL,NULL),('REG ENG 3','Reguler Engineering 3','07:00:00','05:30:00','13:00:00','07:10:00',0,8.00,0,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `jam_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `karyawan`
--

DROP TABLE IF EXISTS `karyawan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `karyawan` (
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_jadwal` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lock_location` tinyint NOT NULL DEFAULT '0',
  `kode_jabatan` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `karyawan`
--

LOCK TABLES `karyawan` WRITE;
/*!40000 ALTER TABLE `karyawan` DISABLE KEYS */;
INSERT INTO `karyawan` VALUES ('admin','admin','BM','superadmin','00000','$2y$10$ZN2OM15UzYWEMkA5MlrtouigTBHnyHHQnnAA5bqsljm.iGI.gK1Ye',NULL,NULL,'S8',NULL,0,NULL),('Eng001','Taryana','ENG','Chief Engineering','00000','$2y$10$Uxi9hpkELyWsUo9Zw34la.npAYMyNk2YRUWsk9Z7U6GKso7Dh/lx2',NULL,NULL,'S8',NULL,0,NULL),('Eng002','Sulaeman','ENG','Team Leader','00000','$2y$10$qWCdyd86/46ZzbUG5LcruuV.3iO1SMPJdaxgtKMIRFLVhNUp3VVJa',NULL,NULL,'S8',NULL,0,NULL),('Eng003','Dwi Haryadi','ENG','Teknisi','00000','$2y$10$LbN7u5Kxpv9W1MJ0KjXJ4uUBwuYz0B2cGi0RB7cgvYJO1RNMqp7g.',NULL,NULL,'S8',NULL,0,NULL),('Eng004','Hilmawan Adi Putra','ENG','Teknisi','00000','$2y$10$pQWAJFGvkccaRXUMCbBKmOxQHFUrmIBt98uWpUW3wsDrvrYSakGSm',NULL,NULL,'S8',NULL,0,NULL),('Eng005','Jumadi','ENG','Teknisi','00000','$2y$10$gkN81kDw797umeNELjYuu.zgy6quZd0C07b0Z0/pPl5HIcoQmQXxK',NULL,NULL,'S8',NULL,0,NULL),('Eng006','Fathurrahman Arif','ENG','Teknisi','00000','$2y$10$PSqUveVrFNxdEl4xHhWU3.0/qY263AsydasW.J7DXhTWyF7jF2RVW',NULL,NULL,'S8',NULL,0,NULL),('Eng007','M Ami Imanu','ENG','Pool Attendant','00000','$2y$10$7OCT.IsawMwX8V2Py4OQbO5txaw9arYxsCsU.eNxKqD.1Uv3jIPui',NULL,NULL,'S8',NULL,0,NULL),('F001','Widowati','FIN','Chief Finance & Accounting','00000','$2y$10$97NKPbt4lzybbnnnhheaiuIG5pHQrSoc.zYcumntrvb9REEDLQWiu',NULL,NULL,'S8',NULL,0,NULL),('F002','Eddy Susanto','FIN','Spv Finance','00000','$2y$10$GJXka3srTLo8hVbZF/PMteyXGuOwBRtHXxvogt37uafiKAxugMG4K',NULL,NULL,'S8',NULL,0,NULL),('GA001','Romy Boy','HRD','General Admin','00000','$2y$10$6uClR/OblUiOa2zotX65gudvtGdlXsCSqWsnM1kH1hzAGXWwDgQfq',NULL,NULL,'S8',NULL,0,NULL),('TR001','Fadhilah Rusydah','TR','Chief TR','00000','$2y$10$3qA.t762SlaJLxhtcewozuwVXQ2XkNYRvNiF9mnWD1GiR6IzlK4lS',NULL,NULL,'S8',NULL,0,NULL),('TR002','Novi','TR','Resepsionis','00000','$2y$10$fZqOkXf1Ok/dGTxsC1zgge.qxU.atg3KPeeNRBsE4xtHL72eTfrSm',NULL,NULL,'S8',NULL,0,NULL),('TR003','Tuti','TR','Resepsionis','00000','$2y$10$W1tB.jltke/kgK4xVZVwqeI9ppsm4qFQ6k3clprHdnFs9lVRn.vNy',NULL,NULL,'S8',NULL,0,NULL),('TR004','Arfina Khiarotina','TR','Resepsionis','00000','$2y$10$I5EmEZhdQSdG89yF6RsmD.8zS2DNPiKYb74EeIQikD5EhdZ.rDHla',NULL,NULL,'S8',NULL,0,NULL);
/*!40000 ALTER TABLE `karyawan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konfigurasi_jadwalkerja`
--

DROP TABLE IF EXISTS `konfigurasi_jadwalkerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konfigurasi_jadwalkerja` (
  `kode_setjadwal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dari` date NOT NULL,
  `sampai` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_setjadwal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konfigurasi_jadwalkerja`
--

LOCK TABLES `konfigurasi_jadwalkerja` WRITE;
/*!40000 ALTER TABLE `konfigurasi_jadwalkerja` DISABLE KEYS */;
/*!40000 ALTER TABLE `konfigurasi_jadwalkerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konfigurasi_jadwalkerja_detail`
--

DROP TABLE IF EXISTS `konfigurasi_jadwalkerja_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konfigurasi_jadwalkerja_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_setjadwal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jadwal` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `konfigurasi_jadwalkerja_detail_kode_setjadwal_foreign` (`kode_setjadwal`),
  CONSTRAINT `konfigurasi_jadwalkerja_detail_kode_setjadwal_foreign` FOREIGN KEY (`kode_setjadwal`) REFERENCES `konfigurasi_jadwalkerja` (`kode_setjadwal`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konfigurasi_jadwalkerja_detail`
--

LOCK TABLES `konfigurasi_jadwalkerja_detail` WRITE;
/*!40000 ALTER TABLE `konfigurasi_jadwalkerja_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `konfigurasi_jadwalkerja_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konfigurasi_jk_karyawan`
--

DROP TABLE IF EXISTS `konfigurasi_jk_karyawan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konfigurasi_jk_karyawan` (
  `nik` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hari` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jam_kerja` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nik`,`hari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konfigurasi_jk_karyawan`
--

LOCK TABLES `konfigurasi_jk_karyawan` WRITE;
/*!40000 ALTER TABLE `konfigurasi_jk_karyawan` DISABLE KEYS */;
INSERT INTO `konfigurasi_jk_karyawan` VALUES ('231011402424','Jumat','REG','2026-07-28 05:29:36','2026-07-28 05:29:36'),('231011402424','Kamis','REG','2026-07-28 05:29:36','2026-07-28 05:29:36'),('231011402424','Rabu','REG','2026-07-28 05:29:36','2026-07-28 05:29:36'),('231011402424','Selasa','REG','2026-07-28 05:29:36','2026-07-28 05:29:36'),('231011402424','Senin','REG','2026-07-28 05:29:36','2026-07-28 05:29:36');
/*!40000 ALTER TABLE `konfigurasi_jk_karyawan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konfigurasi_jk_karyawan_by_date`
--

DROP TABLE IF EXISTS `konfigurasi_jk_karyawan_by_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konfigurasi_jk_karyawan_by_date` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `kode_jam_kerja` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `konfigurasi_jk_karyawan_by_date_nik_tanggal_unique` (`nik`,`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konfigurasi_jk_karyawan_by_date`
--

LOCK TABLES `konfigurasi_jk_karyawan_by_date` WRITE;
/*!40000 ALTER TABLE `konfigurasi_jk_karyawan_by_date` DISABLE KEYS */;
/*!40000 ALTER TABLE `konfigurasi_jk_karyawan_by_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konfigurasi_lokasi`
--

DROP TABLE IF EXISTS `konfigurasi_lokasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konfigurasi_lokasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lokasi_kantor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `radius` smallint NOT NULL,
  `nama_hrd` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Qiana Aqila',
  `jabatan_hrd` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'HRD Manager',
  `nama_pimpinan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Daffa',
  `jabatan_pimpinan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Property Manager',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konfigurasi_lokasi`
--

LOCK TABLES `konfigurasi_lokasi` WRITE;
/*!40000 ALTER TABLE `konfigurasi_lokasi` DISABLE KEYS */;
INSERT INTO `konfigurasi_lokasi` VALUES (1,'-6.187243,106.757976',100,'Romy Boy','HRD','Erwan Yamin L','Property Manager');
/*!40000 ALTER TABLE `konfigurasi_lokasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',2),(3,'2019_08_19_000000_create_failed_jobs_table',2),(4,'2019_12_14_000001_create_personal_access_tokens_table',2),(5,'2024_09_21_221949_create_izinkeluarkantors_table',2),(6,'2026_07_24_104050_align_epresensi_tables',2),(7,'2026_07_24_104300_add_columns_to_pengajuan_izin_table',3),(8,'2026_07_24_105129_fix_database_collation',4),(9,'2026_07_24_113458_add_kode_jadwal_to_karyawan_table',5),(10,'2026_07_24_114329_create_konfigurasi_jadwalkerja_tables',6),(11,'2026_07_24_154700_create_hrd_izin_tables',7),(12,'2026_07_24_170500_create_employee_daily_schedules_tables',8),(13,'2026_07_24_203500_create_hrd_lembur_tables',9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengajuan_izin`
--

DROP TABLE IF EXISTS `pengajuan_izin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengajuan_izin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nik` char(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_izin` date DEFAULT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approved` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_izin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dari` date DEFAULT NULL,
  `sampai` date DEFAULT NULL,
  `jmlhari` int DEFAULT NULL,
  `sid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_izin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `jenis_cuti` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengajuan_izin`
--

LOCK TABLES `pengajuan_izin` WRITE;
/*!40000 ALTER TABLE `pengajuan_izin` DISABLE KEYS */;
INSERT INTO `pengajuan_izin` VALUES (15,'231011402424','2026-07-29','c','healing','0','IC26070001','2026-07-29','2026-07-30',2,NULL,NULL,NULL,NULL,'C01','user'),(16,'231011402424','2026-08-01','c','bunting','1','IC26080001','2026-08-01','2026-10-29',90,NULL,NULL,NULL,NULL,'C02','user');
/*!40000 ALTER TABLE `pengajuan_izin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presensi`
--

DROP TABLE IF EXISTS `presensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presensi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nik` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_presensi` date NOT NULL,
  `jam_in` time NOT NULL,
  `jam_out` time DEFAULT NULL,
  `foto_in` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_out` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_in` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_out` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'h',
  `kode_jadwal` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_jam_kerja` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presensi`
--

LOCK TABLES `presensi` WRITE;
/*!40000 ALTER TABLE `presensi` DISABLE KEYS */;
INSERT INTO `presensi` VALUES (229,'231011402424','2026-07-28','12:31:30','20:05:01','231011402424-2026-07-28-in.png',NULL,'-6.186916,106.758098',NULL,'h','JD03','REG'),(230,'231011402424','2026-07-29','11:33:43',NULL,'231011402424-2026-07-29-in.png',NULL,'-6.18695209077469,106.7581075224719',NULL,'h','JD03','REG');
/*!40000 ALTER TABLE `presensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Eddy','Eddy123@gmail.com',NULL,'$2y$10$ymwqcftCcTJNhQtZuVQ1.eR5TISVbu1Hchw.9mt9hfbH4sydNp9IO',NULL,NULL,NULL),(5,'Admin Satu8','admin@satu8.co.id',NULL,'$2y$10$6A9l7sglwIAz6ig9E18ZVuGN/rV1lm3lc3OJP.SlCQEew7zfiB7Ay',NULL,'2026-07-30 04:28:24','2026-07-30 04:28:24'),(6,'Super Admin Satu8','superadmin@satu8.co.id',NULL,'$2y$10$HExKFSJMB0jGiw7feDKtPue1pC2Ydbt/U7IcICzaE0DL5GT/G048a',NULL,'2026-07-30 04:28:24','2026-07-30 04:28:24');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'eprensensi'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-30 11:43:57
