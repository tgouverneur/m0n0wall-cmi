DROP TABLE IF EXISTS `nat-one2one`;
CREATE TABLE `nat-one2one` (
  `id` int(11) NOT NULL auto_increment,
  `if` varchar(5) NOT NULL,
  `internal` varchar(20) NOT NULL,
  `external` varchar(20) NOT NULL,
  `subnet` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

