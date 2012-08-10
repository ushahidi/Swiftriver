ALTER TABLE `rivers` ADD `river_create_complete` TINYINT(1)  NOT NULL  DEFAULT '0';

UPDATE `rivers` SET `river_create_complete` = 1;
