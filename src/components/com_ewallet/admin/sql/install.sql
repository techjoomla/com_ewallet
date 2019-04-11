CREATE TABLE IF NOT EXISTS `#__wallet_orders` (
  `id` int(11) NOT NULL auto_increment,
  `prefix` VARCHAR( 23 ) NOT NULL,
  `name` varchar(255) default NULL,
  `cdate` datetime default NULL,
  `mdate` datetime default NULL,
  `transaction_id` varchar(100) default NULL,
  `payee_id` varchar(100) default NULL,
  `original_amount` float(10,2) NOT NULL,
  `amount` float(10,2) NOT NULL,
  `coupon_code` varchar(100) NOT NULL,  
  `customer_note` text default NULL,
  `status` varchar(100) default NULL,
  `processor` varchar(100) default NULL,
  `ip_address` varchar(50) default NULL,
  `currency` varchar(16) NOT NULL,
  `extra` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Order Information' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__wallet_transc` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `time` double(15,2) default NULL,
  `spent` double(11,2) default NULL,
  `earn` double(11,2) default NULL,
  `balance` double(11,2) default NULL,
  `type` varchar(11) default NULL,
  `parent` varchar(255) NOT NULL,
  `type_id` int(11) default NULL,
  `comment` text DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Transactions Information' AUTO_INCREMENT=1 ;
