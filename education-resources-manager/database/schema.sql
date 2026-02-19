CREATE TABLE `wp_erm_resource_tracking` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `resource_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT 0,
  `action_date` datetime NOT NULL,
  `action_type` varchar(20) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`),
  KEY `user_id` (`user_id`),
  KEY `action_type` (`action_type`),
  KEY `action_date` (`action_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
