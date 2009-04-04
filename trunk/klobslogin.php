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

$g_realm = $g_realm;
//Loginprozess anstossen
if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
	authentifizieren('Login cancelled',$g_realm);
}


// Analysieren der Variable PHP_AUTH_DIGEST
if (!($daten = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])))  {
	authentifizieren('Falsche Zugangsdaten!',$g_realm);
}

if (!isset($klobs_trainer)) {
	$klobs_trainer="Trainer"; //default
}


// Ist der User Mitglied der $klobs_trainer- Rolle, und wie ist sein Passwort- hash?
$sql    = "SELECT ". TBL_USERS.".usr_password
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
AND ". TBL_ROLES . ".rol_name = \"".$klobs_trainer."\"
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
// gültiger User- ab hier dann weiter im aufrufenden Script...






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