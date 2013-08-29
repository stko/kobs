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


	// create dedicated user data
	$showUser=new User($g_db);
	$showUser->getUser($showID);


	$sql = "SELECT
		". TBL_USERS. ".usr_id as usr_id,
		last_name.usd_value as last_name,
		first_name.usd_value as first_name,
		birthday.usd_value as birthday,
		mitgliedsnummer.usd_value as mitgliedsnummer
		FROM ". TBL_USERS . "
		LEFT JOIN ". TBL_USER_DATA. " as last_name
		ON last_name.usd_usr_id = usr_id
		AND last_name.usd_usf_id = ". $showUser->getProperty("Nachname", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as first_name
		ON first_name.usd_usr_id = usr_id
		AND first_name.usd_usf_id = ". $showUser->getProperty("Vorname", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as birthday
		ON birthday.usd_usr_id = usr_id
		AND birthday.usd_usf_id = ". $showUser->getProperty("Geburtstag", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as mitgliedsnummer
		ON mitgliedsnummer.usd_usr_id = usr_id
		AND mitgliedsnummer.usd_usf_id = ". $showUser->getProperty("Mitgliedsnummer", "usf_id"). "
		WHERE usr_valid = 1
		AND usr_id = ".$showID;


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
	// Construct the QRCode out "normalized" first name, last name and birthday
	$normFirstName = mb_convert_encoding($firstName, "ISO-8859-1", "UTF-8"); //convert name from internal format to UTF8
	$normLastName = mb_convert_encoding($lastName, "ISO-8859-1", "UTF-8"); //convert name from internal format to UTF8
	$normBirthday = $row["birthday"]; // use international YY-MM-DD format for birthday
	//---------------------------------------------------------
	// The choosen key to generate a (hopefully) unique QRCode for a person is as follows
	//
	//  code1 = first name (in UTF8) + last name (in UTF8) + birthday (in YY-MM-DD format)
	//
	//  code2 = md5sum(code1)
	//
	//  code3 = last 10 characters of code2 (to not make the QRCode too big)
	//
	//  final QRCode = UPPERString ( code3) (for better reability on print)
	//
	//---------------------------------------------------------

	$printText[3]= strtoupper(substr(md5($normFirstName.$normLastName.$normBirthday),-10)); // QRCode Description Text
	$printText[2]= "KLOBS".$printText[3]; // QRCode content
	$printText[4]= $firstName . " " . $lastName . "(".$thisDate.")"; // QRCode add. Text

	// get the picture
	if(strlen($showUser->getValue("usr_photo")) > 0)
	{
		$printText[5]= $showUser->getValue("usr_photo");
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
