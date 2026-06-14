-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2026 at 03:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hmps`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `PolygonFromJSON` (`coords` LONGTEXT) RETURNS GEOMETRY DETERMINISTIC BEGIN
  DECLARE i INT DEFAULT 0;
  DECLARE len INT;
  DECLARE x VARCHAR(50);
  DECLARE y VARCHAR(50);
  DECLARE firstX VARCHAR(50);
  DECLARE firstY VARCHAR(50);
  DECLARE wkt LONGTEXT DEFAULT 'POLYGON((';

  -- get number of coordinate pairs
  SET len = JSON_LENGTH(coords);

  WHILE i < len DO
    SET x = JSON_UNQUOTE(JSON_EXTRACT(coords, CONCAT('$[', i, '][0]')));
    SET y = JSON_UNQUOTE(JSON_EXTRACT(coords, CONCAT('$[', i, '][1]')));

    IF i = 0 THEN
      SET firstX = x;
      SET firstY = y;
    END IF;

    SET wkt = CONCAT(wkt, x, ' ', y);

    IF i < len - 1 THEN
      SET wkt = CONCAT(wkt, ',');
    END IF;

    SET i = i + 1;
  END WHILE;

  -- close polygon by repeating first point
  SET wkt = CONCAT(wkt, ',', firstX, ' ', firstY, '))');

  RETURN ST_GeomFromText(wkt);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `area_polygon`
--

CREATE TABLE `area_polygon` (
  `polygon_id` int(11) NOT NULL,
  `coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`coordinates`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area_polygon`
--

INSERT INTO `area_polygon` (`polygon_id`, `coordinates`) VALUES
(2, '[[10.009247545817368,122.80108315640467],[10.009247545817368,122.80108315640467],[10.008296848633748,122.80170829091483],[10.008296848633748,122.80170829091483],[10.004863514544876,122.7997713617241],[10.005193395906517,122.79995618060528],[10.004863514544876,122.7997713617241],[10.001892536496513,122.79896378452428],[10.001892536496513,122.79896378452428],[10.000646038059584,122.79874109712365],[10.000646038059584,122.79874109712365],[10.000031988902549,122.80189637738351],[10.000031988902549,122.80189637738351],[9.999936916611919,122.80192052421009],[9.999936916611919,122.80192052421009],[9.99616833424546,122.80439691542416],[9.99616833424546,122.80439691542416],[9.998129897218465,122.80725276576399],[9.998129897218465,122.80725276576399],[9.99490004575485,122.8089349946819],[9.99490004575485,122.8089349946819],[9.994870995432546,122.80908524160282],[9.994870995432546,122.80908524160282],[10.000335536932644,122.810202920178],[10.000335536932644,122.810202920178],[10.000528322145358,122.81007682008367],[10.000528322145358,122.81007682008367],[10.00278436724901,122.81180222376668],[10.012402429394635,122.81393759373121],[10.012402429394635,122.81393759373121],[10.010882614182973,122.80673682794225],[10.010882614182973,122.80673682794225],[10.009842113786009,122.80201271804087],[10.009842113786009,122.80201271804087],[10.009242597695884,122.80107825655935]]');

-- --------------------------------------------------------

--
-- Table structure for table `category_area`
--

CREATE TABLE `category_area` (
  `category_area_id` int(11) NOT NULL,
  `polygon_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `category_level_id` int(11) NOT NULL,
  `category_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `boundary` polygon NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `category_area`
--
DELIMITER $$
CREATE TRIGGER `category_area_boundary_insert` BEFORE INSERT ON `category_area` FOR EACH ROW BEGIN
  SET NEW.boundary = PolygonFromJSON(NEW.category_coordinates);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `category_area_boundary_update` BEFORE UPDATE ON `category_area` FOR EACH ROW BEGIN
  SET NEW.boundary = PolygonFromJSON(NEW.category_coordinates);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category_list_level`
--

CREATE TABLE `category_list_level` (
  `category_level_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `category_level_name` varchar(50) NOT NULL,
  `category_level_color` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_name`
--

CREATE TABLE `category_name` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `household_list`
--

CREATE TABLE `household_list` (
  `houshold_id` char(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `household_coord` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `household_number` varchar(20) NOT NULL,
  `purok_sitio_id` int(11) NOT NULL,
  `location` point GENERATED ALWAYS AS (st_geometryfromtext(concat('POINT(',substring_index(`household_coord`,',',-1),' ',substring_index(`household_coord`,',',1),')'))) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `household_member_list`
--

CREATE TABLE `household_member_list` (
  `household_member_id` int(11) NOT NULL,
  `household_number` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `person_unique_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `family_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `individual_records_list`
--

CREATE TABLE `individual_records_list` (
  `person_unique_id` char(36) NOT NULL,
  `preson_id` int(11) NOT NULL,
  `phil_sys_id` varchar(100) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(3) DEFAULT NULL,
  `birdthdate` date NOT NULL,
  `birth_place` varchar(150) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `religion` varchar(50) NOT NULL,
  `residential_address` varchar(100) NOT NULL,
  `citizenship` varchar(50) NOT NULL,
  `profession` varchar(50) DEFAULT NULL,
  `contact_no` varchar(16) DEFAULT NULL,
  `email_address` varchar(250) DEFAULT NULL,
  `highest_attainment_education` varchar(50) NOT NULL,
  `highest_attainment_education_specific` varchar(50) NOT NULL,
  `type_of_disability` varchar(150) NOT NULL,
  `type_of_disability_others` varchar(150) DEFAULT NULL,
  `date_encoded` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purok_sitio_list`
--

CREATE TABLE `purok_sitio_list` (
  `purok_sitio_id` int(11) NOT NULL,
  `purok_sitio_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `area_polygon`
--
ALTER TABLE `area_polygon`
  ADD PRIMARY KEY (`polygon_id`);

--
-- Indexes for table `category_area`
--
ALTER TABLE `category_area`
  ADD PRIMARY KEY (`category_area_id`);

--
-- Indexes for table `category_list_level`
--
ALTER TABLE `category_list_level`
  ADD PRIMARY KEY (`category_level_id`);

--
-- Indexes for table `category_name`
--
ALTER TABLE `category_name`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `household_list`
--
ALTER TABLE `household_list`
  ADD PRIMARY KEY (`houshold_id`);

--
-- Indexes for table `household_member_list`
--
ALTER TABLE `household_member_list`
  ADD PRIMARY KEY (`household_member_id`);

--
-- Indexes for table `individual_records_list`
--
ALTER TABLE `individual_records_list`
  ADD PRIMARY KEY (`person_unique_id`),
  ADD UNIQUE KEY `preson_id` (`preson_id`);

--
-- Indexes for table `purok_sitio_list`
--
ALTER TABLE `purok_sitio_list`
  ADD PRIMARY KEY (`purok_sitio_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `area_polygon`
--
ALTER TABLE `area_polygon`
  MODIFY `polygon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category_area`
--
ALTER TABLE `category_area`
  MODIFY `category_area_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category_list_level`
--
ALTER TABLE `category_list_level`
  MODIFY `category_level_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category_name`
--
ALTER TABLE `category_name`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `household_member_list`
--
ALTER TABLE `household_member_list`
  MODIFY `household_member_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `individual_records_list`
--
ALTER TABLE `individual_records_list`
  MODIFY `preson_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purok_sitio_list`
--
ALTER TABLE `purok_sitio_list`
  MODIFY `purok_sitio_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
