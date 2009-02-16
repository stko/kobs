<?php
/******************************************************************************
 * Mitgliederdaten für Kobs als XML- Stream abrufen
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

$g_realm = $g_realm;
//Loginprozess anstossen
if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
	authentifizieren('Login cancelled',$g_realm);
}


// Analysieren der Variable PHP_AUTH_DIGEST
if (!($daten = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])))  {
	authentifizieren('Falsche Zugangsdaten!',$g_realm);
}

if (!isset($kobs_trainer)) {
	$kobs_trainer="Trainer"; //default
}


// Ist der User Mitglied der $kobs_trainer- Rolle, und wie ist sein Passwort- hash?
$sql    = "SELECT ". TBL_USERS.".usr_password
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
AND ". TBL_ROLES . ".rol_name = \"".$kobs_trainer."\"
AND ". TBL_USERS.".usr_login_name = \"".$daten['username']."\"";


$dates_result = $g_db->query($sql);
 
$row = $g_db->fetch_array($dates_result);
if (count($row)<1){
	authentifizieren('Unbekannter User',$g_realm);
}

 //Der zum Berechnen gültige Hash wurde schon damals beim Anlegen des Userpassworts berechnet
// und als Userpasswort-Hash gespeichert. Das war der einzige Zeitpunkt, an dem das Userpasswort 
// im Klartext zur Verfügung steht.
$digestHash=$row[0];


// Überprüfen einer gültigen Antwort
// Diese setzt sich normalerweise folgendermaßen zusammen
//
//  $A1 = md5(USERNAME . ':' . $g_realm . ':' . PASSWORD);
//
// nun kennt man das Password aber ja auf der Serverseite gar nicht,
// weil ja stattdessen nur dessen MD5-Hash gespeichert ist
// also muß man beim Anlegen des Passwort- Eintrags gleich den MD5-Hash
// über den gesamten oben genannten Ausruck erzeugen 

$A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $daten['uri']);
$gueltige_antwort = md5($digestHash . ':' . $daten['nonce'] . ':' . $daten['nc'] .':' . $daten['cnonce'] . ':' . $daten['qop'] . ':' . $A2);

if ($daten['response'] != $gueltige_antwort){
	authentifizieren('Falsches Passwort',$g_realm);
}

// OK, gültiger Benutzername & Passwort


//Array aller User-IDs erstellen, die Trainer sind
$sql    = "SELECT ". TBL_USERS.".usr_id
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
AND ". TBL_ROLES . ".rol_name = \"".$kobs_trainer."\"";




$db_result = $g_db->query($sql);
 
while($row = $g_db->fetch_array($db_result))
{
	$trainer_Ids[$row[0]]=1;
}



    //Falls gefordert, aufrufen alle Leute aus der Datenbank
    $sql = "SELECT usr_id, last_name.usd_value as last_name, first_name.usd_value as first_name, birthday.usd_value as birthday, 
                   city.usd_value as city, phone.usd_value as phone, address.usd_value as address, zip_code.usd_value as zip_code,
                   kartennummer.usd_value as kartennummer, gurt.usd_value as gurt
            FROM ". TBL_USERS. "
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
             AND kartennummer.usd_usf_id = ". $g_current_user->getProperty($kobs_card, "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as gurt
              ON gurt.usd_usr_id = usr_id
             AND gurt.usd_usf_id = ". $g_current_user->getProperty($kobs_belt, "usf_id"). "
            WHERE usr_valid = 1
            ORDER BY last_name, first_name ";


$result_user = $g_db->query($sql);

//Beginn der XML-Ausgabe

header ("content-type: text/xml"); 
echo '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>';
// echo '<?xml-stylesheet href="kobsdata.xsl" type="text/xsl">';

echo "<kobsdata>\n";

// Ausgabe der Mitglieder
echo "  <members>\n";

while($row = $g_db->fetch_array($result_user))
{
	echo "  <member>\n";
	foreach ($row as $key => $value){
		if (!is_numeric($key)) {
			echo "    <$key>$value</$key>\n";
		}
	}
	echo "    <trainer>".(isset($trainer_Ids[$row["usr_id"]]) ? "True" : "False")."</trainer>\n";
	echo "  </member>\n";
}
echo "  </members>\n";



// Ausgabe der Trainingsorte
print <<<ORTE
  <orte>
     <ort>Klippkanner Schule, Brake</ort>
     <ort>BBZ, Brake</ort>
  </orte>
ORTE;

// Ausgabe der Trainingsarten
print <<<TRAININGS
  <trainings>
     <typ id="0" name="Anwesend">
	     <subtyp id="0" name="Anwesend"/>
	     <subtyp id="1" name="Krank/Verletzt"/>
     </typ>
  </trainings>
TRAININGS;

//Ende der XML -Datei
echo "</kobsdata>\n";



// Löscht den Username cache beim Client im Falle einer ungültigen Anmeldung
function authentifizieren($title,$g_realm) {
/*    header('WWW-Authenticate: Basic realm="Test Authentication System"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Bitte geben Sie eine gültige Login-ID und das Passwort für den
        Zugang ein\n";
    exit;
*/
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="' . $g_realm .
           '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($g_realm) .
           '"');

    die($title);

}



// function to parse the http auth header
function http_digest_parse($txt)
{
   
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();

    preg_match_all('@(\w+)=(?:([\'"])([^$2]+)$2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
   
    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? trim($m[3],"\",\'") : trim($m[4],"\",\'");
        unset($needed_parts[$m[1]]);
    }
   foreach ($needed_parts as $n) {
   }

    return $needed_parts ? false : $data;
}

?>