-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.4.11-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win64
-- HeidiSQL Version:             10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für dba201_fallstudie
CREATE DATABASE IF NOT EXISTS `dba201_fallstudie` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `dba201_fallstudie`;

-- Exportiere Struktur von Tabelle dba201_fallstudie.anhaenge
CREATE TABLE IF NOT EXISTS `anhaenge` (
  `AnhangID` int(11) NOT NULL AUTO_INCREMENT,
  `Dateiname` varchar(255) NOT NULL,
  `Groesse` bigint(20) NOT NULL,
  `Typ` varchar(10) NOT NULL,
  `BeitragsID` int(11) NOT NULL,
  PRIMARY KEY (`AnhangID`),
  KEY `FK_anhaenge_beitraege` (`BeitragsID`),
  CONSTRAINT `FK_anhaenge_beitraege` FOREIGN KEY (`BeitragsID`) REFERENCES `beitraege` (`BeitragsID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle dba201_fallstudie.bearbeitungen
CREATE TABLE IF NOT EXISTS `bearbeitungen` (
  `BearbID` int(11) NOT NULL AUTO_INCREMENT,
  `Datum` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Grund` varchar(150) NOT NULL,
  `BenutzerID` int(11) DEFAULT NULL,
  PRIMARY KEY (`BearbID`),
  KEY `FK_bearbeitungen_benutzer` (`BenutzerID`),
  CONSTRAINT `FK_bearbeitungen_benutzer` FOREIGN KEY (`BenutzerID`) REFERENCES `benutzer` (`BenutzerID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle dba201_fallstudie.beitraege
CREATE TABLE IF NOT EXISTS `beitraege` (
  `BeitragsID` int(11) NOT NULL AUTO_INCREMENT,
  `Ueberschrift` varchar(50) NOT NULL,
  `Beitrag` mediumtext NOT NULL DEFAULT '',
  `Erstellungsdatum` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `BearbID` int(11) DEFAULT NULL,
  `ErstellerID` int(11) DEFAULT NULL,
  `ThemenID` int(11) NOT NULL,
  PRIMARY KEY (`BeitragsID`),
  KEY `FK_beitraege_bearbeitungen` (`BearbID`),
  KEY `FK_beitraege_benutzer` (`ErstellerID`),
  KEY `FK_beitraege_themen` (`ThemenID`),
  CONSTRAINT `FK_beitraege_bearbeitungen` FOREIGN KEY (`BearbID`) REFERENCES `bearbeitungen` (`BearbID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_beitraege_benutzer` FOREIGN KEY (`ErstellerID`) REFERENCES `benutzer` (`BenutzerID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_beitraege_themen` FOREIGN KEY (`ThemenID`) REFERENCES `themen` (`ThemenID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle dba201_fallstudie.benutzer
CREATE TABLE IF NOT EXISTS `benutzer` (
  `BenutzerID` int(11) NOT NULL AUTO_INCREMENT,
  `Nachname` varchar(50) NOT NULL DEFAULT '0',
  `Vorname` varchar(50) NOT NULL DEFAULT '0',
  `E-Mail` varchar(100) NOT NULL DEFAULT '0',
  `Registrierungsdatum` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Bild` blob DEFAULT NULL,
  `Benutzername` varchar(20) NOT NULL,
  `RechtID` int(11) NOT NULL,
  PRIMARY KEY (`BenutzerID`),
  KEY `FK_benutzer_login` (`Benutzername`),
  KEY `FK_benutzer_rechtegruppen` (`RechtID`),
  CONSTRAINT `FK_benutzer_login` FOREIGN KEY (`Benutzername`) REFERENCES `login` (`Benutzername`) ON UPDATE CASCADE,
  CONSTRAINT `FK_benutzer_rechtegruppen` FOREIGN KEY (`RechtID`) REFERENCES `rechtegruppen` (`RechtID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle dba201_fallstudie.kategorien
CREATE TABLE IF NOT EXISTS `kategorien` (
  `KatID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Bild` blob DEFAULT NULL,
  `Oberkategorie` varchar(50) NOT NULL,
  `Ebene` tinyint(3) unsigned NOT NULL,
  `Anzahl_Themen` int(10) unsigned NOT NULL,
  `Anzahl_Beitraege` int(10) unsigned NOT NULL,
  `Letzter_Beitrag` int(11) DEFAULT NULL,
  `ErstellerID` int(11) DEFAULT NULL,
  `Uebergeord_KatID` int(11) DEFAULT NULL,
  PRIMARY KEY (`KatID`),
  KEY `FK_kategorien_beitraege` (`Letzter_Beitrag`),
  KEY `FK_kategorien_benutzer` (`ErstellerID`),
  KEY `FK_kategorien_kategorien` (`Uebergeord_KatID`),
  CONSTRAINT `FK_kategorien_beitraege` FOREIGN KEY (`Letzter_Beitrag`) REFERENCES `beitraege` (`BeitragsID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_kategorien_benutzer` FOREIGN KEY (`ErstellerID`) REFERENCES `benutzer` (`BenutzerID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_kategorien_kategorien` FOREIGN KEY (`Uebergeord_KatID`) REFERENCES `kategorien` (`KatID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle dba201_fallstudie.login
CREATE TABLE IF NOT EXISTS `login` (
  `Benutzername` varchar(20) NOT NULL,
  `Passwort` varchar(30) NOT NULL,
  PRIMARY KEY (`Benutzername`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle dba201_fallstudie.rechtegruppen
CREATE TABLE IF NOT EXISTS `rechtegruppen` (
  `RechtID` int(11) NOT NULL AUTO_INCREMENT,
  `Rechtegruppenname` varchar(30) NOT NULL,
  `Beitrag_lesen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Beitrag_erstellen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Beitrag_bearbeiten` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Beitrag_bearbeiten_alle` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Beitrag_loeschen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Beitrag_loeschen_alle` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Thema_lesen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Thema_erstellen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Thema_bearbeiten` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Thema_loeschen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Themen_schließen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Themen_anpinnen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Kategorie_lesen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Kategorie_erstellen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Kategorie_bearbeiten` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Kategorie_loeschen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Benutzer_anzeigen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Benutzer_erstellen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Benutzer_bearbeiten` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Benutzer_loeschen` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `Rechte_bearbeiten` tinyint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`RechtID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle dba201_fallstudie.themen
CREATE TABLE IF NOT EXISTS `themen` (
  `ThemenID` int(11) NOT NULL AUTO_INCREMENT,
  `Thema_geschlossen` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `Thema_angepinnt` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `Anzahl_Antworten` int(10) unsigned NOT NULL DEFAULT 0,
  `Anzahl_Ansichten` int(10) unsigned NOT NULL DEFAULT 0,
  `Erster_Beitrag` int(11) DEFAULT NULL,
  `Letzter_Beitrag` int(11) DEFAULT NULL,
  `ErstellerID` int(11) DEFAULT NULL,
  `KatID` int(11) NOT NULL,
  PRIMARY KEY (`ThemenID`),
  KEY `FK_themen_beitraege` (`Erster_Beitrag`),
  KEY `FK_themen_beitraege_2` (`Letzter_Beitrag`),
  KEY `FK_themen_benutzer` (`ErstellerID`),
  KEY `FK_themen_kategorien` (`KatID`),
  CONSTRAINT `FK_themen_beitraege` FOREIGN KEY (`Erster_Beitrag`) REFERENCES `beitraege` (`BeitragsID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_themen_beitraege_2` FOREIGN KEY (`Letzter_Beitrag`) REFERENCES `beitraege` (`BeitragsID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_themen_benutzer` FOREIGN KEY (`ErstellerID`) REFERENCES `benutzer` (`BenutzerID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_themen_kategorien` FOREIGN KEY (`KatID`) REFERENCES `kategorien` (`KatID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
