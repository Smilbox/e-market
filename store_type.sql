-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 20, 2020 at 01:51 PM
-- Server version: 5.7.28-0ubuntu0.16.04.2
-- PHP Version: 7.0.33-13+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `esakafo`
--

-- --------------------------------------------------------

--
-- Table structure for table `store_type`
--

CREATE TABLE `store_type` (
  `entity_id` int(11) NOT NULL,
  `name_fr` varchar(25) NOT NULL,
  `name_en` varchar(25) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `store_type`
--

INSERT INTO `store_type` (`entity_id`, `name_fr`, `name_en`, `updated_by`, `updated_date`, `created_by`, `created_date`) VALUES
(1, 'Restaurants', 'Restaurants', NULL, NULL, NULL, '2020-05-19 10:18:03'),
(2, 'Telma', 'Telma', NULL, NULL, NULL, '2020-05-19 12:02:39'),
(3, 'Vêtements et Chaussures', 'Clothes and Shoes', NULL, NULL, NULL, '2020-05-19 12:09:34'),
(4, 'Beauté et Santé', 'Beauty and Health', NULL, NULL, NULL, '2020-05-19 12:11:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `store_type`
--
ALTER TABLE `store_type`
  ADD PRIMARY KEY (`entity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `store_type`
--
ALTER TABLE `store_type`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
