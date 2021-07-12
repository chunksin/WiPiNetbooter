# WiPiNetbooter
Rapsberry Pi based Netbooter for Sega Naomi/Chihiro/Triforce arcade boards

WiPi Netbooter Overview

This version of the Pi Netbooter code is a scratch rewrite of the original solution written by devtty0 and has been enhanced with a new user interface and richer functionality. It has full support for all netbootable Sega arcade ROMs for the Naomi, Naomi2, Triforce, Chihiro and the Atomiswave conversions made possible by Darksoft. This version also includes the card reader emulator code for games that support it, the original python scripts were written by Winteriscoming on the arcade-projects.com forums and have been adapted for use in a web interface.
You will need:
A Raspberry Pi v3B, 3B+ or 4B and microSD Card - 32GB Class 10 card recommended
A Naomi, Naomi2, Triforce or Chihiro with a netdimm running firmware 3.03 or greater
A standard network cable and 5v power source for the Pi – you can make a custom cable to draw power directly from the system (see below)
A Web Browser :)
Optional but recommended: a zero security pic chip
Optional: a Trendnet TU-S9 USB-Serial adaptor and custom serial cable for the Card Emulator
Optional: an FTDI based RS485 to USB adaptor for OpenJVS (see https://github.com/OpenJVS/OpenJVS for more information)
General Use Instructions
Download all files from the download link and extract the img file using 7zip or similar.
Write the image to a 32GB microSD card using Win32DiskImager or Etcher and insert into the Pi.
Power up your arcade board and plug the network cable between the Pi and the netdimm network port, power on the Pi.
When the Pi first boots up it will start broadcasting a wireless network called WiPi-Netbooter, join either a computer or mobile device (recommended) to the network using password ‘segarocks’. Once joined open a browser and navigate to http://netbooter.local, this will take you to the main game menu.
The first time you boot up you will need to add a netdimm to the Pi. The network interface on the Pi is set to use IP address 10.0.0.1, enter the service menu on your arcade board and set the netdimm IP address in the NETWORK SETTING menu. Set NETWORK TYPE to ETHER, REMOTE to DISABLE and use the buttons to enter the address, I recommend using IP address 10.0.0.3 and Subnet Mask 255.255.255.0. Next navigate to the Setup Menu on the Pi and ‘Manage Netdimms’, you can either manually add your netdimm details or use the scanner function to find it.
The main page of the web interface is used for launching games, simply browse through the game list and select a game to send the ROM to your arcade system. If you are using the Advanced Menu mode, when you select a game you will see extended game information and a gameplay video if one is present on the Pi along with a Launch Game link. Once the loading process is complete a success message is shown, after that you can safely browse to other pages or shutdown the Pi – note that the Pi needs to stay running if you are using the Time Hack mode with no zero key pic chip.
Options Menu
The Options menu displays the current mode settings and provides links to toggle between them.
Menu Modes
There are 2 menu modes available, Simple and Advanced. Simple mode allows you to boot the ROM directly from the main game list page, Advanced mode links to a game information page that shows you extended information about the game, a video preview if available and a link to launch the game. This information can be easily edited by updating a CSV file held on the Pi, see the Updating Roms, Videos and Images section below for details.
Power Modes
There are two power modes to choose from, Always-On and Power Saver. Always-On works as its name suggests, you should use the Shutdown link from the web interface to safely shut down the Pi. Generally speaking, you probably won't get any problems from simply powering the Pi off but there is chance that the SD Card may become corrupted if you do. Power Saver will start a timer when the Pi is booted and runs for 10 minutes before shutting the Pi down. This leaves enough time to open up the web interface and change any options you need to.
NOTE: the timer cannot be stopped or started from the web interface so switching between power modes requires a reboot of the Pi.
Boot Modes
The Pi Netbooter has 2 different boot modes, Multi and Single. Multi-mode requires you to manually launch the game from the web interface every time you want to play. Switch to Single Boot mode to automatically boot the last played ROM when the Pi starts up.
NOTE: some games may not allow you to hot boot another game while one is already loaded, this affects the Atomiswave games in particular, to avoid getting stuck in a loop when booting those games in Single boot mode you need to disable it, reboot and select your new game before re-enabling Single mode.
Menu Modes
There are 2 menu modes available, Simple and Advanced. Simple mode allows you to boot the ROM directly from the main game list page, Advanced mode links to a game information page that shows you extended information about the game, a video preview if available and a link to launch the game. This information can be easily edited by updating a CSV file held on the Pi, see the ‘Import CSV from boot drive’ section below for details.
Relay Reboot mode is for use with an optional relay connected to the Naomi power or fan speed wire. When a game is launched, and this is enabled it will send out a signal on GPIO pin 40/GPIO26 which triggers the relay to cut power and soft reboot the Naomi. This is for games that will not allow you to hot boot another while it is running.
Time Hack mode is used when a null pic chip is not present in the netdimm. When enabled this will send a special packet to the netdimm to reset its security check. This requires the Pi to be left connected to the netdimm and powered on while the game is running.
Video sound enables or disables sound in the preview videos.
Nav button enables and disables a ‘jump to top’ floating button on the main game select menu.
OpenJVS Support - OpenJVS is a software JVS IO emulator which allows you to connect the Pi to the Naomi using an RS485 to USB connector and play games with virtually any USB game controller. See the OpenJVS github page for full instructions.
LCD Mode allows you to switch between a 16x2 LCD display or 3.5-inch touchscreen attached directly to the Pi as an alternative to the web interface via a browser. If no screen is detected the Pi will switch to display only via it’s webpages.
Setup Menu
The Setup menu is used for one time setup functions and additional features.
Edit Game List
The Edit Game List function is used to show and hide games in the main game list. This is useful if you want to load a full set of ROMs onto your SD card but you'd like to hide all vertical, analog and driving games for instance. Use the link in the Enabled column to toggle the setting between Yes and No to Show/Hide the game.
Manage Netdimms
Here you can set up as many netdimms as you need to support, one Pi can be used to netboot multiple systems. You can add and edit them here, the status of the netdimms is displayed here, shown in green if the Pi can reach the netdimm. The system type must be specified as it will be checked when a relevant game is sent.
Update Netdimm Firmware
Here you can update type 1 netdimms up to version 4.03. It is recommended if upgrading from version 3.x that you update to 4.01 first.
Card Emulator
The Card Emulator runs various scripts on the Pi to send and receive data to your Naomi, Chihiro or Triforce to emulate the magnetic card readers used on the original machines, useful if you want to get the most out of your games or have simply run out of cards!
To use it you need to plug a TrendNet TU-S9 USB-Serial convertor into the Pi connecting to your arcade system via a custom cable. Pinouts for the cables are shown in the Advanced section below.
The Card Emulator saves and loads card data via files held locally on the Pi. There are separate files and folders for each of the games as they are not compatible with each other. Simply select the mode you want to run and then choose a card from the list. Initially you won't have any cards so there is a text box where you can specify a name for your new card, spaces are not currently supported in the card name.
To launch the emulator either hit the submit button if you are creating a new card or the card name link to launch an existing one. The emulator will fire up in the background and start communicating over the serial link. If you've already booted the game, reset it via the test menu and be sure to enable the reader in the game test mode! Once you have finished your game and saved progress to your card you can launch another from the menu to continue playing.
Import CSV from boot drive
This is used if you have made any changes to the CSV data on an external computer via the boot drive copy. This will import your changes and overwrite the copy used to populate the main game list. The CSV file holds all the relevant data used by the Pi, including rom file name, images and videos, description, system type and much more.
View CSV Raw Data
This will display the raw data stored in the main CSV file.
Network Configuration
This is used to view the existing network configuration for the Wifi and Wired interfaces and allows you to customise your setup. A basic knowledge of networking is required to navigate these settings, if for any reason you lose communication with the Pi, it can be reset by creating a text file called ‘reset.txt’ in the boot drive. You can join the Pi to your home network either using the Wifi or Wired networks, use DHCP or fixed IP addressing, details of the supported configurations are detailed in the pages here.
Reboot Raspberry Pi
Self explanatory!
Advanced
For those of you who like to code, you can access the source files for the web interface in /sbin/piforce and /var/www/html. Feel free to have a poke around, generally if something cannot be done in PHP its due to permissions, the PHP page calls a python script to execute it on its behalf. The boot process is as follows:
When the Pi starts up it executes a file called rc.local that fires up a python script /sbin/piforce/check.py. This script checks a few files in the piforce folder to get settings for the power and boot modes. It then sends a netboot command if set to single mode and a shutdown command with a timer if the power mode is set to auto. The CSV file is copied back to the boot partition as part of the shutdown routine.
Most of the web code is PHP so the pages are generated as they are loaded, the benefit is you can make changes on the fly without having to restart the Pi. There is a styles.css file in /var/www/html/css that can be modified to change the colours and look and feel of the menus and webpages. You can also swap out the SEGA PI image in the top left, that is held in the /var/www/html/img folder.
All data for the games is scraped from the romsinfo.csv file held in the /boot/config folder, if you wish to add more columns bear in mind the existing scripts refer to the absolute column reference so you'll need to add any new ones after the existing columns. There is a way to import CSV data in as a multidimensional array using PHP but I got lost quite quickly in the coding for that, so my script just reads and writes line by line.
Here are the cable pinouts you need for the Card Emulator to work, I bought a straight through female serial connector on eBay for £3, cut it in half and crimped a 5 pin JST NH connector on the end.
Naomi <- Serial
1 <- 3
2 <- 2
3 <- 5
4 <- 8
5 <- 7
Chihiro/Triforce <- Serial
3 <- 5
4 <- 2
5 <- 3
6 <- 8
7 <- 7
