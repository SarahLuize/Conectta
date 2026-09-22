-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: conectta
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE conectta;
USE conectta;

--
-- Table structure for table `usuario`
--
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `nome_usuario` varchar(30) NOT NULL,
  `biografia` varchar(160) DEFAULT NULL,
  `foto_perfil` varchar(250) DEFAULT 'img/PLACEHOLDERpfp.png',
  `foto_capa` varchar(250) DEFAULT 'img/PLACEHOLDERheader.png',
  `verificado` tinyint DEFAULT FALSE,
  `numero_seguindo` int DEFAULT 0,
  `numero_seguidores` int DEFAULT 0,
  `numero_curtidos` int DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_usuario` (`nome_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `postagem`
--
CREATE TABLE `postagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `texto` text NOT NULL,
  `anexo` varchar(255) DEFAULT NULL,
  `anexo2` varchar(255) DEFAULT NULL,
  `numero_curtidas` int DEFAULT 0,
  `data_criacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_postagem_usuario` (`id_usuario`),
  CONSTRAINT `fk_postagem_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `comentario`
--
CREATE TABLE `comentario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `conteudo` varchar(140) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_comentario_usuario` (`id_usuario`),
  KEY `fk_comentario_postagem` (`id_post`),
  CONSTRAINT `fk_comentario_postagem` FOREIGN KEY (`id_post`) REFERENCES `postagem` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comentario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `seguidores`
--
CREATE TABLE `seguidores` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `quem_esta_seguindo` INT,
  `quem_foi_seguido` INT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `favoritos`
--
CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `numero_curtidas` int DEFAULT 0,
  `data_favoritado` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_favoritos_postagem_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;