#!/bin/sh 
rm -rf /media/ram/klobs
rm /media/ram/klops.zip
mkdir /media/ram/klobs
cp -r client/java/application/Kobs/dist/* /media/ram/klobs
cp client/java/application/Kobs/Klobslogo* /media/ram/klobs
cp client/java/interface/serial/*.jar client/java/interface/serial/*.lang client/java/interface/serial/*.props  client/java/interface/serial/*.xpm /media/ram/klobs
cp client/java/interface/qrcode/*.jar client/java/interface/qrcode/*.lang client/java/interface/qrcode/*.props  client/java/interface/qrcode/*.xpm /media/ram/klobs
cp client/java/application/Kobs/*.lang  /media/ram/klobs
cp client/java/interface/qrcode/lib/* /media/ram/klobs/lib
cp client/java/interface/serial/lib/* /media/ram/klobs/lib
cp run*.sh /media/ram/klobs/
cp *.desktop /media/ram/klobs/
cp shojikido_logo_h480.png /media/ram/klobs/
cp installklobs.sh /media/ram/klobs/
# (cd /media/ram ; zip -r Klobs.zip klobs)

./makeself/makeself.sh  /media/ram/klobs/ KLOBS_Installer_LINUX.run "Klobs- der Kartenleser ohne besonderen Schwierigkeitsgrad" ./installklobs.sh
makensis klobs_windows_setup.nsi
zip KLOBS_Linux_Setup.zip KLOBS_Installer_LINUX.run
zip KLOBS_Windows_Setup.zip KLOBS_Windows_Setup.exe

