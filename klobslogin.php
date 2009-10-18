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
	showLogin("wrong password");
}
// gültiger User- ab hier dann weiter im aufrufenden Script...


function showLogin($info){
print '
<html>
<head>
<title>Login</title>
<script language="javascript" src="md5.js"></script>
<script language="javascript">
<!--
  function doChallengeResponse() {
    document.login.pw.value = MD5(document.login.password.value);
    document.login.password.value = "";
  }
// -->
</script>

</head>

<body>
'.$info.'<br>
<form name="login" action="'.$_SERVER['PHP_SELF'].'" method="post" onSubmit="doChallengeResponse(); return true;">
<table>
<tr>
<td>User</td>
<td><input type="text" name="user"/></td>
</tr>
<tr>
<td>Passwort</td>
<td><input type="password" name="password"/></td>
</tr>
<tr>
<td><input type="hidden" name="pw"/></td>
<td><input type="submit" value="Login" /></td>
</tr></table>
</form>

After login, the training data will be transfered as a .csv file, which can be used to make pretty pivot table statistics in a spreadsheet programm like OpenOffice Calc


</body></html>';
exit;
}





?>