DROP TABLE IF EXISTS `rules`;
CREATE TABLE `rules` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `type` varchar(10) NOT NULL,
  `if` varchar(10) NOT NULL,
  `protocol` varchar(15) NOT NULL,
  `source` varchar(20) NOT NULL,
  `destination` varchar(20) NOT NULL,
  `sport` varchar(10) NOT NULL,
  `dport` varchar(10) NOT NULL,
  `snot` tinyint(1) NOT NULL default '0',
  `dnot` tinyint(1) NOT NULL default '0',
  `description` varchar(255) NOT NULL,
  `icmptype` varchar(20) NOT NULL,
  `frags` tinyint(1) NOT NULL default '0',
  `log` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

