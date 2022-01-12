CREATE TABLE `mdl_monit_config` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `val` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `monit_conf_nam` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Monitoring configuration variables';
