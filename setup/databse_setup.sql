-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 16. Mai 2017 um 16:26
-- Server-Version: 10.1.23-MariaDB
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `jodel`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abuse`
--

CREATE TABLE `abuse` (
  `abuseID` int(11) NOT NULL,
  `abusedesc` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `colors`
--

CREATE TABLE `colors` (
  `colorID` int(11) NOT NULL,
  `colordesc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `colorhex` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comments`
--

CREATE TABLE `comments` (
  `commentID` int(11) NOT NULL,
  `jodlerIDFK` int(11) NOT NULL,
  `colorIDFK` int(11) NOT NULL,
  `jodelIDFK` int(11) NOT NULL,
  `comment` varchar(3000) COLLATE utf8_unicode_ci NOT NULL,
  `votes_cnt` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '50',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `commentvotes`
--

CREATE TABLE `commentvotes` (
  `voteID` int(11) NOT NULL,
  `jodlerIDFK` int(11) NOT NULL,
  `commentIDFK` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `jodeldata`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `jodeldata` (
`jodelID` int(11)
,`jodlerIDFK` int(11)
,`jodel` varchar(3000)
,`votes_cnt` int(11)
,`comments_cnt` int(11)
,`score` int(11)
,`createdate` timestamp
,`colorID` int(11)
,`colordesc` varchar(255)
,`colorhex` varchar(10)
,`account_state` int(11)
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `jodels`
--

CREATE TABLE `jodels` (
  `jodelID` int(11) NOT NULL,
  `jodlerIDFK` int(11) NOT NULL,
  `colorIDFK` int(11) NOT NULL,
  `jodel` varchar(3000) COLLATE utf8_unicode_ci NOT NULL,
  `votes_cnt` int(11) NOT NULL,
  `comments_cnt` int(11) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` int(11) NOT NULL DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `jodelvotes`
--

CREATE TABLE `jodelvotes` (
  `voteID` int(11) NOT NULL,
  `userIDFK` int(11) NOT NULL,
  `jodelIDFK` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `jodlers`
--

CREATE TABLE `jodlers` (
  `jodlerID` int(11) NOT NULL,
  `jodlerHRID` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `karma` int(11) NOT NULL DEFAULT '50',
  `account_state` int(11) NOT NULL DEFAULT '1',
  `passphrase` varchar(3000) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `moderated`
--

CREATE TABLE `moderated` (
  `modID` int(11) NOT NULL,
  `jodlerIDFK` int(11) NOT NULL,
  `jodelIDFK` int(11) DEFAULT NULL,
  `commentIDFK` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `reportdata`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `reportdata` (
`reportID` int(11)
,`commentIDFK` int(11)
,`jodelDFK` int(11)
,`jodlerIDFK` int(11)
,`abusedesc` varchar(255)
,`jodel` varchar(3000)
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports`
--

CREATE TABLE `reports` (
  `reportID` int(11) NOT NULL,
  `abuseIDFK` int(11) NOT NULL,
  `jodelDFK` int(11) DEFAULT NULL,
  `commentIDFK` int(11) DEFAULT NULL,
  `jodlerIDFK` int(11) NOT NULL COMMENT 'reportedBy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur des Views `jodeldata`
--
DROP TABLE IF EXISTS `jodeldata`;

CREATE ALGORITHM=UNDEFINED DEFINER=`username`@`localhost` SQL SECURITY DEFINER VIEW `jodeldata`  AS  select `jodels`.`jodelID` AS `jodelID`,`jodels`.`jodlerIDFK` AS `jodlerIDFK`,`jodels`.`jodel` AS `jodel`,`jodels`.`votes_cnt` AS `votes_cnt`,`jodels`.`comments_cnt` AS `comments_cnt`,`jodels`.`score` AS `score`,`jodels`.`createdate` AS `createdate`,`colors`.`colorID` AS `colorID`,`colors`.`colordesc` AS `colordesc`,`colors`.`colorhex` AS `colorhex`,`jodlers`.`account_state` AS `account_state` from ((`jodlers` left join `jodels` on((`jodels`.`jodlerIDFK` = `jodlers`.`jodlerID`))) left join `colors` on((`jodels`.`colorIDFK` = `colors`.`colorID`))) where (`jodels`.`jodelID` <> 'null') order by `jodels`.`jodelID` desc ;

-- --------------------------------------------------------

--
-- Struktur des Views `reportdata`
--
DROP TABLE IF EXISTS `reportdata`;

CREATE ALGORITHM=UNDEFINED DEFINER=`username`@`localhost` SQL SECURITY DEFINER VIEW `reportdata`  AS  select `reports`.`reportID` AS `reportID`,`reports`.`commentIDFK` AS `commentIDFK`,`reports`.`jodelDFK` AS `jodelDFK`,`reports`.`jodlerIDFK` AS `jodlerIDFK`,`abuse`.`abusedesc` AS `abusedesc`,`jodels`.`jodel` AS `jodel` from ((`jodels` left join `reports` on((`reports`.`jodelDFK` = `jodels`.`jodelID`))) left join `abuse` on((`reports`.`abuseIDFK` = `abuse`.`abuseID`))) where (`reports`.`reportID` <> 'null') ;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `abuse`
--
ALTER TABLE `abuse`
  ADD PRIMARY KEY (`abuseID`),
  ADD UNIQUE KEY `abusedesc` (`abusedesc`);

--
-- Indizes für die Tabelle `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`colorID`),
  ADD UNIQUE KEY `color` (`colordesc`),
  ADD UNIQUE KEY `colorhex` (`colorhex`);

--
-- Indizes für die Tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `jodlerIDFK` (`jodlerIDFK`),
  ADD KEY `colorIDFK` (`colorIDFK`),
  ADD KEY `jodelIDFK` (`jodelIDFK`);

--
-- Indizes für die Tabelle `commentvotes`
--
ALTER TABLE `commentvotes`
  ADD PRIMARY KEY (`voteID`),
  ADD KEY `jodlerIDFK` (`jodlerIDFK`),
  ADD KEY `commentID` (`commentIDFK`);

--
-- Indizes für die Tabelle `jodels`
--
ALTER TABLE `jodels`
  ADD PRIMARY KEY (`jodelID`),
  ADD KEY `jodlerIDFK` (`jodlerIDFK`),
  ADD KEY `colorIDFK` (`colorIDFK`);

--
-- Indizes für die Tabelle `jodelvotes`
--
ALTER TABLE `jodelvotes`
  ADD PRIMARY KEY (`voteID`),
  ADD KEY `userIDFK` (`userIDFK`),
  ADD KEY `jodelIDFK` (`jodelIDFK`);

--
-- Indizes für die Tabelle `jodlers`
--
ALTER TABLE `jodlers`
  ADD PRIMARY KEY (`jodlerID`),
  ADD UNIQUE KEY `username` (`jodlerHRID`);

--
-- Indizes für die Tabelle `moderated`
--
ALTER TABLE `moderated`
  ADD PRIMARY KEY (`modID`),
  ADD KEY `jodlerIDFK` (`jodlerIDFK`),
  ADD KEY `jodelIDFK` (`jodelIDFK`),
  ADD KEY `commentIDFK` (`commentIDFK`);

--
-- Indizes für die Tabelle `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `abuseIDFK` (`abuseIDFK`),
  ADD KEY `jodelIDFK` (`jodelDFK`),
  ADD KEY `reporter` (`jodlerIDFK`),
  ADD KEY `commentIDFK` (`commentIDFK`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `abuse`
--
ALTER TABLE `abuse`
  MODIFY `abuseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT für Tabelle `colors`
--
ALTER TABLE `colors`
  MODIFY `colorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT für Tabelle `commentvotes`
--
ALTER TABLE `commentvotes`
  MODIFY `voteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT für Tabelle `jodels`
--
ALTER TABLE `jodels`
  MODIFY `jodelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=553;
--
-- AUTO_INCREMENT für Tabelle `jodelvotes`
--
ALTER TABLE `jodelvotes`
  MODIFY `voteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;
--
-- AUTO_INCREMENT für Tabelle `jodlers`
--
ALTER TABLE `jodlers`
  MODIFY `jodlerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT für Tabelle `moderated`
--
ALTER TABLE `moderated`
  MODIFY `modID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `reports`
--
ALTER TABLE `reports`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`colorIDFK`) REFERENCES `colors` (`colorID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`jodelIDFK`) REFERENCES `jodels` (`jodelID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`jodlerIDFK`) REFERENCES `jodlers` (`jodlerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `commentvotes`
--
ALTER TABLE `commentvotes`
  ADD CONSTRAINT `commentvotes_ibfk_1` FOREIGN KEY (`commentIDFK`) REFERENCES `comments` (`commentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commentvotes_ibfk_2` FOREIGN KEY (`jodlerIDFK`) REFERENCES `jodlers` (`jodlerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `jodels`
--
ALTER TABLE `jodels`
  ADD CONSTRAINT `jodels_ibfk_1` FOREIGN KEY (`colorIDFK`) REFERENCES `colors` (`colorID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `jodels_ibfk_2` FOREIGN KEY (`jodlerIDFK`) REFERENCES `jodlers` (`jodlerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `jodelvotes`
--
ALTER TABLE `jodelvotes`
  ADD CONSTRAINT `jodelvotes_ibfk_1` FOREIGN KEY (`jodelIDFK`) REFERENCES `jodels` (`jodelID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jodelvotes_ibfk_2` FOREIGN KEY (`userIDFK`) REFERENCES `jodlers` (`jodlerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `moderated`
--
ALTER TABLE `moderated`
  ADD CONSTRAINT `moderated_ibfk_1` FOREIGN KEY (`jodlerIDFK`) REFERENCES `jodlers` (`jodlerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `moderated_ibfk_2` FOREIGN KEY (`jodelIDFK`) REFERENCES `jodels` (`jodelID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `moderated_ibfk_3` FOREIGN KEY (`commentIDFK`) REFERENCES `comments` (`commentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`abuseIDFK`) REFERENCES `abuse` (`abuseID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`jodlerIDFK`) REFERENCES `jodlers` (`jodlerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`jodelDFK`) REFERENCES `jodels` (`jodelID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`commentIDFK`) REFERENCES `comments` (`commentID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
