<?php
/******************************************************************************
 * Konfigurationsdatei von Admidio
 *
 * Copyright    : (c) 2010-2018 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : https://github.com/stko/kobs
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

// Gibt den Namen der Rolle an, die ein User braucht, um die Funktionen des klobs- Moduls nutzen zu dürfen
$klobs_trainer = "TRAINER";

// Gibt den Namen der Rolle an, die ein User braucht, damit er im Klobs-Client aufgelistet wird und so
// z.B. Ehemalige unterdrückt werden 
$klobs_member = "MITGLIEDER";

// Gibt den Namen des Feldes an, in der sich die Kartennummer befindet
$klobs_card = "KARTENNUMMER";

// Gibt den Namen des Feldes an, in der sich die zweite Klassifizierung befindet
$klobs_belt = "GURT";

// Gibt den Tabellennamen an, in dem sich die Trainingseinheiten befinden
$klobs_training_table = "adm_klobs_training";

// Gibt den Dateinamen an, in dem sich die Trainingseinheiten befinden
$klobs_training_file = "trainings.xml";

// Gibt den Dateinamen an, in dem sich die Locations befinden
$klobs_location_file = "locations.xml";

// Definiert die Überschrift auf der MyInfo-Seite
$klobs_myinfo_header = "Shojikido.de: Deine pers&ouml;nlichen Trainingsdaten im &Uuml;berblick";

// Definiert die Überschrift auf der printCard-Seite
$klobs_printCards_header = "Shojikido.de: Druck der Mitglieder-Karten";

// Gibt die indexnummer an, die in "trainings.xml" fuer Prüfungen vergeben ist
$trainings_type_audit = 2;

// Gibt die indexnummer an, die in "trainings.xml" fuer Prüfungen vergeben ist
$trainings_type_seminar = 3;
?>
