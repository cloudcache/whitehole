Description
===========

"Whitehole" is IaaS Platform based on KVM and Libvirt-PHP.

### Notice

* Prototype level, exception handling is weak.
* Single management node and many KVM-Based computing nodes.
* Simultaneous, multi-processing considerations weak.
* Source code is not clean yet.
* Not support Router/Pirvate-IP yet.
* Default Template: Ubuntu Server 12.04.02 x86_64 (Compressed qcow2 Image, about 320MB)
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

* apache2
* php
* mysql
* libvirt
* qemu-utils
* kpartx

### External Solution

* libvirt-php
* ssh2
* jquery-ui

Directory
=========

### whitehole-home

* Destination DIR: /home/whitehole
* Cron, Monitoring, MRTG-Template, DDNS, Etc...

### whitehole-html

* Destination DIR: /var/www/html
* PHP Sources, MySQL Schema, OpenSource-Board, Etc...

Installation
============

### Support Bash-Script

* Automation Script for Management Web Server: setup.sh

### Requirement for Management Web Server

	Clean Installed Ubuntu 12.04
	apt-get

### Requirement for Physical(Compute) Node

	Clean Installed Ubuntu 12.04
	apt-get
	libvirt, screen, socat, kvm, nfs-common, openssh-server

### Guide

* ./setup.sh
* Input mysql root's password
* Input DDNS Info
* Connect, http://{installed Server IP}
* Web-UI Administrator ID/PW: admin / 1234 (Must change admin's password)
* Add Primary/Secondary Storage (NFS Shared Storage Must be prepared first.)
* Add Template from http://goo.gl/UW1WP (KVM, 64bit, Ubuntu-12.04)
* Add Network-Pool Range: No Router, Direct IP to VM (e.g: 172.21.3.101 ~ 172.21.3.250)
* Add Physical Node(Compute Node) by password method.
* Test Create VM, and Enjoy

License and Author
==================

* Author: JungJungIn (<call518@gmail.com>)
* GNU GENERAL PUBLIC LICENSE Version 2
* Board/Member/Login System's Source from http://www.miwit.com and http://sir.co.kr
