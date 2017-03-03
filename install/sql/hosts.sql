DROP TABLE IF EXISTS `hosts`;
CREATE TABLE `hosts` (
  `id` int(11) NOT NULL auto_increment,
  `lastchange` int(11) NOT NULL,
  `changed` int(11) NOT NULL,
  `fversion` varchar(10) NOT NULL,
  `idsnmp` int(11) NOT NULL,
  `hostname` varchar(200) NOT NULL,
  `version` varchar(4) NOT NULL,
  `domain` varchar(200) NOT NULL,
  `dnsserver` varchar(255) NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `ntpserver` varchar(200) NOT NULL,
  `dnsoverride` int(1) NOT NULL default '1',
  `ntpinterval` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `idbuser` int(11) default NULL,
  `enabled` tinyint(1) NOT NULL,
  `is_tpl` tinyint(1) NOT NULL default '0',
  `use_ip` tinyint(1) NOT NULL default '0',
  `ip` varchar(16) NOT NULL,
  `port` int(5) NOT NULL default '443',
  `https` int(1) NOT NULL default '1',
  `enablednat` tinyint(1) NOT NULL default '1',
  `idsyslog` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

