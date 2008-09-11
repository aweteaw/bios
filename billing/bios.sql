-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2008 at 02:38 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.4

--
-- Database: `bios`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `client_id` mediumint(9) NOT NULL auto_increment,
  `client_ip` varchar(15) default NULL,
  `client_name` varchar(150) NOT NULL default '',
  `client_login` varchar(150) NOT NULL default 'guest',
  `client_status` tinyint(3) unsigned default '0',
  `client_start` datetime default NULL,
  `client_end` datetime default NULL,
  `client_desktop` varchar(100) NOT NULL default 'linux_general',
  PRIMARY KEY  (`client_id`),
  UNIQUE KEY `client_ip` (`client_ip`)
) TYPE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `client_ip`, `client_name`, `client_login`, `client_status`, `client_start`, `client_end`, `client_desktop`) VALUES
(1, NULL, '*Non Internet', 'guest', 0, NULL, NULL, 'linux_general'),
(2, '192.168.1.16', 'Client 01', 'guest', 0, NULL, NULL, 'mandriva');

-- --------------------------------------------------------

--
-- Table structure for table `client_status`
--

DROP TABLE IF EXISTS `client_status`;
CREATE TABLE IF NOT EXISTS `client_status` (
  `client_status_id` mediumint(8) unsigned NOT NULL auto_increment,
  `client_status_old` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`client_status_id`)
) TYPE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Dumping data for table `client_status`
--

INSERT INTO `client_status` (`client_status_id`, `client_status_old`) VALUES
(1, 0),
(2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `client_tambahan`
--

DROP TABLE IF EXISTS `client_tambahan`;
CREATE TABLE IF NOT EXISTS `client_tambahan` (
  `ct_id` int(10) unsigned NOT NULL auto_increment,
  `ct_laporan_id` int(8) unsigned default NULL,
  `ct_produk_id` mediumint(8) unsigned default NULL,
  `ct_jumlah` mediumint(8) unsigned default NULL,
  `ct_harga` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`ct_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `client_tambahan`
--


-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

DROP TABLE IF EXISTS `laporan`;
CREATE TABLE IF NOT EXISTS `laporan` (
  `laporan_id` int(10) unsigned NOT NULL auto_increment,
  `laporan_client` mediumint(8) unsigned default NULL,
  `laporan_start` datetime default NULL,
  `laporan_end` datetime default NULL,
  `laporan_durasi` int(10) unsigned default NULL,
  `laporan_biaya` int(10) unsigned default NULL,
  `laporan_catatan` tinytext,
  `laporan_operator` varchar(100) default NULL,
  PRIMARY KEY  (`laporan_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

--
-- Dumping data for table `laporan`
--


-- --------------------------------------------------------

--
-- Table structure for table `operator`
--

DROP TABLE IF EXISTS `operator`;
CREATE TABLE IF NOT EXISTS `operator` (
  `operator_id` mediumint(8) unsigned NOT NULL auto_increment,
  `operator_name` varchar(100) default NULL,
  `operator_name_full` varchar(255) NOT NULL default 'Sang Penguasa Yang Murah Hati',
  `operator_password` varchar(32) default NULL,
  `operator_last_ip` varchar(15) default NULL,
  `operator_last_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `operator_edit_report` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`operator_id`),
  UNIQUE KEY `operator_name` (`operator_name`)
) TYPE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Dumping data for table `operator`
--

INSERT INTO `operator` (`operator_id`, `operator_name`, `operator_name_full`, `operator_password`, `operator_last_ip`, `operator_last_date`, `operator_edit_report`) VALUES
(1, 'admin', 'Sang Penguasa Yang Murah Hati', 'b93939873fd4923043b9dec975811f66', NULL, '0000-00-00 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

DROP TABLE IF EXISTS `produk`;
CREATE TABLE IF NOT EXISTS `produk` (
  `produk_id` mediumint(9) NOT NULL auto_increment,
  `produk_tanggal` datetime NOT NULL,
  `produk_nama` varchar(100) default NULL,
  `produk_harga` mediumint(8) unsigned default NULL,
  `produk_stok` mediumint(8) NOT NULL default '1',
  `produk_show` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`produk_id`)
) TYPE=MyISAM  AUTO_INCREMENT=4 ;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`produk_id`, `produk_tanggal`, `produk_nama`, `produk_harga`, `produk_stok`, `produk_show`) VALUES
(1, '2008-06-23 15:55:20', 'Print Warna', 1000, 100, 1),
(2, '2008-06-23 15:55:15', 'Print B/W', 500, 100, 1),
(3, '2008-06-24 02:37:16', 'Teh Botol', 2000, 100, 1);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `uid` varchar(100) default NULL,
  `sid` varchar(32) default NULL
) TYPE=MyISAM;

--
-- Dumping data for table `session`
--


-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
CREATE TABLE IF NOT EXISTS `setting` (
  `setting_refresh` tinyint(4) NOT NULL default '1',
  `setting_screenshot_status` tinyint(4) default NULL,
  `setting_screenshot_width` mediumint(9) NOT NULL default '640',
  `setting_screenshot_height` mediumint(9) NOT NULL default '480',
  `setting_screenshot_name` varchar(255) NOT NULL default '.screen.jpg',
  `setting_screenshot_folder` varchar(255) NOT NULL default '.bios',
  `setting_operator_operating_system` char(3) NOT NULL default 'lin',
  `setting_price_every_second` mediumint(9) NOT NULL default '2',
  `setting_receh` mediumint(9) unsigned NOT NULL default '100',
  `setting_status_old` tinyint(4) NOT NULL default '5',
  `setting_refresh_tmp` mediumtext,
  `setting_cafe_name` varchar(255) NOT NULL default 'Warnet Linux',
  `setting_cafe_address` varchar(255) default NULL,
  `setting_screenshot_protocol` varchar(100) default NULL,
  `setting_domain_operator` tinytext,
  `setting_motd` varchar(255) default NULL,
  `setting_error_reporting` tinyint(1) NOT NULL default '1',
  `setting_timezone` char(4) NOT NULL default '+7',
  `setting_layout` varchar(15) NOT NULL default 'standard'
) TYPE=MyISAM;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`setting_refresh`, `setting_screenshot_status`, `setting_screenshot_width`, `setting_screenshot_height`, `setting_screenshot_name`, `setting_screenshot_folder`, `setting_operator_operating_system`, `setting_price_every_second`, `setting_receh`, `setting_status_old`, `setting_refresh_tmp`, `setting_cafe_name`, `setting_cafe_address`, `setting_screenshot_protocol`, `setting_domain_operator`, `setting_motd`, `setting_error_reporting`, `setting_timezone`, `setting_layout`) VALUES
(1, 1, 800, 600, 'bios.jpg', 'tes', 'lin', 1, 1, 1, NULL, 'Warnet Linux', 'Jl. Letda Retta 39 Denpasar, Bali', '', '', 'Selamat datang di warnet kami!', 1, '+7', 'standard');

-- --------------------------------------------------------

--
-- Table structure for table `tarif`
--

DROP TABLE IF EXISTS `tarif`;
CREATE TABLE IF NOT EXISTS `tarif` (
  `tarif_pkl` tinyint(3) unsigned NOT NULL,
  `tarif_perjam` mediumint(8) unsigned default NULL,
  `tarif_min` mediumint(8) unsigned default NULL,
  `tarif_min_durasi` mediumint(8) unsigned default NULL,
  UNIQUE KEY `tarif_pkl` (`tarif_pkl`)
) TYPE=MyISAM;

--
-- Dumping data for table `tarif`
--

INSERT INTO `tarif` (`tarif_pkl`, `tarif_perjam`, `tarif_min`, `tarif_min_durasi`) VALUES
(0, 3000, 1000, 10),
(1, 3000, 1000, 10),
(2, 3000, 1000, 10),
(3, 3000, 1000, 10),
(4, 3000, 1000, 10),
(5, 3000, 1000, 10),
(6, 3000, 1000, 10),
(7, 3000, 1000, 10),
(8, 3000, 1000, 10),
(9, 3000, 1000, 10),
(10, 3000, 1000, 10),
(11, 3000, 1000, 10),
(12, 3000, 1000, 10),
(13, 3000, 1000, 10),
(14, 3000, 1000, 10),
(15, 3000, 1000, 10),
(16, 3000, 1000, 10),
(17, 3000, 1000, 10),
(18, 3000, 1000, 10),
(19, 3000, 1000, 10),
(20, 3000, 1000, 10),
(21, 3000, 1000, 10),
(22, 3000, 1000, 10),
(23, 3000, 1000, 10);
