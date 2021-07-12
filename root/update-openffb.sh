#!/bin/bash

echo '***OpenFFB Update Script***'
echo ''
echo 'Testing Internet Connectivity ...'
echo ''
ping -q -c3 google.com > /dev/null

if [ $? -eq 0 ];
then
echo ''
echo 'Internet Connection Detected'
echo ''
echo 'Downloading Source Files ...'
echo ''
cd /root
git clone https://github.com/Fredobedo/openFFB
echo ''
echo 'Download Complete'
echo ''
echo 'Starting Build'
echo ''
cd openFFB
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
rm -dr /root/openFFB/
echo 'Cleanup Complete'
echo ''
else
echo 'No Internet Connection Detected'
fi