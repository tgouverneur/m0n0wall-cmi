DROP TABLE IF EXISTS `snmp`;
CREATE TABLE `snmp` (
  `id` int(11) NOT NULL auto_increment,
  `enable` tinyint(1) NOT NULL default '1',
  `syslocation` varchar(255) NOT NULL,
  `syscontact` varchar(255) NOT NULL,
  `rocommunity` varchar(255) NOT NULL,
  `bindlan` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

