-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 17 2014 г., 10:17
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
-- Структура таблицы `cs_products`
--

CREATE TABLE IF NOT EXISTS `cs_products` (
  `id_product` int(10) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text,
  `cost` int(10) unsigned NOT NULL,
  `currency_id` int(11) NOT NULL,
  `agent_fee` float NOT NULL,
  `agent_fee_currency` int(11) NOT NULL,
  `agent_fee_in_percents` tinyint(4) NOT NULL DEFAULT '0',
  `subagent_fee` float NOT NULL,
  `subagent_fee_currency` int(11) NOT NULL,
  `subagent_fee_in_percents` tinyint(4) NOT NULL DEFAULT '0',
  `site_contract_percent` tinyint(4) NOT NULL,
  `product_image` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
