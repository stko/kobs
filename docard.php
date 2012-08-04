<?php
/******************************************************************************
 * QRCode Karte selber drucken
 *
 * Copyright    : (c) 2010 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : kobs.googlecode.com
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 *****************************************************************************/

	require("../../system/common.php");
	include("./config.php");
	require("../../system/login_valid.php");
	require("qrcards/card.php");

	$a_user_id = $g_current_user->getValue("usr_id");



	$sql = "SELECT
		". TBL_USERS. ".usr_id as usr_id,
		last_name.usd_value as last_name,
		first_name.usd_value as first_name,
		birthday.usd_value as birthday,
		mitgliedsnummer.usd_value as mitgliedsnummer
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
		LEFT JOIN ". TBL_USER_DATA. " as mitgliedsnummer
		ON mitgliedsnummer.usd_usr_id = usr_id
		AND mitgliedsnummer.usd_usf_id = ". $g_current_user->getProperty("Mitgliedsnummer", "usf_id"). "
		WHERE usr_valid = 1
		AND usr_id = ".$a_user_id;


	$result_user = $g_db->query($sql);
	$row = $g_db->fetch_array($result_user);
	if (count($row)<1){
		showError( "Sie sind kein eingetragenes Vereinsmitglied und bekommen daher auch keinen Ausweis");
	}
	// define some personal output values
	$firstName=$row["first_name"];
	$lastName=$row["last_name"];
	$mitgliedsNr=utf8_encode($row["mitgliedsnummer"]);
	if (strlen($mitgliedsNr)<1){
		showError( "Sie sind kein eingetragenes Vereinsmitglied und bekommen daher auch keinen Ausweis");
	}
	$thisDate = date('d.m.Y');
	$bDay=split("-",$row["birthday"]);
	$birthday=$bDay[2] . "." . $bDay[1] . "." . $bDay[0];

	$printText=array();
	$printText[0]= $firstName . " " . $lastName; // Name
	$printText[1]= $birthday . " " . $mitgliedsNr; // Description Text
	$printText[3]=substr(md5($a_user_id),-10); // QRCode Description Text
	$printText[2]= "KLOBS".$printText[3]; // QRCode content
	$printText[4]= $firstName . " " . $lastName . "(".$thisDate.")"; // QRCode add. Text

	// get the picture
	if(strlen($g_current_user->getValue("usr_photo")) > 0)
	{
		$printText[5]= $g_current_user->getValue("usr_photo");
	}
	else
	{
		// es wurde kein Bild gefunden, dann ein Dummy-Bild zurueckgeben
		$printText[5]=  imagecreatefrompng(THEME_SERVER_PATH. "/images/no_profile_pic.png");
	}


	// Generate the Card
	

	printCard($printText);


/* just debug code
	for ($i=0;$i<5;$i++){
		print ("$i: ".$printText[$i]."<br>\n");
	}

*/



/*
	header("Content-Type: image/jpeg");


	if(strlen($g_current_user->getValue("usr_photo")) > 0)
	{
		echo $g_current_user->getValue("usr_photo");
		//print ("Bild gefunden...<br>\n");
	}
	else
	{
		// es wurde kein Bild gefunden, dann ein Dummy-Bild zurueckgeben
		$no_profile_pic = imagecreatefrompng(THEME_SERVER_PATH. "/images/no_profile_pic.png");
		echo imagepng($no_profile_pic);

		//print ("KEIN! Bild gefunden...<br>\n");
	}

*/

	exit(0);

	function showError($info){
		print '
		<html>
		<head>
		<title>Karte Drucken</title>

		</head>

		<body>
		'.$info.'<br>


		</body></html>';
		exit;
	}

?>
