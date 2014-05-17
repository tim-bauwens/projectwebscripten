
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
    `phonenumber` VARCHAR(225),
	`address` VARCHAR(225),
    `biography` TEXT
);

CREATE table items(
	`id` INTEGER unsigned auto_increment PRIMARY KEY,
	`userID` INTEGER,
	`title` VARCHAR(225),
	`description` VARCHAR(225),
	`startdate` DATE,
	`enddate` DATE,
	`dateadded` DATE,
	`rented` BOOLEAN
);


