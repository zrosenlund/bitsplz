--
-- Database: `bitsplz`
--

-- Drop existing tables
DROP TABLE IF EXISTS  locations;


CREATE TABLE IF NOT EXISTS `locations` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Location ID' ,
  `name` VARCHAR(50) NOT NULL ,
  `category` VARCHAR(50) NOT NULL COMMENT 'Test Center | Condom Provider' ,
  `address` VARCHAR(200) NOT NULL ,
  `state` CHAR(2) NULL DEFAULT 'WA' ,
  `zip` VARCHAR(10) NULL DEFAULT NULL ,
  `longitude` FLOAT NOT NULL ,
  `latitude` FLOAT NOT NULL ,
  `phone` VARCHAR(30) NULL DEFAULT NULL ,
  `hours` VARCHAR(500) NULL DEFAULT NULL ,

  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;
