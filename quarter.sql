-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 22, 2020 at 02:53 PM
-- Server version: 5.7.28-0ubuntu0.16.04.2
-- PHP Version: 7.0.33-13+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eatance`
--

-- --------------------------------------------------------

--
-- Table structure for table `quarter`
--

CREATE TABLE `quarter` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quarter`
--

INSERT INTO `quarter` (`entity_id`, `name`) VALUES
(1, 'any'),
(2, '67 ha'),
(3, 'alarobia'),
(4, 'alasora'),
(5, 'ambatomena'),
(6, 'ambatoroka'),
(7, 'ambohidahy'),
(8, 'ambohimahitsy'),
(9, 'ambohimangakely'),
(10, 'ambohimiandra bird'),
(11, 'ambohimitsimbina'),
(12, 'ambolokandrina'),
(13, 'ampandrana'),
(14, 'ampefiloha'),
(15, 'analakely'),
(16, 'andoharanofotsy'),
(17, 'andraharo'),
(18, 'andraisoro'),
(19, 'andravohangy'),
(20, 'ankadimbahoaka'),
(21, 'ankazomanga'),
(22, 'ankorondrano'),
(23, 'anosibe'),
(24, 'anosizato'),
(25, 'antanimena'),
(26, 'antaninarenina'),
(27, 'antsahavola'),
(28, 'behoririka'),
(29, 'by-pass'),
(30, 'faravohitra'),
(31, 'fenomanana'),
(32, 'ilafy'),
(33, 'isoraka'),
(34, 'ivandry'),
(35, 'ivandry clairefontaine'),
(36, 'mahamasina'),
(37, 'mahazoarivo'),
(38, 'manakambahiny'),
(39, 'mandroseza'),
(40, 'sirama'),
(41, 'soarano'),
(42, 'soavimasoandro'),
(43, 'soavimbahoaka'),
(44, 'tsiadana'),
(45, 'ivato'),
(46, 'ambanidia'),
(47, 'ambatobe'),
(48, 'ambatomainty'),
(49, 'ambatomaro'),
(50, 'ambodimita'),
(51, 'ambodinisotry'),
(52, 'amboditsiry'),
(53, 'ambohibao'),
(54, 'ambohijatovo'),
(55, 'ambohimanarina'),
(56, 'ambohipo'),
(57, 'ambohitrarahaba'),
(58, 'ampahibe'),
(59, 'ampasapito'),
(60, 'analamahitsy'),
(61, 'andohan\'i mandroseza'),
(62, 'andrainarivo'),
(63, 'andrefan\' ambohijanahary'),
(64, 'andrononobe'),
(65, 'ankadifotsy'),
(66, 'ankadindramamy'),
(67, 'ankadindrantombo'),
(68, 'ankatso'),
(69, 'ankerana'),
(70, 'ankorahotra'),
(71, 'anosipatrana'),
(72, 'anosisoa'),
(73, 'anosivavaka'),
(74, 'anosy'),
(75, 'antanimora'),
(76, 'antsahabe'),
(77, 'avaradoha'),
(78, 'iavoloha'),
(79, 'itaosy'),
(80, 'maharoho'),
(81, 'nanisana'),
(82, 'talatamaty'),
(83, 'tanjombato'),
(84, 'tsarahonenana'),
(85, 'tsaralalàna'),
(86, 'tsimbazaza'),
(87, 'ambohimiandra'),
(88, 'ambohitsoa'),
(89, 'andranobevava'),
(90, 'androhibe'),
(91, 'ambatonakanga'),
(92, 'besarety'),
(93, 'betongolo'),
(94, 'mausolée'),
(95, 'tsiazotafo'),
(96, 'andavamamba'),
(97, 'androndra'),
(98, 'anjanahary'),
(99, 'ankadivato'),
(100, 'mahatony'),
(101, 'rasalama'),
(102, 'amboanjobe'),
(103, 'andohalo'),
(104, 'antaninandro'),
(105, 'antsakaviro'),
(106, 'manjakaray'),
(107, 'ambaranjana'),
(108, 'ambatolampy'),
(109, 'ambodivona'),
(110, 'ambodivorikely'),
(111, 'ambondrona'),
(112, 'andohotapenaka'),
(113, 'andranomena'),
(114, 'ankadilalana'),
(115, 'ankazotokana'),
(116, 'imerimanjaka'),
(117, 'isotry'),
(118, 'mahazo'),
(119, 'namontana'),
(120, 'sabotsy-namehana'),
(121, 'soanierana'),
(122, 'antsakavivo'),
(123, 'amparibe'),
(124, 'soavinandriana'),
(125, '67 ha nord'),
(126, '67 ha sud'),
(127, 'ampasanimalo'),
(128, 'ampitatafika'),
(129, 'ankaraobato'),
(130, 'antohomadinika'),
(131, 'antsahamanitra'),
(132, 'ampandrinomby'),
(133, 'fort voyron'),
(134, 'ambohimahintsy'),
(135, 'ambohimamory'),
(136, 'digue'),
(137, 'vontovorona'),
(138, 'akany fitiavana'),
(139, 'ambohimanambola'),
(140, 'fenoarivo'),
(141, 'fiadanana'),
(142, 'ifarihy'),
(143, 'soamanandrariny'),
(144, 'mr james'),
(145, 'ambohintsoa'),
(146, 'ambohimirary'),
(147, 'forello tanjombato'),
(148, 'ambohijanaka'),
(149, 'mahavoky'),
(150, 'fasan\'ny karana'),
(151, '67 ha ouest'),
(152, '67ha'),
(153, 'ambatovinaky'),
(154, 'ambavahaditokana'),
(155, 'ambodiady'),
(156, 'amboniloha'),
(157, 'ambotrimanjaka'),
(158, 'ampandrianomby'),
(159, 'analamanga'),
(160, 'andranomanalina'),
(161, 'anjohy'),
(162, 'ankaditoho'),
(163, 'mascar'),
(164, 'petite merveille'),
(165, 'sakamanga'),
(166, 'admin aventures culinaires'),
(167, 'andohoranofotsy'),
(168, 'antanandrano'),
(169, '67 ha est'),
(170, 'betty\'s dinner'),
(171, 'maroho'),
(172, 'mosole'),
(173, 'ambohijanahary'),
(174, 'fort duchêne'),
(175, 'alakamisy'),
(176, 'ambohitrakely'),
(177, 'by pass'),
(178, 'aquamad'),
(179, 'ambatonilita'),
(180, 'ambohidratrimo'),
(181, 'ampasamadinika'),
(182, 'anjomakely rn7'),
(183, 'ankadilalapotsy'),
(184, 'lazaina'),
(185, 'marohoho'),
(186, 'saropody'),
(187, 'ampasika'),
(188, 'sahavola'),
(189, 'amb'),
(190, 'ilanivato'),
(191, 'tour sahavola');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quarter`
--
ALTER TABLE `quarter`
  ADD PRIMARY KEY (`entity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quarter`
--
ALTER TABLE `quarter`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
