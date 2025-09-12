-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 12, 2025 at 07:19 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `le-media-voironnais`
--

-- --------------------------------------------------------

--
-- Table structure for table `association`
--

DROP TABLE IF EXISTS `association`;
CREATE TABLE IF NOT EXISTS `association` (
  `idAssociation` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `logoPath` varchar(255) DEFAULT NULL,
  `bannerPath` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `idUser` int NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idAssociation`),
  UNIQUE KEY `UQ_idAssociation` (`idAssociation`),
  KEY `idx_association_user` (`idUser`),
  KEY `idx_association_active` (`isActive`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `association`
--

INSERT INTO `association` (`idAssociation`, `name`, `description`, `logoPath`, `bannerPath`, `address`, `phone`, `email`, `website`, `isActive`, `idUser`, `createdAt`, `updatedAt`) VALUES
(1, 'Culture Voiron', 'Association culturelle locale', '/images/logo/culture.png', '/images/banners/culture.jpg', '1 Rue de la Culture, Voiron', '+33 4 12 34 56 78', 'contact@culture-voiron.fr', 'https://culture-voiron.fr', 1, 2, '2025-09-11 12:00:01', NULL),
(2, 'Sports Voironnais', 'Promotion des activités sportives', '/images/logo/sports.png', '/images/banners/sports.jpg', '10 Avenue du Sport, Voiron', '+33 4 87 65 43 21', 'contact@sports-voironnais.fr', 'https://sports-voironnais.fr', 1, 2, '2025-09-11 12:00:01', NULL),
(3, 'Jeux & Esports Voiron', 'Gaming et esports locaux', '/images/logo/esports.png', '/images/banners/esports.jpg', '3 Rue des Jeux, Voiron', NULL, 'contact@jeux-voiron.fr', NULL, 1, 4, '2025-09-11 12:00:01', NULL),
(4, 'Photographie Voiron', 'Club de photo', '/images/logo/photo.png', '/images/banners/photo.jpg', '8 Rue des Artistes, Voiron', NULL, 'hello@photo-voiron.fr', NULL, 1, 5, '2025-09-11 12:00:01', NULL),
(5, 'Théâtre Amateur', 'Troupe de théâtre amateur', '/images/logo/theatre.png', '/images/banners/theatre.jpg', '12 Rue du Théâtre, Voiron', NULL, 'contact@theatre-amateur.fr', NULL, 1, 6, '2025-09-11 12:00:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `association_invitation`
--

DROP TABLE IF EXISTS `association_invitation`;
CREATE TABLE IF NOT EXISTS `association_invitation` (
  `idAssociationInvitation` int NOT NULL AUTO_INCREMENT,
  `idAssociation` int NOT NULL,
  `idUser` int NOT NULL,
  `idInviter` int NOT NULL,
  `status` enum('en_attente','accepte','refuse') NOT NULL DEFAULT 'en_attente',
  `invitedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idAssociationInvitation`),
  UNIQUE KEY `unique_association_user_invitation` (`idAssociation`,`idUser`),
  KEY `FK_user_TO_association_invitation_user` (`idUser`),
  KEY `FK_user_TO_association_invitation_inviter` (`idInviter`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `association_invitation`
--

INSERT INTO `association_invitation` (`idAssociationInvitation`, `idAssociation`, `idUser`, `idInviter`, `status`, `invitedAt`, `respondedAt`) VALUES
(1, 1, 3, 2, 'accepte', '2025-09-11 12:00:01', NULL),
(2, 2, 3, 2, 'en_attente', '2025-09-11 12:00:01', NULL),
(3, 3, 5, 4, 'accepte', '2025-09-11 12:00:01', NULL),
(4, 4, 7, 6, 'refuse', '2025-09-11 12:00:01', NULL),
(5, 5, 8, 6, 'en_attente', '2025-09-11 12:00:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
CREATE TABLE IF NOT EXISTS `chat` (
  `idChat` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `isGroup` tinyint(1) NOT NULL DEFAULT '0',
  `isEventChat` tinyint(1) NOT NULL DEFAULT '0',
  `idEvenement` int DEFAULT NULL,
  `idAssociation` int DEFAULT NULL,
  `createdBy` int NOT NULL,
  `lastMessageAt` datetime DEFAULT NULL,
  `lastMessageId` int DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idChat`),
  UNIQUE KEY `UQ_idChat` (`idChat`),
  KEY `FK_user_TO_chat_creator` (`createdBy`),
  KEY `idx_chat_event` (`idEvenement`),
  KEY `idx_chat_association` (`idAssociation`),
  KEY `idx_chat_last_message` (`lastMessageAt`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`idChat`, `title`, `description`, `isGroup`, `isEventChat`, `idEvenement`, `idAssociation`, `createdBy`, `lastMessageAt`, `lastMessageId`, `createdAt`, `updatedAt`) VALUES
(1, 'Discussion Générale', 'Espace de discussion pour la communauté', 1, 0, NULL, 1, 2, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(2, 'Chat Concert', 'Discussion autour du concert', 1, 1, 1, 1, 2, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(3, 'Chat Esports', 'Discussion tournoi', 1, 1, 3, 3, 4, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(4, 'Photo Club', 'Discussions photo', 1, 0, NULL, 4, 5, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(5, 'Yoga Group', 'Infos yoga', 1, 1, 8, NULL, 12, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(6, 'Marché Livres', 'Organisation', 1, 1, 9, NULL, 9, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat_participant`
--

DROP TABLE IF EXISTS `chat_participant`;
CREATE TABLE IF NOT EXISTS `chat_participant` (
  `idChatParticipant` int NOT NULL AUTO_INCREMENT,
  `idChat` int NOT NULL,
  `idUser` int NOT NULL,
  `role` enum('membre','admin','moderateur') NOT NULL DEFAULT 'membre',
  `joinedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leftAt` datetime DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idChatParticipant`),
  UNIQUE KEY `unique_chat_user_participant` (`idChat`,`idUser`),
  KEY `FK_user_TO_chat_participant` (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_participant`
--

INSERT INTO `chat_participant` (`idChatParticipant`, `idChat`, `idUser`, `role`, `joinedAt`, `leftAt`, `isActive`) VALUES
(1, 1, 2, 'admin', '2025-09-11 12:00:01', NULL, 1),
(2, 1, 3, 'membre', '2025-09-11 12:00:01', NULL, 1),
(3, 2, 2, 'moderateur', '2025-09-11 12:00:01', NULL, 1),
(4, 2, 3, 'membre', '2025-09-11 12:00:01', NULL, 1),
(5, 3, 4, 'admin', '2025-09-11 12:00:01', NULL, 1),
(6, 3, 5, 'membre', '2025-09-11 12:00:01', NULL, 1),
(7, 4, 5, 'admin', '2025-09-11 12:00:01', NULL, 1),
(8, 4, 7, 'membre', '2025-09-11 12:00:01', NULL, 1),
(9, 5, 12, 'admin', '2025-09-11 12:00:01', NULL, 1),
(10, 6, 9, 'admin', '2025-09-11 12:00:01', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `idEntreprise` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `logoPath` varchar(255) DEFAULT NULL,
  `bannerPath` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `siret` varchar(20) DEFAULT NULL,
  `status` enum('brouillon','actif','suspendu') NOT NULL DEFAULT 'actif',
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  `idUser` int NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idEntreprise`),
  UNIQUE KEY `UQ_idEntreprise` (`idEntreprise`),
  KEY `idx_entreprise_user` (`idUser`),
  KEY `idx_entreprise_active` (`isActive`),
  KEY `idx_entreprise_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `entreprise`
--

INSERT INTO `entreprise` (`idEntreprise`, `name`, `description`, `logoPath`, `bannerPath`, `address`, `phone`, `email`, `website`, `siret`, `status`, `isActive`, `idUser`, `createdAt`, `updatedAt`) VALUES
(1, 'Tech Voiron SARL', 'Solutions logicielles locales', '/images/logo/techvoiron.png', '/images/banners/techvoiron.jpg', '25 Rue des Alpes, Voiron', '+33 4 11 22 33 44', 'contact@techvoiron.fr', 'https://techvoiron.fr', '12345678901234', 'actif', 1, 1, '2025-09-11 12:00:01', NULL),
(2, 'Boulangerie Dupont', 'Artisan boulanger-pâtissier', '/images/logo/dupont.png', '/images/banners/dupont.jpg', '7 Place du Marché, Voiron', '+33 4 55 66 77 88', 'bonjour@boulangerie-dupont.fr', NULL, '98765432109876', 'brouillon', 0, 2, '2025-09-11 12:00:01', NULL),
(3, 'Menuiserie Martin', 'Menuiserie et agencement', '/images/logo/menuiserie.png', NULL, '15 Rue du Bois, Voiron', NULL, 'contact@menuiserie-martin.fr', NULL, '11223344556677', 'actif', 1, 7, '2025-09-11 12:00:01', NULL),
(4, 'AutoVoiron', 'Garage automobile', '/images/logo/auto.png', '/images/banners/auto.jpg', '2 Avenue du Garage, Voiron', NULL, 'contact@autovoiron.fr', 'https://autovoiron.fr', '22334455667788', 'actif', 1, 8, '2025-09-11 12:00:01', NULL),
(5, 'Librairie des Alpes', 'Librairie indépendante', '/images/logo/librairie.png', '/images/banners/librairie.jpg', '30 Rue des Livres, Voiron', NULL, 'bonjour@librairie-alpes.fr', NULL, '33445566778899', 'brouillon', 0, 9, '2025-09-11 12:00:01', NULL),
(6, 'Fleuriste Rose', 'Fleuriste et déco', '/images/logo/fleuriste.png', NULL, '9 Rue des Fleurs, Voiron', NULL, 'contact@fleuristerose.fr', NULL, '44556677889900', 'actif', 1, 10, '2025-09-11 12:00:01', NULL),
(7, 'Pizzeria Napoli', 'Restaurant italien', '/images/logo/pizzeria.png', '/images/banners/pizzeria.jpg', '21 Rue de Rome, Voiron', NULL, 'ciao@pizzeria-napoli.fr', 'https://pizzeria-napoli.fr', '55667788990011', 'suspendu', 0, 11, '2025-09-11 12:00:01', NULL),
(8, 'Studio Yoga', 'Cours de yoga', '/images/logo/yoga.png', '/images/banners/yoga.jpg', '5 Rue Zen, Voiron', NULL, 'namaste@yogastudio.fr', NULL, '66778899001122', 'actif', 1, 12, '2025-09-11 12:00:01', NULL),
(9, 'Coiffure Élégance', 'Salon de coiffure', '/images/logo/coiffure.png', NULL, '17 Rue de la Beauté, Voiron', NULL, 'rdv@coiffure-elegance.fr', NULL, '77889900112233', 'actif', 1, 13, '2025-09-11 12:00:01', NULL),
(10, 'Tech Conseil', 'Conseil IT', '/images/logo/techconseil.png', '/images/banners/techconseil.jpg', '50 Rue du Numérique, Voiron', NULL, 'hello@techconseil.fr', 'https://techconseil.fr', '88990011223344', 'brouillon', 0, 14, '2025-09-11 12:00:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
CREATE TABLE IF NOT EXISTS `evenement` (
  `idEvenement` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `shortDescription` varchar(500) DEFAULT NULL,
  `eventDate` datetime NOT NULL,
  `endDate` datetime DEFAULT NULL,
  `registrationDeadline` datetime DEFAULT NULL,
  `maxParticipants` int NOT NULL,
  `currentParticipants` int NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `imagePath` varchar(255) DEFAULT NULL,
  `bannerPath` varchar(255) DEFAULT NULL,
  `status` enum('brouillon','actif','annule','termine') NOT NULL DEFAULT 'brouillon',
  `isPublic` tinyint(1) NOT NULL DEFAULT '1',
  `requiresApproval` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `currency` varchar(3) NOT NULL DEFAULT 'EUR',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  `idUser` int NOT NULL,
  `idAssociation` int DEFAULT NULL,
  `idVille` int NOT NULL,
  `idEventCategory` int DEFAULT NULL,
  PRIMARY KEY (`idEvenement`),
  UNIQUE KEY `UQ_idEvenement` (`idEvenement`),
  KEY `FK_user_TO_evenement_creator` (`idUser`),
  KEY `FK_ville_TO_evenement` (`idVille`),
  KEY `idx_evenement_date` (`eventDate`),
  KEY `idx_evenement_status` (`status`),
  KEY `idx_evenement_public` (`isPublic`),
  KEY `idx_evenement_association` (`idAssociation`),
  KEY `idx_evenement_category` (`idEventCategory`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `evenement`
--

INSERT INTO `evenement` (`idEvenement`, `title`, `description`, `shortDescription`, `eventDate`, `endDate`, `registrationDeadline`, `maxParticipants`, `currentParticipants`, `address`, `latitude`, `longitude`, `imagePath`, `bannerPath`, `status`, `isPublic`, `requiresApproval`, `price`, `currency`, `createdAt`, `updatedAt`, `idUser`, `idAssociation`, `idVille`, `idEventCategory`) VALUES
(1, 'Concert d\'été', 'Grand concert en plein air', 'Concert plein air', '2025-10-11 12:00:01', '2025-10-11 12:00:01', '2025-10-06 12:00:01', 500, 120, 'Parc Central, Voiron', 45.36420000, 5.59230000, '/images/events/concert.jpg', '/images/banners/concert.jpg', 'actif', 1, 0, 10.00, 'EUR', '2025-09-11 12:00:01', NULL, 2, 1, 1, 2),
(2, 'Atelier Peinture', 'Atelier pour débutants', 'Atelier de peinture', '2025-09-21 12:00:01', NULL, '2025-09-18 12:00:01', 20, 8, 'Maison des Arts, Voiron', 45.36500000, 5.59300000, '/images/events/atelier.jpg', NULL, 'brouillon', 1, 0, 0.00, 'EUR', '2025-09-11 12:00:01', NULL, 2, 1, 1, 2),
(3, 'Tournoi Esports', 'Tournoi local', 'Tournoi jeux vidéo', '2025-09-26 12:00:01', NULL, '2025-09-21 12:00:01', 64, 32, 'Salle Polyvalente, Voiron', 45.36450000, 5.59250000, '/images/events/esports.jpg', NULL, 'actif', 1, 0, 5.00, 'EUR', '2025-09-11 12:00:01', NULL, 4, 3, 2, 3),
(4, 'Expo Photo', 'Exposition de photographie', 'Expo photo', '2025-10-01 12:00:01', '2025-10-02 12:00:01', '2025-09-29 12:00:01', 200, 50, 'Galerie Municipale, Voiron', 45.36460000, 5.59260000, '/images/events/photo.jpg', '/images/banners/photo_event.jpg', 'brouillon', 1, 0, 0.00, 'EUR', '2025-09-11 12:00:01', NULL, 5, 4, 1, 2),
(5, 'Match de Foot', 'Match amical', 'Foot amical', '2025-09-16 12:00:01', NULL, '2025-09-14 12:00:01', 22, 18, 'Stade Municipal, Voiron', 45.36470000, 5.59270000, '/images/events/foot.jpg', NULL, 'actif', 1, 0, 0.00, 'EUR', '2025-09-11 12:00:01', NULL, 7, 2, 2, 1),
(6, 'Atelier Théâtre', 'Atelier d\'impro', 'Impro débutants', '2025-09-23 12:00:01', NULL, '2025-09-20 12:00:01', 25, 10, 'Salle Théâtre, Voiron', 45.36480000, 5.59280000, '/images/events/theatre.jpg', NULL, 'brouillon', 1, 0, 0.00, 'EUR', '2025-09-11 12:00:01', NULL, 6, 5, 3, 4),
(7, 'Conférence Business', 'Conférence PME', 'PME et croissance', '2025-10-16 12:00:01', '2025-10-16 12:00:01', '2025-10-11 12:00:01', 150, 60, 'Centre d\'Affaires, Voiron', 45.36490000, 5.59290000, '/images/events/business.jpg', '/images/banners/business.jpg', 'actif', 1, 1, 20.00, 'EUR', '2025-09-11 12:00:01', NULL, 1, NULL, 1, 5),
(8, 'Cours de Yoga', 'Séance découverte', 'Yoga découverte', '2025-09-14 12:00:01', NULL, '2025-09-13 12:00:01', 30, 12, 'Parc Zen, Voiron', 45.36500000, 5.59300000, '/images/events/yoga.jpg', NULL, 'actif', 1, 0, 0.00, 'EUR', '2025-09-11 12:00:01', NULL, 12, NULL, 1, 4),
(9, 'Marché du Livre', 'Vente de livres', 'Livres d\'occasion', '2025-09-19 12:00:01', NULL, '2025-09-17 12:00:01', 100, 40, 'Place du Marché, Voiron', 45.36510000, 5.59310000, '/images/events/livres.jpg', NULL, 'actif', 1, 0, 0.00, 'EUR', '2025-09-11 12:00:01', NULL, 9, NULL, 5, 2),
(10, 'Bal Populaire', 'Bal en soirée', 'Bal d\'été', '2025-10-26 12:00:01', '2025-10-26 12:00:01', '2025-10-21 12:00:01', 400, 120, 'Place Centrale, Voiron', 45.36520000, 5.59320000, '/images/events/bal.jpg', '/images/banners/bal.jpg', 'brouillon', 1, 0, 0.00, 'EUR', '2025-09-11 12:00:01', NULL, 2, 1, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `event_category`
--

DROP TABLE IF EXISTS `event_category`;
CREATE TABLE IF NOT EXISTS `event_category` (
  `idEventCategory` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `color` varchar(7) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventCategory`),
  UNIQUE KEY `UQ_idEventCategory` (`idEventCategory`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_category`
--

INSERT INTO `event_category` (`idEventCategory`, `name`, `description`, `color`, `icon`, `isActive`, `createdAt`) VALUES
(1, 'Sport', 'Sporting events and activities', '#FF6B6B', NULL, 1, '2025-09-11 12:00:01'),
(2, 'Culture', 'Cultural events and exhibitions', '#4ECDC4', NULL, 1, '2025-09-11 12:00:01'),
(3, 'Social', 'Social gatherings and networking', '#45B7D1', NULL, 1, '2025-09-11 12:00:01'),
(4, 'Education', 'Educational workshops and seminars', '#96CEB4', NULL, 1, '2025-09-11 12:00:01'),
(5, 'Business', 'Business meetings and conferences', '#FFEAA7', NULL, 1, '2025-09-11 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `event_image`
--

DROP TABLE IF EXISTS `event_image`;
CREATE TABLE IF NOT EXISTS `event_image` (
  `idEventImage` int NOT NULL AUTO_INCREMENT,
  `idEvenement` int NOT NULL,
  `imagePath` varchar(255) NOT NULL,
  `altText` varchar(255) DEFAULT NULL,
  `isMain` tinyint(1) NOT NULL DEFAULT '0',
  `sortOrder` int NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventImage`),
  UNIQUE KEY `UQ_idEventImage` (`idEventImage`),
  KEY `FK_evenement_TO_event_image` (`idEvenement`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_image`
--

INSERT INTO `event_image` (`idEventImage`, `idEvenement`, `imagePath`, `altText`, `isMain`, `sortOrder`, `createdAt`) VALUES
(1, 1, '/images/events/concert_main.jpg', 'Concert - visuel principal', 1, 1, '2025-09-11 12:00:01'),
(2, 1, '/images/events/concert_2.jpg', 'Concert - vue de la scène', 0, 2, '2025-09-11 12:00:01'),
(3, 3, '/images/events/esports_main.jpg', 'Tournoi - principal', 1, 1, '2025-09-11 12:00:01'),
(4, 3, '/images/events/esports_2.jpg', 'Tournoi - salle', 0, 2, '2025-09-11 12:00:01'),
(5, 4, '/images/events/photo_main.jpg', 'Expo - principal', 1, 1, '2025-09-11 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `event_invitation`
--

DROP TABLE IF EXISTS `event_invitation`;
CREATE TABLE IF NOT EXISTS `event_invitation` (
  `idEventInvitation` int NOT NULL AUTO_INCREMENT,
  `idEvenement` int NOT NULL,
  `idUser` int NOT NULL,
  `idInviter` int NOT NULL,
  `status` enum('en_attente','accepte','refuse') NOT NULL DEFAULT 'en_attente',
  `invitedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondedAt` datetime DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`idEventInvitation`),
  UNIQUE KEY `unique_event_user_invitation` (`idEvenement`,`idUser`),
  KEY `FK_user_TO_event_invitation_user` (`idUser`),
  KEY `FK_user_TO_event_invitation_inviter` (`idInviter`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_invitation`
--

INSERT INTO `event_invitation` (`idEventInvitation`, `idEvenement`, `idUser`, `idInviter`, `status`, `invitedAt`, `respondedAt`, `message`) VALUES
(1, 1, 3, 2, 'accepte', '2025-09-11 12:00:01', NULL, NULL),
(2, 2, 3, 2, 'en_attente', '2025-09-11 12:00:01', NULL, NULL),
(3, 3, 5, 4, 'en_attente', '2025-09-11 12:00:01', NULL, NULL),
(4, 4, 7, 5, 'accepte', '2025-09-11 12:00:01', NULL, NULL),
(5, 5, 10, 7, 'refuse', '2025-09-11 12:00:01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_participant`
--

DROP TABLE IF EXISTS `event_participant`;
CREATE TABLE IF NOT EXISTS `event_participant` (
  `idEventParticipant` int NOT NULL AUTO_INCREMENT,
  `idEvenement` int NOT NULL,
  `idUser` int NOT NULL,
  `status` enum('inscrit','approuve','liste_attente','annule') NOT NULL DEFAULT 'inscrit',
  `joinedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approvedAt` datetime DEFAULT NULL,
  `cancelledAt` datetime DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`idEventParticipant`),
  UNIQUE KEY `unique_event_user_participant` (`idEvenement`,`idUser`),
  KEY `idx_event_participant_evenement` (`idEvenement`),
  KEY `idx_event_participant_user` (`idUser`),
  KEY `idx_event_participant_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_participant`
--

INSERT INTO `event_participant` (`idEventParticipant`, `idEvenement`, `idUser`, `status`, `joinedAt`, `approvedAt`, `cancelledAt`, `notes`) VALUES
(1, 1, 2, 'inscrit', '2025-09-11 12:00:01', NULL, NULL, NULL),
(2, 1, 3, 'approuve', '2025-09-11 12:00:01', NULL, NULL, NULL),
(3, 2, 3, 'liste_attente', '2025-09-11 12:00:01', NULL, NULL, NULL),
(4, 3, 4, 'inscrit', '2025-09-11 12:00:01', NULL, NULL, NULL),
(5, 3, 5, 'approuve', '2025-09-11 12:00:01', NULL, NULL, NULL),
(6, 4, 5, 'inscrit', '2025-09-11 12:00:01', NULL, NULL, NULL),
(7, 5, 7, 'approuve', '2025-09-11 12:00:01', NULL, NULL, NULL),
(8, 5, 10, 'inscrit', '2025-09-11 12:00:01', NULL, NULL, NULL),
(9, 6, 6, 'liste_attente', '2025-09-11 12:00:01', NULL, NULL, NULL),
(10, 7, 1, 'approuve', '2025-09-11 12:00:01', NULL, NULL, NULL),
(11, 8, 12, 'inscrit', '2025-09-11 12:00:01', NULL, NULL, NULL),
(12, 9, 9, 'inscrit', '2025-09-11 12:00:01', NULL, NULL, NULL),
(13, 10, 2, 'inscrit', '2025-09-11 12:00:01', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `idMessage` int NOT NULL AUTO_INCREMENT,
  `idChat` int NOT NULL,
  `idSender` int NOT NULL,
  `content` text NOT NULL,
  `messageType` enum('texte','image','fichier','invitation_evenement','systeme') NOT NULL DEFAULT 'texte',
  `filePath` varchar(255) DEFAULT NULL,
  `isEdited` tinyint(1) NOT NULL DEFAULT '0',
  `editedAt` datetime DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `deletedAt` datetime DEFAULT NULL,
  `replyToMessageId` int DEFAULT NULL,
  `sentAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idMessage`),
  UNIQUE KEY `UQ_idMessage` (`idMessage`),
  KEY `FK_message_TO_message_reply` (`replyToMessageId`),
  KEY `idx_message_chat` (`idChat`),
  KEY `idx_message_sender` (`idSender`),
  KEY `idx_message_sent_at` (`sentAt`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`idMessage`, `idChat`, `idSender`, `content`, `messageType`, `filePath`, `isEdited`, `editedAt`, `isDeleted`, `deletedAt`, `replyToMessageId`, `sentAt`) VALUES
(1, 1, 2, 'Bienvenue à tous !', 'texte', NULL, 0, NULL, 0, NULL, NULL, '2025-09-11 12:00:01'),
(2, 2, 3, 'Qui vient au concert ?', 'texte', NULL, 0, NULL, 0, NULL, NULL, '2025-09-11 12:00:01'),
(3, 3, 4, 'Rendez-vous à 14h pour le tournoi.', 'texte', NULL, 0, NULL, 0, NULL, NULL, '2025-09-11 12:00:01'),
(4, 4, 5, 'Nouvelle exposition la semaine prochaine.', 'texte', NULL, 0, NULL, 0, NULL, NULL, '2025-09-11 12:00:01'),
(5, 5, 12, 'Cours en plein air si beau temps.', 'texte', NULL, 0, NULL, 0, NULL, NULL, '2025-09-11 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `message_status`
--

DROP TABLE IF EXISTS `message_status`;
CREATE TABLE IF NOT EXISTS `message_status` (
  `idMessageStatus` int NOT NULL AUTO_INCREMENT,
  `idMessage` int NOT NULL,
  `idUser` int NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `readAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idMessageStatus`),
  UNIQUE KEY `unique_message_user_status` (`idMessage`,`idUser`),
  KEY `FK_user_TO_message_status` (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message_status`
--

INSERT INTO `message_status` (`idMessageStatus`, `idMessage`, `idUser`, `isRead`, `readAt`) VALUES
(1, 1, 3, 1, '2025-09-11 12:00:01'),
(2, 2, 2, 1, '2025-09-11 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `idNotification` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `type` enum('invitation_evenement','mise_a_jour_evenement','message','invitation_association','rappel_evenement','systeme') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `readAt` datetime DEFAULT NULL,
  `relatedId` int DEFAULT NULL,
  `relatedType` varchar(50) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotification`),
  UNIQUE KEY `UQ_idNotification` (`idNotification`),
  KEY `idx_notification_user` (`idUser`),
  KEY `idx_notification_read` (`isRead`),
  KEY `idx_notification_created` (`createdAt`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`idNotification`, `idUser`, `type`, `title`, `message`, `isRead`, `readAt`, `relatedId`, `relatedType`, `createdAt`) VALUES
(1, 2, 'invitation_evenement', 'Invitation au concert', 'Vous êtes invité(e) au Concert d\'été', 0, NULL, NULL, NULL, '2025-09-11 12:00:01'),
(2, 3, 'mise_a_jour_evenement', 'Mise à jour de l\'événement', 'L\'événement a été mis à jour', 0, NULL, NULL, NULL, '2025-09-11 12:00:01'),
(3, 5, 'message', 'Nouveau message', 'Vous avez reçu un message.', 0, NULL, NULL, NULL, '2025-09-11 12:00:01'),
(4, 12, 'rappel_evenement', 'Rappel', 'Votre cours de yoga est demain.', 0, NULL, NULL, NULL, '2025-09-11 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `idRole` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `description` text,
  `permissions` json DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRole`),
  UNIQUE KEY `UQ_idRole` (`idRole`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`idRole`, `name`, `description`, `permissions`, `createdAt`) VALUES
(1, 'admin', 'System administrator with full access', NULL, '2025-09-11 12:00:01'),
(2, 'user', 'Regular user with basic permissions', NULL, '2025-09-11 12:00:01'),
(3, 'moderator', 'User with moderation permissions', NULL, '2025-09-11 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `idRole` int NOT NULL,
  `firstName` varchar(55) NOT NULL,
  `lastName` varchar(55) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatarPath` varchar(255) DEFAULT NULL,
  `bio` text,
  `dateOfBirth` date DEFAULT NULL,
  `isActivated` tinyint(1) NOT NULL DEFAULT '0',
  `isOnline` tinyint(1) NOT NULL DEFAULT '0',
  `lastSeen` datetime DEFAULT NULL,
  `rgpdAcceptedDate` datetime NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE KEY `UQ_idUser` (`idUser`),
  UNIQUE KEY `UQ_email` (`email`(191)),
  KEY `FK_role_TO_user` (`idRole`),
  KEY `idx_user_email` (`email`(250)),
  KEY `idx_user_online` (`isOnline`),
  KEY `idx_user_last_seen` (`lastSeen`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`idUser`, `idRole`, `firstName`, `lastName`, `email`, `phone`, `password`, `avatarPath`, `bio`, `dateOfBirth`, `isActivated`, `isOnline`, `lastSeen`, `rgpdAcceptedDate`, `token`, `createdAt`, `updatedAt`) VALUES
(1, 1, 'Alice', 'Durand', 'alice@example.com', '+33 6 12 34 56 78', '$2y$10$abcdefghijklmnopqrstuv', NULL, 'Admin of the platform', '1988-05-12', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(2, 2, 'Bruno', 'Martin', 'bruno@example.com', '+33 6 98 76 54 32', '$2y$10$abcdefghijklmnopqrstuv', NULL, 'Association manager', '1992-11-03', 1, 1, '2025-09-11 12:00:01', '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(3, 3, 'Camille', 'Lefevre', 'camille@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1995-02-21', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(4, 2, 'David', 'Bernard', 'david.bernard@example.com', '+33 6 01 02 03 04', '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1985-03-14', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(5, 2, 'Eva', 'Rossi', 'eva.rossi@example.com', '+33 6 05 06 07 08', '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1990-07-09', 1, 1, '2025-09-11 12:00:01', '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(6, 3, 'Farid', 'Lambert', 'farid.lambert@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1993-12-01', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(7, 2, 'Gilles', 'Moreau', 'gilles.moreau@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, 'Sport volunteer', '1982-09-30', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(8, 2, 'Hana', 'Petit', 'hana.petit@example.com', '+33 6 11 22 33 44', '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1998-05-25', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(9, 2, 'Ibrahim', 'Garcia', 'ibrahim.garcia@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1987-01-20', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(10, 2, 'Jade', 'Fournier', 'jade.fournier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1999-04-04', 1, 1, '2025-09-11 12:00:01', '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(11, 2, 'Karim', 'Lopez', 'karim.lopez@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1991-02-17', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(12, 2, 'Laura', 'Garnier', 'laura.garnier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1994-08-11', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(13, 2, 'Mika', 'Chevalier', 'mika.chevalier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1986-10-10', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(14, 2, 'Nora', 'Robert', 'nora.robert@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1997-06-06', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(15, 2, 'Olivier', 'Marchand', 'olivier.marchand@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1984-03-03', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(16, 2, 'Paula', 'Guyot', 'paula.guyot@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1996-01-29', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(17, 2, 'Quentin', 'Colin', 'quentin.colin@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1993-09-19', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(18, 2, 'Rania', 'Da Silva', 'rania.silva@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1990-12-24', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(19, 2, 'Sophie', 'Charpentier', 'sophie.charpentier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1989-07-07', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL),
(20, 2, 'Thomas', 'Barbier', 'thomas.barbier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1992-02-02', 1, 0, NULL, '2025-09-11 12:00:01', NULL, '2025-09-11 12:00:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_association`
--

DROP TABLE IF EXISTS `user_association`;
CREATE TABLE IF NOT EXISTS `user_association` (
  `idUserAssociation` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idAssociation` int NOT NULL,
  `role` enum('membre','admin','moderateur') NOT NULL DEFAULT 'membre',
  `joinedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idUserAssociation`),
  UNIQUE KEY `unique_user_association` (`idUser`,`idAssociation`),
  KEY `FK_association_TO_user_association` (`idAssociation`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_association`
--

INSERT INTO `user_association` (`idUserAssociation`, `idUser`, `idAssociation`, `role`, `joinedAt`, `isActive`) VALUES
(1, 2, 1, 'admin', '2025-09-11 12:00:01', 1),
(2, 3, 1, 'membre', '2025-09-11 12:00:01', 1),
(3, 2, 2, 'moderateur', '2025-09-11 12:00:01', 1),
(4, 4, 3, 'admin', '2025-09-11 12:00:01', 1),
(5, 5, 3, 'membre', '2025-09-11 12:00:01', 1),
(6, 6, 4, 'moderateur', '2025-09-11 12:00:01', 1),
(7, 7, 4, 'membre', '2025-09-11 12:00:01', 1),
(8, 8, 5, 'membre', '2025-09-11 12:00:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ville`
--

DROP TABLE IF EXISTS `ville`;
CREATE TABLE IF NOT EXISTS `ville` (
  `idVille` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `population` int NOT NULL,
  `postalCode` varchar(20) NOT NULL,
  `region` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idVille`),
  UNIQUE KEY `UQ_idVille` (`idVille`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ville`
--

INSERT INTO `ville` (`idVille`, `name`, `population`, `postalCode`, `region`) VALUES
(1, 'Paris', 2161000, '75001', 'Île-de-France'),
(2, 'Lyon', 515695, '69001', 'Auvergne-Rhône-Alpes'),
(3, 'Marseille', 868277, '13001', 'Provence-Alpes-Côte d\'Azur'),
(4, 'Toulouse', 479553, '31000', 'Occitanie'),
(5, 'Nice', 342637, '06000', 'Provence-Alpes-Côte d\'Azur');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
