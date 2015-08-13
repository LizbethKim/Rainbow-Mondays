CREATE TABLE if not EXISTS `categories` (
  `id` int(11) NOT NULL,
  `parentCategoryId` int(11) NOT NULL,
  `categoryName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=228 DEFAULT CHARSET=latin1;


CREATE TABLE if not EXISTS `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext,
  `longitude` text NOT NULL,
  `latitude` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=latin1;


CREATE TABLE if not EXISTS `jobs` (
  `id` int(11) NOT NULL,
  `batchId` int(11) NOT NULL,
  `locationId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54186 DEFAULT CHARSET=latin1;


CREATE TABLE rainbowmondays.batches (
    id INT PRIMARY KEY NOT NULL,
    date INT NOT NULL
);