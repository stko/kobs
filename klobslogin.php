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

$user=$_REQUEST["user"];
$pw=$_REQUEST["pw"];

if (!isset($klobs_trainer)) {
	$klobs_trainer="Trainer"; //default
}

if(!(isset($user) && isset($pw) && $user!="" && $pw!="")){
	showlogin("Login");
}

// Ist der User Mitglied der $klobs_trainer- Rolle, und wie ist sein Passwort- hash?
$sql    = "SELECT ". TBL_USERS.".usr_password
FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
AND ". TBL_ROLES . ".rol_name = \"".$klobs_trainer."\"
AND ". TBL_USERS.".usr_login_name = \"".$user."\"";


$dates_result = $g_db->query($sql);
 
$row = $g_db->fetch_array($dates_result);
if (count($row)<1){
	showLogin("user unknown");
}

 //Abholen des Password- Hashes.
$digestHash=$row[0];



if ($digestHash != $pw){
	echo $digestHash."--".$pw;
	showLogin("wrong password");
}
// gültiger User- ab hier dann weiter im aufrufenden Script...


function showLogin($info){
print '
<html><head><title>Login</title></head>
<body>'.$info.'<br>
<form action="'.$_SERVER['PHP_SELF'].'" method="get">User<input type="text" name="user"/> Passwort<input type="text" name="pw"/>
<input type="submit" value="Login" />
</form></body></html>';
exit;
}





?>