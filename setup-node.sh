#!/bin/bash

if [ "`whoami`" != "root" ]; then
	echo "Retry, with root privileges"
	exit 1
fi

SRC_DIR=`pwd`
HTML_DIR="/var/www/html"
SSH_KEY_DIR="/var/www/.ssh"
LOCAL_IP=`/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`

RESET="\033[0m"

GREEN="\033[1;32m"
BLUE="\033[1;34m"
RED="\033[1;31m" 
YELLOW="\033[1;33m" 
CYAN="\033[1;36m"

reset_color() {
	echo -e "$RESET"
}

msg() {
	msg=$1
	echo
	echo
	echo -e "$CYAN =============================================="
	echo -e "$CYAN      $msg"
	echo -e "$CYAN =============================================="
	reset_color
	sleep 2
}

sub_msg() {
	msg=$1
	echo -e "$GREEN      $msg"
	reset_color
}

alert_msg() {
	msg=$1
	echo
	echo
	echo -e "$RED [Alert]: $msg"
	echo -e "$RED [Alert]: $msg"
	echo -e "$RED [Alert]: $msg"
	echo
	reset_color
}

export DEBIAN_FRONTEND=noninteractive

cd $SRC_DIR
msg "Install Package for Whitehole"
apt-get -y install kvm libvirt-bin sysstat screen socat

cd $SRC_DIR
msg "Configure ETC Env."

cd $SRC_DIR
echo -e "$YELLOW ========================================================="
echo -e "$YELLOW  Congratulations, the installation is complete. ^^"
echo -e "$YELLOW ========================================================="
reset_color
