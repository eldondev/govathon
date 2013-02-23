CREATE TABLE `properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` text,
  `geodata_source` text,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1
CREATE TABLE `property_resources` (
  `property_id` int(11) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1
CREATE TABLE `resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` text,
  `meta` text,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
