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

require_once("../../system/common.php");
include("./config.php");

//-Anmelden per HTTP & gültiger Trainer- Rolle
require_once("./klobslogin.php");



// OK, gültiger Benutzername & Passwort --------------------------------------------------------------------

// Einlesen der übertragenen Session- Data:
$post = file_get_contents("php://input");

foreach (explode('&', $post) as $chunk) {
    $param = explode("=", $chunk);

    if ($param and urldecode($param[0])==="data") {
        updateTables(urldecode($param[1]));
    }
}

//Array aller User-IDs erstellen, die Trainer sind
$sql    = "SELECT ". TBL_USERS.".usr_id
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id 
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id 
AND ". TBL_ROLES . ".rol_name = '".$klobs_trainer."'";


    $rolesStatement = $gDb->query($sql);
    //error_log("gDb Error:\n".$gDb->showError());
    $rolesData      = $rolesStatement->fetchAll();

    foreach ($rolesData as $role){
	$trainer_Ids[$role['usr_id']]=1;
    }

$rolesStatement->closeCursor();

//Array aller User-IDs erstellen, die Mitglied und valid sind
$sql    = "SELECT ". TBL_USERS.".usr_id
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
AND ". TBL_MEMBERS . ".mem_begin <= NOW() 
AND ". TBL_MEMBERS . ".mem_end >= NOW() 
AND ". TBL_ROLES . ".rol_name = '".$klobs_member."'";

$db_result = $gDb->query($sql);

while($row = $db_result->fetch())
{
	$member_Ids[$row[0]]=1;
}

$db_result->closeCursor();


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
             AND last_name.usd_usf_id = ". $gProfileFields->getProperty("LAST_NAME", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as first_name
              ON first_name.usd_usr_id = usr_id
             AND first_name.usd_usf_id = ". $gProfileFields->getProperty("FIRST_NAME", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as birthday
              ON birthday.usd_usr_id = usr_id
             AND birthday.usd_usf_id = ". $gProfileFields->getProperty("BIRTHDAY", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as city
              ON city.usd_usr_id = usr_id
             AND city.usd_usf_id = ". $gProfileFields->getProperty("CITY", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as phone
              ON phone.usd_usr_id = usr_id
             AND phone.usd_usf_id = ". $gProfileFields->getProperty("PHONE", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as address
              ON address.usd_usr_id = usr_id
             AND address.usd_usf_id = ". $gProfileFields->getProperty("ADDRESS", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as zip_code
              ON zip_code.usd_usr_id = usr_id
             AND zip_code.usd_usf_id = ". $gProfileFields->getProperty("POSTCDDE", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as kartennummer
              ON kartennummer.usd_usr_id = usr_id
             AND kartennummer.usd_usf_id = ". $gProfileFields->getProperty($klobs_card, "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as gurt
              ON gurt.usd_usr_id = usr_id
             AND gurt.usd_usf_id = ". $gProfileFields->getProperty($klobs_belt, "usf_id"). "
            WHERE usr_valid = 1
            ORDER BY last_name, first_name ";


$result_user = $gDb->query($sql);

//Beginn der XML-Ausgabe

header ("content-type: text/xml"); 
echo '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>';
// echo '<?xml-stylesheet href="klobsdata.xsl" type="text/xsl">';
echo '<!-- validdata -->';

echo "<klobsdata>\n";

// Ausgabe der Mitglieder
echo "  <members>\n";
//error_log("SQL:\n$sql");
//$gDb->showError();
while($row = $result_user->fetch())
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
readfile("locations.xml");

// Ausgabe der Trainingsarten
readfile("trainings.xml");

//Ende der XML -Datei
echo "</klobsdata>\n";



// erzeugen des SQL-Strings zum Updaten der Datenbank
function updateTables($sessiondata)
{
	/*
	  Please Read! in case of later changes:
	  We using $gDb->escapeString() here to build SQL statements.
	  The escapeString() functions returns Strings already quoted with ' ', so if escapeString() will be replaced somewhere later,
	  we have to take care for the ' ' again!
	*/
	
	global $gCurrentUser , $klobs_training_table , $klobs_card , $klobs_belt  ,$gDb, $gProfileFields;
	if (isset($sessiondata) && $sessiondata!="") {
		$SQLString="";
		$xml = simplexml_load_string($sessiondata);
		//field names coming from the client
		$needed_updateData = array('kartennummer','last_name', 'city', 'address', 'birthday', 'phone', 'first_name', 'gurt', 'zip_code');

		foreach ($xml->updates as $data) {
			/*
			$needed_updateData = array(
					'kartennummer'=>$gCurrentUser->getValue($klobs_card, "usf_id"),
					'last_name'=>$gCurrentUser->getValue("Nachname", "usf_id"),
					'city'=>$gCurrentUser->getValue("Ort", "usf_id"),
					'address'=>$gCurrentUser->getValue("Adresse", "usf_id"),
					'birthday'=>$gCurrentUser->getValue("Geburtstag", "usf_id"),
					'phone'=>$gCurrentUser->getValue("Telefon", "usf_id"),
					'first_name'=>$gCurrentUser->getValue("Vorname", "usf_id"),
					'gurt'=>$gCurrentUser->getValue($klobs_belt, "usf_id"),
					'zip_code'=>$gCurrentUser->getValue("PLZ", "usf_id")
					);
			foreach ($data->member as $member) {
				if (isset($member->usr_id)){
					foreach ($needed_updateData as $field=>$index) {
						if (isset($member->$field)){
							$SQLString.=  "REPLACE INTO ". TBL_USER_DATA. " SET usd_value='".mysql_real_escape_string($member->$field)."' , ";
							$SQLString.=  "  usd_usr_id = ".mysql_real_escape_string($member->usr_id)." , usd_usf_id=  ".$index;
							$db_result = $gDb->query($SQLString);
							$SQLString="";
						}
					}
				}
			}
			*/
			foreach ($data->member as $member) {
				if (isset($member->usr_id)){
					 error_log("Update User id: ".$member->usr_id);

					$thisUser= new User($gDb, $gProfileFields, $member->usr_id );
					foreach ($needed_updateData as $fieldName) {
						if (isset($member->$fieldName)){
							$thisUser->SetValue(strtoupper($fieldName),$member->$fieldName);
						}
					}
					try {
					  $thisUser->save();
					} catch (Exception $e) {
					  error_log("User Save Error:\n".$e->getMessage());
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
					$SQLString.=  "locationId = ".$gDb->escapeString($data->locationid)." , ";
					$SQLString.=  "location = ".$gDb->escapeString($data->location)." , ";
					if (($timestamp = strtotime($data->date)) === false) { //in the string date a valid date?
						$timestamp = strtotime("1.1.1970") ;
					}
					$my_t=getdate($timestamp);
					$SQLString.= "timestamp = ".$timestamp." , ";
					$SQLString.=  "year = ".$my_t["year"]." , ";
					$SQLString.=  "mon = ".$my_t["mon"]." , ";
					$SQLString.=  "mday = ".$my_t["mday"]." , ";
					$SQLString.=  "wday = ".$my_t["wday"]." , ";
					$SQLString.=  "date = '".date("Y-m-d",$timestamp)."' , ";
					foreach ($needed_updateData as $field=>$textFormat) {
						if ($someOut){
							$SQLString.=  " , ";
						}
						else {
							$someOut=true;
						}
						if (isset($member->$field)){
							$SQLString.=  $field." =";
							if ($textFormat==1){ //text ausgabe? (in a previous version we had some ' ' quotes here, but not yet anymore
								$SQLString.=  $gDb->escapeString($member->$field);
							}
							else{ //Zahl ausgabe?
								$SQLString.=  $gDb->escapeString($member->$field);
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
						$timeValues=explode(":",$member->starttime);
						$total = $timeValues[0]*60 + $timeValues[1];
						$SQLString.=  $total;
					}
					$db_result = $gDb->query($SQLString);
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