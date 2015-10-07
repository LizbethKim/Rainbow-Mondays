

create DATABASE if not exists rainbowmondays;
use rainbowmondays;



CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL,
  `parentCategoryId` int(11) NOT NULL,
  `categoryName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext,
  `longitude` text NOT NULL,
  `latitude` text NOT NULL,
  `region_id` int,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL,
  `batchId` int(11) NOT NULL,
  `jobTitle` varchar(255) DEFAULT NUll,
  `locationId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `listedTime` int(11) NOT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`id`, `batchId`)
);

CREATE TABLE IF NOT EXISTS `batches` (
    id INT PRIMARY KEY NOT NULL,
    date INT NOT NULL
);

CREATE TABLE regions
(
  id INT PRIMARY KEY NOT NULL,
  name TEXT NOT NULL,
  `long` INT NOT NULL,
  lat INT NOT NULL
);

CREATE TABLE live_cache
(
  id INT NOT NULL,
  jobTitle TEXT NOT NULL,
  icon_url TEXT NOT NULL,
  locationId INT NOT NULL,
  listedTime INT NOT NULL
);

CREATE TABLE cache_log
(
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  time INT NOT NULL
);

CREATE TABLE searches
(
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  serach_term TEXT NOT NULL,
  category TEXT NOT NULL ,
  sub_category TEXT NOT NULL,
  time_searched INT NOT NULL,
  locationId INT NOT NULL
);

CREATE USER 'rainbowmondays'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON * . * TO 'rainbowmondays'@'localhost';
FLUSH PRIVILEGES;
