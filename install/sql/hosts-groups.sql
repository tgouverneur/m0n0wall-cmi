DROP TABLE IF EXISTS `hosts-groups`;
CREATE TABLE `hosts-groups` (
  `idhost` int(11) NOT NULL,
  `idgroup` int(11) NOT NULL,
  PRIMARY KEY  (`idhost`,`idgroup`)
);

