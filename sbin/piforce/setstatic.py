import subprocess
import os,sys,shutil

int = sys.argv[1]
mode = sys.argv[2]
ip = sys.argv[3]
sm = sys.argv[4]
gw = sys.argv[5]

with open('/etc/network/interfaces', 'r') as file:
    data = file.readlines()

if (int == "wired"):
    data[4] = '#iface eth0 inet dhcp\n'
    data[5] = 'iface eth0 inet static\n'
    data[6] = 'address '+ip+'\n'
    data[7] = 'netmask '+sm+'\n'
    if (gw != "none"):
        data[8] = 'gateway '+gw+'\n'
    else:
        data[8] = '#gateway 10.0.0.254\n'
    if (mode == "home"):
        with open('/etc/network/interfaces.hotspot', 'r') as intfile:
            intdata = intfile.readlines()
    if (mode == "hotspot"):
        with open('/etc/network/interfaces.home', 'r') as intfile:
            intdata = intfile.readlines()
    intdata[4] = '#iface eth0 inet dhcp\n'
    intdata[5] = 'iface eth0 inet static\n'
    intdata[6] = 'address '+ip+'\n'
    intdata[7] = 'netmask '+sm+'\n'
    if (gw != "none"):
        intdata[8] = 'gateway '+gw+'\n'
    else:
        intdata[8] = '#gateway 10.0.0.254\n'
    if (mode == "home"):
        with open('/etc/network/interfaces.hotspot', 'w') as intfile:
            intfile.writelines( intdata )
    if (mode == "hotspot"):
        with open('/etc/network/interfaces.home', 'w') as intfile:
            intfile.writelines( intdata )
    with open('/etc/network/interfaces', 'w') as file:
        file.writelines( data )

if (int == "wireless" and mode == "home"):
    data[11] = '#iface wlan0 inet dhcp\n'
    data[12] = 'iface wlan0 inet static\n'
    data[13] = 'address '+ip+'\n'
    data[14] = 'netmask '+sm+'\n'
    data[15] = 'gateway '+gw+'\n'
    with open('/etc/network/interfaces', 'w') as file:
        file.writelines( data )

if (int == "wireless" and mode == "hotspot"):
    data[11] = '#iface wlan0 inet dhcp\n'
    data[12] = 'iface wlan0 inet static\n'
    data[13] = 'address '+ip+'\n'
    data[14] = 'netmask '+sm+'\n'
    data[15] = 'gateway '+gw+'\n'
    data[16] = 'wireless-power off\n'
    data[17] = 'wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf\n'
    with open('/etc/network/interfaces.home', 'w') as file:
        file.writelines( data )
