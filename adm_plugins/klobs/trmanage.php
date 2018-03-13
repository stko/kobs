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
	require_once("../../adm_program/system/common.php");
	include("./config.php");
	require_once("../../adm_program/system/login_valid.php");


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
	$trData=$result_user->fetchAll();
	foreach($trData as $row)
	//while($row = $result_user->fetch())
	{
		if (!$firstname){
			echo ",";
		}else{
			$firstname=False;
		}
		echo "'".$row["last_name"].", ".$row["first_name"]."'\n";
	}
	//$result_user->closeCursor();

?>];

	var userHash = new Object();
<?php
	foreach($trData as $row)
	//while($row = $result_user->fetch())
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
			if (document.example.date1.value == "") {
				alert("Bitte gebe auch ein Datum ein");
				document.example.date1.focus();
				return false;
			}
			document.example.usrID.value = user_id;
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

// Darf der angemeldete User Mitglieder editieren?
if(!$gCurrentUser->editUsers()){
$gMessage->show("Du hast leider nicht die notwendigen Rechte, um Trainingsdaten editieren zu d&uuml;rfen..");
}


	if (isset($_REQUEST["year"])){
	  $year=$_REQUEST["year"];
	}
	if (!isset($year) || !is_numeric($year)) {
		$year=0; //default
	}

	if (isset($_REQUEST["month"])){
	  $month=$_REQUEST["month"];
	}
	if (!isset($month) || !is_numeric($month)) {
		$month=0; //default
	}

	if (isset($_REQUEST["action"])){
	  $action=$_REQUEST["action"];
	}
	if (!isset($action)) {
		$action=""; //default
	}
	
	if (isset($_REQUEST["tra_id"])){
	  $tra_id=$_REQUEST["tra_id"];
	}
	if (!isset($tra_id) || !is_numeric($tra_id)) {
		$tra_id=0; //default
	}

	// Zusammensuchen und ausfüllen der Eingabemaske

	$memberText="";
	$locID=0;
	$date="";
	$traTyp="";
	$duration=0;
	if ($tra_id!=0 && ($action=="copy" || $action=="edit")){
		$sql = "SELECT last_name.usd_value as last_name,
		first_name.usd_value as first_name ,
		training.tra_id as tra_id,
		training.locationID as locID,
		training.date as date,
		training.typ as typ,
		training.subtyp as subtyp,
		training.duration as duration


		FROM ". TBL_USERS. "
		LEFT JOIN ". TBL_USER_DATA. " as last_name
		ON last_name.usd_usr_id = ". TBL_USERS. ".usr_id
		AND last_name.usd_usf_id = ". $gProfileFields->getProperty("LAST_NAME", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as first_name
		ON first_name.usd_usr_id = ". TBL_USERS. ".usr_id
		AND first_name.usd_usf_id = ". $gProfileFields->getProperty("FIRST_NAME", "usf_id"). "
		JOIN " . $klobs_training_table . " as training
		ON training.usr_Id = ". TBL_USERS. ".usr_id
		WHERE tra_id = ". $tra_id;


		$db_result = $gDb->query($sql);
		while($row = $db_result->fetch()) //kind of senseless, as this result should only have one row, but...
		{
			$memberText=$row['last_name'].", ".$row['first_name'];
			$locID=$row['locID'];
			$date=$row['date'];
			$traTyp=$row['typ'].":".$row['subtyp'];
			$duration=$row['duration'];
		}
	}
?>



	<h1>Der Klobs-Trainings-Editor</h1>
	
	<table cellspacing="10" cellpadding="20" width="95%">
	<tr><td valign="top">
	<h3>Zeitraum:</h3><br>
	Hier w&auml;hlt man das gew&uuml;nschte Jahr und den gew&uuml;nschten Monat aus:

<?php
$sql = "SELECT DISTINCT training.year as year
	FROM ".$klobs_training_table . " as training ORDER BY year";


	$db_result = $gDb->query($sql);
	echo "<ul>\n";
	while($row = $db_result->fetch())
	{
		if ($row['year']==$year){
			echo "<li><a href=\"".$_SERVER['PHP_SELF']."?year=".$row['year']."\">".$row['year']."</a><br><ul>\n";
			$sql = "SELECT DISTINCT training.mon as mon
			FROM ".$klobs_training_table . " as training WHERE training.year = ".$year." ORDER BY mon";
			$db_result2 = $gDb->query($sql);
			while($row2 = $db_result2->fetch())
			{
				echo "<li><a href=\"".$_SERVER['PHP_SELF']."?year=".$row['year']."&month=".$row2['mon']."\">Monat ".$row2['mon']."</a></li>\n";
			}
			
			echo "</ul></li>\n";
		}else{
			echo "<li><a href=\"".$_SERVER['PHP_SELF']."?year=".$row['year']."\">".$row['year']."</a></li>\n";
		}
	}
	echo "</ul>\n";
	echo "<hr><a href=\"myinfo.php\">Leistungsprofil</a>\n";
	echo "</td><td align=\"center\">\n";


	if ($year!=0 & $month!=0){
	//Falls gefordert, aufrufen alle Leute aus der Datenbank
		$sql = "SELECT last_name.usd_value as last_name,
		first_name.usd_value as first_name ,
		training.tra_id as tra_id,
		training.deleted as deleted,
		training.location as location,
		training.locationID as locID,
		training.date as date,
		training.year as year,
		training.mon as mon,
		training.mday as mday,
		training.wday as wday,
		training.typ as typ,
		training.subtyp as subtyp,
		training.trainerid as trainerid,
		training.duration as duration


		FROM ". TBL_USERS. "
		LEFT JOIN ". TBL_USER_DATA. " as last_name
		ON last_name.usd_usr_id = ". TBL_USERS. ".usr_id
		AND last_name.usd_usf_id = ". $gProfileFields->getProperty("LAST_NAME", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as first_name
		ON first_name.usd_usr_id = ". TBL_USERS. ".usr_id
		AND first_name.usd_usf_id = ". $gProfileFields->getProperty("FIRST_NAME", "usf_id"). "
		JOIN " . $klobs_training_table . " as training
		ON training.usr_Id = ". TBL_USERS. ".usr_id
		WHERE usr_valid = 1 
		AND training.year = ". $year ." 
		AND training.mon = ". $month ."
		ORDER BY last_name, first_name ";


		$db_result = $gDb->query($sql);

		//Beginn der Ausgabe
?>
<FORM NAME="example" action="edit.php" method="get" onsubmit="return chkFormular()">
	<input type="hidden" name="usrID" value="0">
	<input type="hidden" name="action" value="<?php
	if ($action==""){
		echo "copy";
	}else{
		echo $action;
	}
	?>">
	<input type="hidden" name="tra_id" value="<?php echo $tra_id; ?>">
	<input type="hidden" name="year" value="<?php echo $year; ?>">
	<input type="hidden" name="month" value="<?php echo $month; ?>">
	<table border="1" rules="all" cellspacing="10" cellpadding="5" width="95%">
	<tr><td colspan="8"><h4>Hier kann man neue Eintr&auml;ge machen oder Bestehende ver&auml;ndern oder kopieren:</h4></td></tr>
	<tr>
	<th bgcolor="#E0E0E0">Name</th>
	<th>Ort</th>
	<th bgcolor="#E0E0E0">Wann</th>
	<th>Training</th>
	<th bgcolor="#E0E0E0">Dauer</th>
	<th colspan=3>Speichern</th>
	</tr>
	<tr>
	<td>
	<input id="text" type="text" name="pattern" value="<?php echo $memberText; ?>" autocomplete="off" size="40" style="display: block"/>
	<div id="suggest"></div>
	</td>
	<td>
	<select name="locID">
<?php
		foreach ($locationHash as $key => $value){
			if ($key==$locID){
				echo "<option selected value=\"$key\">$value</option>\n";
			}else{
				echo "<option value=\"$key\">$value</option>\n";
			}
		}
?>
	</select>
	</td>
	<td>
	<nobr><INPUT TYPE="text" NAME="date1" VALUE="<?php echo $date; ?>" SIZE=10 readonly="readonly">
	<A HREF="#" onClick="cal.select(document.forms['example'].date1,'anchor1','yyyy-MM-dd'); return false;" NAME="anchor1" ID="anchor1"><img src="date.gif" border="0" alt="Date"></A></nobr>
	</td>
	<td>
	<select name="traTyp">
<?php
		foreach ($trainingHash as $key => $value){
			if ($key==$traTyp){
				echo "<option selected value=\"$key\">$value</option>\n";
			}else{
				echo "<option value=\"$key\">$value</option>\n";
			}
		}
?>
	</select>
	</td>
	<td>
	<select name="duration">
<?php
		for ($i=10;$i<250;$i+=10){
			if ($i==$duration){
				echo "<option selected value=\"$i\">$i min</option>\n";
			}else{
				echo "<option value=\"$i\">$i min</option>\n";
			}
		}
?>
	</select>
	</td>
	<td colspan="3">
	<input type="submit" value="<?php
	if ($action=="edit"){
		Echo "Aktualisieren";
	}else{
		Echo "Als Neu speichern";
	}

	?>">
	</td>
	</tr>
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
		while($row = $db_result->fetch())
		{
		/*	foreach ($row as $key => $value){
			if (!is_numeric($key)) {
				echo "$key: $value\n";
			}
		}
		echo "---\n";
		*/
		echo "<tr>";
		echo "<td bgcolor=\"#E0E0E0\">".$row['last_name'].", ".$row['first_name']."</td>\n";
		echo "<td>".$locationHash[$row['locID']]."</td>\n";
		echo "<td bgcolor=\"#E0E0E0\">".$row['date']."</td>\n";
		echo "<td>".$trainingHash[$row['typ'].":".$row['subtyp']]."</td>\n";
		echo "<td bgcolor=\"#E0E0E0\">".$row['duration']." min</td>\n";
		echo "<td  align=\"center\" valign=\"middle\"><a href=\"".$_SERVER['PHP_SELF']."?year=".$year."&month=".$month."&action=edit&tra_id=".$row['tra_id']."\"><img src=\"edit.gif\" border=\"0\" alt=\"Edit\"></a></td>\n";
		echo "<td align=\"center\" valign=\"middle\"><a href=\"".$_SERVER['PHP_SELF']."?year=".$year."&month=".$month."&action=copy&tra_id=".$row['tra_id']."\"><img src=\"copy.gif\" border=\"0\" alt=\"Copy\"></a></td>\n";
		echo "<td align=\"center\" valign=\"middle\">\n";
		if ($row['deleted']==1){
			echo "<a href=\"edit.php?action=undel&tra_id=".$row['tra_id']."\"><img src=\"undelete.gif\" border=\"0\" alt=\"Undelete\"></a>\n";
		}else{
			echo "<a href=\"edit.php?action=del&tra_id=".$row['tra_id']."\"><img src=\"delete.gif\" border=\"0\" alt=\"Delete\"></a>\n";
		}
		echo "</td>\n";
		echo "</tr>";
		}

		echo "</table></FORM>";
		echo "Um Schaden zu vermeiden, kann man Eintr&auml;ge nur auf \"Deleted\" setzen, aber nicht komplett l&ouml;schen. Die als gel&ouml;scht markierten Eintr&auml;ge werden bei Auswertungen nicht mehr mitgez&auml;hlt.";
	}
	echo "</td></tr></table>\n";



?>
<hr><center><small>powered by <a href="https://github.com/stko/kobs">KLOBS</a></small></center>
</div>
</div>
</div>

</body>
</html>

