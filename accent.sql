-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Апр 30 2013 г., 16:47
-- Версия сервера: 5.5.25
-- Версия PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `accent`
--

-- --------------------------------------------------------

--
-- Структура таблицы `blocks`
--

CREATE TABLE IF NOT EXISTS `blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `module` varchar(50) NOT NULL,
  `callback` varchar(255) NOT NULL,
  `urls` text NOT NULL,
  `place` varchar(100) NOT NULL,
  `template` varchar(100) NOT NULL,
  `conf` text NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `cache_key` varchar(250) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `timereg` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `urlalias` (`cache_key`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cache`
--

INSERT INTO `cache` (`cache_key`, `type`, `data`, `timereg`) VALUES
('admin/modules', 'url', 'a:5:{s:2:"cl";s:13:"Module_Module";s:2:"pr";a:1:{i:0;s:14:"system modules";}s:2:"cb";s:7:"modules";s:4:"args";a:0:{}s:5:"title";s:52:"Система управления модулями";}', 1367324429),
('admin/users/edit', 'url', 'a:5:{s:2:"cl";s:11:"Module_User";s:2:"pr";a:1:{i:0;s:12:"user manager";}s:2:"cb";s:12:"userEditForm";s:4:"args";a:0:{}s:5:"title";s:56:"Редактирование учетной записи";}', 1367323958),
('admin/users/list', 'url', 'a:5:{s:2:"cl";s:11:"Module_User";s:2:"pr";a:1:{i:0;s:12:"user manager";}s:2:"cb";s:9:"usersList";s:4:"args";a:0:{}s:5:"title";s:39:"Список пользователей";}', 1367317146),
('admin/users/perms', 'url', 'a:5:{s:2:"cl";s:11:"Module_User";s:2:"pr";a:1:{i:0;s:12:"user manager";}s:2:"cb";s:9:"permsList";s:4:"args";a:0:{}s:5:"title";s:25:"Права доступа";}', 1367323920),
('admin/users/roles', 'url', 'a:5:{s:2:"cl";s:11:"Module_User";s:2:"pr";a:1:{i:0;s:12:"user manager";}s:2:"cb";s:9:"rolesList";s:4:"args";a:0:{}s:5:"title";s:23:"Список ролей";}', 1367323918);

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `body` text NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `uid_2` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dictionary`
--

CREATE TABLE IF NOT EXISTS `dictionary` (
  `code` varchar(128) NOT NULL,
  `parent` varchar(128) NOT NULL,
  `title` text NOT NULL,
  `val` text,
  `stat` int(2) NOT NULL DEFAULT '1',
  `weight` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `code` (`code`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dictionary`
--

INSERT INTO `dictionary` (`code`, `parent`, `title`, `val`, `stat`, `weight`) VALUES
('cfg', '', 'Настройки', '', 1, 0),
('cfg.ccache', 'cfg', 'Кеширование', '', 1, 0),
('cfg.ccache.agress', 'cfg.ccache', 'Агрессивное кеширование', '', 1, 0),
('cfg.ccache.block', 'cfg.ccache', 'Кеширование блоков', '', 1, 3),
('cfg.ccache.jscss', 'cfg.ccache', 'Сжатие и кеширование JS и CSS файлов', '', 1, 4),
('cfg.ccache.page', 'cfg.ccache', 'Кеширование страниц', '', 1, 1),
('cfg.ccache.view', 'cfg.ccache', 'Кеширование представлений', '', 1, 2),
('cfg.templates', 'cfg', 'Шаблоны', '', 1, 1),
('cfg.templates.admin', 'cfg.templates', 'Административная часть', 'accent2', 1, 1),
('cfg.templates.client', 'cfg.templates', 'Клиентская часть', 'default', 1, 0),
('menu', '', 'Меню', '', 1, 0),
('menu.admin', 'menu', 'Панель управления', '', 1, 0),
('menu.admin.config', 'menu.admin', 'Настройки', '', 1, 5),
('menu.admin.config.ace', 'menu.admin.config', 'Ace - редактор кода', '', 1, 5),
('menu.admin.config.ccache', 'menu.admin.config', 'Кеширование', '', 1, 0),
('menu.admin.content', 'menu.admin', 'Содержимое', '', 1, 1),
('menu.admin.content.comments', 'menu.admin.content', 'Комментарии', '', 1, 1),
('menu.admin.content.documents', 'menu.admin.content', 'Документы', '', 1, 0),
('menu.admin.modules', 'menu.admin', 'Модули', '', 1, 3),
('menu.admin.reports', 'menu.admin', 'Отчёты', '', 1, 6),
('menu.admin.structure', 'menu.admin', 'Структура', '', 1, 2),
('menu.admin.structure.blocks', 'menu.admin.structure', 'Блоки', '', 1, 0),
('menu.admin.structure.dictionary', 'menu.admin.structure', 'Словарь', '', 1, 1),
('menu.admin.structure.doctypes', 'menu.admin.structure', 'Типы документов', '', 1, 3),
('menu.admin.structure.views', 'menu.admin.structure', 'Представления', '', 1, 2),
('menu.admin.themes', 'menu.admin', 'Оформление', '', 1, 4),
('menu.admin.users', 'menu.admin', 'Пользователи', '', 1, 0),
('menu.admin.users.list', 'menu.admin.users', 'Список пользователей', '', 1, 0),
('menu.admin.users.perms', 'menu.admin.users', 'Права доступа', '', 1, 2),
('menu.admin.users.roles', 'menu.admin.users', 'Роли', '', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `docs`
--

CREATE TABLE IF NOT EXISTS `docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL COMMENT 'Тип документа',
  `title` varchar(200) NOT NULL COMMENT 'Заголовок',
  `adt` text COMMENT 'Анонс',
  `body` text COMMENT 'Основной текст',
  `uid` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `stat` int(2) NOT NULL DEFAULT '0' COMMENT 'Статус публикации: 0-закрыт для показа; 1-опубликован; 2-удалён',
  `top` int(2) NOT NULL DEFAULT '0' COMMENT 'Всегда на верху',
  `weight` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`uid`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=94 ;

--
-- Дамп данных таблицы `docs`
--

INSERT INTO `docs` (`id`, `alias`, `type`, `title`, `adt`, `body`, `uid`, `created`, `stat`, `top`, `weight`) VALUES
(76, '', 'page', ' Вышла «самая быстрая и отполированная» версия Ubuntu', '25 апреля состоялся релиз новой версии операционной системы Ubuntu – 13.04. Определен срок ее поддержки – до января 2014 года. Сами разработчики описывают ОС как «самую быструю» и «визуально отполированную».', '', 1, 1366959132, 1, 0, 0),
(77, '', 'page', 'Кого накажут за мат в прямом эфире?', 'Предусмотренные законодательством штрафы за употребление бранной лексики в СМИ не будут распространяться на гостей прямого эфира. Об этом говорится в сообщении Роскомнадзора, распространенном по итогам совещания с представителями СМИ.', '', 1, 1366959169, 1, 0, 0),
(78, '', 'page', 'Разговоры по телефону заразительны для окружающих', 'Исследователи из Мичиганского университета пришли к выводу, что разговоры и иные действия с мобильным устройством являются заразительными для окружающих людей. В рамках эксперимента социологи наблюдали за студентами одного из учебных заведений.', '', 1, 1366959198, 1, 0, 0),
(79, '', 'page', 'Назван самый популярный офисный браузер', 'В то время как Google Chrome приобретает всю большую популярность на рынке интернет-браузеров, Internet Explorer занимает первое место среди браузеров, которыми люди пользуются на рабочем месте, судя по рейтингу Forrester.', '', 1, 1366959239, 1, 0, 0),
(80, '', 'page', 'Взломщика iPhone позвали на работу в Google', 'Николас Аллегра (Nicholas Allegra), известный под ником Comex, создатель джейлбрейка для iPhone и iPad, сообщил, что переходит на стажировку в Google. ', '', 1, 1366959265, 1, 0, 0),
(81, '', 'page', 'Путин опроверг ограничение свободы Рунета', 'Президент России Владимир Путин опроверг существование ограничений интернет-сферы в России. Заявление Путина прозвучало во время «прямой линии» 25 апреля.', '', 1, 1366959291, 1, 0, 0),
(82, '', 'page', 'Россия вошла в число крупнейших мировых интернет-держав', 'За последние 10 лет Россия стремительно вошла в перечень крупнейших интернет-держав по целому ряду показателей. Об этом сообщил сегодня глава Минкомсвязи РФ Николай Никифоров, открывая Российский форум по управлению Интернетом (RIGF-2013).', '', 1, 1366959345, 1, 0, 0),
(83, '', 'page', 'В популярном приложении для Android найдена уязвимость', 'Критически опасная уязвимость была обнаружена в популярном Android-приложении для обмена сообщениями Viber. На сегодняшний день приложение было скачано более 100 млн раз. Таким образом, все пользователи, на устройства которых было установлено Viber, могут быть подвержены уязвимости, открывающей доступ к устройству.', '', 1, 1366959375, 1, 0, 0),
(84, '', 'page', 'Скоро Apple покажет новые iOS и OS X', 'Корпорация Microsoft объявила о том, что китайская компания ZTE, один из крупнейших производителей смартфонов, согласилась выплачивать роялти за патенты софтверного гиганта, используемые в выпускаемых им устройствах. ', '', 1, 1366959450, 1, 0, 0),
(85, '', 'page', 'Таксофоны научат раздавать Wi-Fi', 'В Москве до конца 2013 года установят около двухсот таксофонов, являющихся хотспотами Wi-Fi, сообщают «Известия» со ссылкой на представителя городского департамента информационных технологий.', '', 1, 1366959503, 1, 0, 0),
(86, '', 'page', 'Арестован лидер хакерской группы LulzSec', 'Полиция Австралии арестовала «самопровозглашенного лидера» хакерской группировки LulzSec. Об этом сообщается на сайте полиции. В пресс-релизе ведомства не раскрывается имя 24-летнего арестованного. Сообщается, что он проживал в городке Поинт-Клэр в штате Новый Южный Уэльс и работал специалистом по информационным технологиям.', '', 1, 1366959559, 1, 0, 0),
(87, '', 'page', 'Apple отмечает 10 лет iTunes', 'В честь 10-летия со дня создания iTunes (28 апреля 2003 года), Apple запустила интерактивную ленту с хроникой событий под названием «A Decade of iTunes» в iTunes Store.', '', 1, 1366959596, 1, 0, 0),
(88, '', 'page', 'Профессионалы проиграли кибервойну студентам', 'Как сообщило информационное агентство Reuters, 19 апреля, в Гановере (Мэриленд, США) состоялся турнир по «кибервойне» между кадетами Академии военно-воздушных сил США (U.S. Air Force Academy) и хакерами Агентства национальной безопасности США (NSA). Целью турнира, проведенного в рамках ежегодных учений по киберзащите (Cyber Defense Exercise, CDX), стала демонстрация необходимости киберзащиты.', '', 1, 1366959793, 1, 0, 0),
(89, '', 'page', 'Киберпреступники атаковали Sims', 'Хакер под псевдонимом Game Over взломал сайт NewSeaSims, на котором любители Sims могут загружать дополнительный контент для игры. В результате скомпрометированными оказались учетные записи 108 тысяч пользователей. ', '', 1, 1366959840, 1, 0, 0),
(90, '', 'page', 'В генной инженерии нашли применение Java', 'Ученые-биотехнологи из международной научной организации Open Facility Advancing Biotechnology (BIOFAB) работают над созданием механизма управления генетической информацией, который позволит программировать живые клетки. Прообразом «языка программирования тела» ученые выбрали Java, а результаты разработки планируется открыть по модели Open Source.', '', 1, 1366959892, 1, 0, 0),
(91, '', 'page', 'Новая технология подстраивает игру под геймера', 'Группа исследователей из Georgia Tech разработала вычислительную модель, которая может использоваться в современных видеоиграх и позволяет гибко регулировать их сложность в соответствии с возможностями и потребностями конкретного игрока. ', '', 1, 1366960012, 1, 0, 0),
(93, '', 'test', 'test', '', '', 1, 1366962983, 1, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `doc_types`
--

CREATE TABLE IF NOT EXISTS `doc_types` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `comments` tinyint(1) NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Дамп данных таблицы `doc_types`
--

INSERT INTO `doc_types` (`tid`, `name`, `title`, `description`, `comments`) VALUES
(15, 'page', 'Страница', '', 0),
(17, 'test', 'test', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `doc_type_fields`
--

CREATE TABLE IF NOT EXISTS `doc_type_fields` (
  `fid` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `require` tinyint(1) NOT NULL DEFAULT '0',
  `weight` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `indx` (`fid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `doc_type_fields`
--

INSERT INTO `doc_type_fields` (`fid`, `type`, `title`, `description`, `require`, `weight`) VALUES
('adt', 'page', 'Short text', NULL, 0, 1),
('adt', 'test', 'Short text', NULL, 0, 1),
('body', 'page', 'Body', NULL, 0, 2),
('body', 'test', 'Body', NULL, 0, 2),
('field_img', 'page', 'Изображение', '', 0, 3),
('field_link', 'page', 'Ссылка', '', 0, 0),
('title', 'page', 'Title', NULL, 0, 0),
('title', 'test', 'Title', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fields`
--

CREATE TABLE IF NOT EXISTS `fields` (
  `fid` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `undeletable` tinyint(1) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fields`
--

INSERT INTO `fields` (`fid`, `module`, `undeletable`, `config`) VALUES
('adt', 'FieldTextarea', 1, 'a:2:{s:9:"minLenght";s:0:"";s:9:"maxLenght";s:3:"200";}'),
('body', 'FieldTextarea', 1, 'a:3:{s:9:"minLenght";s:0:"";s:9:"maxLenght";s:0:"";s:10:"htmleditor";s:2:"on";}'),
('field_img', 'FieldImage', 0, 'a:3:{s:4:"size";s:0:"";s:9:"dimension";s:7:"300x100";s:12:"saveOriginal";s:2:"on";}'),
('field_link', 'FieldText', 0, 'a:2:{s:9:"minLenght";s:0:"";s:9:"maxLenght";s:0:"";}'),
('title', 'FieldText', 1, 'a:2:{s:9:"minLenght";s:1:"5";s:9:"maxLenght";s:3:"200";}');

-- --------------------------------------------------------

--
-- Структура таблицы `field_img`
--

CREATE TABLE IF NOT EXISTS `field_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc` int(11) NOT NULL,
  `original` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `main` int(2) NOT NULL DEFAULT '0',
  `weight` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `doc` (`doc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `field_link`
--

CREATE TABLE IF NOT EXISTS `field_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc` int(11) NOT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  KEY `doc` (`doc`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Дамп данных таблицы `field_link`
--

INSERT INTO `field_link` (`id`, `doc`, `text`) VALUES
(9, 76, ''),
(10, 77, ''),
(11, 78, ''),
(12, 79, ''),
(13, 80, ''),
(14, 81, ''),
(15, 82, ''),
(16, 83, ''),
(17, 84, ''),
(18, 85, ''),
(19, 86, ''),
(20, 87, ''),
(21, 88, ''),
(22, 89, ''),
(23, 90, ''),
(24, 91, '');

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `sid` varchar(40) NOT NULL,
  `data` text NOT NULL,
  `dreg` int(11) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `package` varchar(32) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Дамп данных таблицы `modules`
--

INSERT INTO `modules` (`id`, `name`, `package`, `type`, `status`) VALUES
(1, 'DbQuery', 'core', 'system', 1),
(2, 'User', 'core', 'system', 1),
(3, 'Page', 'core', 'system', 1),
(4, 'Document', 'core', 'system', 1),
(5, 'Form', 'core', 'system', 1),
(6, 'Menu', 'core', 'system', 1),
(8, 'Module', 'core', 'system', 1),
(9, 'View', 'core', 'system', 1),
(10, 'Message', 'core', 'system', 1),
(11, 'AdminPanel', 'core', 'system', 1),
(12, 'Dictionary', 'core', 'system', 1),
(13, 'Block', 'core', 'system', 1),
(16, 'CCache', 'core', 'system', 1),
(17, 'Test', 'other', 'all', 1),
(18, 'SEO', 'core', 'system', 1),
(19, 'Pagination', 'core', 'system', 1),
(23, 'Ace', 'other', 'all', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Аноним', ''),
(2, 'Участник', ''),
(3, 'Администратор', ''),
(4, 'Тестер', '');

-- --------------------------------------------------------

--
-- Структура таблицы `roles_perms`
--

CREATE TABLE IF NOT EXISTS `roles_perms` (
  `id` int(11) NOT NULL,
  `permission` varchar(30) NOT NULL,
  UNIQUE KEY `id` (`id`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `roles_perms`
--

INSERT INTO `roles_perms` (`id`, `permission`) VALUES
(1, 'document read'),
(2, 'document read'),
(3, 'admin panel'),
(3, 'block'),
(3, 'dictionary'),
(3, 'document comment'),
(3, 'document create'),
(3, 'document delete'),
(3, 'document edit'),
(3, 'document moder'),
(3, 'document read'),
(3, 'document types'),
(3, 'system menu'),
(3, 'system modules'),
(3, 'user manager'),
(3, 'views'),
(4, 'document read');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `dreg` int(11) NOT NULL,
  `lastvisit` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `pass`, `dreg`, `lastvisit`, `status`) VALUES
(1, 'SP', 'd9b1d7db4cd6e70935368a1efb10e377', 0, 1367325720, 1),
(2, 'user', 'd9b1d7db4cd6e70935368a1efb10e377', 0, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `uid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users_roles`
--

INSERT INTO `users_roles` (`uid`, `rid`) VALUES
(1, 3),
(2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `views`
--

CREATE TABLE IF NOT EXISTS `views` (
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `data` text NOT NULL,
  `cache` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `views`
--

INSERT INTO `views` (`name`, `type`, `title`, `description`, `data`, `cache`) VALUES
('pages', 'pg', 'Список страниц', '', 'a:10:{s:8:"document";s:8:"document";s:4:"type";s:4:"page";s:6:"fields";a:2:{i:0;s:5:"title";i:1;s:3:"adt";}s:4:"sort";a:1:{i:0;a:2:{s:3:"fid";s:7:"created";s:8:"sorttype";s:4:"desc";}}s:5:"count";s:0:"";s:4:"rows";s:0:"";s:6:"filter";a:1:{i:0;a:3:{s:3:"fid";s:4:"stat";s:9:"condition";s:4:"@eqf";s:5:"value";s:1:"1";}}s:5:"alias";s:5:"pages";s:8:"bl_cache";b:0;s:8:"pg_cache";b:0;}', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
