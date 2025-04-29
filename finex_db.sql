-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 29 avr. 2025 à 19:11
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `finex_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

CREATE TABLE `administrateurs` (
  `id` int(11) NOT NULL,
  `nom_utilisateur` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `nom_utilisateur`, `mot_de_passe`, `email`, `date_creation`) VALUES
(1, 'Sitrakiniaina', '$2y$10$rTFBbhMOYk6fAgvn1DdYje5jFMQhQ08nR9Xi9kexKg6MCCtC0BBIC', 'sitrakiniainaeddyfrancisco@gmail.com', '2025-04-06 06:49:00');

-- --------------------------------------------------------

--
-- Structure de la table `banque_history`
--

CREATE TABLE `banque_history` (
  `Id` int(11) NOT NULL,
  `solde` decimal(15,2) DEFAULT 10000000.00 COMMENT 'Solde bancaire initial',
  `solde_en_pret` decimal(15,2) DEFAULT NULL COMMENT 'Somme des prêts actifs',
  `solde_estime` decimal(15,2) GENERATED ALWAYS AS (`solde` + `solde_en_pret`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `banque_history`
--

INSERT INTO `banque_history` (`Id`, `solde`, `solde_en_pret`) VALUES
(1, '11423875.50', '5442000.00');

-- --------------------------------------------------------

--
-- Structure de la table `banque_solde_historique`
--

CREATE TABLE `banque_solde_historique` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `solde` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `banque_solde_historique`
--

INSERT INTO `banque_solde_historique` (`id`, `date`, `solde`) VALUES
(1, '2025-04-03 20:12:24', '12579000.00'),
(2, '2025-04-03 20:22:03', '12379000.00'),
(3, '2025-04-03 20:28:32', '12599000.00'),
(4, '2025-04-02 20:30:18', '218363.00'),
(5, '2025-04-01 20:30:18', '608821.00'),
(6, '2025-03-31 20:30:18', '389015.00'),
(7, '2025-03-30 20:30:18', '118612.00'),
(8, '2025-03-29 20:30:18', '426016.00'),
(9, '2025-03-28 20:30:18', '774245.00'),
(10, '2025-04-02 21:30:18', '593179.00'),
(11, '2025-04-01 22:30:18', '643158.00'),
(32, '2025-04-11 16:52:30', '12203000.00'),
(33, '2025-04-12 16:52:30', '4800000.00'),
(34, '2025-04-13 16:52:30', '8109000.00'),
(35, '2025-04-14 16:52:30', '5300000.00'),
(36, '2025-04-14 18:02:22', '12561600.00'),
(37, '2025-04-14 18:03:35', '12061600.00'),
(38, '2025-04-16 20:06:48', '11961600.00'),
(39, '2025-04-16 20:09:08', '11911600.00'),
(40, '2025-04-16 20:10:16', '11901600.00'),
(41, '2025-04-16 20:16:29', '11891600.00'),
(43, '2025-04-17 17:18:01', '12001600.00'),
(44, '2025-04-20 11:13:51', '11001600.00'),
(45, '2025-04-24 11:25:06', '12101600.00'),
(46, '2025-04-24 11:38:39', '11851600.00'),
(47, '2025-04-24 12:09:21', '12126600.00'),
(48, '2025-04-24 12:21:59', '11626600.00'),
(49, '2025-04-24 13:48:28', '11575600.00'),
(50, '2025-04-24 20:16:14', '11375600.00'),
(51, '2025-04-24 21:19:11', '11325600.00'),
(52, '2025-04-25 11:08:10', '11125600.00'),
(53, '2025-04-25 10:08:37', '11783875.50'),
(54, '2025-04-27 10:59:41', '11683875.50'),
(55, '2025-04-27 11:32:38', '11793875.50'),
(56, '2025-04-27 11:32:44', '11903875.50'),
(57, '2025-04-27 11:36:42', '11703875.50'),
(58, '2025-04-27 11:38:34', '11723875.50'),
(59, '2025-04-27 11:39:34', '11923875.50'),
(60, '2025-04-28 17:50:12', '11423875.50');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `numCompte` varchar(20) NOT NULL,
  `Nom` varchar(50) DEFAULT NULL,
  `Prenoms` varchar(50) DEFAULT NULL,
  `Tel` varchar(15) DEFAULT NULL,
  `mail` varchar(100) DEFAULT NULL,
  `dateAdhesion` datetime DEFAULT NULL,
  `id` int(11) NOT NULL,
  `solde` decimal(10,2) DEFAULT 0.00,
  `codePin` varchar(4) DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'actif',
  `profil` varchar(255) DEFAULT '/images/profile.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`numCompte`, `Nom`, `Prenoms`, `Tel`, `mail`, `dateAdhesion`, `id`, `solde`, `codePin`, `statut`, `profil`) VALUES
('02610001', 'Sitrakiniaina', 'eddy francisco', '0345490654', 'Lucifercompte69@gmail.com', '2025-03-12 12:08:53', 1, '504540.00', '1234', 'actif', '/images/profile.jpg'),
('02610015', 'Sitrakiniaina', 'eddy francisco', '0345490654', 'Luciferompte69@gmail.com', '2025-03-12 12:34:06', 15, '97000.00', '0015', 'actif', '/images/profile.jpg'),
('02610016', 'Sitraka', 'eddy francisco', '0345490654', 'Lercompte69@gmail.com', '2025-03-12 12:34:40', 16, '610000.00', '0016', 'actif', '/images/profile.jpg'),
('02610017', 'Sitrakiniaina', 'eddy francisco', '0345490654', 'Lucifercomp69@gmail.com', '2025-03-13 08:28:29', 23, '18000.00', '0017', 'actif', '/images/profile.jpg'),
('02610024', 'tahina', 'eddy francisco', '0345490654', 'Lucifercompte@gmail.com', '2025-03-13 08:38:50', 24, '21540.00', '0000', 'actif', '/images/profile.jpg'),
('02610025', 'Sitrakiniaina', 'eddy francisco', '0345490654', 'kkk@gmail.com', '2025-03-13 10:50:44', 25, '1000.00', '1234', 'supprimé', '/images/profile.jpg'),
('02610026', 'kaka', 'boy', '0342222222', 'kakaboy3@gmail.com', '2025-03-13 16:51:26', 31, '60000.00', '7890', 'actif', '/images/profile.jpg'),
('02610032', 'Beluga', 'cat', '0345555555', 'beluga@gmail.com', '2025-03-18 16:20:43', 32, '-5000.00', '0032', 'actif', '/images/profile.jpg'),
('02610033', 'Hitler', 'Adolf', '0340908270', 'hitler#@gmail.com', '2025-03-18 16:22:59', 33, '80000.00', '0033', 'actif', '/images/profile.jpg'),
('02610034', 'Nani', 'nandeto', '0345503455', 'Nani@gmail.com', '2025-03-19 11:11:04', 34, '500000.00', '0034', 'actif', '/images/profile.jpg'),
('02610035', 'Raul', 'Asencio', '0343503435', 'Raul@gmail.com', '2025-03-19 11:36:15', 35, '2400.00', '0035', 'actif', '/images/profile.jpg'),
('02610036', 'Noob', 'storm', '0343603436', 'storm@gmail.com', '2025-03-19 12:12:34', 36, '56000.00', '0036', 'supprimé', '/images/profile.jpg'),
('02610037', 'Last', 'one', '0343703437', 'Last@gmail.com', '2025-03-19 12:21:31', 37, '5000.00', '0037', 'supprimé', '/images/profile.jpg'),
('02610038', 'ggleka', 'dsfd', '545462462', 'gg@gmail.com', '2025-03-19 12:25:17', 38, '-700000.00', '0038', 'supprimé', '/images/profile.jpg'),
('02610039', 'jjjjj', 'kkkk', '12112121', 'jjj@gmail.com', '2025-03-19 18:29:38', 39, '0.00', '0039', 'actif', '/images/profile.jpg'),
('02610040', 'Trosa', 'banque', '0344003440', 'Trosa@gmail.com', '2025-03-23 19:38:30', 40, '70000.00', '0040', 'actif', '/images/profile.jpg'),
('02610041', 'Lamine', 'Yamal', '0341901919', 'Lamine19@gmail.com', '2025-03-24 06:02:20', 41, '1125000.00', '0041', 'actif', '/images/profile.jpg'),
('02610042', 'Pedri', 'Gon', '0340803408', 'Pedri@gmail.com', '2025-03-24 07:33:08', 42, '2305000.00', '0042', 'actif', '/images/profile.jpg'),
('02610043', 'gilb', 'noob', '0382913128', 'gilb@gmail.com', '2025-03-31 13:59:02', 43, '25000.00', '0043', 'actif', '/images/profile.jpg'),
('02610044', 'Tsiaro', 'beloha', '0346906969', 'tsiaroleka@gmail.com', '2025-03-31 14:17:39', 44, '8000.00', '0044', 'actif', '/images/profile.jpg'),
('02610045', 'Rvaka', 'vkaka', '0345605656', 'rav@gmail.com', '2025-03-31 14:28:20', 45, '90000.00', '0045', 'actif', '/images/profile.jpg'),
('02610046', 'bg', 'demad', '0340202202', 'bgmdg@gmail.com', '2025-04-03 19:21:11', 46, '90000.00', '0046', 'supprimé', '/images/profile.jpg'),
('02610047', 'Kosongo', 'yeye', '0340808270', 'kosongo@gmail.com', '2025-04-14 18:49:43', 47, '41724.50', '0047', 'actif', '/images/profile.jpg'),
('02610048', 'mail', 'tester', '0340011122', 'test-8yj863nw6@srv1.mail-tester.com', '2025-04-24 13:21:26', 49, '551000.00', '0048', 'actif', '/images/profile.jpg'),
('02610050', 'francisco', 'Akartis', '0344648131', 'sitrakiniainaeddyfrancisco@gmail.com', '2025-04-24 21:15:14', 50, '200000.00', '0050', 'actif', '/images/profile.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE `parametres` (
  `nom` varchar(50) NOT NULL,
  `valeur` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `parametres`
--

INSERT INTO `parametres` (`nom`, `valeur`) VALUES
('derniere_maj_frais', '2025-04-22');

-- --------------------------------------------------------

--
-- Structure de la table `preter`
--

CREATE TABLE `preter` (
  `numPret` int(11) NOT NULL,
  `numCompte` varchar(20) NOT NULL,
  `montantPrete` decimal(10,2) NOT NULL,
  `datePret` date NOT NULL,
  `statut` enum('en cours','remboursé','en retard') NOT NULL DEFAULT 'en cours',
  `delais` int(11) NOT NULL,
  `montantARembourser` decimal(10,2) NOT NULL,
  `dateRemboursement` date DEFAULT NULL,
  `dernierCalculFrais` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `preter`
--

INSERT INTO `preter` (`numPret`, `numCompte`, `montantPrete`, `datePret`, `statut`, `delais`, `montantARembourser`, `dateRemboursement`, `dernierCalculFrais`) VALUES
(5, '02610015', '30000.00', '2025-03-18', 'en retard', 1, '968926.10', '2025-01-18', '2025-04-28'),
(6, '02610017', '20000.00', '2025-03-18', 'remboursé', 1, '22000.00', '2025-03-31', NULL),
(7, '02610016', '500000.00', '2025-03-18', 'remboursé', 6, '550000.00', '2025-03-29', NULL),
(8, '02610032', '10000.00', '2025-03-18', 'remboursé', 1, '11000.00', '2025-03-27', NULL),
(9, '02610033', '200000.00', '2025-03-18', 'remboursé', 4, '220000.00', '2025-03-31', NULL),
(10, '02610034', '500000.00', '2025-03-19', 'en cours', 6, '550000.00', '2025-09-19', NULL),
(11, '02610035', '16000.00', '2025-03-19', 'remboursé', 1, '17600.00', '2025-04-06', NULL),
(12, '02610036', '56000.00', '2025-03-19', 'en cours', 2, '61600.00', '2025-05-19', NULL),
(13, '02610037', '65000.00', '2025-03-19', 'en cours', 2, '71500.00', '2025-05-19', NULL),
(14, '02610038', '700000.00', '2025-03-19', 'remboursé', 6, '770000.00', '2025-03-30', NULL),
(15, '02610040', '300000.00', '2025-03-23', 'remboursé', 4, '30000.00', '2025-03-31', NULL),
(19, '02610041', '230000.00', '2025-03-24', 'en cours', 4, '198000.00', '2025-07-24', NULL),
(22, '02610042', '2305000.00', '2025-03-24', 'en cours', 8, '2535500.00', '2025-11-24', NULL),
(23, '02610043', '15000.00', '2025-03-31', 'en cours', 1, '16500.00', '2025-05-01', NULL),
(25, '02610044', '20000.00', '2025-03-31', 'remboursé', 1, '22000.00', '2025-04-01', NULL),
(29, '02610045', '100000.00', '2025-04-03', 'remboursé', 2, '110000.00', '2025-04-17', NULL),
(30, '02610046', '100000.00', '2025-04-03', 'en retard', 2, '303540.68', '2025-04-03', '2025-04-28'),
(31, '02610047', '500000.00', '2025-04-14', 'remboursé', 6, '658275.50', '2025-04-25', '2025-04-25'),
(33, '02610001', '250000.00', '2025-04-24', 'remboursé', 4, '275000.00', '2025-04-24', NULL),
(34, '02610048', '500000.00', '2025-04-24', 'en cours', 6, '550000.00', '2025-10-24', NULL),
(35, '02610050', '200000.00', '2025-04-24', 'en cours', 4, '220000.00', '2025-08-24', NULL),
(36, '02610001', '100000.00', '2025-04-27', 'remboursé', 2, '110000.00', '2025-04-27', NULL),
(37, '02610001', '200000.00', '2025-04-27', 'remboursé', 4, '200000.00', '2025-04-27', NULL),
(38, '02610001', '500000.00', '2025-04-28', 'en cours', 6, '550000.00', '2025-10-28', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `rendre`
--

CREATE TABLE `rendre` (
  `numRend` int(11) NOT NULL,
  `numPret` int(11) NOT NULL,
  `situation` enum('Tout payé','Payé par part') NOT NULL,
  `restePaye` decimal(15,2) DEFAULT 0.00,
  `date_rendu` datetime DEFAULT current_timestamp()
) ;

--
-- Déchargement des données de la table `rendre`
--

INSERT INTO `rendre` (`numRend`, `numPret`, `situation`, `restePaye`, `date_rendu`) VALUES
(1, 8, 'Payé par part', '5000.00', '2025-03-27 19:54:12'),
(2, 8, 'Tout payé', '0.00', '2025-03-27 19:55:07'),
(3, 14, 'Tout payé', '0.00', '2025-03-28 17:44:24'),
(4, 7, 'Tout payé', '0.00', '2025-03-29 19:30:58'),
(5, 14, 'Tout payé', '0.00', '2025-03-30 09:19:25'),
(6, 9, 'Tout payé', '0.00', '2025-03-31 12:12:36'),
(7, 6, 'Tout payé', '0.00', '2025-03-31 12:17:48'),
(8, 13, 'Payé par part', '61500.00', '2025-03-31 12:49:32'),
(9, 13, 'Payé par part', '61500.00', '2025-03-31 12:49:34'),
(10, 13, 'Payé par part', '61500.00', '2025-03-31 12:49:37'),
(11, 13, 'Payé par part', '61500.00', '2025-03-31 12:49:38'),
(12, 13, 'Payé par part', '61500.00', '2025-03-31 12:49:39'),
(13, 13, 'Payé par part', '61500.00', '2025-03-31 12:49:40'),
(14, 13, 'Payé par part', '61500.00', '2025-03-31 12:49:41'),
(15, 11, 'Payé par part', '7600.00', '2025-03-31 13:09:55'),
(16, 11, 'Payé par part', '7600.00', '2025-03-31 13:10:03'),
(17, 5, 'Payé par part', '13000.00', '2025-03-31 13:15:32'),
(18, 15, 'Payé par part', '230000.00', '2025-03-31 13:52:24'),
(19, 15, 'Payé par part', '30000.00', '2025-03-31 13:54:59'),
(20, 15, 'Tout payé', '0.00', '2025-03-31 13:56:24'),
(22, 25, 'Tout payé', '0.00', '2025-04-01 17:29:36'),
(23, 30, 'Tout payé', '0.00', '2025-04-03 19:28:33'),
(24, 11, 'Tout payé', '0.00', '2025-04-06 06:56:50'),
(25, 19, 'Payé par part', '198000.00', '2025-04-07 16:12:14'),
(26, 29, 'Tout payé', '0.00', '2025-04-17 18:18:01'),
(28, 33, 'Tout payé', '0.00', '2025-04-24 13:09:21'),
(29, 31, 'Tout payé', '0.00', '2025-04-25 11:08:37'),
(30, 36, 'Tout payé', '0.00', '2025-04-27 12:32:38'),
(31, 36, 'Tout payé', '0.00', '2025-04-27 12:32:44'),
(32, 37, 'Payé par part', '200000.00', '2025-04-27 12:38:34'),
(33, 37, 'Tout payé', '0.00', '2025-04-27 12:39:34');

-- --------------------------------------------------------

--
-- Structure de la table `versement`
--

CREATE TABLE `versement` (
  `id` int(11) NOT NULL,
  `numCompte` varchar(255) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `dateVersement` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `versement`
--

INSERT INTO `versement` (`id`, `numCompte`, `montant`, `dateVersement`) VALUES
(1, '02610001', '100000.00', '2025-04-16 20:06:48'),
(2, '02610015', '50000.00', '2025-04-16 20:09:08'),
(3, '02610016', '10000.00', '2025-04-16 20:10:16'),
(4, '02610017', '10000.00', '2025-04-16 20:16:29'),
(6, '02610048', '51000.00', '2025-04-24 13:48:28'),
(7, '02610048', '50000.00', '2025-04-24 21:19:11'),
(8, '02610047', '200000.00', '2025-04-25 11:08:10');

-- --------------------------------------------------------

--
-- Structure de la table `virement`
--

CREATE TABLE `virement` (
  `idVirement` int(11) NOT NULL,
  `numCompteEnvoyeur` varchar(20) DEFAULT NULL,
  `numCompteBeneficiaire` varchar(20) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `dateTransfert` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `virement`
--

INSERT INTO `virement` (`idVirement`, `numCompteEnvoyeur`, `numCompteBeneficiaire`, `montant`, `dateTransfert`) VALUES
(1, '02610015', '02610016', '1000.00', '2025-03-12 00:00:00'),
(2, '02610016', '02610015', '500.00', '2025-03-13 09:22:44'),
(3, '02610016', '02610015', '500.00', '2025-03-13 09:22:48'),
(7, '02610001', '02610024', '1000.00', '2025-03-13 13:38:57'),
(8, '02610001', '02610024', '5000.00', '2025-03-13 13:53:01'),
(9, '02610024', '02610001', '2500.00', '2025-03-13 14:17:21'),
(10, '02610001', '02610024', '2000.00', '2025-03-13 14:41:30'),
(11, '02610001', '02610024', '5000.00', '2025-03-13 14:45:18'),
(12, '02610024', '02610001', '9500.00', '2025-03-13 14:51:01'),
(13, '02610001', '02610025', '5000.00', '2025-03-13 14:56:50'),
(14, '02610025', '02610001', '2300.00', '2025-03-13 15:42:55'),
(15, '02610001', '02610024', '1540.00', '2025-03-13 17:56:13'),
(16, '02610025', '02610001', '1700.00', '2025-03-17 11:10:32'),
(17, '02610001', '02610015', '7000.00', '2025-03-25 17:24:59'),
(18, '02610045', '02610001', '40000.00', '2025-04-04 18:59:26'),
(19, '02610001', '02610015', '30000.00', '2025-04-17 18:26:12'),
(20, '02610001', '02610015', '30000.00', '2025-04-17 18:26:49'),
(21, '02610015', '02610001', '30000.00', '2025-04-17 18:38:47'),
(22, '02610015', '02610001', '30000.00', '2025-04-17 18:59:57'),
(23, '02610015', '02610001', '10000.00', '2025-04-17 19:54:39'),
(24, '02610015', '02610001', '10000.00', '2025-04-17 20:00:44'),
(25, '02610015', '02610001', '10000.00', '2025-04-17 20:03:49'),
(26, '02610001', '02610015', '30000.00', '2025-04-17 20:07:33'),
(27, '02610048', '02610001', '50000.00', '2025-04-24 13:49:43'),
(28, '02610001', '02610015', '30000.00', '2025-04-27 12:43:18');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `banque_history`
--
ALTER TABLE `banque_history`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `banque_solde_historique`
--
ALTER TABLE `banque_solde_historique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`numCompte`),
  ADD UNIQUE KEY `id_unique` (`id`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `mail_2` (`mail`);

--
-- Index pour la table `parametres`
--
ALTER TABLE `parametres`
  ADD PRIMARY KEY (`nom`);

--
-- Index pour la table `preter`
--
ALTER TABLE `preter`
  ADD PRIMARY KEY (`numPret`);

--
-- Index pour la table `rendre`
--
ALTER TABLE `rendre`
  ADD PRIMARY KEY (`numRend`),
  ADD KEY `fk_numPret` (`numPret`);

--
-- Index pour la table `versement`
--
ALTER TABLE `versement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_numCompte` (`numCompte`),
  ADD KEY `idx_dateVersement` (`dateVersement`);

--
-- Index pour la table `virement`
--
ALTER TABLE `virement`
  ADD PRIMARY KEY (`idVirement`),
  ADD KEY `numCompteEnvoyeur` (`numCompteEnvoyeur`),
  ADD KEY `numCompteBeneficiaire` (`numCompteBeneficiaire`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `banque_history`
--
ALTER TABLE `banque_history`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `banque_solde_historique`
--
ALTER TABLE `banque_solde_historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `preter`
--
ALTER TABLE `preter`
  MODIFY `numPret` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `rendre`
--
ALTER TABLE `rendre`
  MODIFY `numRend` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `versement`
--
ALTER TABLE `versement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `virement`
--
ALTER TABLE `virement`
  MODIFY `idVirement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `rendre`
--
ALTER TABLE `rendre`
  ADD CONSTRAINT `fk_numPret` FOREIGN KEY (`numPret`) REFERENCES `preter` (`numPret`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `virement`
--
ALTER TABLE `virement`
  ADD CONSTRAINT `virement_ibfk_1` FOREIGN KEY (`numCompteEnvoyeur`) REFERENCES `client` (`numCompte`),
  ADD CONSTRAINT `virement_ibfk_2` FOREIGN KEY (`numCompteBeneficiaire`) REFERENCES `client` (`numCompte`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
