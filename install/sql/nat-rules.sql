DROP TABLE IF EXISTS `nat-rules`;
CREATE TABLE `nat-rules` (
  `id` int(11) NOT NULL auto_increment,
  `idhost` int(11) NOT NULL,
  `if` varchar(10) NOT NULL,
  `external` varchar(25) NOT NULL,
  `eport` varchar(25) NOT NULL,
  `target` varchar(25) NOT NULL,
  `lport` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `proto` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
);

