<?php
/******************************************************************************
 * Mitgliederdaten für Klobs als XML- Stream abrufen
 *
 * Copyright    : (c) 2009 - 2009 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 *****************************************************************************/

require("../../system/common.php");
include("./config.php");


//-Anmelden per HTTP & gültiger Trainer- Rolle
require("./klobslogin.php");

// OK, gültiger Benutzername & Passwort --------------------------------------------------------------------




    //Falls gefordert, aufrufen alle Leute aus der Datenbank
    $sql = "SELECT last_name.usd_value as last_name, first_name.usd_value as first_name , training.location as location, training.date as date
            FROM ". TBL_USERS. "
            LEFT JOIN ". TBL_USER_DATA. " as last_name
              ON last_name.usd_usr_id = ". TBL_USERS. ".usr_id
             AND last_name.usd_usf_id = ". $g_current_user->getProperty("Nachname", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as first_name
              ON first_name.usd_usr_id = ". TBL_USERS. ".usr_id
             AND first_name.usd_usf_id = ". $g_current_user->getProperty("Vorname", "usf_id"). "
            JOIN " . $klobs_training_table . " as training
              ON training.usr_Id = ". TBL_USERS. ".usr_id
            WHERE usr_valid = 1
            ORDER BY last_name, first_name ";


$db_result = $g_db->query($sql);

//Beginn der Ausgabe

header ("content-type: application/vnd.ms-excel; charset=iso-8859-1"); 
header('Content-Disposition: attachment; filename="Trainingsdata.txt"');

// Ausgabe der Daten
	echo "last_name\t";
	echo "first_name\t";
	echo "location\t";
	echo "date\n";
while($row = $g_db->fetch_array($db_result))
{
/*	foreach ($row as $key => $value){
		if (!is_numeric($key)) {
			echo "$key: $value\n";
		}
	}
	echo "---\n";
*/
	echo $row[last_name]."\t";
	echo $row[first_name]."\t";
	echo $row[location]."\t";
	echo $row[date]."\n";
}




?>