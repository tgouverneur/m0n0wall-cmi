DROP TABLE IF EXISTS `rules-int`;
CREATE TABLE `rules-int` (
  `idrule` int(11) NOT NULL,
  `idint` int(11) NOT NULL,
  `position` tinyint(4) NOT NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`idrule`,`idint`)
);

