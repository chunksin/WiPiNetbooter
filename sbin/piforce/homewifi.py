import subprocess
import os,sys,shutil
from time import sleep

shutil.copy('/etc/wpa_supplicant/wpa_supplicant.conf.new','/etc/wpa_supplicant/wpa_supplicant.conf')

bashCommand1 = 'sudo systemctl disable hostapd.service'
os.system(bashCommand1)
bashCommand2 = 'systemctl disable isc-dhcp-server.service'
os.system(bashCommand2)

shutil.copy('/etc/network/interfaces','/etc/network/interfaces.hotspot')
shutil.copy('/etc/network/interfaces.home','/etc/network/interfaces')

ssid = sys.argv[1]
passkey = sys.argv[2]

p1 = subprocess.Popen(
    ["wpa_passphrase", ssid, passkey], 
    stdout=subprocess.PIPE
)

p2 = subprocess.Popen(
    ["sudo", "tee", "-a", "/etc/wpa_supplicant/wpa_supplicant.conf"], 
    stdin=p1.stdout, 
    stdout=subprocess.PIPE
)

p1.stdout.close()

output,err = p2.communicate()

updateCommand1 = 'sudo echo -n home | tee /sbin/piforce/wifimode.txt'
os.system(updateCommand1)

updateCommand2 = 'sudo echo -n | tee /boot/wifi.txt'
os.system(updateCommand2)

sleep(3)
os.system("sudo reboot")