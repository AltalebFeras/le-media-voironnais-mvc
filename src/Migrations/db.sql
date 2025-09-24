-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 24, 2025 at 03:28 PM
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
  `uiid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `logoPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bannerPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `isPublic` tinyint(1) NOT NULL DEFAULT '0',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `idUser` int NOT NULL,
  `idVille` mediumint UNSIGNED NOT NULL DEFAULT '14329',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idAssociation`),
  UNIQUE KEY `UQ_idAssociation` (`idAssociation`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `uiid` (`uiid`),
  KEY `idx_association_user` (`idUser`),
  KEY `idx_association_active` (`isActive`),
  KEY `idx_association_ville` (`idVille`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `association`
--

INSERT INTO `association` (`idAssociation`, `uiid`, `name`, `slug`, `description`, `logoPath`, `bannerPath`, `address`, `phone`, `email`, `website`, `isActive`, `isPublic`, `isDeleted`, `idUser`, `idVille`, `createdAt`, `updatedAt`) VALUES
(30, 'a1b2c3d4e5f6g7h8', 'Club de Randonnée des Alpes', 'club-randonnee-alpes', 'Association de randonneurs passionnés explorant les sentiers alpins de la région Voironnaise.', 'assets/images/uploads/logos/randonnee_logo.jpg', 'assets/images/uploads/banners/montagne_banner.jpg', '15 Avenue des Alpes, Voiron', '0476050101', 'contact@randonnee-alpes.fr', 'https://www.randonnee-alpes.fr', 1, 1, 0, 2, 14329, '2025-09-22 15:49:23', NULL),
(31, 'b2c3d4e5f6g7h8i9', 'Association Culturelle du Voironnais', 'culture-voironnais', 'Promotion de la culture locale à travers spectacles, expositions et événements artistiques.', 'assets/images/uploads/logos/culture_logo.jpg', 'assets/images/uploads/banners/culture_banner.jpg', '8 Place de la Mairie, Voiron', '0476050102', 'info@culture-voironnais.org', 'https://www.culture-voironnais.org', 1, 1, 0, 2, 14329, '2025-09-22 15:49:23', NULL),
(32, 'c3d4e5f6g7h8i9j0', 'Sport Club Isérois', 'sport-club-iserois', 'Club multisports proposant football, tennis et natation pour tous âges.', 'assets/images/uploads/logos/sport_logo.jpg', 'assets/images/uploads/banners/sport_banner.jpg', '22 Rue du Stade, Grenoble', '0476050103', 'secretariat@sport-iserois.com', 'https://www.sport-iserois.com', 1, 1, 0, 3, 38100, '2025-09-22 15:49:23', NULL),
(33, 'd4e5f6g7h8i9j0k1', 'Les Amis de la Nature', 'amis-nature', 'Association environnementale dédiée à la protection de la biodiversité locale.', 'assets/images/uploads/logos/nature_logo.jpg', 'assets/images/uploads/banners/nature_banner.jpg', '45 Chemin des Bois, Grenoble', '0476050104', 'contact@amis-nature.fr', 'https://www.amis-nature.fr', 1, 1, 0, 3, 38100, '2025-09-22 15:49:23', NULL),
(34, 'e5f6g7h8i9j0k1l2', 'Association Jeunesse Voironnaise', 'jeunesse-voironnaise', 'Accompagnement des jeunes dans leurs projets éducatifs et professionnels.', 'assets/images/uploads/logos/jeunesse_logo.jpg', 'assets/images/uploads/banners/jeunesse_banner.jpg', '12 Boulevard de la Jeunesse, Voiron', '0476050105', 'animation@jeunesse-voironnaise.fr', 'https://www.jeunesse-voironnaise.fr', 1, 1, 0, 4, 14329, '2025-09-22 15:49:23', NULL),
(35, 'f6g7h8i9j0k1l2m3', 'Solidarité Seniors Isère', 'solidarite-seniors', 'Aide et accompagnement des personnes âgées de la région iséroise.', 'assets/images/uploads/logos/seniors_logo.jpg', 'assets/images/uploads/banners/seniors_banner.jpg', '30 Avenue des Tilleuls, Voiron', '0476050106', 'aide@solidarite-seniors.org', 'https://www.solidarite-seniors.org', 1, 1, 0, 4, 14329, '2025-09-22 15:49:23', NULL),
(36, 'ed0260cbaa8cc841', 'Sport Club Iséroiss', 'grenoble-38100-sport-club-iseroiss', 'Sport Club IséroisSport Club Isérois', 'assets/images/uploads/logos/68d24b776a41d_1758612343.webp', 'assets/images/uploads/banners/68d24b7eb05d5_1758612350.webp', 'Sport Club IséroisSport Club Isérois', '0780773302', 'feras.altalib@gmail.com', '', 1, 0, 0, 3, 38100, '2025-09-23 09:25:24', '2025-09-23 09:25:54');

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
  `status` enum('en_attente','accepte','refuse') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'en_attente',
  `invitedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idAssociationInvitation`),
  UNIQUE KEY `unique_association_user_invitation` (`idAssociation`,`idUser`),
  KEY `FK_user_TO_association_invitation_user` (`idUser`),
  KEY `FK_user_TO_association_invitation_inviter` (`idInviter`),
  KEY `idAssociation` (`idAssociation`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
CREATE TABLE IF NOT EXISTS `chat` (
  `idChat` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_participant`
--

DROP TABLE IF EXISTS `chat_participant`;
CREATE TABLE IF NOT EXISTS `chat_participant` (
  `idChatParticipant` int NOT NULL AUTO_INCREMENT,
  `idChat` int NOT NULL,
  `idUser` int NOT NULL,
  `role` enum('membre','admin','moderateur') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'membre',
  `joinedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leftAt` datetime DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idChatParticipant`),
  UNIQUE KEY `unique_chat_user_participant` (`idChat`,`idUser`),
  KEY `FK_user_TO_chat_participant` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `idEntreprise` int NOT NULL AUTO_INCREMENT,
  `uiid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `logoPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bannerPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `siret` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  `isPublic` tinyint(1) NOT NULL DEFAULT '0',
  `isPartner` tinyint(1) NOT NULL DEFAULT '0',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `idUser` int NOT NULL,
  `idVille` mediumint UNSIGNED NOT NULL DEFAULT '14329',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  `partnerStartDate` datetime DEFAULT NULL,
  `partnerEndDate` datetime DEFAULT NULL,
  PRIMARY KEY (`idEntreprise`),
  UNIQUE KEY `UQ_idEntreprise` (`idEntreprise`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `uiid` (`uiid`),
  KEY `idx_entreprise_user` (`idUser`),
  KEY `idx_entreprise_active` (`isActive`),
  KEY `idx_entreprise_ville` (`idVille`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `entreprise`
--

INSERT INTO `entreprise` (`idEntreprise`, `uiid`, `name`, `slug`, `description`, `logoPath`, `bannerPath`, `address`, `phone`, `email`, `website`, `siret`, `isActive`, `isPublic`, `isPartner`, `isDeleted`, `idUser`, `idVille`, `createdAt`, `updatedAt`, `partnerStartDate`, `partnerEndDate`) VALUES
(30, 'ent1b2c3d4e5f6g7', 'TechnoSoft Solutions', 'technosoft-solutions', 'Société de développement logiciel spécialisée dans les solutions métiers.', 'assets/images/uploads/logos/techno_logo.jpg', 'assets/images/uploads/banners/tech_banner.jpg', '25 Zone Industrielle, Voiron', '0476060201', 'contact@technosoft.fr', 'https://www.technosoft.fr', '12345678901234', 1, 1, 0, 0, 2, 14329, '2025-09-22 15:49:23', NULL, NULL, NULL),
(31, 'ent2c3d4e5f6g7h8', 'Boulangerie des Chartreuses', 'boulangerie-chartreuses', 'Boulangerie artisanale proposant pains traditionnels et pâtisseries locales.', 'assets/images/uploads/logos/boulangerie_logo.jpg', 'assets/images/uploads/banners/boulangerie_banner.jpg', '5 Rue de la Boulangerie, Voiron', '0476060202', 'info@boulangerie-chartreuses.fr', 'https://www.boulangerie-chartreuses.fr', '23456789012345', 1, 1, 1, 0, 2, 14329, '2025-09-22 15:49:23', NULL, NULL, NULL),
(32, 'ent3d4e5f6g7h8i9', 'Consulting Alpes Isère', 'consulting-alpes-isere', 'Cabinet de conseil en management et stratégie d entreprise.', 'assets/images/uploads/logos/68d24aa3a9936_1758612131.webp', 'assets/images/uploads/banners/68d24aae16403_1758612142.webp', '18 Avenue Jean Jaurès, Grenoble', '0476060203', 'direction@consulting-alpes.com', 'https://www.consulting-alpes.com', '34567890123456', 0, 0, 0, 1, 3, 38100, '2025-09-22 15:49:23', '2025-09-23 09:27:51', NULL, NULL),
(33, 'ent4e5f6g7h8i9j0', 'EcoVert Jardinage', 'ecoverts-jardinage', 'Entreprise de jardinage écologique et aménagement paysager durable.', 'assets/images/uploads/logos/68d24a836ea40_1758612099.webp', 'assets/images/uploads/banners/68d24a900a4d4_1758612112.webp', '33 Route de Lyon, Grenoble', '0476060204', 'devis@ecoverts.fr', 'https://www.ecoverts.fr', '45678901234567', 1, 1, 1, 0, 3, 38100, '2025-09-22 15:49:23', '2025-09-23 09:21:55', NULL, NULL),
(34, 'ent5f6g7h8i9j0k1', 'Artisan Menuiserie Dauphiné', 'menuiserie-dauphine', 'Menuiserie traditionnelle spécialisée dans la restauration de bâtiments anciens.', 'assets/images/uploads/logos/menuiserie_logo.jpg', 'assets/images/uploads/banners/menuiserie_banner.jpg', '7 Impasse des Artisans, Voiron', '0476060205', 'atelier@menuiserie-dauphine.fr', 'https://www.menuiserie-dauphine.fr', '56789012345678', 1, 1, 0, 0, 4, 14329, '2025-09-22 15:49:23', NULL, NULL, NULL),
(35, 'ent6g7h8i9j0k1l2', 'Café-Restaurant Le Montagnard', 'cafe-montagnard', 'Restaurant traditionnel proposant spécialités savoyardes et cuisine de montagne.', 'assets/images/uploads/logos/restaurant_logo.jpg', 'assets/images/uploads/banners/restaurant_banner.jpg', '14 Place du Marché, Voiron', '0476060206', 'reservation@montagnard.fr', 'https://www.montagnard.fr', '67890123456789', 1, 1, 1, 0, 4, 14329, '2025-09-22 15:49:23', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
CREATE TABLE IF NOT EXISTS `evenement` (
  `idEvenement` int NOT NULL AUTO_INCREMENT,
  `uiid` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `shortDescription` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime DEFAULT NULL,
  `registrationDeadline` datetime DEFAULT NULL,
  `maxParticipants` int NOT NULL,
  `currentParticipants` int NOT NULL DEFAULT '0',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bannerPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('brouillon','actif','annule','termine') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'brouillon',
  `isPublic` tinyint(1) NOT NULL DEFAULT '1',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `requiresApproval` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'EUR',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  `idUser` int NOT NULL,
  `idAssociation` int DEFAULT NULL,
  `idEntreprise` int DEFAULT NULL,
  `idVille` mediumint UNSIGNED NOT NULL,
  `idEventCategory` int DEFAULT NULL,
  PRIMARY KEY (`idEvenement`),
  UNIQUE KEY `UQ_idEvenement` (`idEvenement`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `uiid` (`uiid`),
  KEY `FK_user_TO_evenement_creator` (`idUser`),
  KEY `FK_ville_TO_evenement` (`idVille`),
  KEY `idx_evenement_date` (`startDate`),
  KEY `idx_evenement_status` (`status`),
  KEY `idx_evenement_public` (`isPublic`),
  KEY `idx_evenement_association` (`idAssociation`),
  KEY `idx_evenement_entreprise` (`idEntreprise`),
  KEY `idx_evenement_category` (`idEventCategory`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evenement`
--

INSERT INTO `evenement` (`idEvenement`, `uiid`, `title`, `slug`, `description`, `shortDescription`, `startDate`, `endDate`, `registrationDeadline`, `maxParticipants`, `currentParticipants`, `address`, `bannerPath`, `status`, `isPublic`, `isDeleted`, `requiresApproval`, `price`, `currency`, `createdAt`, `updatedAt`, `idUser`, `idAssociation`, `idEntreprise`, `idVille`, `idEventCategory`) VALUES
(73, 'evt1234567890ab', 'Randonnée Mont Aiguille', 'randonnee-mont-aiguille', 'Découverte du Mont Aiguille avec guide expérimenté. Niveau intermédiaire requis.', 'Randonnée guidée au Mont Aiguille', '2025-10-15 08:00:00', '2025-10-15 18:00:00', '2025-10-10 23:59:59', 15, 0, 'Parking Col de la Bataille, Chichilianne', 'assets/images/uploads/banners/randonnee_banner.jpg', 'actif', 1, 0, 0, 25.00, 'EUR', '2025-09-22 15:53:06', NULL, 2, NULL, NULL, 14695, 1),
(74, 'evt2345678901bc', 'Concert de Jazz', 'concert-jazz-voiron', 'Soirée jazz avec le trio vocal Les Voix du Voironnais dans un cadre intimiste.', 'Concert jazz intimiste', '2025-11-08 20:30:00', '2025-11-08 22:30:00', '2025-11-05 18:00:00', 80, 0, 'Salle des Fêtes, Place Charles de Gaulle, Voiron', 'assets/images/uploads/banners/concert_banner.jpg', 'actif', 1, 0, 0, 15.00, 'EUR', '2025-09-22 15:53:06', NULL, 2, NULL, NULL, 14329, 6),
(75, 'evt3456789012cd', 'Formation Développement Web', 'formation-dev-web', 'Formation intensive sur les technologies web modernes (HTML5, CSS3, JavaScript).', 'Formation développement web', '2025-10-25 09:00:00', '2025-10-27 17:00:00', '2025-10-20 23:59:59', 12, 0, '25 Zone Industrielle, Voiron', 'assets/images/uploads/banners/formation_banner.jpg', 'actif', 1, 0, 0, 450.00, 'EUR', '2025-09-22 15:53:06', NULL, 2, NULL, NULL, 14329, 9),
(76, 'evt4567890123de', 'Dégustation Pains Artisanaux', 'degustation-pains', 'Découvrez nos créations artisanales lors d une dégustation commentée.', 'Dégustation pains artisanaux', '2025-11-12 15:00:00', '2025-11-12 17:00:00', '2025-11-10 12:00:00', 20, 0, '5 Rue de la Boulangerie, Voiron', 'assets/images/uploads/banners/degustation_banner.jpg', 'actif', 1, 0, 0, 0.00, 'EUR', '2025-09-22 15:53:06', NULL, 2, NULL, NULL, 14329, 7),
(77, 'evt5678901234ef', 'Trail des Chartreuses', 'trail-chartreuses', 'Course nature de 15km à travers les sentiers du massif de la Chartreuse.', 'Trail 15km Chartreuse', '2025-11-20 09:00:00', '2025-11-20 14:00:00', '2025-11-15 23:59:59', 100, 0, 'Départ Mairie de Voiron', 'assets/images/uploads/banners/trail_banner.jpg', 'actif', 1, 0, 0, 20.00, 'EUR', '2025-09-22 15:53:06', NULL, 2, NULL, NULL, 14329, 1),
(78, 'evt6789012345fg', 'Tournoi de Tennis', 'tournoi-tennis-isere', 'Championnat départemental de tennis ouvert à toutes catégories d âge.', 'Tournoi tennis départemental', '2025-10-28 08:00:00', '2025-10-29 19:00:00', '2025-10-23 23:59:00', 64, 0, 'Courts de Tennis, 22 Rue du Stade, Grenoble', 'assets/images/uploads/banners/68d155117684f_1758549265.webp', 'brouillon', 1, 0, 0, 30.00, 'EUR', '2025-09-22 15:53:06', '2025-09-22 16:03:47', 3, 33, NULL, 38100, 1),
(79, 'evt7890123456gh', 'Nettoyage Rivière Isère', 'nettoyage-riviere-isere', 'Action citoyenne de nettoyage des berges de l Isère avec sensibilisation environnementale.', 'Nettoyage citoyen rivière', '2025-11-05 09:00:00', '2025-11-05 16:00:00', '2025-11-02 18:00:00', 50, 0, 'Berges de l Isère, Pont de la Citadelle, Grenoble', 'assets/images/uploads/banners/68d15fb9d1bb3_1758551993.webp', 'actif', 1, 0, 0, 0.00, 'EUR', '2025-09-22 15:53:06', '2025-09-22 16:39:56', 3, NULL, NULL, 38100, 12),
(80, 'evt8901234567hi', 'Conférence Management', 'conference-management', 'Stratégies de management agile pour les entreprises modernes.', 'Conférence management agile', '2025-11-18 14:00:00', '2025-11-18 17:30:00', '2025-11-15 12:00:00', 40, 0, '18 Avenue Jean Jaurès, Grenoble', 'assets/images/uploads/banners/68d15fc898b81_1758552008.webp', 'actif', 1, 0, 0, 75.00, 'EUR', '2025-09-22 15:53:06', '2025-09-22 16:40:10', 3, NULL, NULL, 38100, 5),
(81, 'evt9012345678ij', 'Atelier Jardinage Bio', 'atelier-jardinage-bio', 'Apprenez les techniques de jardinage biologique et permaculture.', 'Atelier jardinage biologique', '2025-10-30 14:00:00', '2025-10-30 17:00:00', '2025-10-27 20:00:00', 15, 0, '33 Route de Lyon, Grenoble', 'assets/images/uploads/banners/jardinage_banner.jpg', 'actif', 1, 0, 0, 35.00, 'EUR', '2025-09-22 15:53:06', NULL, 3, NULL, NULL, 38100, 4),
(82, 'evt0123456789jk', 'Match de Football Amateur', 'match-football-amateur', 'Rencontre amicale entre équipes locales avec buvette et restauration.', 'Match football amateur', '2025-11-25 15:00:00', '2025-11-25 17:00:00', '2025-11-24 12:00:00', 200, 0, 'Stade Municipal, 22 Rue du Stade, Grenoble', 'assets/images/uploads/banners/68d15fd629167_1758552022.webp', 'actif', 1, 0, 0, 5.00, 'EUR', '2025-09-22 15:53:06', '2025-09-22 16:40:23', 3, NULL, NULL, 38100, 1),
(83, 'evt1357924680kl', 'Atelier Insertion Jeunes', 'atelier-insertion-jeunes', 'Accompagnement personnalisé pour la recherche d emploi et l orientation professionnelle.', 'Atelier insertion professionnelle', '2025-10-22 09:00:00', '2025-10-22 17:00:00', '2025-10-18 18:00:00', 25, 0, '12 Boulevard de la Jeunesse, Voiron', 'assets/images/uploads/banners/insertion_banner.jpg', 'actif', 1, 0, 0, 0.00, 'EUR', '2025-09-22 15:53:06', NULL, 4, NULL, NULL, 14329, 4),
(84, 'evt2468013579lm', 'Goûter Intergénérationnel', 'gouter-intergenerationnel', 'Moment de partage et d échange entre seniors et jeunes autour d un goûter convivial.', 'Goûter seniors-jeunes', '2025-11-14 15:00:00', '2025-11-14 17:30:00', '2025-11-12 12:00:00', 40, 0, '30 Avenue des Tilleuls, Voiron', 'assets/images/uploads/banners/intergenerationnel_banner.jpg', 'actif', 1, 0, 0, 0.00, 'EUR', '2025-09-22 15:53:06', NULL, 4, NULL, NULL, 14329, 10),
(85, 'evt3691470258mn', 'Exposition Menuiserie', 'exposition-menuiserie', 'Présentation de réalisations artisanales et démonstrations de techniques traditionnelles.', 'Expo savoir-faire menuiserie', '2025-11-01 10:00:00', '2025-11-03 18:00:00', '2025-10-28 23:59:59', 100, 0, '7 Impasse des Artisans, Voiron', 'assets/images/uploads/banners/menuiserie_banner.jpg', 'actif', 1, 0, 0, 0.00, 'EUR', '2025-09-22 15:53:06', NULL, 4, NULL, NULL, 14329, 8),
(86, 'evt4815926037no', 'Soirée Savoyarde', 'soiree-savoyarde', 'Repas traditionnel avec spécialités montagnardes et animation folklorique.', 'Soirée gastronomie savoyarde', '2025-11-22 19:00:00', '2025-11-22 23:00:00', '2025-11-19 18:00:00', 60, 0, '14 Place du Marché, Voiron', 'assets/images/uploads/banners/savoyard_banner.jpg', 'actif', 1, 0, 0, 42.00, 'EUR', '2025-09-22 15:53:06', NULL, 4, NULL, NULL, 14329, 7),
(87, 'evt5927384061op', 'Cours de Soutien Scolaire', 'cours-soutien-scolaire', 'Aide aux devoirs et soutien scolaire pour collégiens et lycéens.', 'Soutien scolaire personnalisé', '2025-11-04 16:00:00', '2025-11-04 18:00:00', '2025-11-02 20:00:00', 10, 0, '12 Boulevard de la Jeunesse, Voiron', 'assets/images/uploads/banners/soutien_banner.jpg', 'actif', 1, 0, 0, 15.00, 'EUR', '2025-09-22 15:53:06', NULL, 4, NULL, NULL, 14329, 4),
(88, '524b9a3027fba03e', 'qsdqqqqqsdqdqsd', 'corenc-qsdqqqqqsdqdqsd-art', 'qsqsdqsdqsdqs', 'dqsdqsdqsdqsdq', '2025-10-03 16:38:00', '2025-10-11 16:38:00', '2025-09-25 16:38:00', 33, 0, '42 Rue Henri Duhamel', 'assets/images/uploads/banners/68d24a4448918_1758612036.webp', 'brouillon', 0, 1, 0, 3.00, 'EUR', '2025-09-22 16:39:09', '2025-09-23 09:20:39', 3, NULL, NULL, 14588, 8);

-- --------------------------------------------------------

--
-- Table structure for table `event_category`
--

DROP TABLE IF EXISTS `event_category`;
CREATE TABLE IF NOT EXISTS `event_category` (
  `idEventCategory` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventCategory`),
  UNIQUE KEY `UQ_idEventCategory` (`idEventCategory`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_category`
--

INSERT INTO `event_category` (`idEventCategory`, `name`, `description`, `color`, `icon`, `isActive`, `createdAt`) VALUES
(1, 'Sport', 'Événements sportifs et activités physiques', '#FF6B6B', NULL, 1, '2025-09-11 12:00:01'),
(2, 'Culture', 'Événements culturels et expositions', '#4ECDC4', NULL, 1, '2025-09-11 12:00:01'),
(3, 'Social', 'Rassemblements sociaux et réseautage', '#45B7D1', NULL, 1, '2025-09-11 12:00:01'),
(4, 'Éducation', 'Ateliers éducatifs et séminaires', '#96CEB4', NULL, 1, '2025-09-11 12:00:01'),
(5, 'Affaires', 'Réunions d\'affaires et conférences', '#FFEAA7', NULL, 1, '2025-09-11 12:00:01'),
(6, 'Musique', 'Concerts et événements musicaux', '#A29BFE', NULL, 1, '2025-09-11 12:00:01'),
(7, 'Gastronomie', 'Festivals culinaires et dégustations', '#FD79A8', NULL, 1, '2025-09-11 12:00:01'),
(8, 'Art', 'Expositions d\'art et ateliers créatifs', '#FDCB6E', NULL, 1, '2025-09-11 12:00:01'),
(9, 'Technologie', 'Événements tech et innovations', '#6C5CE7', NULL, 1, '2025-09-11 12:00:01'),
(10, 'Famille', 'Activités familiales et événements pour enfants', '#55A3FF', NULL, 1, '2025-09-11 12:00:01'),
(11, 'Santé', 'Bien-être et événements de santé', '#00B894', NULL, 1, '2025-09-11 12:00:01'),
(12, 'Environnement', 'Événements écologiques et durables', '#00CEC9', NULL, 1, '2025-09-11 12:00:01'),
(13, 'Théâtre', 'Pièces de théâtre et spectacles', '#E17055', NULL, 1, '2025-09-11 12:00:01'),
(14, 'Marché', 'Marchés locaux et foires', '#81ECEC', NULL, 1, '2025-09-11 12:00:01'),
(15, 'Religion', 'Événements religieux et spirituels', '#DDA0DD', NULL, 1, '2025-09-11 12:00:01'),
(16, 'Autre', 'Événements divers et variés', '#CCCCCC', NULL, 1, '2025-09-18 10:44:01');

-- --------------------------------------------------------

--
-- Table structure for table `event_image`
--

DROP TABLE IF EXISTS `event_image`;
CREATE TABLE IF NOT EXISTS `event_image` (
  `idEventImage` int NOT NULL AUTO_INCREMENT,
  `idEvenement` int NOT NULL,
  `imagePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `altText` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isMain` tinyint(1) NOT NULL DEFAULT '0',
  `sortOrder` int NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventImage`),
  UNIQUE KEY `UQ_idEventImage` (`idEventImage`),
  KEY `FK_evenement_TO_event_image` (`idEvenement`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_image`
--

INSERT INTO `event_image` (`idEventImage`, `idEvenement`, `imagePath`, `altText`, `isMain`, `sortOrder`, `createdAt`) VALUES
(19, 78, 'assets/images/uploads/events/68d1551f9911b_1758549279.webp', '', 0, 1, '2025-09-22 15:54:40'),
(20, 88, 'assets/images/uploads/events/68d24a177033d_1758611991.webp', '', 0, 1, '2025-09-23 09:19:54'),
(21, 88, 'assets/images/uploads/events/68d24a29de250_1758612009.webp', '', 0, 2, '2025-09-23 09:20:14'),
(22, 80, 'assets/images/uploads/events/68d24a61a465d_1758612065.webp', '', 0, 1, '2025-09-23 09:21:07');

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
  `status` enum('en_attente','accepte','refuse') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'en_attente',
  `invitedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondedAt` datetime DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`idEventInvitation`),
  UNIQUE KEY `unique_event_user_invitation` (`idEvenement`,`idUser`),
  KEY `FK_user_TO_event_invitation_user` (`idUser`),
  KEY `FK_user_TO_event_invitation_inviter` (`idInviter`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_participant`
--

DROP TABLE IF EXISTS `event_participant`;
CREATE TABLE IF NOT EXISTS `event_participant` (
  `idEventParticipant` int NOT NULL AUTO_INCREMENT,
  `idEvenement` int NOT NULL,
  `idUser` int NOT NULL,
  `status` enum('inscrit','approuve','liste_attente','annule') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inscrit',
  `joinedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approvedAt` datetime DEFAULT NULL,
  `cancelledAt` datetime DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`idEventParticipant`),
  UNIQUE KEY `unique_event_user_participant` (`idEvenement`,`idUser`),
  KEY `idx_event_participant_evenement` (`idEvenement`),
  KEY `idx_event_participant_user` (`idUser`),
  KEY `idx_event_participant_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `idMessage` int NOT NULL AUTO_INCREMENT,
  `idChat` int NOT NULL,
  `idSender` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `messageType` enum('texte','image','fichier','invitation_evenement','systeme') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'texte',
  `filePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `idNotification` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idEvenement` int DEFAULT NULL,
  `type` enum('invitation_evenement','mise_a_jour_evenement','message','invitation_association','rappel_evenement','systeme','alert','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `readAt` datetime DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotification`),
  UNIQUE KEY `UQ_idNotification` (`idNotification`),
  KEY `idx_notification_user` (`idUser`),
  KEY `idx_notification_read` (`isRead`),
  KEY `idx_notification_created` (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `realisation`
--

DROP TABLE IF EXISTS `realisation`;
CREATE TABLE IF NOT EXISTS `realisation` (
  `idRealisation` int NOT NULL AUTO_INCREMENT,
  `uiid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `dateRealized` date DEFAULT NULL,
  `isPublic` tinyint(1) NOT NULL DEFAULT '1',
  `isFeatured` tinyint(1) NOT NULL DEFAULT '0',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `idEntreprise` int NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idRealisation`),
  UNIQUE KEY `UQ_idRealisation` (`idRealisation`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `uiid` (`uiid`),
  KEY `idx_realisation_entreprise` (`idEntreprise`),
  KEY `idx_realisation_public` (`isPublic`),
  KEY `idx_realisation_featured` (`isFeatured`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `realisation`
--

INSERT INTO `realisation` (`idRealisation`, `uiid`, `title`, `slug`, `description`, `dateRealized`, `isPublic`, `isFeatured`, `isDeleted`, `idEntreprise`, `createdAt`, `updatedAt`) VALUES
(1, 'dffdg12d6fg6', 'dfffffffffffffffffffffff', 'fffffffffffffffffffffffffff', 'ffffffdgggggggggggggggggggggggggggg', '2025-09-23', 1, 0, 1, 32, '2025-09-23 15:35:40', '2025-09-24 14:39:10'),
(2, '7e2a452f26f09435', 'ddddddds', 'ecovert-jardinage-ddddddds', 'sdsdfsdfsd', '2025-10-03', 0, 0, 0, 33, '2025-09-24 13:53:48', '2025-09-24 14:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `realisation_image`
--

DROP TABLE IF EXISTS `realisation_image`;
CREATE TABLE IF NOT EXISTS `realisation_image` (
  `idRealisationImage` int NOT NULL AUTO_INCREMENT,
  `idRealisation` int NOT NULL,
  `imagePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `altText` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isMain` tinyint(1) NOT NULL DEFAULT '0',
  `sortOrder` int NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRealisationImage`),
  UNIQUE KEY `UQ_idRealisationImage` (`idRealisationImage`),
  KEY `FK_realisation_TO_realisation_image` (`idRealisation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `idRole` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `permissions` json DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRole`),
  UNIQUE KEY `UQ_idRole` (`idRole`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`idRole`, `name`, `description`, `permissions`, `createdAt`) VALUES
(1, 'super_admin', NULL, NULL, '2025-09-11 12:00:01'),
(2, 'admin', NULL, NULL, '2025-09-11 12:00:01'),
(3, 'user', NULL, NULL, '2025-09-11 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `idRole` int NOT NULL,
  `firstName` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lastName` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `avatarPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bannerPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `dateOfBirth` date DEFAULT NULL,
  `isActivated` tinyint(1) NOT NULL DEFAULT '0',
  `isBanned` tinyint NOT NULL DEFAULT '0',
  `isDeleted` tinyint NOT NULL DEFAULT '0',
  `isOnline` tinyint(1) NOT NULL DEFAULT '0',
  `lastSeen` datetime DEFAULT NULL,
  `rgpdAcceptedDate` datetime NOT NULL,
  `authCode` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  `emailChangedAt` datetime DEFAULT NULL,
  `passwordResetAt` datetime DEFAULT NULL,
  `deletedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE KEY `UQ_idUser` (`idUser`),
  UNIQUE KEY `UQ_email` (`email`(191)),
  UNIQUE KEY `authCode` (`authCode`),
  UNIQUE KEY `authCode_2` (`authCode`),
  KEY `FK_role_TO_user` (`idRole`),
  KEY `idx_user_email` (`email`(250)),
  KEY `idx_user_online` (`isOnline`),
  KEY `idx_user_last_seen` (`lastSeen`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`idUser`, `idRole`, `firstName`, `lastName`, `email`, `phone`, `password`, `avatarPath`, `bannerPath`, `bio`, `dateOfBirth`, `isActivated`, `isBanned`, `isDeleted`, `isOnline`, `lastSeen`, `rgpdAcceptedDate`, `authCode`, `token`, `createdAt`, `updatedAt`, `emailChangedAt`, `passwordResetAt`, `deletedAt`) VALUES
(1, 2, 'Admin', 'Admin', 'admin@le-media-voironnais.fr', NULL, '$2y$10$rzWzNnz7Q22b3WiB4JWeyuDvjTmpzGu4hf/15935BZctTYMWnv3F.', 'http://le-media-voironnais/assets/images/uploads/avatars/68c803d9271c3_1745690908790.jpg', 'http://le-media-voironnais/assets/images/uploads/banners/68c803d3be008_1745690908717.jpg', NULL, NULL, 1, 0, 0, 0, '2025-09-19 09:37:31', '2025-09-15 10:24:49', NULL, NULL, '2025-09-15 10:24:49', NULL, '2025-09-15 14:17:52', NULL, NULL),
(2, 3, 'Thomas', 'Barbier', 'thomas.barbier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, NULL, '1992-02-02', 1, 0, 0, 1, '2025-09-12 14:36:10', '2025-09-11 12:00:01', NULL, NULL, '2025-09-11 12:00:01', '2025-09-15 17:04:06', NULL, NULL, NULL),
(3, 3, 'Feras', 'Altaleb', 'feras.altalib@gmail.com', '0780773302', '$2y$10$Kbxc93eYvvVe58NdCmf6ruBQvfHCY8/aAOo0q6iPNIfVQaJSE.40W', 'http://le-media-voironnais/assets/images/uploads/avatars/default_avatar.png', 'http://le-media-voironnais/assets/images/uploads/banners/default_banner.jpg', 'qdqsdqsdqsd', '2000-10-01', 1, 0, 0, 1, '2025-09-24 16:30:37', '2025-09-15 09:47:56', NULL, '18aab59f6829597f2a447a393b407e35', '2025-09-15 09:47:56', '2025-09-22 11:38:58', '2025-09-15 10:10:17', '2025-09-19 09:35:59', NULL),
(4, 3, 'Feras2011', 'Altaleb2011', 'feras.altalib2011@gmail.com', NULL, '$2y$10$92eJhlvgg7QhaJBGfgzFq.kGjQToKu9sBXr4C9lK3PzNSU27QtbY.', NULL, NULL, NULL, NULL, 1, 0, 0, 1, '2025-09-17 19:16:31', '2025-09-17 19:00:01', NULL, NULL, '2025-09-17 19:00:01', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_association`
--

DROP TABLE IF EXISTS `user_association`;
CREATE TABLE IF NOT EXISTS `user_association` (
  `idUserAssociation` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idAssociation` int NOT NULL,
  `role` enum('membre','admin','moderateur') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'membre',
  `joinedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idUserAssociation`),
  UNIQUE KEY `unique_user_association` (`idUser`,`idAssociation`),
  KEY `FK_association_TO_user_association` (`idAssociation`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_association`
--

INSERT INTO `user_association` (`idUserAssociation`, `idUser`, `idAssociation`, `role`, `joinedAt`, `isActive`) VALUES
(27, 3, 36, 'admin', '2025-09-23 09:25:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ville`
--

DROP TABLE IF EXISTS `ville`;
CREATE TABLE IF NOT EXISTS `ville` (
  `idVille` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ville_departement` varchar(3) DEFAULT NULL,
  `ville_slug` varchar(255) DEFAULT NULL,
  `ville_nom` varchar(45) DEFAULT NULL,
  `ville_nom_simple` varchar(45) DEFAULT NULL,
  `ville_nom_reel` varchar(45) DEFAULT NULL,
  `ville_code_postal` varchar(255) DEFAULT NULL,
  `ville_commune` varchar(3) DEFAULT NULL,
  `ville_code_commune` varchar(5) NOT NULL,
  `ville_population_2012` mediumint UNSIGNED DEFAULT NULL COMMENT 'approximatif',
  `ville_longitude_deg` float DEFAULT NULL,
  `ville_latitude_deg` float DEFAULT NULL,
  `ville_longitude_grd` varchar(9) DEFAULT NULL,
  `ville_latitude_grd` varchar(8) DEFAULT NULL,
  `ville_longitude_dms` varchar(9) DEFAULT NULL,
  `ville_latitude_dms` varchar(8) DEFAULT NULL,
  `ville_zmin` mediumint DEFAULT NULL,
  `ville_zmax` mediumint DEFAULT NULL,
  PRIMARY KEY (`idVille`),
  UNIQUE KEY `ville_code_commune_2` (`ville_code_commune`),
  UNIQUE KEY `ville_slug` (`ville_slug`),
  KEY `ville_departement` (`ville_departement`),
  KEY `ville_nom` (`ville_nom`),
  KEY `ville_nom_reel` (`ville_nom_reel`),
  KEY `ville_code_commune` (`ville_code_commune`),
  KEY `ville_code_postal` (`ville_code_postal`),
  KEY `ville_longitude_latitude_deg` (`ville_longitude_deg`,`ville_latitude_deg`),
  KEY `ville_nom_simple` (`ville_nom_simple`)
) ENGINE=InnoDB AUTO_INCREMENT=38101 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ville`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `association_invitation`
--
ALTER TABLE `association_invitation`
  ADD CONSTRAINT `FK_association_TO_association_invitation` FOREIGN KEY (`idAssociation`) REFERENCES `association` (`idAssociation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_association_invitation_inviter` FOREIGN KEY (`idInviter`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_association_invitation_user` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `entreprise`
--
ALTER TABLE `entreprise`
  ADD CONSTRAINT `FK_user_TO_entreprise` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `FK_association_TO_evenement` FOREIGN KEY (`idAssociation`) REFERENCES `association` (`idAssociation`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_entreprise_TO_evenement` FOREIGN KEY (`idEntreprise`) REFERENCES `entreprise` (`idEntreprise`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_event_category_TO_evenement` FOREIGN KEY (`idEventCategory`) REFERENCES `event_category` (`idEventCategory`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_evenement_creator` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ville_TO_evenement` FOREIGN KEY (`idVille`) REFERENCES `ville` (`idVille`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `event_image`
--
ALTER TABLE `event_image`
  ADD CONSTRAINT `FK_evenement_TO_event_image` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_invitation`
--
ALTER TABLE `event_invitation`
  ADD CONSTRAINT `FK_evenement_TO_event_invitation` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_invitation_inviter` FOREIGN KEY (`idInviter`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_invitation_user` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_participant`
--
ALTER TABLE `event_participant`
  ADD CONSTRAINT `FK_evenement_TO_event_participant` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_participant` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_user_TO_notification` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `realisation`
--
ALTER TABLE `realisation`
  ADD CONSTRAINT `FK_entreprise_TO_realisation` FOREIGN KEY (`idEntreprise`) REFERENCES `entreprise` (`idEntreprise`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `realisation_image`
--
ALTER TABLE `realisation_image`
  ADD CONSTRAINT `FK_realisation_TO_realisation_image` FOREIGN KEY (`idRealisation`) REFERENCES `realisation` (`idRealisation`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_role_TO_user` FOREIGN KEY (`idRole`) REFERENCES `role` (`idRole`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `user_association`
--
ALTER TABLE `user_association`
  ADD CONSTRAINT `FK_association_TO_user_association` FOREIGN KEY (`idAssociation`) REFERENCES `association` (`idAssociation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_user_association` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
