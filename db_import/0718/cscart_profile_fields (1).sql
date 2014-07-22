-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 18 2014 г., 23:37
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
-- Структура таблицы `cscart_profile_fields`
--

CREATE TABLE IF NOT EXISTS `cscart_profile_fields` (
  `field_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(32) NOT NULL DEFAULT '',
  `profile_show` char(1) DEFAULT 'N',
  `profile_required` char(1) DEFAULT 'N',
  `checkout_show` char(1) DEFAULT 'N',
  `checkout_required` char(1) DEFAULT 'N',
  `partner_show` char(1) DEFAULT 'N',
  `partner_required` char(1) DEFAULT 'N',
  `field_type` char(1) NOT NULL DEFAULT 'I',
  `position` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_default` char(1) DEFAULT 'N',
  `section` char(1) DEFAULT 'C',
  `matching_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `class` varchar(100) NOT NULL DEFAULT '',
  `autocomplete_type` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`field_id`),
  KEY `field_name` (`field_name`),
  KEY `checkout_show` (`checkout_show`,`field_type`),
  KEY `profile_show` (`profile_show`,`field_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

--
-- Дамп данных таблицы `cscart_profile_fields`
--

INSERT INTO `cscart_profile_fields` (`field_id`, `field_name`, `profile_show`, `profile_required`, `checkout_show`, `checkout_required`, `partner_show`, `partner_required`, `field_type`, `position`, `is_default`, `section`, `matching_id`, `class`, `autocomplete_type`) VALUES
(6, 'firstname', 'Y', 'Y', 'N', 'N', 'N', 'N', 'I', 20, 'Y', 'C', 0, 'first-name2', 'given-name'),
(7, 'lastname', 'Y', 'Y', 'N', 'N', 'N', 'N', 'I', 30, 'Y', 'C', 0, 'last-name2', 'surname'),
(8, 'company', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 40, 'Y', 'C', 0, 'company', 'organization'),
(9, 'phone', 'Y', 'Y', 'N', 'N', 'N', 'N', 'I', 50, 'Y', 'C', 0, 'phone', 'phone-full'),
(10, 'fax', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 60, 'Y', 'C', 0, 'fax', 'fax-full'),
(11, 'url', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 70, 'Y', 'C', 0, 'url', 'url'),
(14, 'b_firstname', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'I', 90, 'Y', 'B', 15, 'billing-first-name', 'given-name'),
(16, 'b_lastname', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'I', 100, 'Y', 'B', 17, 'billing-last-name', 'surname'),
(32, 'email', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'E', 105, 'Y', 'B', 33, 'billing-email', 'email'),
(18, 'b_address', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'I', 110, 'Y', 'B', 19, 'billing-address', 'street-address'),
(20, 'b_address_2', 'Y', 'N', 'Y', 'N', 'Y', 'N', 'I', 120, 'Y', 'B', 21, 'billing-address-line2', 'address-line2'),
(22, 'b_city', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'I', 130, 'Y', 'B', 23, 'billing-city', 'city'),
(24, 'b_state', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'A', 150, 'Y', 'B', 25, 'billing-state', 'state'),
(26, 'b_country', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'O', 140, 'Y', 'B', 27, 'billing-country', 'country'),
(28, 'b_zipcode', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'Z', 160, 'Y', 'B', 29, 'billing-zip-code', 'postal-code'),
(30, 'b_phone', 'Y', 'N', 'Y', 'N', 'Y', 'N', 'I', 107, 'Y', 'B', 31, 'billing-phone', 'phone-full'),
(15, 's_firstname', 'Y', 'N', 'Y', 'Y', 'N', 'N', 'I', 90, 'Y', 'S', 14, 'shipping-first-name', 'given-name'),
(17, 's_lastname', 'Y', 'N', 'Y', 'Y', 'N', 'N', 'I', 100, 'Y', 'S', 16, 'shipping-last-name', 'surname'),
(33, 'email', 'N', 'Y', 'N', 'Y', 'N', 'Y', 'E', 105, 'Y', 'S', 32, 'shipping-email', 'email'),
(19, 's_address', 'Y', 'N', 'Y', 'Y', 'N', 'N', 'I', 110, 'Y', 'S', 18, 'shipping-address', 'street-address'),
(21, 's_address_2', 'Y', 'N', 'Y', 'N', 'N', 'N', 'I', 120, 'Y', 'S', 20, 'shipping-address-line2', 'address-line2'),
(23, 's_city', 'Y', 'N', 'Y', 'Y', 'N', 'N', 'I', 130, 'Y', 'S', 22, 'shipping-city', 'city'),
(27, 's_country', 'Y', 'N', 'Y', 'Y', 'N', 'N', 'O', 140, 'Y', 'S', 26, 'shipping-country', 'country'),
(25, 's_state', 'Y', 'N', 'Y', 'Y', 'N', 'N', 'A', 150, 'Y', 'S', 24, 'shipping-state', 'state'),
(29, 's_zipcode', 'Y', 'N', 'Y', 'Y', 'N', 'N', 'Z', 160, 'Y', 'S', 28, 'shipping-zip-code', 'postal-code'),
(31, 's_phone', 'Y', 'N', 'Y', 'N', 'N', 'N', 'I', 107, 'Y', 'S', 30, 'shipping-phone', 'phone-full'),
(35, 's_address_type', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 180, 'Y', 'S', 34, 'shipping-address-type', ''),
(36, 'midname', 'Y', 'Y', 'N', 'N', 'N', 'N', 'I', 35, 'N', 'C', 0, 'mid-name2', ''),
(37, 'email', 'Y', 'Y', 'N', 'N', 'N', 'N', 'I', 56, 'N', 'C', 0, 'email', ''),
(38, 'city', 'Y', 'Y', 'N', 'N', 'N', 'N', 'I', 55, 'N', 'C', 0, 'city', ''),
(39, 'curator', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 99, 'N', 'C', 0, 'hidden', ''),
(40, 'login', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 99, 'N', 'C', 0, 'hidden', ''),
(41, 'password', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 99, 'N', 'C', 0, 'hidden', ''),
(42, 'contract_id', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 98, 'N', 'C', 0, 'hidden', ''),
(43, 'status', 'N', 'N', 'N', 'N', 'N', 'N', 'I', 2, 'N', 'C', 0, 'hidden', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
