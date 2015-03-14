# Der QRCode Kartenleser #

Dank der Arbeit des [ZBar- Teams](http://zbar.sourceforge.net/), die ab Version 0.10 auch einen supergut funktionierenden QRCode- Leser über die eingebaute Webcam eines Laptops bereit stellen, konnte KLOBS jetzt um die Möglichkeit ergänzt werden, Mitgliederkarten über die Kamera einlesen zu können.

Das bringt im Gegensatz zum [RFID- Kartenleser](rfid_cardreader.md) zwei große Vorteile:
  * Man braucht keine spezielle Kartenleser- Hardware mehr, jeder Laptop mit Kamera kann's genauso
  * Man braucht keine speziellen Karten mehr, sondern jedes Mitglied kann und soll sich seine Karte selber drucken.
  * Beides zusammen spart viel Geld und entlastet die Vereinsführung


Zum Selberdrucken der Karten gibts dann einen extra Link `<myserver>/admidio/adm_program/modules/kobs/docard.php`, den ein Mitglied bloß ansurfen braucht und unmittelbar seine persönliche Mitgliedskarte als PFD- Download übertragen bekommt.

Er braucht nur noch ausdrucken und ausschneiden :-)