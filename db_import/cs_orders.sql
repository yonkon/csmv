-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 17 2014 г., 17:01
-- Версия сервера: 5.6.16
-- Версия PHP: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `cs`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cs_orders`
--

CREATE TABLE IF NOT EXISTS `cs_orders` (
  `id_order` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `fio` varchar(96) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `date_registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_order` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_company` int(10) unsigned NOT NULL,
  `id_region` int(10) unsigned NOT NULL,
  `id_office` int(10) unsigned NOT NULL,
  `need_delivery` tinyint(4) NOT NULL DEFAULT '0',
  `comment` text,
  `statuc` tinyint(4) NOT NULL DEFAULT '0',
  `client_email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
