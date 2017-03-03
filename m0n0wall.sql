SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `m0n0wall`
--

-- --------------------------------------------------------

--
-- Structure de la table `alias`
--

DROP TABLE IF EXISTS `alias`;
CREATE TABLE IF NOT EXISTS `alias` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `address` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=82 ;

-- --------------------------------------------------------

--
-- Structure de la table `busers`
--

DROP TABLE IF EXISTS `busers`;
CREATE TABLE IF NOT EXISTS `busers` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Structure de la table `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `pages` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Structure de la table `hgroup`
--

DROP TABLE IF EXISTS `hgroup`;
CREATE TABLE IF NOT EXISTS `hgroup` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hosts`
--

DROP TABLE IF EXISTS `hosts`;
CREATE TABLE IF NOT EXISTS `hosts` (
  `id` int(11) NOT NULL auto_increment,
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
  `enablednat` tinyint(1) NOT NULL default '1',
  `idsyslog` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `hosts-groups`
--

DROP TABLE IF EXISTS `hosts-groups`;
CREATE TABLE IF NOT EXISTS `hosts-groups` (
  `idhost` int(11) NOT NULL,
  `idgroup` int(11) NOT NULL,
  PRIMARY KEY  (`idhost`,`idgroup`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `interfaces`
--

DROP TABLE IF EXISTS `interfaces`;
CREATE TABLE IF NOT EXISTS `interfaces` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

--
-- Structure de la table `nat-advout`
--

DROP TABLE IF EXISTS `nat-advout`;
CREATE TABLE IF NOT EXISTS `nat-advout` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `nat-one2one`
--

DROP TABLE IF EXISTS `nat-one2one`;
CREATE TABLE IF NOT EXISTS `nat-one2one` (
  `id` int(11) NOT NULL auto_increment,
  `if` varchar(5) NOT NULL,
  `internal` varchar(20) NOT NULL,
  `external` varchar(20) NOT NULL,
  `subnet` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nat-rules`
--

DROP TABLE IF EXISTS `nat-rules`;
CREATE TABLE IF NOT EXISTS `nat-rules` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Structure de la table `nat-srv`
--

DROP TABLE IF EXISTS `nat-srv`;
CREATE TABLE IF NOT EXISTS `nat-srv` (
  `id` int(11) NOT NULL auto_increment,
  `ipaddr` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Structure de la table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Structure de la table `proxyarp`
--

DROP TABLE IF EXISTS `proxyarp`;
CREATE TABLE IF NOT EXISTS `proxyarp` (
  `id` int(11) NOT NULL auto_increment,
  `if` varchar(20) NOT NULL,
  `network` varchar(25) NOT NULL,
  `from` varchar(25) NOT NULL,
  `to` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `rules`
--

DROP TABLE IF EXISTS `rules`;
CREATE TABLE IF NOT EXISTS `rules` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

-- --------------------------------------------------------

--
-- Structure de la table `rules-int`
--

DROP TABLE IF EXISTS `rules-int`;
CREATE TABLE IF NOT EXISTS `rules-int` (
  `idrule` int(11) NOT NULL,
  `idint` int(11) NOT NULL,
  `position` tinyint(4) NOT NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`idrule`,`idint`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `snmp`
--

DROP TABLE IF EXISTS `snmp`;
CREATE TABLE IF NOT EXISTS `snmp` (
  `id` int(11) NOT NULL auto_increment,
  `enable` tinyint(1) NOT NULL default '1',
  `syslocation` varchar(255) NOT NULL,
  `syscontact` varchar(255) NOT NULL,
  `rocommunity` varchar(255) NOT NULL,
  `bindlan` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `staticroutes`
--

DROP TABLE IF EXISTS `staticroutes`;
CREATE TABLE IF NOT EXISTS `staticroutes` (
  `id` int(11) NOT NULL auto_increment,
  `idhost` int(11) NOT NULL,
  `if` varchar(5) NOT NULL,
  `network` varchar(20) NOT NULL,
  `gateway` varchar(16) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `syslog`
--

DROP TABLE IF EXISTS `syslog`;
CREATE TABLE IF NOT EXISTS `syslog` (
  `id` int(11) NOT NULL auto_increment,
  `reverse` tinyint(1) NOT NULL,
  `dhcp` tinyint(1) NOT NULL,
  `system` tinyint(1) NOT NULL,
  `portalauth` tinyint(1) NOT NULL,
  `vpn` tinyint(1) NOT NULL,
  `nologdefaultblock` tinyint(1) NOT NULL,
  `resolve` tinyint(1) NOT NULL,
  `nentries` int(11) NOT NULL,
  `remoteserver` varchar(255) NOT NULL,
  `filter` varchar(255) NOT NULL,
  `enable` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `groupname` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Structure de la table `vlans`
--

DROP TABLE IF EXISTS `vlans`;
CREATE TABLE IF NOT EXISTS `vlans` (
  `id` int(11) NOT NULL auto_increment,
  `order` int(11) NOT NULL,
  `tag` varchar(10) NOT NULL,
  `if` varchar(5) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idhost` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;
