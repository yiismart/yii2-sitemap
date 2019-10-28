create table if not exists `sitemap`
(
	`id` int(10) not null auto_increment,
	`ownerKey` varchar(200) not null,
	`loc` varchar(2048) not null,
	`lastmod` varchar(30) default null,
	`changefreq` varchar(10) default null,
	`priority` float default null,
	primary key (`id`),
	key `ownerKey` (`ownerKey`)
) engine InnoDB;
