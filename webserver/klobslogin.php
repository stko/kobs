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
require_once("../../system/login_func.php");


if (!isset($klobs_trainer)) {
	$klobs_trainer="Trainer"; //default
}


    $checkLoginReturn =createUserObjectFromPost();
    if (is_string($checkLoginReturn))
    {
        //$gMessage->show($checkLoginReturn);
        showLogin($checkLoginReturn);
        // => EXIT
    }




// Darf der angemeldete User Mitglieder editieren?
if(!$gCurrentUser->editUsers()){
showLogin("Du hast leider nicht die notwendigen Rechte, um alle Trainingsdaten sehen zu d&uuml;rfen..");
}



function showLogin($info){
//$info= htmlentities($info);
print '
<html>
<head>
<title>Login</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />



</head>

<body>
'.$info.'<br>
<form name="login" action="'.$_SERVER['PHP_SELF'].'" method="post" >
<table>
<tr>
<td>User</td>
<td><input type="text" name="usr_login_name"/></td>
</tr>
<tr>
<td>Passwort</td>
<td><input type="password" name="usr_password"/></td>
</tr>
<tr>

<td><input type="submit" value="Login" /></td>
</tr></table>
</form>

After login, the training data will be transfered as a .csv file, which can be used to make pretty pivot table statistics in a spreadsheet programm like OpenOffice Calc


</body></html>';
exit;
}





?>