DROP TABLE IF EXISTS `hw-int`;
CREATE TABLE `hw-int` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(10) NOT NULL,
  `mac` varchar(50) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

