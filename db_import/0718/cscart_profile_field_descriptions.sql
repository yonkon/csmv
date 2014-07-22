-- phpMyAdmin SQL Dump
-- version 4.2.0-rc1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 18 2014 г., 21:51
-- Версия сервера: 5.5.37-0ubuntu0.14.04.1
-- Версия PHP: 5.5.9-1ubuntu4.2

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
-- Структура таблицы `cscart_profile_field_descriptions`
--

CREATE TABLE IF NOT EXISTS `cscart_profile_field_descriptions` (
  `object_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `object_type` char(1) NOT NULL DEFAULT 'F',
  `lang_code` char(2) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cscart_profile_field_descriptions`
--

INSERT INTO `cscart_profile_field_descriptions` (`object_id`, `description`, `object_type`, `lang_code`) VALUES
(6, 'Имя', 'F', 'ru'),
(7, 'Фамилия', 'F', 'ru'),
(8, 'Компания', 'F', 'ru'),
(9, 'Телефон', 'F', 'ru'),
(10, 'Факс', 'F', 'ru'),
(11, 'Веб-сайт', 'F', 'ru'),
(14, 'Имя', 'F', 'ru'),
(15, 'Имя', 'F', 'ru'),
(16, 'Фамилия', 'F', 'ru'),
(17, 'Фамилия', 'F', 'ru'),
(18, 'Адрес', 'F', 'ru'),
(19, 'Адрес', 'F', 'ru'),
(20, 'Адрес (строка 2)', 'F', 'ru'),
(21, 'Адрес (строка 2)', 'F', 'ru'),
(22, 'Город', 'F', 'ru'),
(23, 'Город', 'F', 'ru'),
(24, 'Область/район', 'F', 'ru'),
(25, 'Область/район', 'F', 'ru'),
(26, 'Страна', 'F', 'ru'),
(27, 'Страна', 'F', 'ru'),
(28, 'Почтовый индекс', 'F', 'ru'),
(29, 'Почтовый индекс', 'F', 'ru'),
(30, 'Телефон', 'F', 'ru'),
(31, 'Телефон', 'F', 'ru'),
(32, 'E-mail', 'F', 'ru'),
(33, 'E-mail', 'F', 'ru'),
(35, 'Тип адреса', 'F', 'ru'),
(5, 'Обращение', 'F', 'ru'),
(12, 'Обращение', 'F', 'ru'),
(13, 'Обращение', 'F', 'ru'),
(6, 'First name', 'F', 'en'),
(7, 'Last name', 'F', 'en'),
(8, 'Company', 'F', 'en'),
(9, 'Phone', 'F', 'en'),
(10, 'Fax', 'F', 'en'),
(11, 'URL', 'F', 'en'),
(14, 'First name', 'F', 'en'),
(15, 'First name', 'F', 'en'),
(16, 'Last name', 'F', 'en'),
(17, 'Last name', 'F', 'en'),
(18, 'Address', 'F', 'en'),
(19, 'Address', 'F', 'en'),
(20, 'Address', 'F', 'en'),
(21, 'Address', 'F', 'en'),
(22, 'City', 'F', 'en'),
(23, 'City', 'F', 'en'),
(24, 'State/province', 'F', 'en'),
(25, 'State/province', 'F', 'en'),
(26, 'Country', 'F', 'en'),
(27, 'Country', 'F', 'en'),
(28, 'Zip/postal code', 'F', 'en'),
(29, 'Zip/postal code', 'F', 'en'),
(30, 'Phone', 'F', 'en'),
(31, 'Phone', 'F', 'en'),
(32, 'E-mail', 'F', 'en'),
(33, 'E-mail', 'F', 'en'),
(35, 'Address type', 'F', 'en'),
(5, 'Обращение', 'F', 'en'),
(12, 'Обращение', 'F', 'en'),
(13, 'Обращение', 'F', 'en'),
(36, 'Отчество', 'F', 'ru'),
(36, 'Отчество', 'F', 'en'),
(37, 'E-mail', 'F', 'ru'),
(37, 'Email', 'F', 'en'),
(38, 'Ваш город', 'F', 'ru'),
(38, 'Ваш город', 'F', 'en'),
(39, 'ID Куратора', 'F', 'ru'),
(39, 'ID Куратора', 'F', 'en'),
(40, 'Логин', 'F', 'ru'),
(40, 'ID Куратора', 'F', 'en'),
(41, 'Пароль', 'F', 'ru'),
(41, 'ID Куратора', 'F', 'en'),
(42, 'ID Агентского договора', 'F', 'ru'),
(42, 'ID Агентского договора', 'F', 'en'),
(43, 'Статус', 'F', 'ru'),
(43, 'Статус', 'F', 'en'),
(44, 'Статус', 'F', 'ru'),
(44, 'Статус', 'F', 'en'),
(45, 'E-mail', 'F', 'ru'),
(45, 'E-mail', 'F', 'en');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cscart_profile_field_descriptions`
--
ALTER TABLE `cscart_profile_field_descriptions`
 ADD PRIMARY KEY (`object_id`,`object_type`,`lang_code`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
