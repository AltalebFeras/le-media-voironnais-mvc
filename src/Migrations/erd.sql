CREATE TABLE group_members
(
  group_id  INT NOT NULL,
  person_id INT NOT NULL
);

CREATE TABLE `groups`
(
  group_id  INT          NOT NULL AUTO_INCREMENT,
  name      varchar(255) NOT NULL,
  tirage_id INT          NOT NULL,
  PRIMARY KEY (group_id)
);

ALTER TABLE `groups`
  ADD CONSTRAINT UQ_group_id UNIQUE (group_id);

CREATE TABLE lists
(
  list_id     INT          NOT NULL AUTO_INCREMENT,
  name        varchar(255) NOT NULL,
  description TEXT         NULL    ,
  created_at  DATETIME     NOT NULL,
  user_id     INT          NOT NULL,
  PRIMARY KEY (list_id)
);

ALTER TABLE lists
  ADD CONSTRAINT UQ_list_id UNIQUE (list_id);

CREATE TABLE persons
(
  person_id    INT          NOT NULL AUTO_INCREMENT,
  last_name    VARCHAR(50) NOT NULL,
  first_name   VARCHAR(50) NOT NULL,
  gender       ENUM('masculin', 'féminin', 'ne se prononce pas') NOT NULL,
  french_level TINYINT UNSIGNED NOT NULL CHECK (french_level BETWEEN 1 AND 4),
  was_dwwm     TINYINT(1) NOT NULL,
  tech_level   TINYINT UNSIGNED NOT NULL CHECK (tech_level BETWEEN 1 AND 4),
  profile      ENUM('timide', 'réservé', 'à l’aise') NOT NULL,
  age          TINYINT UNSIGNED NOT NULL CHECK (age BETWEEN 1 AND 99),
  created_at   DATETIME     NOT NULL,
  list_id      INT          NOT NULL,
  PRIMARY KEY (person_id)
);

ALTER TABLE persons
  ADD CONSTRAINT UQ_person_id UNIQUE (person_id);

CREATE TABLE tirage
(
  tirage_id  INT      NOT NULL AUTO_INCREMENT,
  created_at DATETIME NOT NULL,
  list_id    INT      NOT NULL,
  PRIMARY KEY (tirage_id)
);

ALTER TABLE tirage
  ADD CONSTRAINT UQ_tirage_id UNIQUE (tirage_id);

ALTER TABLE persons
  ADD CONSTRAINT FK_lists_TO_persons
    FOREIGN KEY (list_id)
    REFERENCES lists (list_id);

ALTER TABLE group_members
  ADD CONSTRAINT FK_groups_TO_group_members
    FOREIGN KEY (group_id)
    REFERENCES `groups` (group_id);

ALTER TABLE group_members
  ADD CONSTRAINT FK_persons_TO_group_members
    FOREIGN KEY (person_id)
    REFERENCES persons (person_id);

ALTER TABLE tirage
  ADD CONSTRAINT FK_lists_TO_tirage
    FOREIGN KEY (list_id)
    REFERENCES lists (list_id);

ALTER TABLE `groups`
  ADD CONSTRAINT FK_tirage_TO_groups
    FOREIGN KEY (tirage_id)
    REFERENCES tirage (tirage_id);
