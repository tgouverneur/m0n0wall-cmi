DROP TABLE IF EXISTS `vlans`;
CREATE TABLE `vlans` (
  `id` int(11) NOT NULL auto_increment,
  `order` int(11) NOT NULL,
  `tag` varchar(10) NOT NULL,
  `if` varchar(5) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

