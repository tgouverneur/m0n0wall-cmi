DROP TABLE IF EXISTS `interfaces`;
CREATE TABLE `interfaces` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(3) NOT NULL,
  `num` int(2) NOT NULL,
  `enable` tinyint(1) NOT NULL default '1',
  `if` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ipaddr` varchar(16) NOT NULL,
  `subnet` varchar(16) NOT NULL,
  `media` varchar(50) NOT NULL,
  `mediaopt` varchar(50) NOT NULL,
  `gateway` varchar(16) NOT NULL,
  `dhcp` varchar(16) NOT NULL,
  `blockpriv` tinyint(1) NOT NULL,
  `bridge` varchar(10) NOT NULL,
  `mtu` varchar(6) NOT NULL,
  `spoofmac` varchar(50) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

