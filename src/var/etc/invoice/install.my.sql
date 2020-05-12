-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.5.27 - MySQL Community Server (GPL)
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Struktur von Tabelle liebesex.invoice
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `invoice_number` varchar(64) NOT NULL,
  `invoice_date` datetime NOT NULL,
  `status` enum('unpaid','paid','escalated','charged_off') NOT NULL,
  `num_remindings` int(10) unsigned NOT NULL DEFAULT '0',
  `last_reminded` datetime DEFAULT NULL,
  `text_html` text NOT NULL,
  `currency` varchar(10) NOT NULL,
  `file_pdf` varchar(128) DEFAULT NULL,
  `tax_included` tinyint(1) unsigned NOT NULL,
  `address_id` int(10) unsigned NOT NULL,
  `locale` varchar(8) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle liebesex.invoice_address
CREATE TABLE IF NOT EXISTS `invoice_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  `phone` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle liebesex.invoice_item
CREATE TABLE IF NOT EXISTS `invoice_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  `unit_price` decimal(10,2) unsigned NOT NULL,
  `tax_rate` decimal(2,2) unsigned NOT NULL,
  `invoice_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
