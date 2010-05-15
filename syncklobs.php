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

require("../../system/common.php");
include("./config.php");

//-Anmelden per HTTP & gültiger Trainer- Rolle
require("./klobslogin.php");



// OK, gültiger Benutzername & Passwort --------------------------------------------------------------------

// Einlesen der übertragenen Session- Data:
$data = file_get_contents("php://input");
updateTables($data);

//Array aller User-IDs erstellen, die Trainer sind
$sql    = "SELECT ". TBL_USERS.".usr_id
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
AND ". TBL_ROLES . ".rol_name = \"".$klobs_trainer."\"";




$db_result = $g_db->query($sql);
 
while($row = $g_db->fetch_array($db_result))
{
	$trainer_Ids[$row[0]]=1;
}


//Array aller User-IDs erstellen, die Mitglied und valid sind
$sql    = "SELECT ". TBL_USERS.".usr_id
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
AND ". TBL_MEMBERS . ".mem_valid = 1 
AND ". TBL_ROLES . ".rol_name = \"".$klobs_member."\"";




$db_result = $g_db->query($sql);
 
while($row = $g_db->fetch_array($db_result))
{
	$member_Ids[$row[0]]=1;
}



    //Falls gefordert, aufrufen alle Leute aus der Datenbank
    $sql = "SELECT
		  ". TBL_USERS. ".usr_id as usr_id,
		  last_name.usd_value as last_name,
		  first_name.usd_value as first_name,
		  birthday.usd_value as birthday, 
                  city.usd_value as city,
		  phone.usd_value as phone,
		  address.usd_value as address,
		  zip_code.usd_value as zip_code,
                  kartennummer.usd_value as kartennummer, 
		  gurt.usd_value as gurt
	    FROM ". TBL_USERS . "
            LEFT JOIN ". TBL_USER_DATA. " as last_name
              ON last_name.usd_usr_id = usr_id
             AND last_name.usd_usf_id = ". $g_current_user->getProperty("Nachname", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as first_name
              ON first_name.usd_usr_id = usr_id
             AND first_name.usd_usf_id = ". $g_current_user->getProperty("Vorname", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as birthday
              ON birthday.usd_usr_id = usr_id
             AND birthday.usd_usf_id = ". $g_current_user->getProperty("Geburtstag", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as city
              ON city.usd_usr_id = usr_id
             AND city.usd_usf_id = ". $g_current_user->getProperty("Ort", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as phone
              ON phone.usd_usr_id = usr_id
             AND phone.usd_usf_id = ". $g_current_user->getProperty("Telefon", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as address
              ON address.usd_usr_id = usr_id
             AND address.usd_usf_id = ". $g_current_user->getProperty("Adresse", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as zip_code
              ON zip_code.usd_usr_id = usr_id
             AND zip_code.usd_usf_id = ". $g_current_user->getProperty("PLZ", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as kartennummer
              ON kartennummer.usd_usr_id = usr_id
             AND kartennummer.usd_usf_id = ". $g_current_user->getProperty($klobs_card, "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as gurt
              ON gurt.usd_usr_id = usr_id
             AND gurt.usd_usf_id = ". $g_current_user->getProperty($klobs_belt, "usf_id"). "
            WHERE usr_valid = 1
            ORDER BY last_name, first_name ";


$result_user = $g_db->query($sql);

//Beginn der XML-Ausgabe

header ("content-type: text/xml"); 
echo '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>';
// echo '<?xml-stylesheet href="klobsdata.xsl" type="text/xsl">';
echo '<!-- validdata -->';

echo "<klobsdata>\n";

// Ausgabe der Mitglieder
echo "  <members>\n";

while($row = $g_db->fetch_array($result_user))
{
	if (isset($member_Ids[$row["usr_id"]])){// wenn Mitglied, dann
//	if (1){// wenn Mitglied, dann
	  echo "  <member>\n";
	  foreach ($row as $key => $value){
		  if (!is_numeric($key)) {
			  echo "    <$key>$value</$key>\n";
		  }
	  }
	  echo "    <trainer>".(isset($trainer_Ids[$row["usr_id"]]) ? "True" : "False")."</trainer>\n";
	  echo "  </member>\n";
	}
}
echo "  </members>\n";



// Ausgabe der Trainingsorte
readfile($klobs_location_file);

// Ausgabe der Trainingsarten
readfile($klobs_training_file);

//Ende der XML -Datei
echo "</klobsdata>\n";



// erzeugen des SQL-Strings zum Updaten der Datenbank
function updateTables($sessiondata)
{
	global $g_current_user , $klobs_training_table , $klobs_card , $klobs_belt  ,$g_db;
	if (isset($sessiondata) && $sessiondata!="") {
		$SQLString="";
		$xml = simplexml_load_string($sessiondata);
		foreach ($xml->updates as $data) {
			$needed_updateData = array(
					'kartennummer'=>$g_current_user->getProperty($klobs_card, "usf_id"),
					'last_name'=>$g_current_user->getProperty("Nachname", "usf_id"),
					'city'=>$g_current_user->getProperty("Ort", "usf_id"),
					'address'=>$g_current_user->getProperty("Adresse", "usf_id"),
					'birthday'=>$g_current_user->getProperty("Geburtstag", "usf_id"),
					'phone'=>$g_current_user->getProperty("Telefon", "usf_id"),
					'first_name'=>$g_current_user->getProperty("Vorname", "usf_id"),
					'gurt'=>$g_current_user->getProperty($klobs_belt, "usf_id"),
					'zip_code'=>$g_current_user->getProperty("PLZ", "usf_id")
					);
			foreach ($data->member as $member) {
				if (isset($member->usr_id)){
					foreach ($needed_updateData as $field=>$index) {
						if (isset($member->$field)){
							$SQLString.=  "REPLACE INTO ". TBL_USER_DATA. " SET usd_value=\"".mysql_real_escape_string($member->$field)."\" , ";
							$SQLString.=  "  usd_usr_id = ".mysql_real_escape_string($member->usr_id)." , usd_usf_id=  ".$index;
							$db_result = $g_db->query($SQLString);
							$SQLString="";
						}
					}
				}
			}
		}
		foreach ($xml->trainings as $data) {
			$needed_updateData = array('usr_id'=>0,'typ'=>0,'subtyp'=>0,'trainerid'=>0,'starttime'=>1,'duration'=>0);
			foreach ($data->training as $member) {
				if (isset($member->usr_id)){
					$someOut=false;
					$SQLString.=  "INSERT IGNORE INTO ".$klobs_training_table." SET ";
					$SQLString.=  "locationId = ".mysql_real_escape_string($data->locationid)." , ";
					$SQLString.=  "location = \"".mysql_real_escape_string($data->location)."\" , ";
					$timestamp=strtotime ($data->date);
					$my_t=getdate($timestamp);
					$SQLString.= "timestamp = ".$timestamp." , ";
					$SQLString.=  "year = ".$my_t["year"]." , ";
					$SQLString.=  "mon = ".$my_t["mon"]." , ";
					$SQLString.=  "mday = ".$my_t["mday"]." , ";
					$SQLString.=  "wday = ".$my_t["wday"]." , ";
					$SQLString.=  "date = \"".date("Y-m-d",$timestamp)."\" , ";
					foreach ($needed_updateData as $field=>$textFormat) {
						if ($someOut){
							$SQLString.=  " , ";
						}
						else {
							$someOut=true;
						}
						if (isset($member->$field)){
							$SQLString.=  $field." =";
							if ($textFormat==1){ //text ausgabe?
								$SQLString.=  "\"".mysql_real_escape_string($member->$field)."\"";
							}
							else{ //Zahl ausgabe?
								$SQLString.=  mysql_real_escape_string($member->$field);
							}
						}
						$SQLString.=  " ";
					}
					// Berechnen der Anfangszeit als Int- Value
					if (isset($member->starttime)){
						if ($someOut){
							$SQLString.=  " , ";
						}
						$SQLString.=  "starttimeint =";
						list($h,$m)=split(":",$member->starttime);
						$total = $h*60 + $m;
						$SQLString.=  $total;
					}
					$db_result = $g_db->query($SQLString);
					$SQLString="";

				}
			}
		}
		return $SQLString;
	} 
	else {
		return "";
	}
}


?>