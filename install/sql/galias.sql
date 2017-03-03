DROP TABLE IF EXISTS `galias`;
CREATE TABLE `galias` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `address` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

