DROP TABLE IF EXISTS `staticroutes`;
CREATE TABLE `staticroutes` (
  `id` int(11) NOT NULL auto_increment,
  `idhost` int(11) NOT NULL,
  `if` varchar(5) NOT NULL,
  `network` varchar(20) NOT NULL,
  `gateway` varchar(16) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

