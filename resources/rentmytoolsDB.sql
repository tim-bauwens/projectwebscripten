
drop database `rentmytoolsDB`;
SET time_zone = "+00:00";
CREATE DATABASE `rentmytoolsDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `rentmytoolsDB`;

CREATE TABLE accounts(
    `id` INTEGER unsigned auto_increment PRIMARY KEY,
    `username` VARCHAR(225),
    `password` VARCHAR(225),
    `firstname` VARCHAR(225),
	`lastname` VARCHAR(225),
	`email` VARCHAR(225),
    `phonenumber` VARCHAR(45),
	`address` VARCHAR(225),
    `biography` LONGTEXT
);

CREATE table items(
	`id` INTEGER unsigned auto_increment PRIMARY KEY,
	`userID` INTEGER,
	`title` VARCHAR(225),
	`description` LONGTEXT,
	`startdate` DATE,
	`enddate` DATE,
	`dateadded` DATE,
	`rented` BOOLEAN,
	`town` VARCHAR(45),
	`postalcode` INTEGER,
	`province` VARCHAR(45)
);

--STORED PROCEDURES --

DELIMITER //
CREATE PROCEDURE getToolsByUser(IN userId INT, IN pageStart INT, IN pageEnd INT)
	BEGIN
		DROP TABLE IF EXISTS `tools`;
		CREATE TEMPORARY TABLE `tools` (tmpid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE=MyISAM  AS (SELECT items.*, accounts.username from items INNER JOIN accounts ON items.userId = accounts.id WHERE accounts.id = userId);
		SELECT * from `tools` WHERE tools.tmpid between pageStart AND pageEnd;
	END //
DELIMITER ;


DELIMITER //
CREATE PROCEDURE getToolsBySearch(IN keyword VARCHAR(225), IN pageStart INT, IN pageEnd INT)
	BEGIN
		DROP TABLE IF EXISTS `tools`;
		CREATE TEMPORARY TABLE `tools` (tmpid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE=MyISAM  AS (SELECT items.*, accounts.username from items INNER JOIN accounts ON items.userId = accounts.id WHERE items.title LIKE keyword OR items.description LIKE keyword OR accounts.username LIKE keyword);
		SELECT * from `tools` WHERE tools.tmpid between pageStart AND pageEnd;
	END //
DELIMITER ;


DELIMITER //
CREATE PROCEDURE getToolsBySearchCount(IN keyword VARCHAR(225))
	BEGIN
		DROP TABLE IF EXISTS `tools`;
		CREATE TEMPORARY TABLE `tools` (tmpid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE=MyISAM  AS (SELECT items.*, accounts.username from items INNER JOIN accounts ON items.userId = accounts.id WHERE items.title LIKE keyword);
		SELECT COUNT(*) AS total from `tools`;
	END //
DELIMITER ;


