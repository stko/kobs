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

	//Öffnen der XML- Location & Trainingsinhalte- files

	$trainingXML = simplexml_load_file($klobs_training_file);
	$trainingHash=array();
	foreach ($trainingXML->typ as $typ){
		foreach ($typ->subtyp as $subtyp){
			$trainingHash[ $typ->id.":".$subtyp->id ] = $typ->name." : ".$subtyp->name;
		}
	}

	$locationXML = simplexml_load_file($klobs_location_file);
	$locationHash=array();
	foreach ($locationXML->ort as $ort){
		$locationHash[ (string) $ort->ort_id ] = $ort->name;
	}



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
	<SCRIPT LANGUAGE="JavaScript" SRC="./CalendarPopup.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript">
	var cal = new CalendarPopup();
	</SCRIPT>

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

	var userHash = new Object();
<?php
	mysql_data_seek($result_user, 0);
	while($row = $g_db->fetch_array($result_user))
	{
		echo "userHash['".$row["last_name"].", ".$row["first_name"]."'] = '".$row["usr_id"]."';\n";
	}

?>	
      var start = function(){new Suggest.Local("text", "suggest", list);};
      window.addEventListener ?
        window.addEventListener('load', start, false) :
        window.attachEvent('onload', start);
    //-->
    </script>
	<script type="text/javascript" language="javascript">
	<!--
		function chkFormular () {
			if (document.example.pattern.value == "") {
				alert("Bitte gebe auch einen Teilnehmer ein");
				document.example.pattern.focus();
				return false;
			}
			var user_id = userHash[document.example.pattern.value];
			if (user_id == undefined){
				alert(""  + document.example.pattern.value + " ist kein bekannter Teilnehmer" );
				document.example.pattern.focus();
				return false;
			}
			document.example.showID.value = user_id;
			return true;
		}
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


	$isTrainer=count($row)>1;


	$showID=$_REQUEST["showID"];
	if (!isset($showID) || !is_numeric($showID) || !$isTrainer) {
		$showID=$a_user_id; //default
	}


	// Erst mal die Stammdaten des Users abfragen


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
		mitgliedsnummer.usd_value as mitgliedsnummer,
		passnummer.usd_value as passnummer,
		abodatum.usd_value as abodatum,
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
		LEFT JOIN ". TBL_USER_DATA. " as passnummer
		ON passnummer.usd_usr_id = usr_id
		AND passnummer.usd_usf_id = ". $g_current_user->getProperty("Passnummer", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as mitgliedsnummer
		ON mitgliedsnummer.usd_usr_id = usr_id
		AND mitgliedsnummer.usd_usf_id = ". $g_current_user->getProperty("Mitgliedsnummer", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as abodatum
		ON abodatum.usd_usr_id = usr_id
		AND abodatum.usd_usf_id = ". $g_current_user->getProperty("Abodatum", "usf_id"). "
		WHERE usr_valid = 1 AND usr_id = ".$showID."
		ORDER BY last_name, first_name ";

	$db_result = $g_db->query($sql);
	$memberData = $g_db->fetch_array($db_result);//kind of senseless, as this result should only have one row, but...
	echo "<h1>Und hier Deine Details auf einen Blick</h1>\n";
	echo "<h3>&Uuml;bersicht f&uuml;r ".$memberData[first_name]. " ".$memberData[last_name].":</h3>\n";
	
?>
	<table cellspacing="10" cellpadding="20" width="95%">
	<tr><td valign="top">
	<!-- Hier bleibt im Layout noch Platz für ein mögliches späteres Menü -->
	</td><td align="center">
<?php




	//alle Trainingsdaten ziehen
		$sql = "SELECT ". TBL_USERS. ".usr_id as usr_id, last_name.usd_value as last_name, first_name.usd_value as first_name , training.tra_id as tra_id, training.deleted as deleted, training.location as location,training.locationID as locID, training.date as date, training.year as year, training.mon as mon, training.mday as mday, training.wday as wday, training.typ as typ, training.subtyp as subtyp, training.trainerid as trainerid, training.duration as duration


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
		AND ". TBL_USERS. ".usr_id = ".$showID."
		ORDER BY last_name, first_name ";


		$db_result = $g_db->query($sql);

		//Beginn der Ausgabe
		if ($isTrainer){
?>
<FORM NAME="example" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" onsubmit="return chkFormular()">
<table>
	<input type="hidden" name="showID" value="0">
<tr><td>	<input id="text" type="text" name="pattern" value="<?php echo $memberText; ?>" autocomplete="off" size="40" style="display: block"/><div id="suggest"></div></td><td><input type="submit" value="Mitglied ausw&auml;hlen"></td></tr>
</table>
</FORM>
<?php
}
?>	
	<table border="1" rules="all" cellspacing="10" cellpadding="5" width="95%">

<?php
		echo "<tr><td colspan=\"8\"><h4>Dies sind die aktuellen Eintr&auml;ge des Monats ".$month." / ".$year."</h4></td></tr>\n";
		// Ausgabe der Daten
		echo "<tr>";
		echo "<th bgcolor=\"#E0E0E0\">Name</th>";
		echo "<th>Ort</th>";
		echo "<th bgcolor=\"#E0E0E0\">Wann</th>";
		echo "<th>Training</th>";
		echo "<th bgcolor=\"#E0E0E0\">Dauer</th>";
		echo "<th>Edit</th>";
		echo "<th>Copy</th>";
		echo "<th>Delete</th>";
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
		echo "<td bgcolor=\"#E0E0E0\">".$row[last_name].", ".$row[first_name]."</td>\n";
		echo "<td>".$locationHash[$row[locID]]."</td>\n";
		echo "<td bgcolor=\"#E0E0E0\">".$row[date]."</td>\n";
		echo "<td>".$trainingHash[$row[typ].":".$row[subtyp]]."</td>\n";
		echo "<td bgcolor=\"#E0E0E0\">".$row[duration]." min</td>\n";
		echo "<td  align=\"center\" valign=\"middle\"><a href=\"".$_SERVER['PHP_SELF']."?year=".$year."&month=".$month."&action=edit&tra_id=".$row[tra_id]."\"><img src=\"edit.gif\" border=\"0\" alt=\"Edit\"></a></td>\n";
		echo "<td align=\"center\" valign=\"middle\"><a href=\"".$_SERVER['PHP_SELF']."?year=".$year."&month=".$month."&action=copy&tra_id=".$row[tra_id]."\"><img src=\"copy.gif\" border=\"0\" alt=\"Copy\"></a></td>\n";
		echo "<td align=\"center\" valign=\"middle\">\n";
		if ($row[deleted]==1){
			echo "<a href=\"edit.php?action=undel&tra_id=".$row[tra_id]."\"><img src=\"undelete.gif\" border=\"0\" alt=\"Undelete\"></a>\n";
		}else{
			echo "<a href=\"edit.php?action=del&tra_id=".$row[tra_id]."\"><img src=\"delete.gif\" border=\"0\" alt=\"Delete\"></a>\n";
		}
		echo "</td>\n";
		echo "</tr>";
		}

		echo "</table>";
		echo "Um Schaden zu vermeiden, kann man Eintr&auml;ge nur auf \"Deleted\" setzen, aber nicht komplett l&ouml;schen. Die als gel&ouml;scht markierten Eintr&auml;ge werden bei Auswertungen nicht mehr mitgez&auml;hlt.";

?>

</td></tr></table>
<hr><center><small>powered by <a href="http://kobs.googlecode.com">KLOBS</a></small></center>
</div>
</div>
</div>

</body>
</html>

