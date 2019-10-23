CREATE TABLE `#__0001_cerp_product` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`title`    varchar(40) Not NULL ,
	`vp`       decimal(10,2) Not NULL ,
	`accountsell`  int(11) Not NULL ,
	`description`  mediumtext NULL,
	
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);


CREATE TABLE `#__0001_davUserCat` (
	`id`       INT(11)     NOT NULL ,
	`url`    varchar(128) Not NULL ,
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__0001_cerp_configpersonal` (
	`id`       INT(11)     NOT NULL , 
	`smtp_host`    varchar(128) NULL ,
	`smtp_port`    varchar(10) NULL ,
	`smtp_sendername` varchar(128) NULL ,
	`smtp_username` varchar(128) NULL ,
	`smtp_password` varchar(128) NULL ,
	
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__0001_cerp_configglobal` (
	`id`       INT(11)     NOT NULL , 
	
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);


CREATE TABLE `#__0001_cerp_facturarun` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`status`   INT(11)     NOT NULL,
	`title`    varchar(40) Not NULL ,
	`templateid`  INT(11)     NOT NULL,
	`fakturadatum` date Not NULL ,
	`fakturadatumformat` varchar(20) Not NULL default 'Y-m-d',
	`zahlungsfrist` date Not NULL ,
	`zahlungsfristformat` varchar(20) Not NULL default 'Y-m-d',
	`verrechnetbis` date Not NULL ,
	`verrechnetbisformat` varchar(20) Not NULL default 'Y-m-d',
	
	`nullerrechnung`  INT(11) NOT NULL,
	
	`email_subject` varchar(120) NULL ,
	`email_body`   mediumtext NULL,
	
	`param1` varchar(100) NULL ,
	`param2` varchar(100) NULL ,
	`param3` varchar(100) NULL ,
	`param4` varchar(100) NULL ,
	
	`comment` text NULL ,
	
	`remindlevel`  INT(11)     NOT NULL default 0,
	`remindcost`  decimal(10,2) NOT NULL default 0,
	
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__0001_cerp_factura` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`status`   INT(11)     NOT NULL,
	`title`    varchar(40) Not NULL ,
	`betrag`   decimal(10,2) Not NULL ,
	`fakturadatum` date Not NULL ,
	`zahlungsfrist` date Not NULL ,
	`zahlungsdatum` date NULL ,
	`zahlungsbetrag` decimal(10,2) Not NULL default 0,
	
	`email` varchar(100) NULL ,
	`email_subject` varchar(120) NULL ,
	`email_body`   mediumtext NULL,
	`email_attachment` text NULL ,
	`customerid` INT(11)  NULL,
	`productid`  INT(11)  NULL,
	
	`comment` text NULL ,
	`runid`  INT(11)  NULL,
	
	`readyForSend`  INT(11)  Not NULL default 0,
	`versanddatum` date Not NULL ,
	
	`remindlevel`  INT(11)     NOT NULL default 0,
	`remindcost`  decimal(10,2) NOT NULL default 0,

	`fibuid`  INT(11) NULL default 0,
	`facturemedium` tinyint  NOT NULL default 1,
	
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);


CREATE TABLE `#__0001_cerp_facturastate` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`statustext`  varchar(40) Not NULL ,	
	PRIMARY KEY (`id`)
);

insert into `#__0001_cerp_facturastate` (`id`,	`statustext` ) values
(1,'In Aufbereitung'),
(2,'Aufbereitet'),
(3,'Versendet'),
(4,'Bemahnt'),
(5,'Bezahlt'),
(9,'Erledigt');



CREATE TABLE `#__0001_cerp_template` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`title`    varchar(40) Not NULL ,
	 `template`   mediumtext NULL,
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);

--Liste von Postfächer, die abzuhorchen sind
CREATE TABLE `#__0001_cerp_maildistributer` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`title`    varchar(40) Not NULL ,
	`smtp_host`    varchar(128) NULL ,
	`smtp_port`    varchar(10) NULL ,
	`smtp_username` varchar(128) NULL ,
	`smtp_password` varchar(128) NULL ,
	`selUserId` int null,
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__0001_cerp_mailwhitelist` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`sender`    varchar(128) Not NULL ,
	`forwardsender`    varchar(128) NULL ,
	`forwardsendername` varchar(128) NULL ,
	`myselfuserid` int not null,
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__0001_cerp_customers` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`catid`    INT(11)     NULL,
	`firstname` varchar(30) Not NULL ,
	`lastname` varchar(30) Not NULL ,
	`email` varchar(100) NULL ,
	`address` varchar(50) NULL ,
	`zipcode` varchar(10) NULL ,
	`town` varchar(30) NULL ,
	`phone` varchar(100) NULL ,
	`mobil` varchar(100) NULL ,
	`birth` date NULL ,
	`entrydate` int NULL ,
	`sayhello` varchar(60) NULL ,
	`price1` decimal(10,2) NULL ,
	`price2` decimal(10,2) NULL ,
	`productabo` int(11) NULL ,
	
	`joomlauserid` int(11) NULL default 0,
	`propagateUser` int(11) NULL default 1,
	`propagateDav` int(11) NULL default 1,
	`david` varchar(200) NULL ;
	
	`publicationrestriction` int(11) NULL default 0,
	`maildistribution` varchar(50) NULL default '',
	
	`fak_name` varchar(50) NULL ,
	`fak_address` varchar(50) NULL ,
	`fak_zipcode` varchar(10) NULL ,
	`fak_town` varchar(30) NULL ,
	
	`comment` text NULL ,
	`facturatedtill` date NULL,
	
	`catidfuture`    INT(11) NOT NULL default 0,
	`catidfutureby` date NULL ,
	
	`facturemedium` tinyint  NOT NULL default 1,
	
	
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);


CREATE TABLE `#__0001_kontorahmen` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`kr_parentid`  INT(11)   NULL,
	`kr_kontonr` VARCHAR(40) NOT NULL,
	`kr_bezeichnung`  varchar(250) NOT NULL default '',
	`kr_typ` int NOT NULL,
	`kr_bilancewhenzero` tinyint(4) NULL DEFAULT '1',
	
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);



CREATE TABLE `#__0001_konto` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`ko_kontorahmen`  INT(11)   NULL,
	`ko_kontonr` VARCHAR(40) NOT NULL,
	`ko_bezeichnung` VARCHAR(250) NOT NULL,
	`ko_waehrung` varchar(3) NOT NULL,

	`ko_bebuchbar` tinyint(4) NULL DEFAULT '1',
	`ko_bilancewhenzero` tinyint(4) NULL DEFAULT '1',
	`ko_bilancewhenempty` tinyint(4) NULL DEFAULT '0',
	
	`ko_firstbilance` int(11) NULL DEFAULT '0',
	`ko_sum` int(11) NULL DEFAULT '0',
	`ko_sumlastperiod` int(11) NULL ,
	`ko_budget` int(11) NULL ,
	`ko_budgetlastperiod` int(11) NULL,
	`ko_countbook` int(11) NULL DEFAULT '0',
	`ko_lastbook` datetime NULL ,

	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,
	`params` TEXT NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
	
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `#__0001_buchung` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`bu_datum` date   Not NULL,
	`bu_belegnr` VARCHAR(40)   NULL,
	`bu_sammelparent`  INT(11)   NULL,
	`bu_text` VARCHAR(250)   NULL,
	`bu_belegfile` BLOB  NULL,
	`bu_belegfilename` varchar(100) null,
	`bu_belegfilemime` varchar(30) null,
	`addDate` datetime NULL ,
	`updDate` datetime NULL ,
	`addUser` varchar(100) NULL ,
	`updUser` varchar(100) NULL ,	
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__0001_buchungdetail` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`bd_parentid`  INT(11)   NOT NULL,	
	`bd_konto`  INT(11)   NOT NULL,
	`bd_sollhaben`  INT(11)   NOT NULL,
	`bd_waehrung`  varchar(3)   NOT NULL,
	`bd_betrag` int(11)   NOT NULL,
	`bd_kurs` int(11)   NOT NULL,
	`bd_text` VARCHAR(250)   NULL,
	PRIMARY KEY (`id`)
);


Insert Into `#__0001_kontorahmen` (`id`, `kr_parentid`,	`kr_kontonr`, `kr_bezeichnung` ,`kr_typ`, `addDate`,`addUser`)values
('1',null,'1','Aktiven', 1,CURRENT_TIMESTAMP(),'install'),
('2',null,'2','Passiven', 2,CURRENT_TIMESTAMP(),'install'),
('3',null,'3','Ertrag', 3,CURRENT_TIMESTAMP(),'install'),
('4',null,'4','Aufwand', 1,CURRENT_TIMESTAMP(),'install');
