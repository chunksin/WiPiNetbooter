import subprocess
import os,sys,shutil

ssid = sys.argv[1]
psk = sys.argv[2]

with open('/etc/hostapd/hostapd.conf', 'r') as file:
    data = file.readlines()

data[2] = 'ssid='+ssid+'\n'
data[9] = 'wpa_passphrase='+psk+'\n'

with open('/etc/hostapd/hostapd.conf', 'w') as file:
    file.writelines( data )