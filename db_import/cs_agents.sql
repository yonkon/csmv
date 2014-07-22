-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 17 2014 г., 10:16
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
-- Структура таблицы `cs_agents`
--

CREATE TABLE IF NOT EXISTS `cs_agents` (
  `id_agent` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `surname` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `midname` varchar(32) NOT NULL,
  `town` int(11) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `super_agent` int(10) unsigned DEFAULT NULL,
  `email` varchar(64) NOT NULL,
  `contract_id` varchar(32) DEFAULT NULL,
  `password` varchar(32) NOT NULL,
  `login` varchar(32) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
