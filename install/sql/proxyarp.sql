DROP TABLE IF EXISTS `proxyarp`;
CREATE TABLE `proxyarp` (
  `id` int(11) NOT NULL auto_increment,
  `if` varchar(20) NOT NULL,
  `network` varchar(25) NOT NULL,
  `from` varchar(25) NOT NULL,
  `to` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

