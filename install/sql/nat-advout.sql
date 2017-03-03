DROP TABLE IF EXISTS `nat-advout`;
CREATE TABLE `nat-advout` (
  `id` int(11) NOT NULL auto_increment,
  `enable` tinyint(1) NOT NULL,
  `noportmap` tinyint(1) NOT NULL,
  `if` varchar(5) NOT NULL,
  `source` varchar(25) NOT NULL,
  `destination` varchar(25) NOT NULL,
  `dnot` tinyint(1) NOT NULL,
  `target` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

