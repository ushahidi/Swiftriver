-- -----------------------------------------------------
-- Table `river_filters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `river_filters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `river_id` bigint(20) unsigned NOT NULL,
  `filter` varchar(15) NOT NULL,
  `filter_date_add` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `filter_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_river_filter_type` (`river_id`,`filter`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `river_filter_parameters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `river_filter_parameters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `river_filter_id` bigint(20) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_river_filter_parameter` (`river_filter_id`,`parameter`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;