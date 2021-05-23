-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 23, 2021 alle 18:49
-- Versione del server: 10.4.14-MariaDB
-- Versione PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_auth`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `comment`
--

INSERT INTO `comment` (`comment_id`, `comment`, `member_id`, `photo_id`) VALUES
(10, 'Wow che bel PC', 8, 2);

--
-- Trigger `comment`
--
DELIMITER $$
CREATE TRIGGER `trigger_commenti` AFTER INSERT ON `comment` FOR EACH ROW BEGIN
  INSERT INTO log_commenti SET
  comment_id = NEW.comment_id,
  member_id = NEW.member_id,
  photo_id = NEW.photo_id,
  data_log = NOW();

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_carica_foto`
--

CREATE TABLE `log_carica_foto` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `data_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `log_carica_foto`
--

INSERT INTO `log_carica_foto` (`photo_id`, `member_id`, `data_log`) VALUES
(1, 1, '2021-05-22 15:40:01'),
(2, 8, '2021-05-22 17:27:06'),
(3, 8, '2021-05-22 17:28:57'),
(4, 8, '2021-05-22 17:52:09'),
(5, 2, '2021-05-22 22:55:30'),
(6, 9, '2021-05-22 23:17:04'),
(7, 8, '2021-05-23 17:44:56'),
(8, 8, '2021-05-23 17:51:55'),
(9, 8, '2021-05-23 17:58:34'),
(10, 8, '2021-05-23 17:58:52'),
(11, 8, '2021-05-23 17:59:08'),
(12, 9, '2021-05-23 18:25:06');

-- --------------------------------------------------------

--
-- Struttura della tabella `log_commenti`
--

CREATE TABLE `log_commenti` (
  `comment_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `photo_id` int(8) NOT NULL,
  `data_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `log_commenti`
--

INSERT INTO `log_commenti` (`comment_id`, `member_id`, `photo_id`, `data_log`) VALUES
(1, 1, 1, '2021-05-22 15:42:01'),
(2, 2, 4, '2021-05-22 19:45:30'),
(3, 8, 6, '2021-05-22 23:18:19'),
(4, 2, 6, '2021-05-22 23:18:46'),
(5, 9, 6, '2021-05-22 23:19:07'),
(6, 9, 6, '2021-05-22 23:19:13'),
(7, 9, 6, '2021-05-22 23:19:16'),
(8, 9, 6, '2021-05-22 23:19:18'),
(9, 9, 6, '2021-05-22 23:19:22'),
(10, 8, 2, '2021-05-22 23:38:12'),
(11, 9, 10, '2021-05-23 18:21:41');

-- --------------------------------------------------------

--
-- Struttura della tabella `log_elimina_foto`
--

CREATE TABLE `log_elimina_foto` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `data_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `log_elimina_foto`
--

INSERT INTO `log_elimina_foto` (`photo_id`, `member_id`, `data_log`) VALUES
(6, 9, '2021-05-22 23:19:31'),
(8, 8, '2021-05-23 17:55:42'),
(11, 8, '2021-05-23 18:01:12'),
(4, 8, '2021-05-23 18:22:24'),
(10, 8, '2021-05-23 18:24:09'),
(9, 8, '2021-05-23 18:24:11');

-- --------------------------------------------------------

--
-- Struttura della tabella `log_foto_update`
--

CREATE TABLE `log_foto_update` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `description` varchar(255) NOT NULL,
  `data_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `log_foto_update`
--

INSERT INTO `log_foto_update` (`photo_id`, `member_id`, `description`, `data_log`) VALUES
(2, 8, ' Ma che bella tastiera', '2021-05-23 17:54:16'),
(10, 8, ' Il più grande motovlogger della storia', '2021-05-23 18:01:26'),
(7, 8, ' Una macchina bianca', '2021-05-23 18:01:58');

-- --------------------------------------------------------

--
-- Struttura della tabella `log_messaggi`
--

CREATE TABLE `log_messaggi` (
  `message_id` int(8) NOT NULL,
  `sender_id` int(8) NOT NULL,
  `receiver_id` int(8) NOT NULL,
  `data_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `log_messaggi`
--

INSERT INTO `log_messaggi` (`message_id`, `sender_id`, `receiver_id`, `data_log`) VALUES
(1, 8, 2, '2021-05-22 23:12:10'),
(2, 2, 8, '2021-05-22 23:12:36'),
(3, 8, 2, '2021-05-22 23:13:06'),
(4, 9, 2, '2021-05-22 23:24:39'),
(5, 9, 2, '2021-05-22 23:25:06'),
(6, 9, 8, '2021-05-23 15:45:18'),
(7, 2, 8, '2021-05-23 16:23:07');

-- --------------------------------------------------------

--
-- Struttura della tabella `log_segnalazioni`
--

CREATE TABLE `log_segnalazioni` (
  `photo_id` int(8) NOT NULL,
  `member_id` int(8) NOT NULL,
  `segnalazione` tinyint(1) NOT NULL,
  `data_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `log_segnalazioni`
--

INSERT INTO `log_segnalazioni` (`photo_id`, `member_id`, `segnalazione`, `data_log`) VALUES
(2, 8, 1, '2021-05-22 17:27:17'),
(2, 8, 0, '2021-05-22 20:24:09'),
(5, 2, 1, '2021-05-22 23:24:20'),
(5, 2, 0, '2021-05-22 23:25:08'),
(4, 8, 1, '2021-05-23 16:25:16'),
(4, 8, 0, '2021-05-23 16:25:38'),
(10, 8, 1, '2021-05-23 18:10:16'),
(12, 9, 1, '2021-05-23 18:39:56');

-- --------------------------------------------------------

--
-- Struttura della tabella `log_vote`
--

CREATE TABLE `log_vote` (
  `member_id` int(8) NOT NULL,
  `photo_id` int(8) NOT NULL,
  `vote` int(11) NOT NULL,
  `data_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `log_vote`
--

INSERT INTO `log_vote` (`member_id`, `photo_id`, `vote`, `data_log`) VALUES
(2, 1, 4, '2021-05-22 15:45:54'),
(2, 4, 4, '2021-05-22 19:27:43'),
(2, 5, 3, '2021-05-22 23:04:04'),
(2, 10, 5, '2021-05-23 18:07:18'),
(8, 2, 5, '2021-05-22 23:37:49'),
(8, 3, 4, '2021-05-22 21:27:47'),
(8, 4, 1, '2021-05-22 22:20:37'),
(8, 5, 5, '2021-05-23 16:27:49'),
(9, 5, 5, '2021-05-23 16:27:02');

-- --------------------------------------------------------

--
-- Struttura della tabella `members`
--

CREATE TABLE `members` (
  `member_id` int(8) NOT NULL,
  `member_surname` varchar(255) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `member_password` varchar(64) NOT NULL,
  `member_email` varchar(255) NOT NULL,
  `member_token` varchar(255) NOT NULL,
  `member_profile_picture` varchar(255) NOT NULL,
  `member_verified` tinyint(1) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `is_locked` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `members`
--

INSERT INTO `members` (`member_id`, `member_surname`, `member_name`, `member_password`, `member_email`, `member_token`, `member_profile_picture`, `member_verified`, `is_admin`, `is_locked`) VALUES
(2, 'Khaled', 'Mustafa', '$2y$10$Q113Y0aOAQQxHKxSLhJH0eglNJAeoTOp4apjQaUuH5ku0riQkIoeC', 'george.patrut@studenti.fauser.edu', 'ab78b86bb3f76ee6', 'george.patrut@studenti.fauser.edu.png', 1, 0, 0),
(8, 'Patrut', 'George', '$2y$10$XTTJZf9umgdLhcRuHXa2HenYHH3/XE7aFUhOfP4uEJWtC10xtJM12', 'george.patrut@libero.it', '86ab2328fc1f6aba', 'george.patrut@libero.it.jpg', 1, 0, 0),
(9, 'Marco', 'Ferrari', '$2y$10$0GQAjDibTzcK45ftSm7wdu0qbqFWIGWUf.ZEcesLbOlBvnwg2qlKS', 'marcoferrari@gmail.com', 'fbd434e7c02d1d9a', 'marcoferrari@gmail.com.jpg', 1, 1, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `message`
--

CREATE TABLE `message` (
  `message_id` int(8) NOT NULL,
  `context` varchar(255) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `sender_id` int(8) NOT NULL,
  `receiver_id` int(8) NOT NULL,
  `recsen_id` varchar(255) GENERATED ALWAYS AS (case when `sender_id` < `receiver_id` then concat(cast(`sender_id` as char(255) charset utf8mb4),cast(`receiver_id` as char(255) charset utf8mb4)) else concat(cast(`receiver_id` as char(255) charset utf8mb4),cast(`sender_id` as char(255) charset utf8mb4)) end) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `message`
--

INSERT INTO `message` (`message_id`, `context`, `time`, `sender_id`, `receiver_id`) VALUES
(1, 'Ciao mi piace molto questo imam', '2021-05-22 23:12:10', 8, 2),
(2, 'Ti ringrazio, anche a me', '2021-05-22 23:12:36', 2, 8),
(3, 'che bello :)', '2021-05-22 23:13:06', 8, 2),
(6, 'Ciao sono marco ferrari', '2021-05-23 15:45:18', 9, 8),
(7, 'Non ci provare mai più, hai capito?', '2021-05-23 16:23:07', 2, 8);

--
-- Trigger `message`
--
DELIMITER $$
CREATE TRIGGER `trigger_messaggi` AFTER INSERT ON `message` FOR EACH ROW BEGIN
  INSERT INTO log_messaggi SET
  message_id = NEW.message_id,
  sender_id = NEW.sender_id,
  receiver_id = NEW.receiver_id,
  data_log = NOW();

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `photo`
--

CREATE TABLE `photo` (
  `photo_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `height` int(8) DEFAULT NULL,
  `width` int(8) DEFAULT NULL,
  `occupazione` varchar(255) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  `data_scatto` datetime DEFAULT NULL,
  `1_stella` int(11) NOT NULL,
  `2_stelle` int(11) NOT NULL,
  `3_stelle` int(11) NOT NULL,
  `4_stelle` int(11) NOT NULL,
  `5_stelle` int(11) NOT NULL,
  `segnalazione` tinyint(1) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `member_id` int(11) NOT NULL,
  `media` float GENERATED ALWAYS AS ((1 * `1_stella` + 2 * `2_stelle` + 3 * `3_stelle` + 4 * `4_stelle` + 5 * `5_stelle`) / (`1_stella` + `2_stelle` + `3_stelle` + `4_stelle` + `5_stelle`)) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `photo`
--

INSERT INTO `photo` (`photo_id`, `file_name`, `description`, `height`, `width`, `occupazione`, `lat`, `lng`, `data_scatto`, `1_stella`, `2_stelle`, `3_stelle`, `4_stelle`, `5_stelle`, `segnalazione`, `hidden`, `member_id`) VALUES
(2, 'lol.jpg', ' Ma che bella tastiera', 2016, 980, '146.65 KB', 0, 0, '2021-05-22 17:27:06', 0, 0, 0, 0, 1, 0, 0, 8),
(3, 'DSCN0010.jpg', ' vera foto', 480, 640, '157.92 KB', 43.4674, 11.8851, '2021-05-22 17:28:57', 0, 0, 0, 1, 0, 0, 0, 8),
(5, 'F180329MA03.jpg', ' imam', 1365, 2048, '674.42 KB', 0, 0, '2021-05-22 22:55:30', 0, 0, 0, 0, 3, 0, 0, 2),
(7, '1359207519_tmp_carofthemonth_flat.jpg', ' Una macchina bianca', 682, 1024, '159.96 KB', 0, 0, '2021-05-23 17:44:56', 0, 0, 0, 0, 0, 0, 0, 8),
(12, 'Screenshot (13).png', ' Animale grosso\r\n', 0, 0, '0', 0, 0, '2021-05-23 18:25:06', 0, 0, 0, 0, 0, 1, 0, 9);

--
-- Trigger `photo`
--
DELIMITER $$
CREATE TRIGGER `trigger_carica_foto` AFTER INSERT ON `photo` FOR EACH ROW BEGIN
  INSERT INTO log_carica_foto SET
  photo_id = NEW.photo_id,
  member_id = NEW.member_id,
  data_log = NOW();

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_elimina_foto` AFTER DELETE ON `photo` FOR EACH ROW BEGIN
  INSERT INTO log_elimina_foto SET
  photo_id = OLD.photo_id,
  member_id = OLD.member_id,
  data_log = NOW();

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_foto_update` AFTER UPDATE ON `photo` FOR EACH ROW BEGIN
    IF !(NEW.description <=> OLD.description) THEN
      INSERT INTO log_foto_update SET
      photo_id = NEW.photo_id,
      member_id = NEW.member_id,
      description = NEW.description,
      data_log = NOW();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_segnalazioni` AFTER UPDATE ON `photo` FOR EACH ROW BEGIN

    IF !(NEW.segnalazione <=> OLD.segnalazione) THEN
      INSERT INTO log_segnalazioni SET
      photo_id = NEW.photo_id,
      member_id = NEW.member_id,
      segnalazione = NEW.segnalazione,
      data_log = NOW();
   END IF;


END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_token_auth`
--

CREATE TABLE `tbl_token_auth` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `selector_hash` varchar(255) NOT NULL,
  `is_expired` int(11) NOT NULL DEFAULT 0,
  `expiry_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tbl_token_auth`
--

INSERT INTO `tbl_token_auth` (`id`, `username`, `password_hash`, `selector_hash`, `is_expired`, `expiry_date`) VALUES
(1, 'george.patrut@libero.it', '$2y$10$t161B7oJBV1cB5P9pNZZkOjV9FfQ.QhT.wc.hyuWulg76Wxa5yMHW', '$2y$10$mtJoLw4n6cm/Ur8BrXV9e.R30qL82WmQM4t9qYubuK9R/Tetm0HGW', 1, '2021-05-22 16:33:54'),
(2, 'george.patrut@studenti.fauser.edu', '$2y$10$eOMqO477eIWQorzLaNjBoOVJT2r/Fi3Rz/WePPCdu.sJwvxGMi0YS', '$2y$10$Coxpy1zTKkjMgOc4SzQ35e04hVLh8CKAtexubbIM7YHGK3MKwR9ky', 0, '2021-06-21 13:45:31'),
(3, 'george.patrut@libero.it', '$2y$10$/gYpuD3/FMzOWiejpL/SbeYwy1PeCiVd7JlCU02dHRsh0V63pd9f.', '$2y$10$Ssib8qKA15MFD8oCeYPf6.gDMi05.af7GKfSpCdSTKD/n4/DCBMFW', 1, '2021-05-22 21:02:46'),
(4, 'george.patrut@libero.it', '$2y$10$ZY5O3Bk29xEYQUiamgUnk.gEu9A4NbAHK9jtTkh1nX55wi/LG10g.', '$2y$10$XXtSjCXbv7tdW2HzaqKCte3yb5b6fqmyEgwqTAOcfhyQSlk6WEGPO', 0, '2021-06-21 21:02:46');

-- --------------------------------------------------------

--
-- Struttura della tabella `vote`
--

CREATE TABLE `vote` (
  `member_id` int(8) NOT NULL,
  `photo_id` int(8) NOT NULL,
  `vote` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `vote`
--

INSERT INTO `vote` (`member_id`, `photo_id`, `vote`) VALUES
(2, 5, 3),
(8, 2, 5),
(8, 3, 4),
(8, 5, 5),
(9, 5, 5);

--
-- Trigger `vote`
--
DELIMITER $$
CREATE TRIGGER `trigger_voti` AFTER INSERT ON `vote` FOR EACH ROW BEGIN
  INSERT INTO log_vote SET
  member_id = NEW.member_id,
  photo_id = NEW.photo_id,
  vote = NEW.vote,
  data_log = NOW();

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_voti_update` AFTER UPDATE ON `vote` FOR EACH ROW BEGIN
  UPDATE log_vote SET
  vote = NEW.vote,
  data_log = NOW()
  WHERE member_id = NEW.member_id AND photo_id = NEW.photo_id;

END
$$
DELIMITER ;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `photo_id` (`photo_id`);

--
-- Indici per le tabelle `log_vote`
--
ALTER TABLE `log_vote`
  ADD PRIMARY KEY (`member_id`,`photo_id`);

--
-- Indici per le tabelle `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `member_email` (`member_email`);

--
-- Indici per le tabelle `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indici per le tabelle `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indici per le tabelle `tbl_token_auth`
--
ALTER TABLE `tbl_token_auth`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `vote`
--
ALTER TABLE `vote`
  ADD PRIMARY KEY (`member_id`,`photo_id`),
  ADD KEY `photo_id` (`photo_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la tabella `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `photo`
--
ALTER TABLE `photo`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `tbl_token_auth`
--
ALTER TABLE `tbl_token_auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`photo_id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `photo_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_ibfk_2` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`photo_id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventi
--
CREATE DEFINER=`root`@`localhost` EVENT `locked` ON SCHEDULE AT '2021-05-23 23:24:39' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE `members` SET `is_locked` = 0 WHERE `members`.`member_id` = 2$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
