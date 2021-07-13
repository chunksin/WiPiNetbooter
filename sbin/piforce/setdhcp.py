import subprocess
import os,sys,shutil

int = sys.argv[1]
mode = sys.argv[2]

with open('/etc/network/interfaces', 'r') as file:
    data = file.readlines()

if (int == "wired"):
    data[4] = 'iface eth0 inet dhcp\n'
    data[5] = '#iface eth0 inet static\n'
    data[6] = '#address 10.0.0.1\n'
    data[7] = '#netmask 255.255.255.0\n'
    data[8] = '#gateway 10.0.0.254\n'
    if (mode == "home"):
        with open('/etc/network/interfaces.hotspot', 'r') as intfile:
            intdata = intfile.readlines()
    if (mode == "hotspot"):
        with open('/etc/network/interfaces.home', 'r') as intfile:
            intdata = intfile.readlines()
    intdata[4] = 'iface eth0 inet dhcp\n'
    intdata[5] = '#iface eth0 inet static\n'
    intdata[6] = '#address 10.0.0.1\n'
    intdata[7] = '#netmask 255.255.255.0\n'
    intdata[8] = '#gateway 10.0.0.254\n'
    if (mode == "home"):
        with open('/etc/network/interfaces.hotspot', 'w') as intfile:
            intfile.writelines( intdata )
    if (mode == "hotspot"):
        with open('/etc/network/interfaces.home', 'w') as intfile:
            intfile.writelines( intdata )

if (int == "wireless"):
    data[11] = 'iface wlan0 inet dhcp\n'
    data[12] = '#iface wlan0 inet static\n'
    data[13] = '#address 192.168.42.1\n'
    data[14] = '#netmask 255.255.255.0\n'
    data[15] = '#gateway 192.168.42.254\n'

with open('/etc/network/interfaces', 'w') as file:
    file.writelines( data )