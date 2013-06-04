-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 04, 2013 at 12:20 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lu_events`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE IF NOT EXISTS `alerts` (
  `alert_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient_id` int(11) NOT NULL,
  `type` enum('promote','demote') COLLATE utf8_latvian_ci NOT NULL,
  `message` varchar(300) COLLATE utf8_latvian_ci NOT NULL,
  PRIMARY KEY (`alert_id`),
  KEY `recipient_id` (`recipient_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`alert_id`, `recipient_id`, `type`, `message`) VALUES
(3, 30, 'promote', 'Tu esi paaugstināts par prasmīgu lietotāju, tagad tev ir tiesības pievienot jaunas birkas!'),
(4, 31, 'demote', 'Tu savā pievienotajā komentārā pārkāpi vietnes noteikumus, komentārs tika izdzēsts un tavs profils bloķēts!');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `event_id` varchar(50) COLLATE utf8_latvian_ci NOT NULL,
  `attribute` enum('w','l','d','p','f','t','dc','a') COLLATE utf8_latvian_ci NOT NULL,
  `message` varchar(300) COLLATE utf8_latvian_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `edited_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `author_id` (`author_id`),
  KEY `event_id` (`event_id`),
  KEY `author_id_2` (`author_id`,`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci AUTO_INCREMENT=49 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `author_id`, `event_id`, `attribute`, `message`, `created_at`, `edited_at`) VALUES
(40, 30, 'klasesvakars', 'd', 'Man neder, nav iespējams pārcelt uz aizaiznākam piektdienu ?', 1370764069, 1370764113),
(41, 29, 'klasesvakars', 'd', 'Labi', 1370764129, NULL),
(43, 32, 'dzimsanasdiena', 'w', 'Izskatās foršs pasākums, bet es laikam neesmu gaidīts :(', 1370767756, NULL),
(44, 29, 'klasesvakars', 'w', 'Nevaru sagaidīt', 1370768385, NULL),
(45, 29, 'gumijasleksana', 'w', 'Izklausās jau ļoti interesanti un jautri, es noteikti piedalos', 1370768447, NULL),
(46, 0, 'klasesvakars', 'w', 'wuhuu, noteikti būšu', 1370768540, NULL),
(47, 30, 'harrypottermarathon', 'w', 'ee, labs es piekrītu', 1370768918, NULL),
(48, 29, 'instrumentikoncerts', 'f', 'Pārāk dārgi, nebūšu', 1370769798, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` varchar(50) COLLATE utf8_latvian_ci NOT NULL,
  `type` enum('public','private') COLLATE utf8_latvian_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_latvian_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_latvian_ci NOT NULL,
  `location` varchar(300) COLLATE utf8_latvian_ci NOT NULL,
  `date` varchar(300) COLLATE utf8_latvian_ci NOT NULL,
  `participants_min` int(5) DEFAULT NULL,
  `participants_max` int(5) DEFAULT NULL,
  `entry_fee` int(4) DEFAULT NULL,
  `takeaway` varchar(300) COLLATE utf8_latvian_ci DEFAULT NULL,
  `dress_code` varchar(300) COLLATE utf8_latvian_ci DEFAULT NULL,
  `assistants` varchar(300) COLLATE utf8_latvian_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `type`, `title`, `description`, `location`, `date`, `participants_min`, `participants_max`, `entry_fee`, `takeaway`, `dress_code`, `assistants`, `created_at`) VALUES
('dzimsanasdiena', 'private', 'Mana dzimšanas diena', 'Man paliek 21, tāpēc es to gribu kārtīgi nosvinēt.', 'Pirts pārdaugavā, divos stāvos un ir arī baseins.', '14. jūnijs un sākums ap 18:00', NULL, NULL, NULL, 'Dāvana', NULL, NULL, 1370765906),
('gumijasleksana', 'public', 'Lekšana ar gumiju', 'Es gribu beidzot sadūšoties un aizbraukt izlekt ar gumiju. Nekas to neesmu darījis. Nav vēl kāds gribētājs, kas grib lekt ar gumiju pirmo reizi bailīgu cilvēku barā.', 'Es domāju, ka varētu lekt Siguldā no tā vagoniņa, bet par satikšanos un nokļūšanu tur varam saorganizēt visu.', 'kaut kad vasarā, būtībā vienalga kad, tikai ne darba dienā.', NULL, NULL, NULL, 'Dūša', NULL, NULL, 1370768032),
('harrypottermarathon', 'public', 'Harry Potter filmu maratons', 'Vēl kāds Harrija Pottera fans grib noskatīties visas filmas ? es gribu noorganizēt filmu maratonu, kurā mēs noskatītos visas filmas pēc kārtas', 'Manās mājās, būs lielais televizors', 'Šo vai aiznākam piektidien ?', 2, 7, NULL, 'Ēdiens', NULL, NULL, 1370768879),
('instrumentikoncerts', 'private', 'Grupas "instrumenti" koncerts', 'Grupa "Instrumenti" sniegs koncertu.', 'Folkbārs "Ala"', '24. jūlijs', NULL, NULL, 2, NULL, NULL, NULL, 1370769738),
('kailaisvelobrauciens', 'public', 'Kailais velobrauciens', 'Kailais brauciens no Juglas līdz Brīvības piemineklim.', 'sākums Juglā', '21. jūnijs', NULL, NULL, NULL, 'ritenis', 'pilnīgi nekas', NULL, 1370780245),
('klasesvakars', 'public', 'Klases vakars', 'Sen neesam tikušies, tāpēc izdomāju, ka varētu noorganizēt kaut kādu  kopā saiešanu.', 'Es domāju, ka tā varētu būt Vaidera pirts, tur bija labi, ērti un arī salīdzinoši lēti.', 'Nekas vēl nav skaidri noteikts, bet es domāju, ka varētu aiznākam piektdien vai sestdien, ko jūs sakat ?', NULL, NULL, 5, 'Groziņš', NULL, NULL, 1370763435),
('velobrauciens', 'public', 'Velo pārbrauciens', 'Gribu izveidot velo pārbraucienu no Ventspils līdz Rīgai', 'Sākuma Ventspilī, lielajā laukumā.', '23. jūlijs, pati braukšana varētu iet nedēļu.', NULL, NULL, NULL, 'Ritenis, ķivere, telts un ēdiens', 'ērti', NULL, 1370780112);

-- --------------------------------------------------------

--
-- Table structure for table `has_tag`
--

CREATE TABLE IF NOT EXISTS `has_tag` (
  `tag_id` int(11) NOT NULL,
  `event_id` varchar(50) COLLATE utf8_latvian_ci NOT NULL,
  PRIMARY KEY (`tag_id`,`event_id`),
  KEY `tag_id` (`tag_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci;

--
-- Dumping data for table `has_tag`
--

INSERT INTO `has_tag` (`tag_id`, `event_id`) VALUES
(17, 'klasesvakars'),
(18, 'gumijasleksana'),
(19, 'klasesvakars'),
(20, 'kailaisvelobrauciens'),
(20, 'velobrauciens'),
(21, 'dzimsanasdiena'),
(21, 'klasesvakars'),
(22, 'dzimsanasdiena'),
(22, 'instrumentikoncerts'),
(23, 'gumijasleksana'),
(23, 'kailaisvelobrauciens'),
(23, 'velobrauciens'),
(24, 'kailaisvelobrauciens'),
(25, 'harrypottermarathon'),
(26, 'harrypottermarathon'),
(26, 'instrumentikoncerts'),
(27, 'instrumentikoncerts');

-- --------------------------------------------------------

--
-- Table structure for table `invites`
--

CREATE TABLE IF NOT EXISTS `invites` (
  `invite_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `event_id` varchar(50) COLLATE utf8_latvian_ci NOT NULL,
  `email` varchar(254) COLLATE utf8_latvian_ci DEFAULT NULL,
  `message` varchar(500) COLLATE utf8_latvian_ci DEFAULT NULL,
  `access_key` char(32) COLLATE utf8_latvian_ci DEFAULT NULL,
  PRIMARY KEY (`invite_id`),
  KEY `sender_id` (`sender_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `invites`
--

INSERT INTO `invites` (`invite_id`, `sender_id`, `recipient_id`, `event_id`, `email`, `message`, `access_key`) VALUES
(11, 29, NULL, 'klasesvakars', 'karlis3@inbox.lv', 'Taisam klases salidojumu !!', '8743aeae13bf9054c15caa95411eb068'),
(12, 29, NULL, 'klasesvakars', 'krish77@inbox.lv', 'Taisam klases salidojumu ne ?', '6871aacd6efe98ae8f78d8c34eceb70b'),
(13, 29, 32, 'klasesvakars', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE IF NOT EXISTS `participants` (
  `participant_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(50) COLLATE utf8_latvian_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`participant_id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci AUTO_INCREMENT=43 ;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`participant_id`, `event_id`, `user_id`, `role`) VALUES
(35, 'klasesvakars', 29, 10),
(36, 'klasesvakars', 30, 1),
(37, 'dzimsanasdiena', 31, 10),
(38, 'gumijasleksana', 32, 10),
(39, 'harrypottermarathon', 0, 10),
(40, 'instrumentikoncerts', 35, 10),
(41, 'velobrauciens', 36, 10),
(42, 'kailaisvelobrauciens', 36, 10);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE IF NOT EXISTS `requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `event_id` varchar(50) COLLATE utf8_latvian_ci NOT NULL,
  PRIMARY KEY (`request_id`),
  KEY `sender_id` (`sender_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `sender_id`, `event_id`) VALUES
(2, 31, 'klasesvakars');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(40) COLLATE utf8_latvian_ci NOT NULL,
  `event_count` int(10) NOT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci AUTO_INCREMENT=28 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `author_id`, `title`, `event_count`) VALUES
(17, 29, 'klases vakars', 1),
(18, 29, 'pirmo reizi', 1),
(19, 29, 'salidojums', 1),
(20, 29, 'riteņbraukšana', 2),
(21, 29, 'pirts', 2),
(22, 30, 'ballīte', 2),
(23, 32, 'ekstrēmi', 3),
(24, 32, 'mazliet nelegāli', 2),
(25, 30, 'filmas', 1),
(26, 30, 'chill', 2),
(27, 29, 'koncerts', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_latvian_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_latvian_ci DEFAULT NULL,
  `surname` varchar(50) COLLATE utf8_latvian_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_latvian_ci NOT NULL,
  `email` varchar(254) COLLATE utf8_latvian_ci NOT NULL,
  `last_login` varchar(25) COLLATE utf8_latvian_ci NOT NULL,
  `login_hash` varchar(255) COLLATE utf8_latvian_ci NOT NULL,
  `profile_fields` text COLLATE utf8_latvian_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `group` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_latvian_ci AUTO_INCREMENT=37 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `id`, `username`, `name`, `surname`, `password`, `email`, `last_login`, `login_hash`, `profile_fields`, `created_at`, `group`) VALUES
(0, 0, 'dzēsts lietotājs', '---', '---', '---', '---', '1370766557', '---', '---', 1370766557, 0),
(29, 29, 'Krišjānis', 'Krišjānis', 'Šmits', 'sTCz3NALYQVcoxAvo+Lb60yaKGG5ICEpQ4w+M8PWU6w=', 'krisjanis.smits@gmail.com', '1370779917', '07cd2599ff0211db655ecc7652c3ff9b26fe5145', 'a:0:{}', 1370762868, 100),
(30, 30, 'Aivis', NULL, NULL, 'sTCz3NALYQVcoxAvo+Lb60yaKGG5ICEpQ4w+M8PWU6w=', 'aivis@gmail.com', '1370779945', 'bf7050579ca473f1570f3986071836ed17fa7f77', 'a:0:{}', 1370763752, 10),
(31, 31, 'Kārlis', NULL, NULL, 'sTCz3NALYQVcoxAvo+Lb60yaKGG5ICEpQ4w+M8PWU6w=', 'karlis@gmail.com', '1370764429', '205ba48acf7a6c5df74fb170961b9f71adf220df', 'a:0:{}', 1370764428, -1),
(32, 32, 'Rūdis', NULL, NULL, 'sTCz3NALYQVcoxAvo+Lb60yaKGG5ICEpQ4w+M8PWU6w=', 'rudis@gmail.com', '1370766557', 'ad13b7b7777206f8781b179d56042ab29dd7b151', 'a:0:{}', 1370766557, 100),
(35, 35, 'Instrumenti', NULL, NULL, 'sTCz3NALYQVcoxAvo+Lb60yaKGG5ICEpQ4w+M8PWU6w=', 'instrumenti@gmail.com', '1370769576', 'ec9677a75f11e2ceeaf8d79e623936108435aa81', 'a:0:{}', 1370769576, 1),
(36, 36, 'Līva', NULL, NULL, 'sTCz3NALYQVcoxAvo+Lb60yaKGG5ICEpQ4w+M8PWU6w=', 'liva@gmail.com', '1370779978', 'af097e48acbe75be954630d789b07db8afaf6554', 'a:0:{}', 1370779978, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `alerts_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `has_tag`
--
ALTER TABLE `has_tag`
  ADD CONSTRAINT `has_tag_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `has_tag_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `invites`
--
ALTER TABLE `invites`
  ADD CONSTRAINT `invites_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `invites_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `invites_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
