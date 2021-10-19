#!/bin/bash

VER=$(cat /proc/device-tree/model | awk '{print $3}')

if [ $VER = "3" ]; then
   echo "Pi 3 detected, adding config.txt entries"
if ! grep -Fxq "dtoverlay=disable-bt" /boot/config.txt; then
   echo "dtoverlay=disable-bt" | sudo tee -a /boot/config.txt
   sudo systemctl disable hciuart
else
   echo "Entries already found!"
   sudo systemctl disable hciuart
fi
   echo "Copying OpenJVS config file"
   sudo cp ./config.pi3hat /etc/openjvs/config
fi
if [ $VER = "4" ]; then
   echo "Pi 4 detected, adding config.txt entries"
if ! grep -Fxq "dtoverlay=uart3" /boot/config.txt; then
   echo "dtoverlay=uart3" | sudo tee -a /boot/config.txt
   echo "dtoverlay=uart4" | sudo tee -a /boot/config.txt
else
   echo "Entries already found!"
fi
   echo "Copying OpenJVS config file"
   sudo cp ./config.pi4hat /etc/openjvs/config
fi

cp ./cmdline.txt /boot/cmdline.txt
systemctl unmask serial-getty@ttyS0
systemctl enable serial-getty@ttyS0