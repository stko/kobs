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
    <title>Klobs Training Editor</title>
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

    <script type="text/javascript" src="./suggest.js"></script>
    <script type="text/javascript" language="javascript">
    <!--
      var list = [<?php
	$firstname=True;
	while($row = $g_db->fetch_array($result_user))
	{
		if (!$firstname){
			echo ",";
		}else{
			$firstname=False;
		}
		echo "'".$row["last_name"].", ".$row["first_name"]."'\n";
	}

?>];
      var start = function(){new Suggest.Local("text", "suggest", list);};
      window.addEventListener ?
        window.addEventListener('load', start, false) :
        window.attachEvent('onload', start);
    //-->
    </script>
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

	if (count($row)<2){
		echo "Keine Zugriffsberechtigung auf diesen Bereich...<br>\n";
		exit;
	}

	$year=$_REQUEST["year"];
	$month=$_REQUEST["month"];

	if (!isset($year) || !is_numeric($year)) {
		$year=""; //default
	}

	if (!isset($month) || !is_numeric($month)) {
		$month=""; //default
	}
?>

          <div style="margin-left:30px; margin-top:4px;">
            <input id="text" type="text" name="pattern" value="" autocomplete="off" size="40" style="display: block"/>

            <div id="suggest"></div>
          </div>
 
<?php

	
	echo "<table>\n";
	echo "<tr><td valign=\"top\">\n";
	echo "<h3>Zeitraum:</h3><br>\n";

	$sql = "SELECT DISTINCT training.year as year
	FROM ".$klobs_training_table . " as training ORDER BY year";


	$db_result = $g_db->query($sql);
	echo "<em>Jahr</em><br>\n";
	echo "<ul>\n";
	while($row = $g_db->fetch_array($db_result))
	{
		if ($row[year]==$year){
			echo "<li><a href=\"".$_SERVER[´PHP_SELF´]."?year=".$row[year]."\">".$row[year]."</a><br><em>Monat</em><br><ul>\n";
			$sql = "SELECT DISTINCT training.mon as mon
			FROM ".$klobs_training_table . " as training WHERE training.year = ".$year." ORDER BY mon";
			$db_result2 = $g_db->query($sql);
			while($row2 = $g_db->fetch_array($db_result2))
			{
				echo "<li><a href=\"".$_SERVER[´PHP_SELF´]."?year=".$row[year]."&month=".$row2[mon]."\">".$row2[mon]."</a></li>\n";
			}
			
			echo "</ul></li>\n";
		}else{
			echo "<li><a href=\"".$_SERVER[´PHP_SELF´]."?year=".$row[year]."\">".$row[year]."</a></li>\n";
		}
	}
	echo "<ul>\n";

	echo "</td><td>\n";


	if ($year!="" & $month!=""){
	//Falls gefordert, aufrufen alle Leute aus der Datenbank
		$sql = "SELECT last_name.usd_value as last_name, first_name.usd_value as first_name , training.tra_id as tra_id, training.deleted as deleted, training.location as location, training.date as date, training.year as year, training.mon as mon, training.mday as mday, training.wday as wday, training.typ as typ, training.subtyp as subtyp, training.trainerid as trainerid, training.duration as duration


		FROM ". TBL_USERS. "
		LEFT JOIN ". TBL_USER_DATA. " as last_name
		ON last_name.usd_usr_id = ". TBL_USERS. ".usr_id
		AND last_name.usd_usf_id = ". $g_current_user->getProperty("Nachname", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as first_name
		ON first_name.usd_usr_id = ". TBL_USERS. ".usr_id
		AND first_name.usd_usf_id = ". $g_current_user->getProperty("Vorname", "usf_id"). "
		JOIN " . $klobs_training_table . " as training
		ON training.usr_Id = ". TBL_USERS. ".usr_id
		WHERE usr_valid = 1 
		AND training.year = ". $year ." 
		AND training.mon = ". $month ."
		ORDER BY last_name, first_name ";


		$db_result = $g_db->query($sql);

		//Beginn der Ausgabe
		echo "<table>";

		// Ausgabe der Daten
		echo "<tr>";
		echo "<th>last_name</th>";
		echo "<th>first_name</th>";
		echo "<th>location</th>";
		echo "<th>date</th>";
		echo "<th>year</th>";
		echo "<th>mon</th>";
		echo "<th>mday</th>";
		echo "<th>wday</th>";
		echo "<th>typ</th>";
		echo "<th>subtyp</th>";
		echo "<th>trainerid</th>";
		echo "<th>duration</th>";
		echo "<th></th>";
		echo "</tr>";
		while($row = $g_db->fetch_array($db_result))
		{
		/*	foreach ($row as $key => $value){
			if (!is_numeric($key)) {
				echo "$key: $value\n";
			}
		}
		echo "---\n";
		*/
		echo "<tr>";
		placeTD($row[last_name]);
		echo "<td>".$row[first_name]."</td>\n";
		echo "<td>".$row[location]."</td>\n";
		echo "<td>".$row[date]."</td>\n";
		echo "<td>".$row[year]."</td>\n";
		echo "<td>".$row[mon]."</td>\n";
		echo "<td>".$row[mday]."</td>\n";
		echo "<td>".$row[wday]."</td>\n";
		echo "<td>".$row[typ]."</td>\n";
		echo "<td>".$row[subtyp]."</td>\n";
		echo "<td>".$row[trainerid]."</td>\n";
		echo "<td>".$row[duration]."</td>\n";
		echo "<td>\n";
		echo "<a href=\"edit.php?action=edit&tra_id=".$row[tra_id]."\"><img src=\"edit.gif\" border=\"0\" alt=\"Edit\"></a>\n";
		echo "<a href=\"edit.php?action=copy&tra_id=".$row[tra_id]."\"><img src=\"copy.gif\" border=\"0\" alt=\"Copy\"></a>\n";
		if ($row[deleted]==1){
			echo "<a href=\"edit.php?action=undel&tra_id=".$row[tra_id]."\"><img src=\"undelete.gif\" border=\"0\" alt=\"Undelete\"></a>\n";
		}else{
			echo "<a href=\"edit.php?action=del&tra_id=".$row[tra_id]."\"><img src=\"delete.gif\" border=\"0\" alt=\"Delete\"></a>\n";
		}
		echo "</td>\n";
		echo "</tr>";
		}

		echo "</table>";
	}
	echo "</td></tr></table>\n";

function placeTD($text){
	//echo "<td>".utf8_decode($text)."</td>\n";
	echo "<td>".$text."</td>\n";
}


?>
<hr><center>Strotzt die Site voll H&auml;sslichkeit,<br>ist der Autor knapp mit Zeit..</center>
</div>
</div>
</div>
</body>
</html>

