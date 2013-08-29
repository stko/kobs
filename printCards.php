<?php
/******************************************************************************
 * trainingsdaten editieren
 *
 * Copyright    : (c) 2010 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : kobs.googlecode.com
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 * Icons taken from http://www.freeiconsweb.com/16x16_Computer_File_icons.htm
 * Uebergaben:
 *
 *****************************************************************************/
	require("../../system/common.php");
	include("./config.php");
	require("../../system/login_valid.php");


	function mysql_fetch_full_result_array($result)
	{
	$table_result=array();
	$r=0;
	while($row = mysql_fetch_assoc($result)){
		$arr_row=array();
		$c=0;
		while ($c < mysql_num_fields($result)) {
		$col = mysql_fetch_field($result, $c);
		$arr_row[$col -> name] = $row[$col -> name];
		$c++;
		}
		$table_result[$r] = $arr_row;
		$r++;
	}
	return $table_result;
	}

	$a_user_id = $g_current_user->getValue("usr_id");



// Initialisierung: Usernamen mit userID abspeichern

   $sql = "SELECT
		  ". TBL_USERS. ".usr_id as usr_id,
		  last_name.usd_value as last_name,
		  first_name.usd_value as first_name
	    FROM ". TBL_USERS . "
            LEFT JOIN ". TBL_USER_DATA. " as last_name
              ON last_name.usd_usr_id = usr_id
             AND last_name.usd_usf_id = ". $g_current_user->getProperty("Nachname", "usf_id"). "
            LEFT JOIN ". TBL_USER_DATA. " as first_name
              ON first_name.usd_usr_id = usr_id
             AND first_name.usd_usf_id = ". $g_current_user->getProperty("Vorname", "usf_id"). "
            WHERE usr_valid = 1
            ORDER BY last_name, first_name ";


	$result_user = $g_db->query($sql);



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

	// Ist der User Mitglied der $klobs_trainer- Rolle?
	$sql    = "SELECT ". TBL_USERS.".usr_login_name
	FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
	WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
	AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
	AND ". TBL_ROLES . ".rol_name = \"".$klobs_trainer."\"
	AND ". TBL_USERS.".usr_id = ".$a_user_id."";



	$dates_result = $g_db->query($sql);
	$row = $g_db->fetch_array($dates_result);


	$isTrainer=count($row)>1;

	if ( !$isTrainer) {
		exit;
	}

	echo "<h1>$klobs_printCards_header</h1>\n";
	echo "<h3>Klicke auf den gew&uuml;nschten Namen zum Drucken:</h3>\n";


?>

	<table>

	<!-- Hier bleibt im Layout noch Platz für ein mögliches späteres Menü -->
<?php
	mysql_data_seek($result_user, 0);
	while($row = $g_db->fetch_array($result_user))
	{
		echo "<tr><td><a href='docard.php?showID=".$row["usr_id"]."'>".$row["last_name"].", ".$row["first_name"]."</a></td></tr>\n";
	}

?>
	</table>

<hr><center><small>powered by <a href="http://kobs.googlecode.com">KLOBS</a></small></center>
</div>
</div>
</div>

</body>
</html>

