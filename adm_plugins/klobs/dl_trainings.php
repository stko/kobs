<?php
/******************************************************************************
 * Mitgliederdaten für Klobs als XML- Stream abrufen
 *
 * Copyright    : (c) 2010 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : kobs.googlecode.com
* Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 *****************************************************************************/

	require_once("../../system/common.php");
	include("./config.php");
	require_once("../../system/login_valid.php");

	$a_user_id = $gCurrentUser->getValue("usr_id");


	// Darf der angemeldete User Mitglieder editieren?
	if(!$gCurrentUser->editUsers()){
	    $gMessage->show("Du hast leider nicht die notwendigen Rechte, um Trainingsdaten runterladen zu d&uuml;rfen..");
	}


// OK, gültiger Benutzername & Passwort --------------------------------------------------------------------




    //Falls gefordert, aufrufen alle Leute aus der Datenbank
    $sql = "SELECT last_name.usd_value as last_name, first_name.usd_value as first_name , training.location as location, training.date as date, training.year as year, training.mon as mon, training.mday as mday, training.wday as wday, training.typ as typ, training.subtyp as subtyp, training.trainerid as trainerid, training.duration as duration


            FROM ". TBL_USERS. "
            LEFT JOIN ". TBL_USER_DATA. " as last_name
              ON last_name.usd_usr_id = ". TBL_USERS. ".usr_id
             AND last_name.usd_usf_id = ". $gProfileFields->getProperty("LAST_NAME",  "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as first_name
              ON first_name.usd_usr_id = ". TBL_USERS. ".usr_id
             AND first_name.usd_usf_id = ". $gProfileFields->getProperty("FIRST_NAME",  "usf_id"). "
            JOIN " . $klobs_training_table . " as training
              ON training.usr_Id = ". TBL_USERS. ".usr_id
            WHERE usr_valid = 1
		AND training.deleted = 0
            ORDER BY last_name, first_name ";


$db_result = $gDb->query($sql);

//Beginn der Ausgabe

header ("content-type: application/vnd.ms-excel; charset=iso-8859-1"); 
header('Content-Disposition: attachment; filename="Trainingsdata.txt"');

// Ausgabe der Daten
	echo "last_name\t";
	echo "first_name\t";
	echo "location\t";
	echo "date\t";
	echo "year\t";
	echo "mon\t";
	echo "mday\t";
	echo "wday\t";
	echo "typ\t";
	echo "subtyp\t";
	echo "trainerid\t";
	echo "duration\n";
while($row = $db_result->fetch())
{
/*	foreach ($row as $key => $value){
		if (!is_numeric($key)) {
			echo "$key: $value\n";
		}
	}
	echo "---\n";
*/
	echo $row['last_name']."\t";
	echo $row['first_name']."\t";
	echo $row['location']."\t";
	echo $row['date']."\t";
	echo $row['year']."\t";
	echo $row['mon']."\t";
	echo $row['mday']."\t";
	echo $row['wday']."\t";
	echo $row['typ']."\t";
	echo $row['subtyp']."\t";
	echo $row['trainerid']."\t";
	echo $row['duration']."\n";
}

$_referer = $_SERVER["HTTP_REFERER"];
if (strpos($_referer,$_SERVER['PHP_SELF'])>0){
      $_referer="trmanage.php";
}


header("Location: ".$_referer); /* Redirect browser back to original page*/

?>