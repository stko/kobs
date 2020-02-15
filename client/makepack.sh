#!/bin/sh 
rm -rf /media/ram/klobs
rm /media/ram/klops.zip
mkdir /media/ram/klobs
cp -r java/application/Kobs/dist/* /media/ram/klobs
cp java/application/Kobs/Klobslogo* /media/ram/klobs
cp java/interface/serial/*.jar java/interface/serial/*.lang java/interface/serial/*.props  java/interface/serial/*.xpm /media/ram/klobs
cp java/interface/qrcode/*.jar java/interface/qrcode/*.lang java/interface/qrcode/*.props  java/interface/qrcode/*.xpm /media/ram/klobs
cp java/application/Kobs/*.lang  /media/ram/klobs
cp java/interface/qrcode/lib/* /media/ram/klobs/lib
cp java/interface/serial/lib/* /media/ram/klobs/lib
cp run*.sh /media/ram/klobs/
cp *.desktop /media/ram/klobs/
cp shojikido_logo_h480.png /media/ram/klobs/
cp installklobs.sh /media/ram/klobs/
# (cd /media/ram ; zip -r Klobs.zip klobs)

./makeself/makeself.sh  /media/ram/klobs/ KLOBS_Installer_LINUX.run "Klobs- der Kartenleser ohne besonderen Schwierigkeitsgrad" ./installklobs.sh
makensis klobs_windows_setup.nsi
zip KLOBS_Linux_Setup.zip KLOBS_Installer_LINUX.run
zip KLOBS_Windows_Setup.zip KLOBS_Windows_Setup.exe
DIR=$(pwd)
(cd .. ; zip $DIR/KLOBS_Server_Setup.zip -r adm_plugins)

