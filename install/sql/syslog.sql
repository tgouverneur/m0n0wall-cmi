DROP TABLE IF EXISTS `syslog`;
CREATE TABLE `syslog` (
  `id` int(11) NOT NULL auto_increment,
  `reverse` tinyint(1) NOT NULL,
  `dhcp` tinyint(1) NOT NULL,
  `system` tinyint(1) NOT NULL,
  `rawfilter` varchar(1) NOT NULL default '0',
  `portalauth` tinyint(1) NOT NULL,
  `vpn` tinyint(1) NOT NULL,
  `nologdefaultblock` tinyint(1) NOT NULL,
  `resolve` tinyint(1) NOT NULL,
  `nentries` int(11) NOT NULL,
  `remoteserver` varchar(255) NOT NULL,
  `filter` varchar(255) NOT NULL,
  `enable` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
);

