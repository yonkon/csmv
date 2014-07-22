-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 17 2014 г., 14:41
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
-- Структура таблицы `cs_clients`
--

CREATE TABLE IF NOT EXISTS `cs_clients` (
  `id_client` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fio` varchar(96) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `email` varchar(64) NOT NULL,
  `date_regisration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
