# Installation & Update #

Zum Update vorab: Wenn Klobs bereits auf einem Rechner installiert ist, d.h. es sind die notwendigen Einträge vorhanden, die Klobs im jeweiligen Desktop-Menü erscheinen lassen, ist es einfach, hier ein Update zu machen, weil nur die Programmdateien selber ausgetauscht werden müssen.

## Download ##

Zuerst einmal geht man auf die Klobs- Downloadseite http://code.google.com/p/kobs/downloads/list


![http://kobs.googlecode.com/svn/trunk/docs/images/install_download.png](http://kobs.googlecode.com/svn/trunk/docs/images/install_download.png)

und lädt das aktuellste Programmarchiv zum Beispiel auf den Desktop.

## Linux Installation ##
Beim Linux- Installer-ZIP- File wird man gefragt, ob man die ZIP- Datei mit einem Archiv- Betrachter öffnen möchte

Dies bejaht man und landet im Archivbetrachter

![http://kobs.googlecode.com/svn/trunk/docs/images/install_unpack.png](http://kobs.googlecode.com/svn/trunk/docs/images/install_unpack.png)

Dort wählt man dann "Entpacken" (in ein beliebiges Verzeichnis). Nach dem Entpacken wird man gefragt, ob man in das Entpackverzeichnis wechseln möchte ("Ziel öffnen"). Dies bejaht man ebenfalls

![http://kobs.googlecode.com/svn/trunk/docs/images/install_go_directory.png](http://kobs.googlecode.com/svn/trunk/docs/images/install_go_directory.png)


Dadurch landet man im Dateimanager und sieht dort die Datei KLOBS\_Installer\_LINUX.run.


![http://kobs.googlecode.com/svn/trunk/docs/images/install_run.png](http://kobs.googlecode.com/svn/trunk/docs/images/install_run.png)


Auf diese Datei macht man einen Doppelklick und wird dann gefragt, ob man die Datei "Ausführen" oder "Anzeigen" möchte.


![http://kobs.googlecode.com/svn/trunk/docs/images/install_terminal.png](http://kobs.googlecode.com/svn/trunk/docs/images/install_terminal.png)

Da kann man dann "Ausführen im Terminal" auswählen. Es huscht dann ein Fenster vorbei und die neue Version ist installiert


### Installieren der QRCode - Software ###

Die für QRCode notwendige Software holt man sich per
> sudo apt-get install zbar-tools


## Windows Installation ##
Man downloaded den Windows- Installer und führt ihn aus...


## Konfiguration ##

Standardmäßig ist die Kamera als erste Kamera im System (Windows) bzw /dev/video (Linux) und die RFID-Schnittstelle als /dev/ttyUSB0 (Linux) eingestellt, was eigentlich immer passt.

Lediglich unter Windows müßte man bei Verwendung eines RFID-Lesers die richtige COM- Schnittstelle einstellen. Diese Einstellung macht man mit einem Texteditor, der Unix- Zeilenumbrüche beherrschen muß (z.B. Notepad++) in der Datei %appdata%\Klobs\klobsserial.props

## Probleme ? ##
Bei Problemen bitte einen Eintrag in den [Bugtracker](http://code.google.com/p/kobs/issues/list)