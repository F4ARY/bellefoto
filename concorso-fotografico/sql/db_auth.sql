CREATE TABLE `members` (
  `member_id` int(8) PRIMARY KEY AUTO_INCREMENT,
  `member_surname` varchar(255) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `member_password` varchar(64) NOT NULL,
  `member_email` varchar(255) NOT NULL,
  `member_token` varchar(255) NOT NULL,
  `member_profile_picture` varchar(255) NOT NULL,
  `member_verified` boolean NOT NULL,
  `is_admin` boolean NOT NULL
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

ALTER TABLE `photo` ADD FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

ALTER TABLE `comment` ADD FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `comment` ADD FOREIGN KEY (`photo_id`) REFERENCES `photo` (`photo_id`) ON DELETE CASCADE;

ALTER TABLE `message` ADD FOREIGN KEY (`sender_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `message` ADD FOREIGN KEY (`receiver_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `vote` ADD FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `vote` ADD FOREIGN KEY (`photo_id`) REFERENCES `photo` (`photo_id`) ON DELETE CASCADE;

ALTER TABLE `photo` ADD COLUMN media FLOAT AS ((1 * `1_stella` + 2 * `2_stelle` + 3 * `3_stelle` + 4 * `4_stelle` + 5 * `5_stelle`) / (`1_stella` + `2_stelle` + `3_stelle` + `4_stelle` + `5_stelle`));