import subprocess
import os,sys,shutil
from time import sleep

shutil.copy('/etc/wpa_supplicant/wpa_supplicant.conf.new','/etc/wpa_supplicant/wpa_supplicant.conf')

bashCommand1 = 'sudo systemctl enable hostapd.service'
os.system(bashCommand1)
bashCommand2 = 'systemctl enable isc-dhcp-server.service'
os.system(bashCommand2)

shutil.copy('/etc/network/interfaces.restore','/etc/network/interfaces')

updateCommand1 = 'sudo echo -n hotspot | tee /sbin/piforce/wifimode.txt'
os.system(updateCommand1)

updateCommand2 = 'sudo echo -n | tee /boot/wifi.txt'
os.system(updateCommand2)