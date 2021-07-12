#!/bin/bash

echo '***OpenJVS Update Script***'
echo ''
echo 'Testing Internet Connectivity ...'
echo ''
echo 'Backing up config file ...'
cp /etc/openjvs/config /root/config
echo ''
ping -q -c3 google.com > /dev/null

if [ $? -eq 0 ]
then
echo ''
echo 'Internet Connection Detected'
echo ''
echo 'Downloading Source Files ...'
echo ''
cd /root
git clone https://github.com/OpenJVS/OpenJVS
echo ''
echo 'Download Complete'
echo ''
echo 'Starting Build'
echo ''
cd OpenJVS
make
echo ''
echo 'Build Complete'
echo ''
echo 'Installing ...'
echo ''
sudo make install
echo ''
echo 'Install Complete'
echo 'Cleaning Up ...'
cd /root
rm -dr /root/OpenJVS/
echo 'Cleanup Complete'
echo ''
echo 'Restoring config file ...'
cp /root/config /etc/openjvs/config
else
echo 'No Internet Connection Detected'
fi