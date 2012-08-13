-- -----------------------------------------------------
-- Table `filters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `filters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filter_target` varchar(20) NOT NULL DEFAULT 'river' COMMENT 'Object on which the filter is to be applied i.e. river or bucket',
  `filter_target_id` bigint(20) NOT NULL COMMENT 'ID of the filter target i.e. river|bucket id',
  `filter_name` varchar(50) NOT NULL COMMENT 'Name of the filter',
  `filter_date_add` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation date of the filter',
  `filter_enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is the filter active?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_filter` (`filter_target`,`filter_target_id`,`filter_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `filter_parameters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `filter_parameters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filter_id` bigint(20) NOT NULL,
  `parameter_type` varchar(50) NOT NULL COMMENT 'Type of the filter parameter e.g. place, tags, keyword/phrase etc',
  `parameter` varchar(255) NOT NULL COMMENT 'Parameter value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_filter_parameter` (`filter_id`,`parameter_type`,`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
