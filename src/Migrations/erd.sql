-- Improved Database Schema for Event Management and Chat System
-- This schema addresses the logical issues and adds missing functionality

-- =============================================
-- CORE USER AND ROLE MANAGEMENT
-- =============================================

CREATE TABLE role (
  idRole INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(55) NOT NULL,
  description TEXT NULL,
  permissions JSON NULL, -- Store role permissions as JSON
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idRole),
  UNIQUE KEY UQ_idRole (idRole)
);

CREATE TABLE `user` (
  idUser INT NOT NULL AUTO_INCREMENT,
  idRole INT NOT NULL,
  firstName VARCHAR(55) NOT NULL,
  lastName VARCHAR(55) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NULL,
  password VARCHAR(255) NOT NULL,
  avatarPath VARCHAR(255) NULL,
  bio TEXT NULL,
  dateOfBirth DATE NULL,
  isActivated BOOLEAN NOT NULL DEFAULT FALSE,
  isOnline BOOLEAN NOT NULL DEFAULT FALSE,
  lastSeen DATETIME NULL,
  rgpdAcceptedDate DATETIME NOT NULL,
  token VARCHAR(255) NULL,
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt DATETIME NULL,
  PRIMARY KEY (idUser),
  UNIQUE KEY UQ_idUser (idUser),
  UNIQUE KEY UQ_email (email(191))
);

-- =============================================
-- ASSOCIATION MANAGEMENT
-- =============================================

CREATE TABLE association (
  idAssociation INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  logoPath VARCHAR(255) NULL,
  bannerPath VARCHAR(255) NULL,
  address VARCHAR(255) NULL,
  phone VARCHAR(20) NULL,
  email VARCHAR(255) NULL,
  website VARCHAR(255) NULL,
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  idUser INT NOT NULL, -- Foreign key to user who created the association
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt DATETIME NULL,
  PRIMARY KEY (idAssociation),
  UNIQUE KEY UQ_idAssociation (idAssociation)
);

-- User-Association membership with roles
CREATE TABLE user_association (
  idUserAssociation INT NOT NULL AUTO_INCREMENT,
  idUser INT NOT NULL,
  idAssociation INT NOT NULL,
  role ENUM('membre', 'admin', 'moderateur') NOT NULL DEFAULT 'membre',
  joinedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (idUserAssociation),
  UNIQUE KEY unique_user_association (idUser, idAssociation)
);

-- Association invitations
CREATE TABLE association_invitation (
  idAssociationInvitation INT NOT NULL AUTO_INCREMENT,
  idAssociation INT NOT NULL,
  idUser INT NOT NULL,
  idInviter INT NOT NULL,
  status ENUM('en_attente', 'accepte', 'refuse') NOT NULL DEFAULT 'en_attente',
  invitedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  respondedAt DATETIME NULL,
  PRIMARY KEY (idAssociationInvitation),
  UNIQUE KEY unique_association_user_invitation (idAssociation, idUser)
);

-- =============================================
-- ENTREPRISE MANAGEMENT
-- =============================================

CREATE TABLE entreprise (
  idEntreprise INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  logoPath VARCHAR(255) NULL,
  bannerPath VARCHAR(255) NULL,
  address VARCHAR(255) NULL,
  phone VARCHAR(20) NULL,
  email VARCHAR(255) NULL,
  website VARCHAR(255) NULL,
  siret VARCHAR(20) NULL,
  status ENUM('brouillon', 'actif', 'suspendu') NOT NULL DEFAULT 'actif',
  isActive BOOLEAN NOT NULL DEFAULT FALSE,
  idUser INT NOT NULL,
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt DATETIME NULL,
  PRIMARY KEY (idEntreprise),
  UNIQUE KEY UQ_idEntreprise (idEntreprise)
);

-- =============================================
-- EVENT MANAGEMENT
-- =============================================

CREATE TABLE event_category (
  idEventCategory INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  color VARCHAR(7) NULL, -- Hex color code
  icon VARCHAR(50) NULL,
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idEventCategory),
  UNIQUE KEY UQ_idEventCategory (idEventCategory)
);

CREATE TABLE ville (
  idVille INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  population INT NOT NULL,
  postalCode VARCHAR(20) NOT NULL,
  region VARCHAR(100) NULL,
  PRIMARY KEY (idVille),
  UNIQUE KEY UQ_idVille (idVille)
);

CREATE TABLE evenement (
  idEvenement INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  shortDescription VARCHAR(500) NULL,
  startDate DATETIME NOT NULL,
  endDate DATETIME NULL,
  registrationDeadline DATETIME NULL,
  maxParticipants INT NOT NULL,
  currentParticipants INT NOT NULL DEFAULT 0,
  address VARCHAR(255) NOT NULL,
  latitude DECIMAL(10, 8) NULL,
  longitude DECIMAL(11, 8) NULL,
  imagePath VARCHAR(255) NULL,
  bannerPath VARCHAR(255) NULL,
  status ENUM('brouillon', 'actif', 'annule', 'termine') NOT NULL DEFAULT 'brouillon',
  isPublic BOOLEAN NOT NULL DEFAULT TRUE,
  requiresApproval BOOLEAN NOT NULL DEFAULT FALSE,
  price DECIMAL(10, 2) NULL DEFAULT 0.00,
  currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt DATETIME NULL,
  idUser INT NOT NULL, -- Event creator
  idAssociation INT NULL, -- Event belongs to association
  idVille INT NOT NULL,
  idEventCategory INT NULL,
  PRIMARY KEY (idEvenement),
  UNIQUE KEY UQ_idEvenement (idEvenement)
);

-- Event participants with status
CREATE TABLE event_participant (
  idEventParticipant INT NOT NULL AUTO_INCREMENT,
  idEvenement INT NOT NULL,
  idUser INT NOT NULL,
  status ENUM('inscrit', 'approuve', 'liste_attente', 'annule') NOT NULL DEFAULT 'inscrit',
  joinedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  approvedAt DATETIME NULL,
  cancelledAt DATETIME NULL,
  notes TEXT NULL, -- Admin notes about participant
  PRIMARY KEY (idEventParticipant),
  UNIQUE KEY unique_event_user_participant (idEvenement, idUser)
);

-- Event invitations
CREATE TABLE event_invitation (
  idEventInvitation INT NOT NULL AUTO_INCREMENT,
  idEvenement INT NOT NULL,
  idUser INT NOT NULL,
  idInviter INT NOT NULL,
  status ENUM('en_attente', 'accepte', 'refuse') NOT NULL DEFAULT 'en_attente',
  invitedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  respondedAt DATETIME NULL,
  message TEXT NULL, -- Personal message with invitation
  PRIMARY KEY (idEventInvitation),
  UNIQUE KEY unique_event_user_invitation (idEvenement, idUser)
);

-- Event images (multiple images per event)
CREATE TABLE event_image (
  idEventImage INT NOT NULL AUTO_INCREMENT,
  idEvenement INT NOT NULL,
  imagePath VARCHAR(255) NOT NULL,
  altText VARCHAR(255) NULL,
  isMain BOOLEAN NOT NULL DEFAULT FALSE,
  sortOrder INT NOT NULL DEFAULT 0,
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idEventImage),
  UNIQUE KEY UQ_idEventImage (idEventImage)
);

-- =============================================
-- CHAT SYSTEM
-- =============================================

CREATE TABLE chat (
  idChat INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NULL,
  description TEXT NULL,
  isGroup BOOLEAN NOT NULL DEFAULT FALSE,
  isEventChat BOOLEAN NOT NULL DEFAULT FALSE, -- Chat related to specific event
  idEvenement INT NULL, -- Link to event if it's an event chat
  idAssociation INT NULL, -- Link to association if it's an association chat
  createdBy INT NOT NULL, -- User who created the chat
  lastMessageAt DATETIME NULL,
  lastMessageId INT NULL,
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt DATETIME NULL,
  PRIMARY KEY (idChat),
  UNIQUE KEY UQ_idChat (idChat)
);

-- Chat participants with roles
CREATE TABLE chat_participant (
  idChatParticipant INT NOT NULL AUTO_INCREMENT,
  idChat INT NOT NULL,
  idUser INT NOT NULL,
  role ENUM('membre', 'admin', 'moderateur') NOT NULL DEFAULT 'membre',
  joinedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  leftAt DATETIME NULL,
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (idChatParticipant),
  UNIQUE KEY unique_chat_user_participant (idChat, idUser)
);

CREATE TABLE message (
  idMessage INT NOT NULL AUTO_INCREMENT,
  idChat INT NOT NULL,
  idSender INT NOT NULL,
  content TEXT NOT NULL,
  messageType ENUM('texte', 'image', 'fichier', 'invitation_evenement', 'systeme') NOT NULL DEFAULT 'texte',
  filePath VARCHAR(255) NULL, -- For file/image messages
  isEdited BOOLEAN NOT NULL DEFAULT FALSE,
  editedAt DATETIME NULL,
  isDeleted BOOLEAN NOT NULL DEFAULT FALSE,
  deletedAt DATETIME NULL,
  replyToMessageId INT NULL, -- For reply functionality
  sentAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idMessage),
  UNIQUE KEY UQ_idMessage (idMessage)
);

-- Message read status for each user
CREATE TABLE message_status (
  idMessageStatus INT NOT NULL AUTO_INCREMENT,
  idMessage INT NOT NULL,
  idUser INT NOT NULL,
  isRead BOOLEAN NOT NULL DEFAULT FALSE,
  readAt DATETIME NULL,
  PRIMARY KEY (idMessageStatus),
  UNIQUE KEY unique_message_user_status (idMessage, idUser)
);

-- =============================================
-- NOTIFICATION SYSTEM
-- =============================================

CREATE TABLE notification (
  idNotification INT NOT NULL AUTO_INCREMENT,
  idUser INT NOT NULL,
  type ENUM('invitation_evenement', 'mise_a_jour_evenement', 'message', 'invitation_association', 'rappel_evenement', 'systeme') NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  isRead BOOLEAN NOT NULL DEFAULT FALSE,
  readAt DATETIME NULL,
  relatedId INT NULL, -- ID of related event, message, etc.
  relatedType VARCHAR(50) NULL, -- Type of related entity
  createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idNotification),
  UNIQUE KEY UQ_idNotification (idNotification)
);

-- =============================================
-- FOREIGN KEY CONSTRAINTS
-- =============================================

-- User constraints
ALTER TABLE `user`
  ADD CONSTRAINT FK_role_TO_user
    FOREIGN KEY (idRole) REFERENCES role (idRole);

-- Association constraints
ALTER TABLE association
  ADD CONSTRAINT FK_user_TO_association
    FOREIGN KEY (idUser) REFERENCES `user` (idUser);

-- Entreprise constraints
ALTER TABLE entreprise
  ADD CONSTRAINT FK_user_TO_entreprise
    FOREIGN KEY (idUser) REFERENCES `user` (idUser);

ALTER TABLE user_association
  ADD CONSTRAINT FK_user_TO_user_association
    FOREIGN KEY (idUser) REFERENCES `user` (idUser),
  ADD CONSTRAINT FK_association_TO_user_association
    FOREIGN KEY (idAssociation) REFERENCES association (idAssociation);

ALTER TABLE association_invitation
  ADD CONSTRAINT FK_association_TO_association_invitation
    FOREIGN KEY (idAssociation) REFERENCES association (idAssociation),
  ADD CONSTRAINT FK_user_TO_association_invitation_user
    FOREIGN KEY (idUser) REFERENCES `user` (idUser),
  ADD CONSTRAINT FK_user_TO_association_invitation_inviter
    FOREIGN KEY (idInviter) REFERENCES `user` (idUser);

-- Event constraints
ALTER TABLE evenement
  ADD CONSTRAINT FK_user_TO_evenement_creator
    FOREIGN KEY (idUser) REFERENCES `user` (idUser),
  ADD CONSTRAINT FK_association_TO_evenement
    FOREIGN KEY (idAssociation) REFERENCES association (idAssociation),
  ADD CONSTRAINT FK_ville_TO_evenement
    FOREIGN KEY (idVille) REFERENCES ville (idVille),
  ADD CONSTRAINT FK_event_category_TO_evenement
    FOREIGN KEY (idEventCategory) REFERENCES event_category (idEventCategory);

ALTER TABLE event_participant
  ADD CONSTRAINT FK_evenement_TO_event_participant
    FOREIGN KEY (idEvenement) REFERENCES evenement (idEvenement),
  ADD CONSTRAINT FK_user_TO_event_participant
    FOREIGN KEY (idUser) REFERENCES `user` (idUser);

ALTER TABLE event_invitation
  ADD CONSTRAINT FK_evenement_TO_event_invitation
    FOREIGN KEY (idEvenement) REFERENCES evenement (idEvenement),
  ADD CONSTRAINT FK_user_TO_event_invitation_user
    FOREIGN KEY (idUser) REFERENCES `user` (idUser),
  ADD CONSTRAINT FK_user_TO_event_invitation_inviter
    FOREIGN KEY (idInviter) REFERENCES `user` (idUser);

ALTER TABLE event_image
  ADD CONSTRAINT FK_evenement_TO_event_image
    FOREIGN KEY (idEvenement) REFERENCES evenement (idEvenement);

-- Chat constraints
ALTER TABLE chat
  ADD CONSTRAINT FK_user_TO_chat_creator
    FOREIGN KEY (createdBy) REFERENCES `user` (idUser),
  ADD CONSTRAINT FK_evenement_TO_chat
    FOREIGN KEY (idEvenement) REFERENCES evenement (idEvenement),
  ADD CONSTRAINT FK_association_TO_chat
    FOREIGN KEY (idAssociation) REFERENCES association (idAssociation);

ALTER TABLE chat_participant
  ADD CONSTRAINT FK_chat_TO_chat_participant
    FOREIGN KEY (idChat) REFERENCES chat (idChat),
  ADD CONSTRAINT FK_user_TO_chat_participant
    FOREIGN KEY (idUser) REFERENCES `user` (idUser);

ALTER TABLE message
  ADD CONSTRAINT FK_chat_TO_message
    FOREIGN KEY (idChat) REFERENCES chat (idChat),
  ADD CONSTRAINT FK_user_TO_message_sender
    FOREIGN KEY (idSender) REFERENCES `user` (idUser),
  ADD CONSTRAINT FK_message_TO_message_reply
    FOREIGN KEY (replyToMessageId) REFERENCES message (idMessage);

ALTER TABLE message_status
  ADD CONSTRAINT FK_message_TO_message_status
    FOREIGN KEY (idMessage) REFERENCES message (idMessage),
  ADD CONSTRAINT FK_user_TO_message_status
    FOREIGN KEY (idUser) REFERENCES `user` (idUser);

-- Notification constraints
ALTER TABLE notification
  ADD CONSTRAINT FK_user_TO_notification
    FOREIGN KEY (idUser) REFERENCES `user` (idUser);

-- =============================================
-- INDEXES FOR PERFORMANCE
-- =============================================

-- User indexes
CREATE INDEX idx_user_email ON `user` (email);
CREATE INDEX idx_user_online ON `user` (isOnline);
CREATE INDEX idx_user_last_seen ON `user` (lastSeen);

-- Association indexes
CREATE INDEX idx_association_user ON association (idUser);
CREATE INDEX idx_association_active ON association (isActive);

-- Entreprise indexes
CREATE INDEX idx_entreprise_user ON entreprise (idUser);
CREATE INDEX idx_entreprise_active ON entreprise (isActive);
CREATE INDEX idx_entreprise_status ON entreprise (status);

-- Event indexes
CREATE INDEX idx_evenement_date ON evenement (startDate);
CREATE INDEX idx_evenement_status ON evenement (status);
CREATE INDEX idx_evenement_public ON evenement (isPublic);
CREATE INDEX idx_evenement_association ON evenement (idAssociation);
CREATE INDEX idx_evenement_category ON evenement (idEventCategory);

-- Event Participant indexes
CREATE INDEX idx_event_participant_evenement ON event_participant (idEvenement);
CREATE INDEX idx_event_participant_user ON event_participant (idUser);
CREATE INDEX idx_event_participant_status ON event_participant (status);

-- Chat indexes
CREATE INDEX idx_chat_event ON chat (idEvenement);
CREATE INDEX idx_chat_association ON chat (idAssociation);
CREATE INDEX idx_chat_last_message ON chat (lastMessageAt);

-- Message indexes
CREATE INDEX idx_message_chat ON message (idChat);
CREATE INDEX idx_message_sender ON message (idSender);
CREATE INDEX idx_message_sent_at ON message (sentAt);

-- Notification indexes
CREATE INDEX idx_notification_user ON notification (idUser);
CREATE INDEX idx_notification_read ON notification (isRead);
CREATE INDEX idx_notification_created ON notification (createdAt);

-- =============================================
-- SAMPLE DATA
-- =============================================

-- Insert default roles
INSERT INTO role (name, description) VALUES 
('admin', 'System administrator with full access'),
('user', 'Regular user with basic permissions'),
('moderator', 'User with moderation permissions');

-- Insert sample event categories
INSERT INTO event_category (name, description, color) VALUES 
('Sport', 'Sporting events and activities', '#FF6B6B'),
('Culture', 'Cultural events and exhibitions', '#4ECDC4'),
('Social', 'Social gatherings and networking', '#45B7D1'),
('Education', 'Educational workshops and seminars', '#96CEB4'),
('Business', 'Business meetings and conferences', '#FFEAA7');

-- Insert sample cities
INSERT INTO ville (name, population, postalCode, region) VALUES 
('Paris', 2161000, '75001', 'Île-de-France'),
('Lyon', 515695, '69001', 'Auvergne-Rhône-Alpes'),
('Marseille', 868277, '13001', 'Provence-Alpes-Côte d''Azur'),
('Toulouse', 479553, '31000', 'Occitanie'),
('Nice', 342637, '06000', 'Provence-Alpes-Côte d''Azur');

-- Insert users
INSERT INTO `user` (idRole, firstName, lastName, email, phone, password, avatarPath, bio, dateOfBirth, isActivated, isOnline, lastSeen, rgpdAcceptedDate)
VALUES
(1, 'Alice', 'Durand', 'alice@example.com', '+33 6 12 34 56 78', '$2y$10$abcdefghijklmnopqrstuv', NULL, 'Admin of the platform', '1988-05-12', TRUE, FALSE, NULL, NOW()),
(2, 'Bruno', 'Martin', 'bruno@example.com', '+33 6 98 76 54 32', '$2y$10$abcdefghijklmnopqrstuv', NULL, 'Association manager', '1992-11-03', TRUE, TRUE, NOW(), NOW()),
(3, 'Camille', 'Lefevre', 'camille@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1995-02-21', TRUE, FALSE, NULL, NOW());

-- Insert associations
INSERT INTO association (name, description, logoPath, bannerPath, address, phone, email, website, isActive, idUser)
VALUES
('Culture Voiron', 'Association culturelle locale', '/images/logo/culture.png', '/images/banners/culture.jpg', '1 Rue de la Culture, Voiron', '+33 4 12 34 56 78', 'contact@culture-voiron.fr', 'https://culture-voiron.fr', TRUE, 2),
('Sports Voironnais', 'Promotion des activités sportives', '/images/logo/sports.png', '/images/banners/sports.jpg', '10 Avenue du Sport, Voiron', '+33 4 87 65 43 21', 'contact@sports-voironnais.fr', 'https://sports-voironnais.fr', TRUE, 2);

-- User-Association memberships
INSERT INTO user_association (idUser, idAssociation, role, joinedAt, isActive) VALUES
(2, 1, 'admin', NOW(), TRUE),
(3, 1, 'membre', NOW(), TRUE),
(2, 2, 'moderateur', NOW(), TRUE);

-- Association invitations
INSERT INTO association_invitation (idAssociation, idUser, idInviter, status, invitedAt)
VALUES
(1, 3, 2, 'accepte', NOW()),
(2, 3, 2, 'en_attente', NOW());

-- Insert entreprises
INSERT INTO entreprise (name, description, logoPath, bannerPath, address, phone, email, website, siret, status, isActive, idUser)
VALUES
('Tech Voiron SARL', 'Solutions logicielles locales', '/images/logo/techvoiron.png', '/images/banners/techvoiron.jpg', '25 Rue des Alpes, Voiron', '+33 4 11 22 33 44', 'contact@techvoiron.fr', 'https://techvoiron.fr', '12345678901234', 'actif', TRUE, 1),
('Boulangerie Dupont', 'Artisan boulanger-pâtissier', '/images/logo/dupont.png', '/images/banners/dupont.jpg', '7 Place du Marché, Voiron', '+33 4 55 66 77 88', 'bonjour@boulangerie-dupont.fr', NULL, '98765432109876', 'brouillon', FALSE, 2);

-- Insert events
INSERT INTO evenement (title, description, shortDescription, startDate, endDate, registrationDeadline, maxParticipants, currentParticipants, address, latitude, longitude, imagePath, bannerPath, status, isPublic, requiresApproval, price, currency, idUser, idAssociation, idVille, idEventCategory)
VALUES
('Concert d\'été', 'Grand concert en plein air', 'Concert plein air', DATE_ADD(NOW(), INTERVAL 30 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 500, 120, 'Parc Central, Voiron', 45.36420000, 5.59230000, '/images/events/concert.jpg', '/images/banners/concert.jpg', 'actif', TRUE, FALSE, 10.00, 'EUR', 2, 1, 1, 2),
('Atelier Peinture', 'Atelier pour débutants', 'Atelier de peinture', DATE_ADD(NOW(), INTERVAL 10 DAY), NULL, DATE_ADD(NOW(), INTERVAL 7 DAY), 20, 8, 'Maison des Arts, Voiron', 45.36500000, 5.59300000, '/images/events/atelier.jpg', NULL, 'brouillon', TRUE, FALSE, 0.00, 'EUR', 2, 1, 1, 2);

-- Event participants
INSERT INTO event_participant (idEvenement, idUser, status, joinedAt)
VALUES
(1, 2, 'inscrit', NOW()),
(1, 3, 'approuve', NOW()),
(2, 3, 'liste_attente', NOW());

-- Event invitations
INSERT INTO event_invitation (idEvenement, idUser, idInviter, status, invitedAt)
VALUES
(1, 3, 2, 'accepte', NOW()),
(2, 3, 2, 'en_attente', NOW());

-- Event images
INSERT INTO event_image (idEvenement, imagePath, altText, isMain, sortOrder)
VALUES
(1, '/images/events/concert_main.jpg', 'Concert - visuel principal', TRUE, 1),
(1, '/images/events/concert_2.jpg', 'Concert - vue de la scène', FALSE, 2);

-- Chats
INSERT INTO chat (title, description, isGroup, isEventChat, idEvenement, idAssociation, createdBy, lastMessageAt, createdAt)
VALUES
('Discussion Générale', 'Espace de discussion pour la communauté', TRUE, FALSE, NULL, 1, 2, NOW(), NOW()),
('Chat Concert', 'Discussion autour du concert', TRUE, TRUE, 1, 1, 2, NOW(), NOW());

-- Chat participants
INSERT INTO chat_participant (idChat, idUser, role, joinedAt, isActive)
VALUES
(1, 2, 'admin', NOW(), TRUE),
(1, 3, 'membre', NOW(), TRUE),
(2, 2, 'moderateur', NOW(), TRUE),
(2, 3, 'membre', NOW(), TRUE);

-- Messages
INSERT INTO message (idChat, idSender, content, messageType, sentAt)
VALUES
(1, 2, 'Bienvenue à tous !', 'texte', NOW()),
(2, 3, 'Qui vient au concert ?', 'texte', NOW());

-- Message read status
INSERT INTO message_status (idMessage, idUser, isRead, readAt)
VALUES
(1, 3, TRUE, NOW()),
(2, 2, TRUE, NOW());

-- Notifications
INSERT INTO notification (idUser, type, title, message, isRead, createdAt)
VALUES
(2, 'invitation_evenement', 'Invitation au concert', 'Vous êtes invité(e) au Concert d\'été', FALSE, NOW()),
(3, 'mise_a_jour_evenement', 'Mise à jour de l\'événement', 'L\'événement a été mis à jour', FALSE, NOW());

-- ======================================================
-- ADDITIONAL BULK FAKE DATA (deterministic explicit IDs)
-- ======================================================

-- More users (IDs 4..20)
INSERT INTO `user` (idUser, idRole, firstName, lastName, email, phone, password, avatarPath, bio, dateOfBirth, isActivated, isOnline, lastSeen, rgpdAcceptedDate, createdAt)
VALUES
(4, 2, 'David', 'Bernard', 'david.bernard@example.com', '+33 6 01 02 03 04', '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1985-03-14', TRUE, FALSE, NULL, NOW(), NOW()),
(5, 2, 'Eva', 'Rossi', 'eva.rossi@example.com', '+33 6 05 06 07 08', '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1990-07-09', TRUE, TRUE, NOW(), NOW(), NOW()),
(6, 3, 'Farid', 'Lambert', 'farid.lambert@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1993-12-01', TRUE, FALSE, NULL, NOW(), NOW()),
(7, 2, 'Gilles', 'Moreau', 'gilles.moreau@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, 'Sport volunteer', '1982-09-30', TRUE, FALSE, NULL, NOW(), NOW()),
(8, 2, 'Hana', 'Petit', 'hana.petit@example.com', '+33 6 11 22 33 44', '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1998-05-25', TRUE, FALSE, NULL, NOW(), NOW()),
(9, 2, 'Ibrahim', 'Garcia', 'ibrahim.garcia@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1987-01-20', TRUE, FALSE, NULL, NOW(), NOW()),
(10, 2, 'Jade', 'Fournier', 'jade.fournier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1999-04-04', TRUE, TRUE, NOW(), NOW(), NOW()),
(11, 2, 'Karim', 'Lopez', 'karim.lopez@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1991-02-17', TRUE, FALSE, NULL, NOW(), NOW()),
(12, 2, 'Laura', 'Garnier', 'laura.garnier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1994-08-11', TRUE, FALSE, NULL, NOW(), NOW()),
(13, 2, 'Mika', 'Chevalier', 'mika.chevalier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1986-10-10', TRUE, FALSE, NULL, NOW(), NOW()),
(14, 2, 'Nora', 'Robert', 'nora.robert@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1997-06-06', TRUE, FALSE, NULL, NOW(), NOW()),
(15, 2, 'Olivier', 'Marchand', 'olivier.marchand@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1984-03-03', TRUE, FALSE, NULL, NOW(), NOW()),
(16, 2, 'Paula', 'Guyot', 'paula.guyot@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1996-01-29', TRUE, FALSE, NULL, NOW(), NOW()),
(17, 2, 'Quentin', 'Colin', 'quentin.colin@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1993-09-19', TRUE, FALSE, NULL, NOW(), NOW()),
(18, 2, 'Rania', 'Da Silva', 'rania.silva@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1990-12-24', TRUE, FALSE, NULL, NOW(), NOW()),
(19, 2, 'Sophie', 'Charpentier', 'sophie.charpentier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1989-07-07', TRUE, FALSE, NULL, NOW(), NOW()),
(20, 2, 'Thomas', 'Barbier', 'thomas.barbier@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, NULL, '1992-02-02', TRUE, FALSE, NULL, NOW(), NOW());

-- More associations (IDs 3..5)
INSERT INTO association (idAssociation, name, description, logoPath, bannerPath, address, phone, email, website, isActive, idUser, createdAt)
VALUES
(3, 'Jeux & Esports Voiron', 'Gaming et esports locaux', '/images/logo/esports.png', '/images/banners/esports.jpg', '3 Rue des Jeux, Voiron', NULL, 'contact@jeux-voiron.fr', NULL, TRUE, 4, NOW()),
(4, 'Photographie Voiron', 'Club de photo', '/images/logo/photo.png', '/images/banners/photo.jpg', '8 Rue des Artistes, Voiron', NULL, 'hello@photo-voiron.fr', NULL, TRUE, 5, NOW()),
(5, 'Théâtre Amateur', 'Troupe de théâtre amateur', '/images/logo/theatre.png', '/images/banners/theatre.jpg', '12 Rue du Théâtre, Voiron', NULL, 'contact@theatre-amateur.fr', NULL, TRUE, 6, NOW());

-- More user-association memberships
INSERT INTO user_association (idUser, idAssociation, role, joinedAt, isActive) VALUES
(4, 3, 'admin', NOW(), TRUE),
(5, 3, 'membre', NOW(), TRUE),
(6, 4, 'moderateur', NOW(), TRUE),
(7, 4, 'membre', NOW(), TRUE),
(8, 5, 'membre', NOW(), TRUE);

-- More association invitations
INSERT INTO association_invitation (idAssociation, idUser, idInviter, status, invitedAt)
VALUES
(3, 5, 4, 'accepte', NOW()),
(4, 7, 6, 'refuse', NOW()),
(5, 8, 6, 'en_attente', NOW());

-- More entreprises (IDs 3..10)
INSERT INTO entreprise (idEntreprise, name, description, logoPath, bannerPath, address, phone, email, website, siret, status, isActive, idUser, createdAt)
VALUES
(3, 'Menuiserie Martin', 'Menuiserie et agencement', '/images/logo/menuiserie.png', NULL, '15 Rue du Bois, Voiron', NULL, 'contact@menuiserie-martin.fr', NULL, '11223344556677', 'actif', TRUE, 7, NOW()),
(4, 'AutoVoiron', 'Garage automobile', '/images/logo/auto.png', '/images/banners/auto.jpg', '2 Avenue du Garage, Voiron', NULL, 'contact@autovoiron.fr', 'https://autovoiron.fr', '22334455667788', 'actif', TRUE, 8, NOW()),
(5, 'Librairie des Alpes', 'Librairie indépendante', '/images/logo/librairie.png', '/images/banners/librairie.jpg', '30 Rue des Livres, Voiron', NULL, 'bonjour@librairie-alpes.fr', NULL, '33445566778899', 'brouillon', FALSE, 9, NOW()),
(6, 'Fleuriste Rose', 'Fleuriste et déco', '/images/logo/fleuriste.png', NULL, '9 Rue des Fleurs, Voiron', NULL, 'contact@fleuristerose.fr', NULL, '44556677889900', 'actif', TRUE, 10, NOW()),
(7, 'Pizzeria Napoli', 'Restaurant italien', '/images/logo/pizzeria.png', '/images/banners/pizzeria.jpg', '21 Rue de Rome, Voiron', NULL, 'ciao@pizzeria-napoli.fr', 'https://pizzeria-napoli.fr', '55667788990011', 'suspendu', FALSE, 11, NOW()),
(8, 'Studio Yoga', 'Cours de yoga', '/images/logo/yoga.png', '/images/banners/yoga.jpg', '5 Rue Zen, Voiron', NULL, 'namaste@yogastudio.fr', NULL, '66778899001122', 'actif', TRUE, 12, NOW()),
(9, 'Coiffure Élégance', 'Salon de coiffure', '/images/logo/coiffure.png', NULL, '17 Rue de la Beauté, Voiron', NULL, 'rdv@coiffure-elegance.fr', NULL, '77889900112233', 'actif', TRUE, 13, NOW()),
(10, 'Tech Conseil', 'Conseil IT', '/images/logo/techconseil.png', '/images/banners/techconseil.jpg', '50 Rue du Numérique, Voiron', NULL, 'hello@techconseil.fr', 'https://techconseil.fr', '88990011223344', 'brouillon', FALSE, 14, NOW());

-- More events (IDs 3..10)
INSERT INTO evenement (idEvenement, title, description, shortDescription, startDate, endDate, registrationDeadline, maxParticipants, currentParticipants, address, latitude, longitude, imagePath, bannerPath, status, isPublic, requiresApproval, price, currency, idUser, idAssociation, idVille, idEventCategory, createdAt)
VALUES
(3, 'Tournoi Esports', 'Tournoi local', 'Tournoi jeux vidéo', DATE_ADD(NOW(), INTERVAL 15 DAY), NULL, DATE_ADD(NOW(), INTERVAL 10 DAY), 64, 32, 'Salle Polyvalente, Voiron', 45.3645, 5.5925, '/images/events/esports.jpg', NULL, 'actif', TRUE, FALSE, 5.00, 'EUR', 4, 3, 2, 3, NOW()),
(4, 'Expo Photo', 'Exposition de photographie', 'Expo photo', DATE_ADD(NOW(), INTERVAL 20 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), DATE_ADD(NOW(), INTERVAL 18 DAY), 200, 50, 'Galerie Municipale, Voiron', 45.3646, 5.5926, '/images/events/photo.jpg', '/images/banners/photo_event.jpg', 'brouillon', TRUE, FALSE, 0.00, 'EUR', 5, 4, 1, 2, NOW()),
(5, 'Match de Foot', 'Match amical', 'Foot amical', DATE_ADD(NOW(), INTERVAL 5 DAY), NULL, DATE_ADD(NOW(), INTERVAL 3 DAY), 22, 18, 'Stade Municipal, Voiron', 45.3647, 5.5927, '/images/events/foot.jpg', NULL, 'actif', TRUE, FALSE, 0.00, 'EUR', 7, 2, 2, 1, NOW()),
(6, 'Atelier Théâtre', 'Atelier d\'impro', 'Impro débutants', DATE_ADD(NOW(), INTERVAL 12 DAY), NULL, DATE_ADD(NOW(), INTERVAL 9 DAY), 25, 10, 'Salle Théâtre, Voiron', 45.3648, 5.5928, '/images/events/theatre.jpg', NULL, 'brouillon', TRUE, FALSE, 0.00, 'EUR', 6, 5, 3, 4, NOW()),
(7, 'Conférence Business', 'Conférence PME', 'PME et croissance', DATE_ADD(NOW(), INTERVAL 35 DAY), DATE_ADD(NOW(), INTERVAL 35 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 150, 60, 'Centre d\'Affaires, Voiron', 45.3649, 5.5929, '/images/events/business.jpg', '/images/banners/business.jpg', 'actif', TRUE, TRUE, 20.00, 'EUR', 1, NULL, 1, 5, NOW()),
(8, 'Cours de Yoga', 'Séance découverte', 'Yoga découverte', DATE_ADD(NOW(), INTERVAL 3 DAY), NULL, DATE_ADD(NOW(), INTERVAL 2 DAY), 30, 12, 'Parc Zen, Voiron', 45.3650, 5.5930, '/images/events/yoga.jpg', NULL, 'actif', TRUE, FALSE, 0.00, 'EUR', 12, NULL, 1, 4, NOW()),
(9, 'Marché du Livre', 'Vente de livres', 'Livres d\'occasion', DATE_ADD(NOW(), INTERVAL 8 DAY), NULL, DATE_ADD(NOW(), INTERVAL 6 DAY), 100, 40, 'Place du Marché, Voiron', 45.3651, 5.5931, '/images/events/livres.jpg', NULL, 'actif', TRUE, FALSE, 0.00, 'EUR', 9, NULL, 5, 2, NOW()),
(10, 'Bal Populaire', 'Bal en soirée', 'Bal d\'été', DATE_ADD(NOW(), INTERVAL 45 DAY), DATE_ADD(NOW(), INTERVAL 45 DAY), DATE_ADD(NOW(), INTERVAL 40 DAY), 400, 120, 'Place Centrale, Voiron', 45.3652, 5.5932, '/images/events/bal.jpg', '/images/banners/bal.jpg', 'brouillon', TRUE, FALSE, 0.00, 'EUR', 2, 1, 4, 3, NOW());

-- More event participants
INSERT INTO event_participant (idEvenement, idUser, status, joinedAt) VALUES
(3, 4, 'inscrit', NOW()),
(3, 5, 'approuve', NOW()),
(4, 5, 'inscrit', NOW()),
(5, 7, 'approuve', NOW()),
(5, 10, 'inscrit', NOW()),
(6, 6, 'liste_attente', NOW()),
(7, 1, 'approuve', NOW()),
(8, 12, 'inscrit', NOW()),
(9, 9, 'inscrit', NOW()),
(10, 2, 'inscrit', NOW());

-- More event invitations
INSERT INTO event_invitation (idEvenement, idUser, idInviter, status, invitedAt) VALUES
(3, 5, 4, 'en_attente', NOW()),
(4, 7, 5, 'accepte', NOW()),
(5, 10, 7, 'refuse', NOW());

-- More event images
INSERT INTO event_image (idEvenement, imagePath, altText, isMain, sortOrder) VALUES
(3, '/images/events/esports_main.jpg', 'Tournoi - principal', TRUE, 1),
(3, '/images/events/esports_2.jpg', 'Tournoi - salle', FALSE, 2),
(4, '/images/events/photo_main.jpg', 'Expo - principal', TRUE, 1);

-- More chats (IDs 3..6)
INSERT INTO chat (idChat, title, description, isGroup, isEventChat, idEvenement, idAssociation, createdBy, lastMessageAt, createdAt)
VALUES
(3, 'Chat Esports', 'Discussion tournoi', TRUE, TRUE, 3, 3, 4, NOW(), NOW()),
(4, 'Photo Club', 'Discussions photo', TRUE, FALSE, NULL, 4, 5, NOW(), NOW()),
(5, 'Yoga Group', 'Infos yoga', TRUE, TRUE, 8, NULL, 12, NOW(), NOW()),
(6, 'Marché Livres', 'Organisation', TRUE, TRUE, 9, NULL, 9, NOW(), NOW());

-- More chat participants
INSERT INTO chat_participant (idChat, idUser, role, joinedAt, isActive) VALUES
(3, 4, 'admin', NOW(), TRUE),
(3, 5, 'membre', NOW(), TRUE),
(4, 5, 'admin', NOW(), TRUE),
(4, 7, 'membre', NOW(), TRUE),
(5, 12, 'admin', NOW(), TRUE),
(6, 9, 'admin', NOW(), TRUE);

-- More messages
INSERT INTO message (idChat, idSender, content, messageType, sentAt) VALUES
(3, 4, 'Rendez-vous à 14h pour le tournoi.', 'texte', NOW()),
(4, 5, 'Nouvelle exposition la semaine prochaine.', 'texte', NOW()),
(5, 12, 'Cours en plein air si beau temps.', 'texte', NOW());

-- More notifications
INSERT INTO notification (idUser, type, title, message, isRead, createdAt) VALUES
(5, 'message', 'Nouveau message', 'Vous avez reçu un message.', FALSE, NOW()),
(12, 'rappel_evenement', 'Rappel', 'Votre cours de yoga est demain.', FALSE, NOW());
