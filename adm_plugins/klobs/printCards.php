<?php
/******************************************************************************
 * trainingsdaten editieren
 *
 * Copyright    : (c) 2010-2018 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : https://github.com/stko/kobs
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 * Icons taken from http://www.freeiconsweb.com/16x16_Computer_File_icons.htm
 * Uebergaben:
 *
 *****************************************************************************/
	require("../../adm_program/system/common.php");
	include("./config.php");
	require("../../adm_program/system/login_valid.php");



// Initialisierung: Usernamen mit userID abspeichern

   $sql = "SELECT
		  ". TBL_USERS. ".usr_id as usr_id,
		  last_name.usd_value as last_name,
		  first_name.usd_value as first_name
	    FROM ". TBL_USERS . "
            LEFT JOIN ". TBL_USER_DATA. " as last_name
              ON last_name.usd_usr_id = usr_id
             AND last_name.usd_usf_id = ". $gProfileFields->getProperty("LAST_NAME",  "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as first_name
              ON first_name.usd_usr_id = usr_id
             AND first_name.usd_usf_id = ". $gProfileFields->getProperty("FIRST_NAME", "usf_id"). "
            WHERE usr_valid = 1
            ORDER BY last_name, first_name ";


	$result_user = $gDb->query($sql);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <link rel="stylesheet" href="./base.css" type="text/css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <title>Klobs Mitglieder- Info</title>
    <style type="text/css">
      <!--
        #suggest {
          position: absolute;
          background-color: #FFFFFF;
          border: 1px solid #CCCCFF;
          width: 252px;
        }
        #suggest div {
          padding: 1px;
          display: block;
          width: 250px;
          overflow: hidden;
          white-space: nowrap;
        }
        #suggest div.select{
          color: #FFFFFF;
          background-color: #3366FF;
        }
        #suggest div.over{
          background-color: #99CCFF;
        }
        -->
    </style>
	
  </head>
  <body>
    <div id="all">
      <div id="body">
        <div id="contents">
<?php

// Darf der angemeldete User Mitglieder editieren?
if(!$gCurrentUser->editUsers()){
$gMessage->show("Du hast leider nicht die notwendigen Rechte, um f&uuml;r Andere Karten drucken zu d&uuml;rfen..");
}


	echo "<h1>$klobs_printCards_header</h1>\n";
	echo "<h3>Klicke auf den gew&uuml;nschten Namen zum Drucken:</h3>\n";


?>

	<table>

	<!-- Hier bleibt im Layout noch Platz für ein mögliches späteres Menü -->
<?php
	while($row = $result_user->fetch())
	{
		echo "<tr><td><a href='docard.php?showID=".$row["usr_id"]."'>".$row["last_name"].", ".$row["first_name"]."</a></td></tr>\n";
	}

?>
	</table>

<hr><center><small>powered by <a href="https://github.com/stko/kobs">KLOBS</a></small></center>
</div>
</div>
</div>

</body>
</html>

