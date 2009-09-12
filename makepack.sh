#!/usr/bin/sh 
rm -rf /media/ram/klobs
rm /media/ram/klops.zip
mkdir /media/ram/klobs
cp -r client/java/application/Kobs/dist/* /media/ram/klobs
cp client/java/application/Kobs/Klobslogo* /media/ram/klobs
cp client/java/interface/serial/*.class client/java/interface/serial/*.lang client/java/interface/serial/*.props  /media/ram/klobs
cp client/java/application/Kobs/*.lang client/java/application/Kobs/*.props  /media/ram/klobs
cp client/java/interface/serial/lib/* /media/ram/klobs/lib
cp runKlobs.sh /media/ram/klobs/
cp Klobs.desktop /media/ram/klobs/
cp shojikido_logo_h480.png /media/ram/klobs/
cp installklobs.sh /media/ram/klobs/
# (cd /media/ram ; zip -r Klobs.zip klobs)

./makeself/makeself.sh  /media/ram/klobs/ KLOBS_Installer_LINUX.run "Klobs- der Kartenleser ohne besonderen Schwierigkeitsgrad" ./installklobs.sh
zip KLOBS_Installer.zip KLOBS_Installer_LINUX.run
