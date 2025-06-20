-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 20 juin 2025 à 21:51
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet_parking`
--

-- --------------------------------------------------------

--
-- Structure de la table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `spot_id` int DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_cancelled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `spot_id`, `start_time`, `end_time`, `total_price`, `is_cancelled`) VALUES
(2, 6, 1, '2025-06-21 17:01:00', '2025-06-21 18:01:00', 0.00, 0),
(3, 6, 6, '2025-06-20 17:01:00', '2025-06-22 17:01:00', 0.00, 0),
(5, 6, 5, '2025-06-20 17:10:00', '2025-06-21 17:09:00', 0.00, 0),
(6, 6, 1, '2025-06-20 17:15:00', '2025-06-20 17:18:00', 0.00, 1),
(7, 3, 1, '2025-06-23 20:20:00', '2025-06-23 21:21:00', 2.03, 0),
(8, 14, 6, '2025-06-23 20:26:00', '2025-06-24 19:26:00', 51.00, 0),
(9, 3, 7, '2025-06-23 20:50:00', '2025-06-23 22:50:00', 4.00, 0),
(10, 3, 1, '2025-06-21 20:51:00', '2025-06-21 22:51:00', 10.00, 0),
(11, 3, 12, '2025-06-30 20:56:00', '2025-06-30 23:00:00', 4.13, 0);

-- --------------------------------------------------------

--
-- Structure de la table `parking_spots`
--

DROP TABLE IF EXISTS `parking_spots`;
CREATE TABLE IF NOT EXISTS `parking_spots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `is_occupied` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `parking_spots`
--

INSERT INTO `parking_spots` (`id`, `number`, `type`, `is_occupied`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 1),
(3, 3, 2, 0),
(4, 4, 2, 0),
(5, 5, 3, 0),
(6, 6, 1, 0),
(7, 7, 1, 0),
(8, 8, 1, 0),
(9, 9, 1, 0),
(12, 12, 1, 0),
(13, 10, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `pricing`
--

DROP TABLE IF EXISTS `pricing`;
CREATE TABLE IF NOT EXISTS `pricing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `spot_type` int DEFAULT NULL,
  `start_hour` time DEFAULT NULL,
  `end_hour` time DEFAULT NULL,
  `days` set('mon','tue','wed','thu','fri','sat','sun') DEFAULT NULL,
  `price_per_hour` decimal(6,2) DEFAULT NULL,
  `min_duration_minutes` int DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `pricing`
--

INSERT INTO `pricing` (`id`, `label`, `spot_type`, `start_hour`, `end_hour`, `days`, `price_per_hour`, `min_duration_minutes`, `active`) VALUES
(1, 'Semaine Journée', 1, '08:00:00', '18:00:00', 'mon,tue,wed,thu,fri', 3.00, 0, 1),
(3, 'Semaine Soirée', 1, '18:00:00', '23:00:00', 'mon,tue,wed,thu,fri', 2.00, 0, 1),
(4, 'Semaine Nuit', 1, '00:00:00', '07:59:00', 'mon,tue,wed,thu,fri', 1.00, 0, 1),
(5, 'Semaine Nuit', 2, '00:00:00', '07:59:00', 'mon,tue,wed,thu,fri', 0.50, 0, 1),
(6, 'Semaine Journée', 2, '08:00:00', '17:59:00', 'mon,tue,wed,thu,fri', 2.00, 0, 1),
(7, 'Semaine Soirée', 2, '18:00:00', '23:59:00', 'mon,tue,wed,thu,fri', 1.00, 0, 1),
(8, 'Week-end Journée', 1, '08:00:00', '18:00:00', 'sat,sun', 3.50, 0, 1),
(9, 'Week-end Nuit', 1, '00:00:00', '08:00:00', 'sat,sun', 2.50, 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(25) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` int DEFAULT '1',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `password`, `role`, `active`) VALUES
(3, 'Philippe', 'Boudegna', 'philippe.boudegna@gmail.com', '+3378197409699', '$2y$10$iUWL2UfJEKnFaNF4tCJrw.wtEwj0o80tmw6FQjKDia/mqJ3nb3XIe', 2, 1),
(5, 'test', 'test', 'test@gmail.com', '+33123456766', '$2y$10$/Q2h51fTIipBnL2XSZnvyuHbWiyfWbLBkSzNj9OjuW9wu4Zuv2QWe', 1, 1),
(6, 'user', 'user', 'user@gmail.com', '+331234567899', '$2y$10$o/HRnj.oeUyCLSijoWt3eu4MRT/Tptnqruc8.s9oIkMTCE/jSL9Di', 2, 0),
(7, 'admin', 'admin', 'admin@admin.com', NULL, '$2y$10$gP89p2kHLqfO5cvONFSH3ewJoNYpP1jqeIAF3kNsHAwvKKGvLBlzi', 2, 1),
(8, 'user2', 'user2', 'user2@gmail.com', NULL, '$2y$10$DclISuvFa2t92WFVjpgAB.5vLNgkODSHoqjrRtXfo/2Z/PvksnvLi', 1, 1),
(9, 'user3', 'user3', 'user3@gmai.com', NULL, '$2y$10$22XpMobrTGlXEqMxzHaSZ.KphKpS.Kv8POrNSj1Ffi4l5t5/iMpfm', 1, 1),
(10, 'user4', 'user4', 'user4@gmail.com', NULL, '$2y$10$85MxSrXWsM1l369wiRyKjunCSdpoSZf6PZ0RJfxxAay3L9mfi4gey', 1, 1),
(12, 'user6', 'user6', 'user6@gmail.com', NULL, '$2y$10$8y34lClWBMsnSbpaclg7.u1x4rvuzAG2yVLjPPz9pH/t7h3sYqVOu', 1, 1),
(13, 'user8', 'user8', 'user8@gmail.com', NULL, '$2y$10$ogiY7icEh1mf05YqLt8use0H5QlkJ4gysZCBiGpNSWNZsv8AK1aIa', 1, 1),
(14, 'user10', 'user10', 'user10@gmail.com', NULL, '$2y$10$/Ab6EMXOD8.hsKbsNHjaVeMb9lJrrEHkFoXCbl67N9I0xmxx/gYbK', 1, 1),
(15, 'user34', '34', 'user34@gmail.com', NULL, '$2y$10$eimq5MN2h.OQhf1SIYwgSexHOxsd9oURHed8Z.po47h7/Achv84u2', 1, 1),
(16, 'juju', 'juju', 'juju@gmail.com', '04444444444', '$2y$10$njY3e1q3hxK79UwPYhxLy.y9sHhM2SNsd8yFeqgzStX4vH/2gDF32', 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
