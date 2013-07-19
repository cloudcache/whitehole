Description
===========

"Whitehole" is IaaS Platform (with KVM).

### Notice

* Prototype level, exception handling is weak.
* Single management node and many KVM-Based computing nodes.
* Simultaneous, multi-processing considerations weak.
* Source code is not clean yet.
* Not support Router/Pirvate-IP yet.
* Default Template: Ubuntu Server 12.04.02 (x86_64) : Download http://goo.gl/UW1WP
* Default Password is set, If necessary, Null processing and Make Change.

Screenshot
==========

Click to view.

[![VM List](https://raw.github.com/call518/whitehole/master/screenshot/screenshot-whitehole-1.PNG)](https://raw.github.com/call518/whitehole/master/screenshot/screenshot-whitehole-1.PNG)

Feature
=======

* VM Instance: Create(from Template/ISO)/Delte/On/Off/Reboot
* SSH-Keypair
* Live-Migration
* Monitoring: Traffic/Packet/CPU/DiskIO(Byte/Count)
* 2nd-Volume (not like EBS, Can not online attache/detache)
* Security Group
* Snapshot (Current Can not Live, Require Reboot)
* Custom Template Image: Create(from Snapshot)/Delete/Create New VM(from Custom Template)
* Privat-DNS: Hostname.test.org <-> IP-Address
* Primary Storage: for VM HDD Image
* Secondary Storage: Template/SSH-Keypair/Etc...


Requirements
============

### Platform

	# lsb_release -a

	No LSB modules are available.
	Distributor ID:	Ubuntu
	Description:	Ubuntu 12.04.2 LTS
	Release:	12.04
	Codename:	precise

* Ubuntu Server 12.04 (x86_64)
* KVM Hypervisor
* NFS Storage (Primary/Secondary)
* MRTG/SNMP/Etc..

Software
========

### Ubuntu Packages

	apache2                2.2.22-1ubuntu1.3				  Apache HTTP Server metapackage
	apache2-mpm-itk        2.2.22-1ubuntu1.3				  multiuser MPM for Apache 2.2
	apache2-utils          2.2.22-1ubuntu1.3				  utility programs for webservers
	apache2.2-bin          2.2.22-1ubuntu1.3				  Apache HTTP Server common binary files
	apache2.2-common       2.2.22-1ubuntu1.3				  Apache HTTP Server common files
	libapache2-mod-php5    5.3.10-1ubuntu3.6				  server-side, HTML-embedded scripting language (Apache 2 module)
	libssh2-php            0.11.2-1							  PHP Bindings for libssh2
	php-pear               5.3.10-1ubuntu3.6				  PEAR - PHP Extension and Application Repository
	php-xml-parser         1.3.2-4							  PHP PEAR module for parsing XML
	php-xml-serializer     0.20.0-2.1						  swiss-army knife for reading and writing XML files
	php5                   5.3.10-1ubuntu3.6				  server-side, HTML-embedded scripting language (metapackage)
	php5-cli               5.3.10-1ubuntu3.6				  command-line interpreter for the php5 scripting language
	php5-common            5.3.10-1ubuntu3.6				  Common files for packages built from the php5 source
	php5-curl              5.3.10-1ubuntu3.6				  CURL module for php5
	php5-dev               5.3.10-1ubuntu3.6				  Files for PHP5 module development
	php5-fpm               5.3.10-1ubuntu3.6				  server-side, HTML-embedded scripting language (FPM-CGI binary)
	php5-gd                5.3.10-1ubuntu3.6				  GD module for php5
	php5-imagick           3.1.0~rc1-1						  ImageMagick module for php5
	php5-imap              5.3.5-0ubuntu2					  IMAP module for php5
	php5-intl              5.3.10-1ubuntu3.6				  internationalisation module for php5
	php5-mcrypt            5.3.5-0ubuntu1					  MCrypt module for php5
	php5-memcache          3.0.6-1							  memcache extension module for PHP5
	php5-ming              1:0.4.3-1.2ubuntu2				  Ming module for php5
	php5-mysql             5.3.10-1ubuntu3.6				  MySQL module for php5
	php5-ps                1.3.6-6							  ps module for PHP 5
	php5-pspell            5.3.10-1ubuntu3.6				  pspell module for php5
	php5-recode            5.3.10-1ubuntu3.6				  recode module for php5
	php5-snmp              5.3.10-1ubuntu3.6				  SNMP module for php5
	php5-sqlite            5.3.10-1ubuntu3.6				  SQLite module for php5
	php5-tidy              5.3.10-1ubuntu3.6				  tidy module for php5
	php5-xmlrpc            5.3.10-1ubuntu3.6				  XML-RPC module for php5
	php5-xsl               5.3.10-1ubuntu3.6				  XSL module for php5
	libdbd-mysql-perl      4.020-1build2					  Perl5 database interface to the MySQL database
	libmysql-ruby          2.8.2+gem2deb-1build1			  Transitional package for ruby-mysql
	libmysqlclient18       5.5.31-0ubuntu0.12.04.2			  MySQL database client library
	libqt4-sql-mysql       4:4.8.1-0ubuntu4.4				  Qt 4 MySQL database driver
	mysql-client           5.5.31-0ubuntu0.12.04.2			  MySQL database client (metapackage depending on the latest version)
	mysql-client-5.5       5.5.31-0ubuntu0.12.04.1			  MySQL database client binaries
	mysql-client-core-5.5  5.5.31-0ubuntu0.12.04.2			  MySQL database core client binaries
	mysql-common           5.5.31-0ubuntu0.12.04.2			  MySQL database common files, e.g. /etc/mysql/my.cnf
	mysql-server           5.5.31-0ubuntu0.12.04.2			  MySQL database server (metapackage depending on the latest version)
	mysql-server-5.5       5.5.31-0ubuntu0.12.04.1			  MySQL database server binaries and system database setup
	mysql-server-core-5.5  5.5.31-0ubuntu0.12.04.1			  MySQL database server binaries
	ruby-mysql             2.8.2+gem2deb-1build1			  MySQL module for Ruby
    bind9                  1:9.8.1.dfsg.P1-4ubuntu0.6		  Internet Domain Name Server
    bind9-host             1:9.8.1.dfsg.P1-4ubuntu0.6		  Version of 'host' bundled with BIND 9.X
    bind9utils             1:9.8.1.dfsg.P1-4ubuntu0.6		  Utilities for BIND
    libsnmp-base           5.4.3~dfsg-2.4ubuntu1.1            SNMP (Simple Network Management Protocol) MIBs and documentation
    libsnmp-perl           5.4.3~dfsg-2.4ubuntu1.1            SNMP (Simple Network Management Protocol) Perl5 support
    libsnmp-session-perl   1.13-1ubuntu1                      Perl support for accessing SNMP-aware devices
    libsnmp15              5.4.3~dfsg-2.4ubuntu1.1            SNMP (Simple Network Management Protocol) library
    snmp                   5.4.3~dfsg-2.4ubuntu1.1            SNMP (Simple Network Management Protocol) applications
    snmp-mibs-downloader   1.1                                Install and manage Management Information Base (MIB) files
    snmpd                  5.4.3~dfsg-2.4ubuntu1.1            SNMP (Simple Network Management Protocol) agents
    mrtg                   2.17.3-2ubuntu1                    multi router traffic grapher
    nfs-common             1:1.2.5-3ubuntu3.1                 NFS support files common to client and server
    libvirt-dev            0.9.8-2ubuntu17.10                 development files for the libvirt library

### External Solution

* libvirt-php >= 0.4.8
* ssh2 >=0.12
* jquery-ui >=1.10.3

Directory
=========

### whitehole-home

- Destination DIR: /home/whitehole
- Cron, Monitoring, MRTG-Template, DDNS, Etc...

### whitehole-html

- Destination DIR: /var/www/html
- PHP Sources, MySQL Schema, OpenSource-Board, Etc...

Installation
============

- Sorry, still being written to the installation manual and Automatic installation script. Will be added soon.

License and Author
==================

- Author: JungJungIn (<call518@gmail.com>)
- GNU GENERAL PUBLIC LICENSE Version 2
- Board/Member/Login System's Original Source is http://www.miwit.com and http://sir.co.kr
