<?php
/******************************************************************************
 * Downloads anhand von Datumsfeldern erlauben
 *
 * Copyright    : (c) 2009 - 2010  Shojikido-Karate Brake
 * Homepage     : http://www.shojikido.de
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 *****************************************************************************/

require("../../system/common.php");
include("./config.php");
require("../../system/login_valid.php");
    $a_user_id = $g_current_user->getValue("usr_id");




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
            AND usr_id = ".$a_user_id."
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
echo "<user_id>$a_user_id</user_id>\n";

while($row = $g_db->fetch_array($result_user))
{
//	if (isset($member_Ids[$row["usr_id"]])){// wenn Mitglied, dann
	if (1){// wenn Mitglied, dann
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


//Ende der XML -Datei
echo "</klobsdata>\n";


?>