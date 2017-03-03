DROP TABLE IF EXISTS `nat-srv`;
CREATE TABLE `nat-srv` (
  `id` int(11) NOT NULL auto_increment,
  `ipaddr` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

