-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 10, 2025 at 08:10 AM
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `idContact` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lastName` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('nouveau','lu','traite','archive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'nouveau',
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `repliedAt` datetime DEFAULT NULL,
  `uiid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idContact`),
  UNIQUE KEY `UQ_idContact` (`idContact`),
  UNIQUE KEY `uiid` (`uiid`),
  KEY `idx_contact_email` (`email`),
  KEY `idx_contact_status` (`status`),
  KEY `idx_contact_created` (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `hasRequestForActivation` tinyint NOT NULL DEFAULT '0',
  `isPartner` tinyint(1) NOT NULL DEFAULT '0',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `idUser` int NOT NULL,
  `idVille` mediumint UNSIGNED NOT NULL DEFAULT '14329',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  `partnerStartDate` datetime DEFAULT NULL,
  `partnerEndDate` datetime DEFAULT NULL,
  `requestDate` datetime DEFAULT NULL,
  `activationDate` datetime DEFAULT NULL,
  PRIMARY KEY (`idEntreprise`),
  UNIQUE KEY `UQ_idEntreprise` (`idEntreprise`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `uiid` (`uiid`),
  KEY `idx_entreprise_user` (`idUser`),
  KEY `idx_entreprise_active` (`isActive`),
  KEY `idx_entreprise_ville` (`idVille`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
CREATE TABLE IF NOT EXISTS `evenement` (
  `idEvenement` int NOT NULL AUTO_INCREMENT,
  `uiid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
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
  KEY `idx_evenement_public` (`isPublic`),
  KEY `idx_evenement_association` (`idAssociation`),
  KEY `idx_evenement_entreprise` (`idEntreprise`),
  KEY `idx_evenement_category` (`idEventCategory`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_category`
--

DROP TABLE IF EXISTS `event_category`;
CREATE TABLE IF NOT EXISTS `event_category` (
  `idEventCategory` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventCategory`),
  UNIQUE KEY `UQ_idEventCategory` (`idEventCategory`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_comment`
--

DROP TABLE IF EXISTS `event_comment`;
CREATE TABLE IF NOT EXISTS `event_comment` (
  `idEventComment` int NOT NULL AUTO_INCREMENT,
  `uiid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uiidEventComment` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `idEvenement` int NOT NULL,
  `idUser` int NOT NULL,
  `content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parentId` int DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`idEventComment`),
  UNIQUE KEY `uiid` (`uiid`),
  KEY `idx_event_comment_event` (`idEvenement`),
  KEY `idx_event_comment_user` (`idUser`),
  KEY `idx_event_comment_parent` (`parentId`)
) ENGINE=InnoDB AUTO_INCREMENT=218 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_comment_like`
--

DROP TABLE IF EXISTS `event_comment_like`;
CREATE TABLE IF NOT EXISTS `event_comment_like` (
  `idEventCommentLike` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idEventComment` int NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventCommentLike`),
  UNIQUE KEY `unique_event_comment_like` (`idUser`,`idEventComment`),
  KEY `idx_event_comment_like_user` (`idUser`),
  KEY `idx_event_comment_like_comment` (`idEventComment`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_comment_report`
--

DROP TABLE IF EXISTS `event_comment_report`;
CREATE TABLE IF NOT EXISTS `event_comment_report` (
  `idEventCommentReport` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idEventComment` int NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventCommentReport`),
  UNIQUE KEY `unique_event_comment_report` (`idUser`,`idEventComment`),
  KEY `idx_event_comment_report_user` (`idUser`),
  KEY `idx_event_comment_report_comment` (`idEventComment`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_favourite`
--

DROP TABLE IF EXISTS `event_favourite`;
CREATE TABLE IF NOT EXISTS `event_favourite` (
  `idEventFavourite` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idEvenement` int NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventFavourite`),
  UNIQUE KEY `unique_event_favourite` (`idUser`,`idEvenement`),
  KEY `idx_event_favourite_user` (`idUser`),
  KEY `idx_event_favourite_event` (`idEvenement`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `event_like`
--

DROP TABLE IF EXISTS `event_like`;
CREATE TABLE IF NOT EXISTS `event_like` (
  `idEventLike` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idEvenement` int NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEventLike`),
  UNIQUE KEY `unique_event_like` (`idUser`,`idEvenement`),
  KEY `idx_event_like_user` (`idUser`),
  KEY `idx_event_like_event` (`idEvenement`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_participant`
--

DROP TABLE IF EXISTS `event_participant`;
CREATE TABLE IF NOT EXISTS `event_participant` (
  `idEventParticipant` int NOT NULL AUTO_INCREMENT,
  `idEvenement` int NOT NULL,
  `idUser` int NOT NULL,
  `status` enum('inscrit','liste_attente','annule','refuse') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inscrit',
  `joinedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approvedAt` datetime DEFAULT NULL,
  `cancelledAt` datetime DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`idEventParticipant`),
  UNIQUE KEY `unique_event_user_participant` (`idEvenement`,`idUser`),
  KEY `idx_event_participant_evenement` (`idEvenement`),
  KEY `idx_event_participant_user` (`idUser`),
  KEY `idx_event_participant_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `idNotification` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idEvenement` int DEFAULT NULL,
  `type` enum('activation','inscription','preinscription','invitation','mise_a_jour','rappel','systeme','alert','message','contact','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'systeme',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `priority` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `readAt` datetime DEFAULT NULL,
  `deliveredAt` datetime DEFAULT NULL,
  `expiresAt` datetime DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotification`),
  KEY `idx_notification_user` (`idUser`),
  KEY `idx_notification_read` (`isRead`),
  KEY `idx_notification_created` (`createdAt`),
  KEY `idx_notification_user_read_created` (`idUser`,`isRead`,`createdAt`),
  KEY `idx_notification_type_created` (`type`,`createdAt`),
  KEY `idx_notification_priority_created` (`priority`,`createdAt`),
  KEY `FK_evenement_TO_notification` (`idEvenement`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `uiid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
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
  UNIQUE KEY `uiid` (`uiid`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `UQ_email` (`email`(191)),
  UNIQUE KEY `authCode` (`authCode`),
  UNIQUE KEY `authCode_2` (`authCode`),
  KEY `FK_role_TO_user` (`idRole`),
  KEY `idx_user_email` (`email`(250)),
  KEY `idx_user_online` (`isOnline`),
  KEY `idx_user_last_seen` (`lastSeen`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `FK_user_TO_chat_creator` FOREIGN KEY (`createdBy`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat_participant`
--
ALTER TABLE `chat_participant`
  ADD CONSTRAINT `FK_chat_TO_chat_participant` FOREIGN KEY (`idChat`) REFERENCES `chat` (`idChat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_chat_participant` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `FK_user_TO_contact` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Constraints for table `event_comment`
--
ALTER TABLE `event_comment`
  ADD CONSTRAINT `FK_evenement_TO_event_comment` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_event_comment_parent` FOREIGN KEY (`parentId`) REFERENCES `event_comment` (`idEventComment`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_comment` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_comment_like`
--
ALTER TABLE `event_comment_like`
  ADD CONSTRAINT `FK_event_comment_TO_event_comment_like` FOREIGN KEY (`idEventComment`) REFERENCES `event_comment` (`idEventComment`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_comment_like` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_comment_report`
--
ALTER TABLE `event_comment_report`
  ADD CONSTRAINT `FK_event_comment_TO_event_comment_report` FOREIGN KEY (`idEventComment`) REFERENCES `event_comment` (`idEventComment`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_comment_report` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_favourite`
--
ALTER TABLE `event_favourite`
  ADD CONSTRAINT `FK_evenement_TO_event_favourite` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_favourite` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `event_like`
--
ALTER TABLE `event_like`
  ADD CONSTRAINT `FK_evenement_TO_event_like` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_like` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_participant`
--
ALTER TABLE `event_participant`
  ADD CONSTRAINT `FK_evenement_TO_event_participant` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_event_participant` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_chat_TO_message` FOREIGN KEY (`idChat`) REFERENCES `chat` (`idChat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_message_TO_message_reply` FOREIGN KEY (`replyToMessageId`) REFERENCES `message` (`idMessage`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_message_sender` FOREIGN KEY (`idSender`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message_status`
--
ALTER TABLE `message_status`
  ADD CONSTRAINT `FK_message_TO_message_status` FOREIGN KEY (`idMessage`) REFERENCES `message` (`idMessage`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_TO_message_status` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_evenement_TO_notification` FOREIGN KEY (`idEvenement`) REFERENCES `evenement` (`idEvenement`) ON DELETE SET NULL ON UPDATE CASCADE,
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
