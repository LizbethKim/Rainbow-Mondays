CREATE USER 'rainbowmondays'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON * . * TO 'rainbowmondays'@'localhost';
FLUSH PRIVILEGES;


create database rainbowmondays;
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
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL,
  `batchId` int(11) NOT NULL,
  `locationId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `listedTime` int(11) NOT NULL,
  PRIMARY KEY (`id`, `batchId`)
);

CREATE TABLE IF NOT EXISTS `batches` (
    id INT PRIMARY KEY NOT NULL,
    date INT NOT NULL
);
