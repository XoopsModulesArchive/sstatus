# phpMyAdmin SQL Dump
# version 2.5.6-rc1
# http://www.phpmyadmin.net
#
# $Id: mysql.sql,v 1.2 2004/07/01 14:33:41 eric_juden Exp $
# --------------------------------------------------------

#
# Table structure for table `sstatus_memos`
#

CREATE TABLE sstatus_memos (
  id int(11) NOT NULL auto_increment,
  serviceid int(11) NOT NULL default '0',
  uid int(11) NOT NULL default '0',
  memo mediumtext NOT NULL,
  status int(11) NOT NULL default '0',
  posted int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY serviceid (serviceid,uid,status),
  KEY posted (posted)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `sstatus_services`
#

CREATE TABLE sstatus_services (
  id int(11) NOT NULL auto_increment,
  name varchar(35) NOT NULL default '',
  description mediumtext,
  status int(11) NOT NULL default '0',
  lastUpdated int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY name (name),
  KEY status (status)
) TYPE=MyISAM;
