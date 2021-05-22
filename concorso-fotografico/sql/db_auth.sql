CREATE TABLE `members` (
  `member_id` int(8) PRIMARY KEY AUTO_INCREMENT,
  `member_surname` varchar(255) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `member_password` varchar(64) NOT NULL,
  `member_email` varchar(255) NOT NULL,
  `member_token` varchar(255) NOT NULL,
  `member_profile_picture` varchar(255) NOT NULL,
  `member_verified` boolean NOT NULL,
  `is_admin` boolean NOT NULL,
  `is_locked` boolean NOT NULL

);

CREATE TABLE `tbl_token_auth` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `selector_hash` varchar(255) NOT NULL,
  `is_expired` int(11) NOT NULL DEFAULT '0',
  `expiry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `photo` (
  `photo_id` int PRIMARY KEY AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `height` int(8),
  `width` int(8),
  `occupazione` varchar(255),
  `lat` float,
  `lng` float,
  `data_scatto` datetime,
  `1_stella` int NOT NULL,
  `2_stelle` int NOT NULL,
  `3_stelle` int NOT NULL,
  `4_stelle` int NOT NULL,
  `5_stelle` int NOT NULL,
  `segnalazione` boolean NOT NULL,
  `hidden` boolean NOT NULL,
  `member_id` int NOT NULL
);

CREATE TABLE `comment` (
  `comment_id` int PRIMARY KEY AUTO_INCREMENT,
  `comment` varchar(255) NOT NULL,
  `member_id` int NOT NULL,
  `photo_id` int NOT NULL
);

CREATE TABLE `message` (
  `message_id` int(8) PRIMARY KEY AUTO_INCREMENT,
  `context` varchar(255) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sender_id` int(8) NOT NULL,
  `receiver_id` int(8) NOT NULL
);

CREATE TABLE `vote` (
  `member_id` int(8) NOT NULL,
  `photo_id` int(8) NOT NULL,
  `vote` int NOT NULL,
  PRIMARY KEY (`member_id`, `photo_id`)
);

CREATE TABLE `log_vote` (
  `member_id` int(8) NOT NULL,
  `photo_id` int(8) NOT NULL,
  `vote` int NOT NULL,
  `data_log` DATETIME NOT NULL ,
  PRIMARY KEY (`member_id`, `photo_id`)
);

CREATE TABLE `log_commenti` (
  `comment_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `photo_id` int(8) NOT NULL,
  `data_log` DATETIME NOT NULL
);

CREATE TABLE `log_carica_foto` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `data_log` DATETIME NOT NULL
);

CREATE TABLE `log_foto_update` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `description` varchar(255) NOT NULL,
  `data_log` DATETIME NOT NULL
);

CREATE TABLE `log_segnalazioni` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `segnalazione` boolean NOT NULL,
  `data_log` DATETIME NOT NULL
);

CREATE TABLE `log_elimina_foto` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `data_log` DATETIME NOT NULL
);

CREATE TABLE `log_messaggi` (
  `message_id` int(8) NOT NULL,
  `sender_id` int(8) NOT NULL,
  `receiver_id` int(8) NOT NULL,
  `data_log` DATETIME NOT NULL
);

ALTER TABLE `photo` ADD FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `comment` ADD FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `comment` ADD FOREIGN KEY (`photo_id`) REFERENCES `photo` (`photo_id`) ON DELETE CASCADE;

ALTER TABLE `message` ADD FOREIGN KEY (`sender_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `message` ADD FOREIGN KEY (`receiver_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `vote` ADD FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `vote` ADD FOREIGN KEY (`photo_id`) REFERENCES `photo` (`photo_id`) ON DELETE CASCADE;

ALTER TABLE `photo` ADD COLUMN media FLOAT AS ((1 * `1_stella` + 2 * `2_stelle` + 3 * `3_stelle` + 4 * `4_stelle` + 5 * `5_stelle`) / (`1_stella` + `2_stelle` + `3_stelle` + `4_stelle` + `5_stelle`));

ALTER TABLE `message` ADD COLUMN recsen_id VARCHAR (255) AS (CASE WHEN `sender_id` < `receiver_id` THEN (CONCAT(CAST(`sender_id` AS VARCHAR (255)), CAST(`receiver_id` AS VARCHAR (255)))) ELSE (CONCAT(CAST(`receiver_id` AS VARCHAR (255)), CAST(`sender_id` AS VARCHAR (255)))) END);

DELIMITER |

CREATE TRIGGER trigger_voti AFTER INSERT ON vote
  FOR EACH ROW
BEGIN
  INSERT INTO log_vote SET
  member_id = NEW.member_id,
  photo_id = NEW.photo_id,
  vote = NEW.vote,
  data_log = NOW();

END;

|

DELIMITER ;

DELIMITER |

CREATE TRIGGER trigger_voti_update AFTER UPDATE ON vote
  FOR EACH ROW
BEGIN
  UPDATE log_vote SET
  vote = NEW.vote,
  data_log = NOW()
  WHERE member_id = NEW.member_id AND photo_id = NEW.photo_id;

END;

|

DELIMITER ;

DELIMITER |

CREATE TRIGGER trigger_commenti AFTER INSERT ON comment
  FOR EACH ROW
BEGIN
  INSERT INTO log_commenti SET
  comment_id = NEW.comment_id,
  member_id = NEW.member_id,
  photo_id = NEW.photo_id,
  data_log = NOW();

END;

|

DELIMITER ;

DELIMITER |

CREATE TRIGGER trigger_carica_foto AFTER INSERT ON photo
  FOR EACH ROW
BEGIN
  INSERT INTO log_carica_foto SET
  photo_id = NEW.photo_id,
  member_id = NEW.member_id,
  data_log = NOW();

END;

|

DELIMITER ;

DELIMITER |

CREATE TRIGGER trigger_foto_update AFTER UPDATE ON photo
  FOR EACH ROW
BEGIN
    IF !(NEW.description <=> OLD.description) THEN
      INSERT INTO log_foto_update SET
      photo_id = NEW.photo_id,
      member_id = NEW.member_id,
      description = NEW.description,
      data_log = NOW();
    END IF;
END;

|

DELIMITER ;

DELIMITER |

CREATE TRIGGER trigger_segnalazioni AFTER UPDATE ON photo
  FOR EACH ROW
BEGIN

    IF !(NEW.segnalazione <=> OLD.segnalazione) THEN
      INSERT INTO log_segnalazioni SET
      photo_id = NEW.photo_id,
      member_id = NEW.member_id,
      segnalazione = NEW.segnalazione,
      data_log = NOW();
   END IF;


END;

|

DELIMITER ;

DELIMITER |

CREATE TRIGGER trigger_messaggi AFTER INSERT ON message
  FOR EACH ROW
BEGIN
  INSERT INTO log_messaggi SET
  message_id = NEW.message_id,
  sender_id = NEW.sender_id,
  receiver_id = NEW.receiver_id,
  data_log = NOW();

END;

|

DELIMITER ;

DELIMITER |

CREATE TRIGGER trigger_elimina_foto AFTER DELETE ON photo
  FOR EACH ROW
BEGIN
  INSERT INTO log_elimina_foto SET
  photo_id = OLD.photo_id,
  member_id = OLD.member_id,
  data_log = NOW();

END;

|

DELIMITER ;