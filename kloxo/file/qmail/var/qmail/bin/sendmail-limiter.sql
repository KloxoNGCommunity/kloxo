-- phpMyAdmin SQL Dump
-- version 4.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas generowania: 20 Kwi 2015, 14:47
-- Wersja serwera: 10.0.17-MariaDB
-- Wersja PHP: 5.4.39

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `sendmailwrapper`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `client_sendmail`
--

CREATE TABLE IF NOT EXISTS `client_sendmail` (
  `id` int(11) NOT NULL,
  `client_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `client_name` varchar(50) NOT NULL,
  `client_loglevel` int(1) unsigned NOT NULL DEFAULT '1',
  `sm_group` int(1) unsigned NOT NULL DEFAULT '1',
  `sm_ignore` tinyint(1) NOT NULL DEFAULT '0',
  `count` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `first_request` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_request` datetime NOT NULL DEFAULT '1975-02-18 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `client_sendmail`
--

INSERT INTO `client_sendmail` (`id`, `client_uid`, `client_name`, `client_loglevel`, `sm_group`, `sm_ignore`, `count`, `first_request`, `last_request`) VALUES
(1, 7830, 'stanislawbil', 1, 1, 0, 0, '2014-01-18 18:48:02', '2015-04-17 08:09:06'),
(2, 7848, 'gregor76', 1, 1, 0, 0, '2014-01-18 19:00:07', '2014-02-24 17:17:12'),
(3, 2369, 'rafal.zimny', 1, 1, 0, 0, '2014-01-18 19:11:57', '2015-02-20 13:39:37'),
(4, 7899, 'renata_ra3', 1, 1, 0, 0, '2014-01-18 19:13:02', '2014-06-19 23:13:02'),
(5, 7826, 'artisan', 1, 1, 0, 0, '2014-01-18 19:27:01', '2014-01-22 21:27:01'),
(6, 7888, 'rtsmistrzpol', 1, 1, 0, 0, '2014-01-18 19:28:12', '2014-10-30 17:58:53'),
(7, 1911, 'msbkm', 1, 1, 0, 0, '2014-01-18 19:28:52', '2015-04-12 14:58:20'),
(8, 2093, 'zaserd', 1, 1, 0, 1, '2014-01-18 19:55:44', '2015-04-20 15:40:20'),
(9, 2474, 'admin2', 1, 1, 0, 1, '2014-01-18 23:38:32', '2015-04-20 15:38:40'),
(10, 2117, 'monia_mal', 1, 1, 0, 0, '2014-01-19 01:03:15', '2015-01-27 04:29:00'),
(11, 1628, 'maciejka150', 1, 1, 0, 0, '2014-01-19 01:56:57', '2015-04-09 12:10:24'),
(12, 1886, 'trafek', 1, 1, 0, 0, '2014-01-19 12:53:25', '2015-04-20 15:35:57'),
(13, 2455, 'ddamianek7', 1, 1, 0, 0, '2014-01-20 14:03:21', '2015-04-18 22:23:43'),
(14, 1840, 'zbyn77', 1, 1, 0, 0, '2014-01-21 11:23:19', '2015-04-17 21:23:30'),
(15, 7802, 'sancho1313', 1, 1, 0, 0, '2014-01-21 18:23:10', '2015-04-15 10:19:54'),
(16, 1095, 'topmedium', 1, 1, 0, 0, '2014-01-22 06:35:21', '2015-03-26 06:20:11'),
(17, 48, 'apache', 1, 1, 0, 0, '2014-01-22 11:51:18', '2015-04-17 18:11:39'),
(18, 2156, 'smogo', 1, 1, 0, 0, '2014-01-22 15:00:11', '2015-02-16 11:52:50'),
(19, 7893, 'jdean85', 1, 1, 0, 0, '2014-01-28 12:11:04', '2014-09-24 01:27:05'),
(20, 2547, 'bysior19', 1, 1, 0, 0, '2014-01-29 10:02:01', '2015-03-31 10:23:51'),
(21, 7865, 'kenet', 1, 1, 0, 0, '2014-01-31 07:15:06', '2015-02-13 16:45:30'),
(22, 7836, 'robomatik', 1, 1, 0, 0, '2014-01-31 22:43:01', '2014-02-21 01:38:43'),
(23, 7858, 'osiempieckm', 1, 1, 0, 0, '2014-02-02 07:16:44', '2014-08-07 08:55:14'),
(24, 7883, 'luki', 1, 1, 0, 0, '2014-02-02 08:41:56', '2014-09-16 10:00:18'),
(25, 1006, 'admin', 1, 1, 0, 0, '2014-02-02 12:49:01', '2014-04-14 21:20:02'),
(26, 501, 'lxlabs', 1, 1, 1, 0, '2014-02-02 13:51:54', '2014-02-17 18:16:03'),
(27, 0, 'root', 1, 1, 0, 0, '2014-02-06 02:17:15', '2014-02-17 12:30:02'),
(28, 7901, 'wisniowa94', 1, 1, 0, 0, '2014-02-06 16:05:18', '2014-07-21 11:20:19'),
(29, 7812, 'gatek89', 1, 2, 0, 0, '2014-02-06 21:41:37', '2015-04-17 23:01:59'),
(30, 7907, 'eremi21', 1, 1, 0, 0, '2014-02-07 20:58:40', '2014-09-29 14:22:13'),
(31, 7908, 'hamer831', 1, 1, 0, 0, '2014-02-08 14:12:33', '2014-07-08 18:37:09'),
(32, 7920, 'keczuk1', 1, 1, 0, 0, '2014-02-08 22:01:48', '2014-02-08 23:01:48'),
(33, 7921, 'dariusznowy', 1, 1, 0, 0, '2014-02-10 11:57:57', '2014-09-12 20:59:16'),
(34, 7884, 'broznik', 1, 1, 0, 0, '2014-02-12 15:34:11', '2015-01-10 09:24:06'),
(35, 7922, 'kasiuniagd', 1, 1, 0, 0, '2014-02-13 22:06:09', '2014-09-23 11:43:54'),
(36, 2344, 'mikep13', 1, 1, 0, 0, '2014-02-26 13:23:52', '2014-05-20 10:42:29'),
(37, 2267, 'greg-98', 1, 1, 0, 0, '2014-02-26 22:48:36', '2015-04-13 10:46:33'),
(38, 7926, 'mistrz-chaosu', 1, 1, 0, 0, '2014-02-27 15:06:16', '2014-10-21 21:50:01'),
(39, 2513, 'piotr_k2_2004', 1, 1, 0, 0, '2014-02-28 15:40:41', '2015-04-09 12:46:41'),
(40, 7903, 'warome', 1, 1, 0, 0, '2014-03-03 22:27:14', '2014-03-03 23:27:14'),
(41, 7911, 'markub', 1, 1, 0, 0, '2014-03-04 15:41:26', '2014-07-24 23:46:57'),
(42, 2390, 'mar_wr', 1, 1, 0, 0, '2014-03-10 07:06:21', '2015-01-16 22:15:02'),
(43, 7910, 'nowekontoxxl', 1, 1, 0, 0, '2014-03-12 10:36:05', '2015-01-25 23:18:11'),
(44, 7861, 'michalrydzio', 1, 1, 0, 0, '2014-03-13 09:12:57', '2014-11-21 13:14:29'),
(45, 7930, 'larekgi', 1, 1, 0, 0, '2014-03-13 16:18:01', '2014-11-07 07:03:06'),
(46, 7894, 'haker_ha', 1, 2, 0, 0, '2014-03-19 07:58:17', '2014-12-12 07:07:04'),
(47, 2299, 'viciu777', 1, 1, 0, 0, '2014-03-21 07:29:50', '2015-04-17 15:34:20'),
(48, 7931, 'pawel665', 1, 1, 0, 0, '2014-03-21 17:13:02', '2014-09-26 00:13:02'),
(49, 7913, 'tonyhalik', 1, 1, 0, 0, '2014-03-26 14:29:21', '2014-04-17 11:51:14'),
(50, 7924, 'drobiazgow', 1, 1, 0, 0, '2014-03-27 11:05:52', '2015-04-09 16:20:36'),
(51, 7923, 'neox66', 1, 1, 0, 0, '2014-03-28 10:07:50', '2014-06-13 15:32:58'),
(52, 2497, 'walldstar', 1, 1, 0, 0, '2014-03-31 14:28:22', '2014-03-31 16:28:22'),
(53, 7932, 'jaroslaw1957', 1, 1, 0, 0, '2014-04-04 07:46:46', '2014-06-23 10:04:26'),
(54, 7944, 'efa2005', 1, 1, 0, 0, '2014-04-13 08:55:22', '2014-04-19 17:15:59'),
(55, 7943, 'kamka_de', 1, 1, 0, 0, '2014-04-13 20:28:18', '2014-04-13 22:28:18'),
(56, 7945, 'ravan32lt', 1, 1, 0, 0, '2014-04-14 14:17:02', '2014-07-11 14:34:48'),
(57, 7946, 'agadkatalog', 1, 1, 0, 0, '2014-04-15 11:43:43', '2014-04-15 19:16:50'),
(58, 7942, 'b_bozek', 1, 1, 0, 0, '2014-04-15 15:38:53', '2014-04-15 17:38:53'),
(59, 2503, 'piotrk', 1, 1, 0, 0, '2014-04-21 16:44:35', '2015-01-10 07:33:55'),
(60, 2548, 'magda_00aa', 1, 1, 0, 0, '2014-04-22 15:06:52', '2014-08-21 09:02:10'),
(61, 7952, 'marcowy77', 1, 1, 0, 0, '2014-05-14 19:09:21', '2014-05-14 21:09:21'),
(62, 2336, 'kaplan161', 1, 1, 0, 0, '2014-05-15 10:27:59', '2014-05-15 12:27:59'),
(63, 7914, 'markub2', 1, 1, 0, 0, '2014-05-23 10:36:14', '2014-05-23 12:36:14'),
(64, 7963, 'dariusz_h1', 1, 1, 0, 0, '2014-05-27 20:08:01', '2014-09-15 09:12:04'),
(65, 2050, 'agona2', 1, 1, 0, 0, '2014-06-09 06:53:37', '2015-04-16 15:21:40'),
(66, 7950, 'it_electronics', 1, 1, 0, 0, '2014-06-13 13:18:35', '2014-07-11 14:32:20'),
(67, 7927, 'v_korecki', 1, 1, 0, 0, '2014-06-13 13:26:39', '2014-07-11 14:37:01'),
(68, 7909, 'daronio', 1, 1, 0, 0, '2014-06-13 13:36:54', '2014-06-13 15:36:54'),
(69, 7956, 'rochstar79', 1, 1, 0, 0, '2014-06-13 13:38:57', '2014-09-23 12:34:07'),
(70, 7897, 'czeslaw1234', 1, 1, 0, 0, '2014-06-13 23:35:03', '2015-04-17 22:30:01'),
(71, 7906, 'ssso', 1, 1, 0, 0, '2014-06-20 06:57:49', '2014-08-19 09:26:18'),
(72, 7972, 'sarah10', 1, 1, 0, 0, '2014-06-26 09:36:57', '2014-06-26 11:36:57'),
(73, 7976, 'mar_szwaracki', 1, 1, 0, 0, '2014-06-30 20:06:22', '2014-09-27 15:51:53'),
(74, 7968, 'mste2211', 1, 1, 0, 0, '2014-07-01 15:03:01', '2014-07-18 17:57:57'),
(75, 7979, 'speva', 1, 1, 0, 0, '2014-07-12 19:41:32', '2014-07-13 21:01:50'),
(76, 1863, 'justynamiga', 1, 1, 0, 0, '2014-07-17 10:48:33', '2014-11-25 15:56:23'),
(77, 7948, 'pan_na_wlosciach', 1, 1, 0, 0, '2014-07-21 13:56:25', '2014-12-01 14:38:32'),
(78, 7977, 'km', 1, 1, 0, 0, '2014-07-25 09:48:47', '2014-08-13 16:56:49'),
(79, 7967, 'kasiadavies', 1, 1, 0, 0, '2014-07-25 11:57:51', '2014-09-14 18:40:14'),
(80, 2098, 'slawek-s-06', 1, 1, 0, 0, '2014-07-28 10:18:18', '2014-11-13 22:24:45'),
(81, 7982, 'mefistokn', 1, 1, 0, 0, '2014-08-18 14:36:22', '2014-08-18 16:36:22'),
(82, 7984, 'przemasek1979', 1, 1, 0, 0, '2014-08-21 21:17:36', '2014-10-19 15:21:11'),
(83, 7985, 'eristiff', 1, 1, 0, 0, '2014-08-24 18:03:02', '2015-02-01 21:51:01'),
(84, 2223, 'rik_shop', 1, 1, 0, 0, '2014-09-02 10:40:41', '2014-11-06 18:01:15'),
(85, 7970, 'ubudubu13', 1, 1, 0, 0, '2014-09-05 07:53:08', '2015-03-25 12:36:25'),
(86, 7986, 'jasinski3', 1, 1, 0, 0, '2014-09-16 09:40:00', '2014-09-16 11:40:00'),
(87, 7997, 'walldy', 1, 1, 0, 2, '2014-10-11 15:37:02', '2015-04-20 15:47:01'),
(88, 7995, 'belcysia', 1, 1, 0, 0, '2014-10-12 20:48:03', '2014-10-27 21:14:41'),
(89, 7833, 'syska161', 1, 1, 0, 0, '2014-10-21 15:53:23', '2015-02-26 12:03:19'),
(90, 8003, 'kleju87', 1, 1, 0, 0, '2014-10-21 20:27:33', '2015-02-17 17:35:24'),
(91, 7994, 'kamilwrobel', 1, 1, 0, 0, '2014-11-12 14:15:54', '2015-01-20 19:54:50'),
(92, 8001, 'sun1984', 1, 1, 0, 0, '2014-11-19 09:15:04', '2015-03-25 01:32:02'),
(93, 8010, 'koczki86', 1, 1, 0, 0, '2014-11-26 01:07:01', '2015-02-16 11:07:03'),
(94, 2440, 'ggolubski', 1, 1, 0, 0, '2014-11-26 05:54:29', '2015-01-03 19:15:18'),
(95, 8014, 'bodehaa', 1, 1, 0, 0, '2014-12-07 00:12:02', '2015-04-07 18:20:04'),
(96, 8009, 'km85', 1, 1, 0, 0, '2014-12-12 10:55:52', '2014-12-12 11:55:52'),
(97, 8012, 'bponeta', 1, 1, 0, 0, '2014-12-28 18:03:45', '2015-04-17 11:43:47'),
(98, 8013, 'smoqiu', 1, 1, 0, 0, '2015-02-24 04:58:04', '2015-02-24 05:58:04'),
(99, 8021, 'prze_my_slaw_k', 1, 1, 0, 0, '2015-03-25 11:57:59', '2015-04-17 08:12:21'),
(100, 2508, 'v-art', 1, 1, 0, 0, '2015-03-29 02:12:36', '2015-04-04 00:30:16'),
(101, 7887, 'airon25', 1, 1, 0, 0, '2015-03-31 14:46:28', '2015-03-31 16:46:28'),
(102, 8031, 'rochstar79', 1, 1, 0, 0, '2015-04-07 13:22:31', '2015-04-07 15:22:31'),
(103, 8019, 'uzytkownik1805', 1, 1, 0, 0, '2015-04-09 13:13:12', '2015-04-09 15:13:12');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `client_sendmail`
--
ALTER TABLE `client_sendmail`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_uid` (`client_uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `client_sendmail`
--
ALTER TABLE `client_sendmail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=104;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;