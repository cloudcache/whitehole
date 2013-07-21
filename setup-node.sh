#!/bin/bash

if [ "`whoami`" != "root" ]; then
	echo "Retry, with root privileges"
	exit 1
fi

SRC_DIR=`pwd`
LOCAL_IP=`/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`
GATEWAY=`/sbin/route -n | grep 'UG' | awk '{ print $2}'`
NETMASK=`/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f4 | awk '{ print $1}'`
DNS="@_DNS_@"

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
apt-get update
apt-get -q -y install kvm libvirt-bin sysstat screen socat nfs-common

cd $SRC_DIR
msg "Configure Bridge & Network Env."
echo "# The loopback network interface
auto lo
iface lo inet loopback

# The bridge network interface
auto br0
iface br0 inet static
        address $LOCAL_IP
        netmask $NETMASK
        gateway $GATEWAY
        # dns-* options are implemented by the resolvconf package, if installed
        dns-nameservers $DNS
        bridge_ports eth0
                bridge_stp off
                bridge_fd 0
                #bridge_maxwait 0" > /etc/network/interfaces
echo "nameserver $DNS" > /etc/resolvconf/resolv.conf.d/head
service networking restart

cd $SRC_DIR
echo -e "$YELLOW ========================================================="
echo -e "$YELLOW  Congratulations, the installation is complete. ^^"
echo -e "$YELLOW ========================================================="
reset_color
