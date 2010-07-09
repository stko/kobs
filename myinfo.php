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
				alert("Bitte gebe ein Mitglied ein");
				document.example.pattern.focus();
				return false;
			}
			var user_id = userHash[document.example.pattern.value];
			if (user_id == undefined){
				alert(""  + document.example.pattern.value + " ist kein bekanntes Mitglied" );
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
	echo "<h1>$klobs_myinfo_header</h1>\n";
	echo "<h3>&Uuml;bersicht f&uuml;r ".$memberData[first_name]. " ".$memberData[last_name].":</h3>\n";
	
?>
	<table cellspacing="10" cellpadding="20" width="95%">
	<tr><td valign="top">
	<!-- Hier bleibt im Layout noch Platz für ein mögliches späteres Menü -->
<?php
	if ($isTrainer){
		echo "<a href=\"trmanage.php\">Trainingszeiten-Editor</a>\n";
	}

?>
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
		AND deleted = 0
		AND ". TBL_USERS. ".usr_id = ".$showID."
		ORDER BY date ";


		$db_result = $g_db->query($sql);

		//Beginn der Ausgabe
		if ($isTrainer){
?>
<hr>
Da Du Mitglied der "<?php echo $klobs_trainer;?>" - Gruppe bist, hast Du die M&ouml;glichkeit, neben deinen Daten auch die der anderen Mitglieder anzusehen:

<FORM NAME="example" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" onsubmit="return chkFormular()">
<table>
	<input type="hidden" name="showID" value="0">
	<tr>
		<td>
			<input id="text" type="text" name="pattern" value="<?php echo $memberText; ?>" autocomplete="off" size="40" style="display: block"/><div id="suggest"></div></td><td><input type="submit" value="Mitglied ausw&auml;hlen">
		</td>
	</tr>
</table>
</FORM>
<hr>
<?php
}


///////////////////////////// Beginn der anwenderspezifischen Auswertung //////////////////////////////
//          Manche Ausgaben  zu den Daten des Mitglieds sind ja abhängig von der aktuellen Klobs- Anwendung
//           diese Fälle können in diesem Bereich hier programmiert werden


		echo "<table border=\"1\" rules=\"all\" cellspacing=\"10\" cellpadding=\"5\" width=\"95%\">\n";
		echo "<tr><td colspan=\"5\"><h4> Allgemeines &uuml;ber Dich</h4></td></tr>\n";
		
		echo "<tr>";
		echo "<th>Vereinsmitgliedschaft:</th>\n";
		if (!isset($memberData[mitgliedsnummer]) || $memberData[mitgliedsnummer]==""){
			echo "<td bgcolor=\"#FFE0E0\">Du bist noch nicht im Verein angemeldet. Dies solltest Du baldm&ouml;glichst tun, da Du z.B. auch nur dann beim Training versichert bist und dann auch Pr&uuml;fungen ablegen kannst</td>\n";
		}else{
			echo "<td bgcolor=\"#E0FFE0\"> Du bist in mindestens einem Verein eingetragen. Das ist gut so, denn damit bist Du z.B. versichert und kannst auch an Pr&uuml;fungen teilnehmen. Deine Mitgliedsnummer lautet ".$memberData[mitgliedsnummer]."</td>\n";
		}
		echo "</tr>";

		echo "<tr>";
		echo "<th>ITBF- Mitgliedschaft:</th>\n";
		if (!isset($memberData[passnummer]) || $memberData[passnummer]==""){
			echo "<td bgcolor=\"#FFE0E0\">Du bist noch nicht im ITBF angemeldet. Dies solltest Du baldm&ouml;glichst tun, da Du nur dann auch Pr&uuml;fungen ablegen kannst</td>\n";
		}else{
			echo "<td bgcolor=\"#E0FFE0\"> Du bist beim ITBF eingetragen. Das ist gut so, denn damit kannst Du auch an Pr&uuml;fungen teilnehmen. Deine ITBF-Nummer lautet ".$memberData[passnummer]."</td>\n";
		}
		echo "</tr>";

		echo "<tr>";
		echo "<th>Pr&uuml;fungsprogramm- Download:</th>\n";
		if (!isset($memberData[abodatum]) || $memberData[abodatum]==""  || strtotime($memberData[abodatum])<time()){
			echo "<td bgcolor=\"#FFE0E0\">Du hast zur Zeit kein Download- Abo und kannst Dir das aktuelle Pr&uuml;fungsprogramm nicht downloaden</td>\n";
		}else{
			echo "<td bgcolor=\"#E0FFE0\"> Du hast ein aktuelles Download-Abo und kannst noch bis zum ".$memberData[abodatum]." immer das aktuelle Pr&uuml;fungsprogramm herunterladen</td>\n";
		}
		echo "</tr>";

		echo "</table>";






///////////////////////////// Ende der anwenderspezifischen Auswertung ////////////////////////////////
		$lastTest="-";
		$lastTestDesc="Bislang keine Prüfung";
		$trData=mysql_fetch_full_result_array($db_result);
		// erstmal einen Scanlauf vorab, wann die letzte Prüfung war
		$totalTimes=array();
		foreach($trData as $row)
		{
			if ($row[typ]==$trainings_type_audit){ // wenn Prüfung
			$lastTest=$row[date];
			$lastTestDesc=$trainingHash[$row[typ].":".$row[subtyp]];
			}else{
				if($trainings_type_seminar != $row[typ] ){
					if (!isset($totalTimes[$row[typ]])){
						$totalTimes[$row[typ]]=array();
					}
					if (!isset($totalTimes[$row[typ]][$row[subtyp]])){
						$totalTimes[$row[typ]][$row[subtyp]]=0;
					}
					$totalTimes[$row[typ]][$row[subtyp]]+=$row[duration];
				}
			}
		}
		unset ($row); // to reset references

		// Ausgabe & gleichzeitige Summenberechnung der Daten seit der letzen Prüfung
		echo "<table border=\"1\" rules=\"all\" cellspacing=\"10\" cellpadding=\"5\" width=\"95%\">\n";
		if ($lastTest == "-"){
			echo "<tr><td colspan=\"5\"><h4> Deine bisherigen Lehrg&auml;nge</h4></td></tr>\n";
		}else{
			echo "<tr><td colspan=\"5\"><h4> Deine Lehrg&auml;nge seit deiner letzten $lastTestDesc am $lastTest</h4></td></tr>\n";
		}
		echo "<tr>";
		echo "<th bgcolor=\"#E0E0E0\">Name</th>";
		echo "<th>Ort</th>";
		echo "<th bgcolor=\"#E0E0E0\">Wann</th>";
		echo "<th>Lehrgang</th>";
		echo "<th bgcolor=\"#E0E0E0\">Dauer</th>";
		echo "</tr>";
		// Initalisierung des Summen- Arrays
		$lastTimes=array();
		foreach($trData as $row)
		{
			if ($row[date]>=$lastTest){
				if($trainings_type_seminar == $row[typ] ){
					echo "<tr>";
					echo "<td bgcolor=\"#E0E0E0\">".$row[last_name].", ".$row[first_name]."</td>\n";
					echo "<td>".$locationHash[$row[locID]]."</td>\n";
					echo "<td bgcolor=\"#E0E0E0\">".$row[date]."</td>\n";
					echo "<td>".$trainingHash[$row[typ].":".$row[subtyp]]."</td>\n";
					echo "<td bgcolor=\"#E0E0E0\">".$row[duration]." min</td>\n";
					echo "</tr>";
				}else{
					if($trainings_type_audit != $row[typ] ){
						if (!isset($lastTimes[$row[typ]])){
							$lastTimes[$row[typ]]=array();
						}
						if (!isset($lastTimes[$row[typ]][$row[subtyp]])){
							$lastTimes[$row[typ]][$row[subtyp]]=0;
						}
						$lastTimes[$row[typ]][$row[subtyp]]+=$row[duration];
					}
				}
			}
		}
		unset ($row); // to reset references
		echo "</table>";


		// Ausgabe  der Leistungsdaten seit der letzen Prüfung
		echo "<table border=\"1\" rules=\"all\" cellspacing=\"10\" cellpadding=\"5\" width=\"95%\">\n";
		if ($lastTest == "-"){
			echo "<tr><td colspan=\"5\"><h4> Dein bisheriges Trainingsprofil</h4></td></tr>\n";
		}else{
			echo "<tr><td colspan=\"5\"><h4> Dein bisheriges Trainingsprofil seit deiner letzten $lastTestDesc am $lastTest</h4></td></tr>\n";
		}
		echo "<tr>";
		echo "<th bgcolor=\"#E0E0E0\">Training</th>";
		echo "<th >Dauer</th>";
		echo "</tr>";
		foreach($lastTimes as $trTypKey => $trTyp)
		{
			foreach($trTyp as $trSubTypKey => $duration)
			{
				echo "<tr>";
				echo "<td>".$trainingHash[$trTypKey.":".$trSubTypKey]."</td>\n";
				echo "<td>$duration</td>\n";
				echo "</tr>";
			}
		}
		unset ($row); // to reset references
		echo "</table>";

		// Ausgabe  der gesamten Leistungsdaten
		echo "<table border=\"1\" rules=\"all\" cellspacing=\"10\" cellpadding=\"5\" width=\"95%\">\n";
		echo "<tr><td colspan=\"5\"><h4> Dein gesamtes Trainingsprofil</h4></td></tr>\n";
		echo "<tr>";
		echo "<th bgcolor=\"#E0E0E0\">Training</th>";
		echo "<th >Dauer</th>";
		echo "</tr>";
		foreach($totalTimes as $trTypKey => $trTyp)
		{
			foreach($trTyp as $trSubTypKey => $duration)
			{
				echo "<tr>";
				echo "<td>".$trainingHash[$trTypKey.":".$trSubTypKey]."</td>\n";
				echo "<td>$duration</td>\n";
				echo "</tr>";
			}
		}
		unset ($row); // to reset references
		echo "</table>";


		// Ausgabe der gesamten Daten
		echo "<table border=\"1\" rules=\"all\" cellspacing=\"10\" cellpadding=\"5\" width=\"95%\">\n";
		echo "<tr><td colspan=\"5\"><h4> Hier alle deine Eintr&auml;ge im &Uuml;berblick:</h4></td></tr>\n";
		echo "<tr>";
		echo "<th bgcolor=\"#E0E0E0\">Name</th>";
		echo "<th>Ort</th>";
		echo "<th bgcolor=\"#E0E0E0\">Wann</th>";
		echo "<th>Training</th>";
		echo "<th bgcolor=\"#E0E0E0\">Dauer</th>";
		echo "</tr>";
		foreach($trData as $row)
		{
			echo "<tr>";
			echo "<td bgcolor=\"#E0E0E0\">".$row[last_name].", ".$row[first_name]."</td>\n";
			echo "<td>".$locationHash[$row[locID]]."</td>\n";
			echo "<td bgcolor=\"#E0E0E0\">".$row[date]."</td>\n";
			echo "<td>".$trainingHash[$row[typ].":".$row[subtyp]]."</td>\n";
			echo "<td bgcolor=\"#E0E0E0\">".$row[duration]." min</td>\n";
			echo "</tr>";
		}
		unset ($row); // to reset references
		echo "</table>";

?>

</td></tr></table>
<hr><center><small>powered by <a href="http://kobs.googlecode.com">KLOBS</a></small></center>
</div>
</div>
</div>

</body>
</html>

