-- MySQL dump 10.13  Distrib 8.0.39, for Win64 (x86_64)
--
-- Host: serverkn.ddns.net    Database: knockout
-- ------------------------------------------------------
-- Server version	8.0.39

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
-- Current Database: `knockout`
--

DROP DATABASE IF EXISTS `knockout`;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `knockout` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `knockout`;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descripcion` text,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'mma','lucha libre');
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lucha`
--

DROP TABLE IF EXISTS `lucha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lucha` (
  `id_lucha` int NOT NULL AUTO_INCREMENT,
  `id_luchador1` varchar(255) NOT NULL,
  `id_luchador2` varchar(255) NOT NULL,
  `id_categoria` int NOT NULL,
  `id_ganador` varchar(255) DEFAULT NULL,
  `num_rondas` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_final` time NOT NULL,
  `estado` enum('pendiente','luchando','finalizada','cancelada') NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  PRIMARY KEY (`id_lucha`),
  KEY `id_luchador1` (`id_luchador1`),
  KEY `id_luchador2` (`id_luchador2`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_ganador` (`id_ganador`),
  CONSTRAINT `lucha_ibfk_1` FOREIGN KEY (`id_luchador1`) REFERENCES `luchador` (`email`),
  CONSTRAINT `lucha_ibfk_2` FOREIGN KEY (`id_luchador2`) REFERENCES `luchador` (`email`),
  CONSTRAINT `lucha_ibfk_3` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`),
  CONSTRAINT `lucha_ibfk_4` FOREIGN KEY (`id_ganador`) REFERENCES `luchador` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lucha`
--

LOCK TABLES `lucha` WRITE;
/*!40000 ALTER TABLE `lucha` DISABLE KEYS */;
INSERT INTO `lucha` VALUES (1,'usuario1@gmail.com','usuario2@gmail.com',1,'usuario1@gmail.com',2,'2024-10-15','00:30:00','00:52:00','finalizada','Madrid');
/*!40000 ALTER TABLE `lucha` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `luchador`
--

DROP TABLE IF EXISTS `luchador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `luchador` (
  `email` varchar(255) NOT NULL,
  `peso` int NOT NULL,
  `altura` int NOT NULL,
  `victorias` int DEFAULT NULL,
  `derrotas` int DEFAULT NULL,
  `puntos` int DEFAULT NULL,
  `grupoSang` enum('A+','B+','AB+','O+','A-','B-','AB-','O-') NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `lateralidad` enum('diestro','zurdo','ambi') NOT NULL,
  `buscando_pelea` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`email`),
  CONSTRAINT `luchador_ibfk_1` FOREIGN KEY (`email`) REFERENCES `usuario` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `luchador`
--

LOCK TABLES `luchador` WRITE;
/*!40000 ALTER TABLE `luchador` DISABLE KEYS */;
INSERT INTO `luchador` VALUES ('usuario1@gmail.com', 50, 150, NULL,NULL,NULL, 'A+', 'Madrid', 'diestro',NULL), ('usuario2@gmail.com', 50, 150, NULL,NULL,NULL, 'A+', 'Madrid', 'diestro',NULL);
/*!40000 ALTER TABLE `luchador` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `replays`
--

DROP TABLE IF EXISTS `replays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `replays` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_lucha` int DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_lucha` (`id_lucha`),
  CONSTRAINT `replays_ibfk_1` FOREIGN KEY (`id_lucha`) REFERENCES `lucha` (`id_lucha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `replays`
--

LOCK TABLES `replays` WRITE;
/*!40000 ALTER TABLE `replays` DISABLE KEYS */;
/*!40000 ALTER TABLE `replays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `edad` int NOT NULL,
  `sexo` enum('masculino','femenino') NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES ('usuario1@gmail.com', 'usuario1', '1234', 'nombre1', 'apellido1', 18, 'Masculino'), ('usuario2@gmail.com', 'usuario2', '1234', 'nombre2', 'apellido2', 18, 'femenino');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-24 21:09:56
